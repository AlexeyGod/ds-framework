<?php

/**
 * Created by Digital-Solution.Ru web-studio.
 * https://digital-solution.ru
 * support@digital-solution.ru
 *
 *
 * This a "configStorage object of application used BD"
 */

namespace framework\components;

use framework\core\Application;
use framework\exceptions\ErrorException;
use framework\exceptions\NotInstallFrameworkException;
use framework\helpers\ArrayHelper;


class Settings
{
   public $config = [];
   public $db_class = 'framework\\models\\DbSettings';
   protected $_rows = [];

   public function __construct(Array $config = [])
   {
      $this->config = $config;
      if($this->checkUseDb())
      {
         try {
            $dbConfig = $this->_loadAllConfigFromDb();
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

   protected function _loadAllConfigFromDb()
   {
      $staticClass = $this->db_class;

      $this->_rows = $staticClass::findAll();
      return ArrayHelper::map($this->_rows, 'name', 'value');
   }

   public function reload()
   {
      $this->_loadAllConfigFromDb();
      return $this;
   }

   public function getAllConfig()
   {
      return $this->config;
   }

   public function getRows()
   {
      return $this->_rows;
   }

   public function getConfig($configName)
   {
      return isset($this->config[$configName]) ? $this->config[$configName] : '';
   }

   public function updateSettings($settings = [])
   {
      if($this->checkUseDb())
      {
         $sClass = $this->db_class;
         return $sClass::updateSettings($settings);
      }
      else
         throw new ErrorException("При отсутствии БД изменении настроек не предусмотренно");

   }
}