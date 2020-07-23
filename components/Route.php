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
use framework\components\routing\UrlManager;
use framework\components\routing\UrlRule;
use framework\helpers\Words;


class Route
{
    // Значения по-умолчанию
    public $defaultController = 'default';
    public $defaultAction = 'index';
    public $defaultControllerNamespace = 'application\\controllers';

    public $errorPage = 'default/error';
    public $controllerNamespace = 'application\\controllers';


    public $module = false;
    public $controller = false;
    public $action = false;

    public $params = [];

    public $route;

    public $moduleClass;

    /**
    * Свойство $status массив, который будет заполнен после роутинга (ф-ия execute())
     * Ключи:
     * module
     * controller
     * action
     */
    public $status = [];


    public function __construct($options = [])
    {
        if(is_array($options))
        {
            if(count($options) > 0)
                foreach ($options as $key => $val)
                {
                    if(property_exists($this, $key)) $this->$key = $val;
                }
        }

    }

    public function run()
    {
        $uri = Application::app()->request->getCurrent();

        $route = Application::app()->urlManager->parseUrl($uri, $_GET);

        $this->route = $route;

        //exit(var_dump($route));

        if(!$route)
            return $this->notFound();

        return $this->route($route);
    }

    public function actionNormalize($action)
    {
        if(strpos($action, '-') !== false)
        {
            $newString = '';
            $ex = explode("-", $action);
            foreach($ex as $st)
                $newString .= ucfirst($st);

            $action = $newString;
        }
        return 'action'.ucfirst($action);
    }

    public function controllerNormalize($controller)
    {
        if(strpos($controller, '-') !== false)
        {
            $parts = explode('-', $controller);
            $controller = '';
            foreach($parts as $part)
            {
                $controller .= ucfirst($part);
            }
        }

        return $this->controllerNamespace.'\\'.ucfirst($controller).'Controller';
    }


    public function is_action($controller = false, $action)
    {
        $controller = (!$controller ? $this->defaultController : $controller);
        $controller = $this->controllerNormalize($controller);

        return method_exists($controller, $this->actionNormalize($action));
    }

    public function is_controller($controller)
    {
        return class_exists($this->controllerNormalize($controller));
    }

    public function is_module($module)
    {
       return Application::app()->is_module($module);
    }

    public function setModule($moduleName)
    {
        $this->module = $moduleName;
        $module = Application::app()->getModule($moduleName);
        $this->moduleClass = $module->class;
        $this->controllerNamespace = $module->getControllerNamespace();
    }

    public function unSetModule()
    {
        $this->module = false;
        $this->moduleClass = false;
        $this->controllerNamespace = $this->defaultControllerNamespace;
    }

