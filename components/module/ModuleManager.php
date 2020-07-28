<?php

/**
 * Created by Digital-Solution.Ru web-studio.
 * https://digital-solution.ru
 * support@digital-solution.ru
 */

namespace framework\components\module;

use framework\models\Modules;
use framework\core\Application;

class ModuleManager
{
   public static function getAllasObjects() {
      if(Application::app()->is_component('db'))
         return Modules::find()->orderBy(['priority' => 'asc'])->all();

      else
         return static::getTmpObjects();
   }

   public static function getTmpObjects() {
      return [];
   }

}