<?php

/**
 * Created by Digital-Solution.Ru web-studio.
 * https://digital-solution.ru
 * support@digital-solution.ru
 */

namespace framework\components;

use framework\core\Application;
use framework\models\DbSetting;

class Settings
{
   public static function checkUseDb()
   {
      return Application::app()->is_component('db');
   }

   public static function getConfig($configName)
   {
      if(static::checkUseDb())
      {
         return DbSetting::getConfig($configName);
      }

      else
         return static::getStaticConfig($configName); 
   }

   public static function getStaticConfig($configName)
   {
      switch ($configName):

         case 'theme':
            return 'basic';
         break;

         case 'secretKey':
            return getenv('HTTP_HOST');
         break;

         default:
         return false;
         break;

      endswitch;
   }
}