<?php

/**
 * Created by Digital-Solution.Ru web-studio.
 * https://digital-solution.ru
 * support@digital-solution.ru
 */

namespace framework\components\module;

use framework\components\rbac\Role;
use framework\core\Application;
use framework\exceptions\ErrorException;


class ModuleComponent implements ModuleInterface
{
   const INSTALL_SUCCESS = 'ok';
   const INSTALL_ERROR= 'error';

   static $moduleName = '';

   public $routes = [];


   protected $_menu = [];


   public function __construct($options = [])
   {

   }

   public function getControllerNamespace()
   {
      $path = explode('\\', static::class);
      $last = count($path)-1;

      unset($path[$last]);

      $path[] = 'controllers';

      return implode('\\', $path);

   }

   public function moduleName()
   {
      return basename(static::class);
   }

   public function contextMenu()
   {
      if(property_exists(static::class, '_menu'))
         return $this->_menu;
   }

   public static function install()
   {
      // В модуле должны быть добавлены используемые им роли
     
      // и полномочия
      // ..
      //**** все полномочия  и роли должны быть присвоены пользователю, который добавляет модуль

      // Внесены необходимые изменения в структуру БД


      // При успешной установке модуль должен возвращать массив
      /*
      return [
            'status' => self::INSTALL_SUCCESS,
            'data' => 
                [
                    'name' => self::$moduleName,
                    'class' => static::class,
                    'icon' => 'icon icon-diamonds',
                    'priority' => '0',
                    'status' => '0', // 1 - если включено
                    'system' => '0' // 1 - если сиcтемное
                ]   
            ];
      */
      return false;
   }

   public static function unInstall()
   {
      return false;
   }

   public static function getModuleInstallPath()
   {
      $reflection = new \ReflectionClass(static::class);
      return dirname($reflection->getFileName());
   }
}