    public function route($route)
    {
        $url = $route['route'];
        $params = $route['params'];

        $this->status['route-executed'] = $route;
        $routes = explode('/', $url);

        $this->params = $params;

        switch(count($routes)):
            /**
            * Адрес /post - это может быть действие по умолчанию или контроллер или модуль
             */
            case 1:
                if($this->is_action($this->defaultController, $routes[0]))
                {
                    $this->controller = $this->defaultController;
                    $this->action = $routes[0];
                    return $this->execute();
                }
                if($this->is_controller($routes[0]))
                {
                    if($this->is_action($routes[0], $this->defaultAction))
                    {
                        $this->controller = $routes[0];
                        $this->action = $this->defaultAction;
                        return $this->execute();
                    }

                }
                if($this->is_module($routes[0]))
                {
                    $this->setModule($routes[0]);

                    if($this->is_action($this->defaultController, $this->defaultAction))
                    {
                        $this->controller = $this->defaultController;
                        $this->action = $this->defaultAction;
                        return $this->execute();
                    }
                    else
                    {
                        $this->unSetModule();
                    }
                }
                break;
            /**
            * Для адресов /post/manage - это может быть контроллер-действие или модуль-контроллер или модуль-действие контроллера по умолчанию
             */
            case 2:
                if($this->is_controller($routes[0]))
                {
                    if($this->is_action($routes[0], $routes[1]))
                    {
                        $this->controller = $routes[0];
                        $this->action = $routes[1];
                        return $this->execute();
                    }

                }
                // ..модуль-контроллер
                if($this->is_module($routes[0]))
                {

                    $this->setModule($routes[0]);

                    // модуль-контроллер
                    if($this->is_controller($routes[1]))
                    {
                        // действие по умолчанию
                        if($this->is_action($routes[1], $this->defaultAction))
                        {
                            $this->controller = $routes[1];
                            $this->action = $this->defaultAction;
                            return $this->execute();
                        }

                    }

                    // Действие контроллера по умолчанию (в модуле)
                    if($this->is_action($this->defaultController, $routes[1]))
                    {
                        $this->controller = $this->defaultController;
                        $this->action = $routes[1];
                        return $this->execute();
                    }
                }
                break;

                /**
                * Для адресов blog/post/view - модуль-контроллер действие
                 */
                case 3:
                    if($this->is_module($routes[0]))
                    {
                        $this->setModule($routes[0]);
                        if($this->is_action($routes[1], $routes[2]))
                        {
                            $this->controller = $routes[1];
                            $this->action = $routes[2];
                            return $this->execute();
                        }
                    }
                break;

        endswitch;

        return $this->notFound();
    }

    public function notFound()
    {
        if(true)
        {
            $url = Application::app()->request->getCurrent();

            echo '<h1>Request: <code>'.(empty($url) ? '(Пусто)' : $url).'</code></h1>';
            echo '<table width="100%" border="1">';

            //$rules = array_reverse(Application::app()->urlManager->rules);
            $rules = Application::app()->urlManager->getRules();

            echo '<tr>'
                .'<th>Pattern</th>'
                .'<th>Route</th>'
                .'<th>Check</th>'
                .'</tr>';
            foreach($rules as $rule)
            {
                $check = $rule->parseUrl($url, $_GET);
                echo "<tr".($check ? ' style="background: #5ef15e"' : '').">";
                echo '<td>'.($rule->pattern == '' ? '(пусто)' : htmlspecialchars($rule->pattern)).'</td>';
                echo '<td>'.($rule->route == '' ? '(пусто)' : htmlspecialchars($rule->route)).'</td>';
                echo '<td>'.($check ? '<b>OK</b>' : 'no').'</td>';
                echo "</tr>";

            }
            echo "</table>";
            //exit('DEBUG');

        }
        exit('404: Not found (re-work in '.__FILE__.' on line '.__LINE__.')'.PHP_EOL
            .'Request: '.Application::app()->request->getCurrent().PHP_EOL
            //.'is_module: '.var_export($this->is_module('content'), true).PHP_EOL
            .var_export($this->route, true)
            .'Status: '.var_export($this->status, true)
        );
    }

    public function execute()
    {
       // echo '<pre>'
       //     .'route: '.$this->route['route'].PHP_EOL
       //     .'m: '.$this->module.PHP_EOL
       //     .'c: '.$this->controller.'('.$this->controllerNormalize($this->controller).')'.PHP_EOL
       //     .'a: '.$this->action.'('.$this->actionNormalize($this->action).')'.PHP_EOL
       //     .'Params: '.var_export($this->params, true).PHP_EOL
       //     .'Routes: '.htmlspecialchars(var_export(Application::app()->urlManager->rules, true)).PHP_EOL
       //     .'</pre>';

        $this->status['module'] = $this->module;
        $this->status['controller'] = $this->controller;
        $this->status['action'] = $this->action;
        $this->status['params'] = $this->params;

        $options = [];
        if($this->module) $options['moduleName'] = $this->module;
        if($this->moduleClass) $options['moduleClass'] = $this->moduleClass;

        $controller = Application::app()->createObject($this->controllerNormalize($this->controller), $options);
        $action = $this->actionNormalize($this->action);

        return call_user_func_array([$controller, $action], $this->params);
        return $controller->$action($this->params);
    }
}