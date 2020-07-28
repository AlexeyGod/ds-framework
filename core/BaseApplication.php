<?php
/**
 * Created by PhpStorm.
 * User: Алексей-дом
 * Date: 19.03.2019
 * Time: 17:15
 */

namespace framework\core;

use framework\exceptions\ErrorException;
use framework\gi\Container;
use framework\DefaultConfig;
use framework\components\module\ModuleManager;
use framework\components\Settings;

class BaseApplication extends Container
{
    public static $_aliases = [];
    protected $_components; // Массив созданных компонентов
    protected $_modules; // Массив созданных модулей
    public $appConfig; // Массив настроек
    protected $_systemConfig; // Массив Всех системных настроек

    public function __construct($appConfig = [])
    {
        $this->appConfig = $appConfig;
        // Обработчик ошибок
        set_exception_handler(['framework\\exceptions\\Handler', 'errorHandler']);

        // Настройка алиасов
        // .. статические
        static::setAlias('@framework', dirname(__DIR__));
        //.. динамические
        if(isset($this->appConfig['aliases']))
            foreach ($this->appConfig['aliases'] as $aliasName => $aliasValue)
            {
                static::setAlias($aliasName, $aliasValue);
            }

        // Настройка DI-Контейнеров
        if(isset($this->appConfig['containers']))
            foreach ($this->appConfig['containers'] as $containerName => $containerOptions)
            {
                static::setContainer($containerName, $containerOptions);
            }

        // Запуск базовых компонентов
        if(empty($this->appConfig['components'])) exit("Build error: components list in config is empty");

        foreach ($this->appConfig['components'] as $componentName => $componentOptions)
        {
            if(!class_exists($componentOptions['class']))
                throw new ErrorException("Не удается создать компонент ".$componentName." класса ".$componentOptions['class']." проверьте конфигурацию");

            $this->_components[$componentName] = static::createObject($componentOptions['class'], $componentOptions['options']);
        }

        // Запуск модулей
        $modules = ModuleManager::getAllasObjects();
        foreach ($modules as $module)
        {
            $this->__setModule($module);
        }

        // Запуск модуля отладки
       if(isset($this->appConfig['debug']))
       {
           if($this->appConfig['debug'])
           {
               $debug = new \stdClass();
               $debug->name = 'debug';
               $debug->class = 'framework\\helpers\\debug\\DebugModule';
               $this->__setModule($debug);
           }
       }
    }

    // Регистрация модулей
    protected function __setModule($moduleArObject)
    {
        $object = $this->createObject($moduleArObject->class);
        // Есть модуль содержит маршруты - добавляем их
        if(count($object->routes) > 0)
        {
            $this->urlManager->addRules($object->routes);
        }

        // Включаем модуль в список
        $this->_modules[$moduleArObject->name] = $object;
    }

    // Проверка на существование компонента
    public function is_component($componentName)
    {
        if(isset($this->_components[$componentName]))
            return true;
        else
            return false;
    }

    // Модули
    public function getModule($moduleName)
    {
        if(isset($this->_modules[$moduleName]))
            return $this->_modules[$moduleName];
        else
            return null;
    }

    public function is_module($moduleName)
    {
        return isset($this->_modules[$moduleName]);
    }

    // Алиасы
    // Получить
    public static function getAlias($aliasName)
    {
        return (isset(static::$_aliases[$aliasName]) ? static::$_aliases[$aliasName] : null);
    }
    // Установить
    public static function setAlias($aliasName, $value)
    {
        static::$_aliases[$aliasName] = $value;
    }
    // Получиить путь с заменой алиасов
    public static function getRealPath($path)
    {
        return str_replace('//', '/', str_replace(array_keys(static::$_aliases), static::$_aliases, $path));
    }

    public function getConfig($configName){
        return Settings::getConfig($configName);
    }

    // Магический метод для доступа к компонентам
    public function __get($name)
    {
        if(isset($this->_components[$name]))
            return $this->_components[$name];
        else
            return false;
    }

    public function getComponentList()
    {
        return array_keys($this->_components);
    }

    public function addLog($class, $name, $value)
    {
        if($this->is_component('logger'))
        {
            Application::app()->logger->setLog($class, $name, $value);
        }
    }

    public function getLog()
    {
        if($this->is_component('logger'))
        {
            return Application::app()->logger->getLog();
        }
        else
            throw new ErrorException("Ошибка при попытке получить данные из LOG. Компонент logger не сконфигурирован.");
    }

    public function clearLog()
    {
        if($this->is_component('logger'))
        {
            return Application::app()->logger->clearLog();
        }
    }
}