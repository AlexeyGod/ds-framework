<?php
/**
 * Created by Digital-Solution.Ru web-studio.
 * https://digital-solution.ru
 * support@digital-solution.ru
 *
 *
 * Класс Route - компонент приложения, которое можно сконфигурировать черрез DI контейнер.
 * Запускается приложением (Application) при старте Через метод run()
 * Обрабатывает запрос REQUEST_URI и передает действие контроллеру
 */

namespace framework\components;


use framework\core\Application;
use framework\exceptions\AccessDeniedException;
use framework\exceptions\ErrorException;
use framework\exceptions\NotFoundHttpException;
use framework\models\Modules;

class RouteBak
{
    // Основные
    public static $controllerNamespaces = 'application\\controllers';

    public $modules = [];

    protected $_request = false;
    protected $roadMap = [];

    protected $_moduleName = false;
    protected $_moduleClass = false;

    protected $_controllerName = false;
    protected $_controllerClass = false;

    protected $_actionName = false;
    protected $_actionExecuteName = false;

    protected $_params = [];
    protected $_get_data = [];

    const DEFAULT_CONTROLLER = 'default';
    const DEFAULT_ACTION = 'index';
    const DEFAULT_ERROR_ACTION = 'error';

    // Конструктор
    public function __construct($options = [])
    {
        // Загрузка маршрутов
        if(!empty($options['roadMap']))
            $this->roadMap = $options['roadMap'];
        // Нормализуем запрос
        $this->_request = self::requestNormalize(Application::app()->request->uri());
        // Подгрузка всех модулей
        $this->modules = Modules::getList();
        //exit("Modules: ".var_export($this->modules, true));
    }

    // Сеттеры
    protected function setModule($name) {

        $this->_moduleName = $name;
        $mClass = $this->getModuleClass($name);;
        $this->_moduleClass = $mClass;

        if(!empty($mClass::$access) AND !Application::app()->identy->can($mClass::$access))
        {
            throw new AccessDeniedException("Вам запрещен доступ в этот раздел");
        }

        // Namespaces для контроллеров модуля
        if(property_exists($this->_moduleClass, 'controllerNamespaces'))
        {
            $staticClass = $this->_moduleClass;
            static::$controllerNamespaces = $staticClass::$controllerNamespaces;
        }
        else
        {
            $path = explode('\\', $this->_moduleClass);
            unset($path[(count($path)-1)]);
            static::$controllerNamespaces = implode("\\", $path).'\\controllers';
        }
    }

    protected function setController($name) {
        $this->_controllerName = $name;
        $this->_controllerClass = $this->getControllerClass($name);
    }

    protected function setAction($name) {
        $this->_actionName = $name;
        $this->_actionExecuteName = self::actionNormalize($name);
    }

    // Геттеры
    public function getModule($return = 'name') {}
    public function getController($return = 'name') {}
    public function getAction($return = 'name') {}

    public function getControllerNamespaces() {
        return static::$controllerNamespaces;
    }

    // Нормализация GET-Запроса
    public static function requestNormalize($name)
    {
        if(substr($name, 0, 1) == '/')
            $name = substr($name, 1);

        if(substr($name, -1) == '/')
            $name = substr($name,0, -1);

        return $name;
    }

    // Нормализует часть $name
    public static function partNormalize($name)
    {
        $newName = '';

        $parts = explode('-', $name);
        foreach($parts as $part)
        {
            $newName .= ucfirst($part);
        }

        return $newName;
    }

    // Нормализация экшена для запуска
    public static function actionNormalize($name)
    {
        return 'action'.self::partNormalize($name);
    }

    public function getModuleClass($name)
    {
        $module = Modules::getByName($name);
        //return 'application\\modules\\'.strtolower($name).'\\'.self::partNormalize($name).'Module';
        return $module->class;
    }

    public function getControllerClass($name)
    {
        return static::$controllerNamespaces.'\\'.self::partNormalize($name).'Controller';
    }

    // Проверяет является ли $name модулем
    protected function is_module($name)
    {
        return in_array($name, $this->modules);
    }

    // Проверяет является ли $name контроллером
    protected function is_controller($name)
    {
        return class_exists($this->getControllerClass($name));
    }

    // Проверяет является ли $name действием
    protected function is_action($controllerClass, $actionName)
    {
        return method_exists($controllerClass, self::actionNormalize($actionName));
    }

    // Определение параметров метода
    public static function getMethodParams($class, $method)
    {
        $params = [];
        if(!method_exists($class, $method))
            throw new ErrorException("Метод $method не найден в классе $class");
        // Определяем рефлексию
        $reflection = new \ReflectionMethod($class, $method);
        $objects = $reflection->getParameters();

        if(count($objects) == 0) return $params;
        else
            foreach($objects as $object)
            {
                $params[] = $object->name;
            }

        return $params;
    }

    // Сверка параметров
    public function checkUrlParams($urlParts, $start,  $methodParams)
    {
        // Обнуляем параметры
        $this->_params = [];

        // Подготавливаем параметры URL
        $urlArray = [];
        $insert = true;
        $lastKey = 0;

        foreach($urlParts as $key => $value)
        {
            if($key < $start) continue;

            if($insert) {
                $urlArray[$lastKey] = $value;
                $insert = false;
                $lastKey++;
            }
            else
            {
                $this->_params[$urlArray[($lastKey-1)]] = $value;
                $insert = true;
            }
        }

        $diff = array_diff($urlArray, $methodParams);

       return empty($diff);
    }

