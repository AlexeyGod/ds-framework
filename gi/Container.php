<?php

/**
 * Created by Digital-Solution.Ru web-studio.
 * https://digital-solution.ru
 * support@digital-solution.ru
 */

namespace framework\gi;

use framework\exceptions\ErrorException;

class Container
{

    protected static $_containers = [];
    protected static $_objects = [];

    protected static function createContainer($class, $options = []) {
        static::$_containers[$class] = $options;
    }
    protected static function updateContainer($class, $options = []) {
        static::$_containers[$class] = array_replace_recursive($options, $options);
    }

    public static function setContainer($class, $options)
    {
        if(!isset(static::$_containers[$class]))
            static::createContainer($class, $options);
        else
            static::updateContainer($class, $options);
    }

    public static function getContainer($containerName){
        if(isset(static::$_containers[$containerName]))
            return static::$_containers[$containerName];
        else
            return false;
    }

    public static function getContainers(){
            return static::$_containers;
    }

    public static function createObject($class, $options = [])
    {
        // Создаем новый на основе DI-контейнера
        if(isset(static::$_containers[$class]))
        {
            $options = array_replace_recursive(static::$_containers[$class], $options);
        }

        if(!class_exists($class))
        {
            throw new ErrorException("CreateObject не может создать объект класса ".$class." (вызван в ".get_called_class().")");
        }

        return new $class($options);
    }
}