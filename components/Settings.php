<?php

/**
 * Created by Digital-Solution.Ru web-studio.
 * https://digital-solution.ru
 * support@digital-solution.ru
 */

namespace framework\components;

use framework\core\Application;
use framework\exceptions\NotInstallFrameworkException;
use framework\helpers\ArrayHelper;


class Settings
{
   public $config = [];
   public $db_class = 'framework\\models\\DbSettings';

   public function __construct(Array $config = [])
   {
      $this->config = $config;
      if($this->checkUseDb())
      {
         try {
            $dbConfig = $this->loadAllConfigFromDb();
         }
         catch(\PDOException $e)
         {
            // exit(123);
            throw new NotInstallFrameworkException($e->getMessage());
         }

         $this->config = array_merge($this->config, $dbConfig);
      }
   }

   public function checkUseDb()
   {
      if(!isset($this->config['_system']['use_db']))
         $this->config['_system']['use_db'] = Application::app()->is_component('db');

      return $this->config['_system']['use_db'];
   }

   public function loadAllConfigFromDb()
   {
      $staticClass = $this->db_class;
      return ArrayHelper::map($staticClass::findAll(), 'name', 'value');
   }

   public function getConfig($configName)
   {
      return isset($this->config[$configName]) ? $this->config[$configName] : '';
   }
}