    // Подготовка для роутинга с учетом модуля
    protected function moduleRoute($moduleName)
    {
        $this->setModule($moduleName);
        $offset = strlen($moduleName);
        $this->_request = static::requestNormalize(substr($this->_request, $offset));

        return $this->route();
    }

    // Разбор URL
    public function route()
    {
        if($this->_request == '')
        {
            $this->setController(self::DEFAULT_CONTROLLER);
            $this->setAction(self::DEFAULT_ACTION);
            return $this->executeController();
        }

        // Разбиваем на части
        // 1. Отсекаем параметры
        $url_parts = explode('?', $this->_request);

        if(!empty($url_parts[1]))
            $getData = explode('=', $url_parts[1]);

        if(!empty($getData))
        {
            $before = 'UNDEFINED';

            foreach ($getData as $key => $value)
            {
                if($key % 2)
                {
                    $this->_get_data[$before] = trim($value);
                }
                else
                {
                    $before = trim($value);
                }
            }
        }

        // 2. Разбиваем адрес на части
        $request_parts = explode('/', $url_parts[0]);

        // Проверка на модуль
        if($this->is_module($request_parts[0]) and empty($this->_moduleName)) {
            // Запуск роутинга с учетом модуля
            return $this->moduleRoute($request_parts[0]);
        }

        //exit(var_dump($request_parts));

        // Проверка на действие контроллера по умолчанию
        if($this->is_action($this->getControllerClass(self::DEFAULT_CONTROLLER), $request_parts[0]))
        {
            $defaultParams = self::getMethodParams($this->getControllerClass(self::DEFAULT_CONTROLLER), self::actionNormalize($request_parts[0]));
            $this->checkUrlParams($request_parts, 1, $defaultParams);

            $this->setController(self::DEFAULT_CONTROLLER);
            $this->setAction($request_parts[0]);
            return $this->executeController();
        }

        // Проверка на контроллер
        if($this->is_controller($request_parts[0]))
        {
            $this->setController($request_parts[0]);
            // Параметры контроллера по умолчанию, если он существует
            if(method_exists($this->_controllerClass,self::actionNormalize(self::DEFAULT_ACTION)))
            {
                $defaultParams = self::getMethodParams($this->_controllerClass, self::actionNormalize(self::DEFAULT_ACTION));
                if($this->checkUrlParams($request_parts, 1, $defaultParams))
                {
                    // Запуск метода по умолчанию
                    $this->setAction(self::DEFAULT_ACTION);
                    return $this->executeController();
                }
            }

                // Ищем метод контроллера
                if($this->is_action($this->_controllerClass, $request_parts[1]))
                {
                    $defaultParams = self::getMethodParams($this->_controllerClass, self::actionNormalize($request_parts[1]));
                    $this->checkUrlParams($request_parts, 2, $defaultParams);
                    $this->setAction($request_parts[1]);
                    return $this->executeController();
                }
                else
                    return $this->notFound();

        }
        else
        {
            //exit("c:". $this->getControllerClass($request_parts[0]));
            return $this->notFound();
        }
    }

    // Выброс ошибки 404
    public function notFound()
    {
        $this->registerStatus();
        throw new NotFoundHttpException();
    }

    // Регистрация статуса
    public function registerStatus()
    {
        Application::app()->request->registerRouting(
            [
                'final_request' => $this->_request,
                'module' => ['class' => $this->_moduleClass, 'name' =>$this->_moduleName],
                'controller' => ['class' => $this->_controllerClass, 'name' =>$this->_controllerName],
                'action' => ['execute' => $this->_actionExecuteName, 'name' =>$this->_actionName],
                'params' => $this->_params,
            ]
        );
    }

    // Запуск контроллера
    public function executeController()
    {
        $controller = Application::createObject($this->_controllerClass, [
            'moduleName' => $this->_moduleName,
            'moduleClass' => $this->_moduleClass
        ]);

        $this->_params = array_merge($this->_params, $this->_get_data);

        $this->registerStatus();
        //exit(var_export(array_merge($this->_params, $this->_get_data), true));

        return call_user_func_array([$controller, $this->_actionExecuteName], $this->_params);
    }

    // Маршруты
    public function loadMap()
    {
        if(!empty($this->roadMap))
            foreach ($this->roadMap as $pattern => $replacePattern)
            {
                if(preg_match('~'.$pattern.'~', $this->_request, $matches))
                {
                    foreach ($matches as $keyMatch => $valueMatch)
                    {
                        if(is_numeric($keyMatch)) continue; //unset($matches[$keyMatch]);
                        $this->_request = str_replace('{'.$keyMatch.'}', $valueMatch, $replacePattern);
                    }

                    $this->_request = self::requestNormalize($this->_request);
                    //exit('matched! : '.var_dump($matches));
                    break;
                }
            }
    }

    // Запуск
    public function run()
    {
        if(!empty($this->_request))
            $this->loadMap();
        $this->route();
        echo 'Routing is end :)';
        $this->registerStatus();
        echo '<br><div><pre>'.var_export(Application::app()->request->getRouting(), true).'</pre></div>';
    }



}