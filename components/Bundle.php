<?php

/**
 * Created by Digital-Solution.Ru web-studio.
 * https://digital-solution.ru
 * support@digital-solution.ru
 */

namespace framework\components;

use framework\core\Application;
use framework\exceptions\ErrorException;
use framework\helpers\FileManager;


class Bundle
{
   /*
    * Все нижеприведнные переменные должны быть массивом
    * */
   public  $webPath = '';
   public  $sourcePath = '';

   public  $js = [];
   public  $css = [];

   public $cssOptions = [];
   public $jsOptions = [];

   public  $depends = [];

   public $slug;

   public $assetsPath = '@root/assets';
   public $assetsWebPath = '/assets';

   const DIRECTORY_SEPARATOR = '/';

   public function __construct($options = [])
   {
      // Определяем метку
      $this->_setSlug();
      // пользовательская инициализация
      $this->init();

      // Если пакет требует публикации - публикуем
      if(!empty($this->sourcePath))
        $this->_publish();
      else // Если не требует - нормализуем адрес ресурсов с учетом WEB пути
      {
         $this->js = $this->_webLinksNormalize($this->js);
         $this->css = $this->_webLinksNormalize($this->css);
      }
   }

   public function init()
   {
      return true;
   }

   protected function _setSlug()
   {
      $this->slug = substr(md5(strtolower(substr(basename(static::class),0,-6))), 0, 10);
   }

   public static function register()
   {
      $slug = Application::app()->assetManager->setBundle(static::class);
      Application::app()->addLog(static::class, 'register in AssetManager', static::class);

      return $slug;
   }

   protected function _publish()
   {
      $destination = Application::app()->getRealPath($this->assetsPath.self::DIRECTORY_SEPARATOR.$this->slug);
      $source = Application::app()->getRealPath($this->sourcePath);


      if(!is_dir($destination)) // Директория не существует - проект не опубликован
      {
         // создаем папку
         mkdir($destination);
         // Перемещаем все ее содержимое
         FileManager::copyDir($source, $destination);

         Application::app()->addLog(static::class, 'Bundle published', 'Bundle published in '.$destination);
      }
      else
      {
         Application::app()->addLog(static::class, 'Bundle not published', 'Bundle exists in '.$destination);
      }
      // Нормализуем адреса
      $this->js = $this->_linksNormalizeWitchSlug($this->js);
      $this->css = $this->_linksNormalizeWitchSlug($this->css);

   }

   protected function _webLinksNormalize($array)
   {
      if(!empty($array))
      {
         $normalize = [];
         foreach($array as $fileSrc)
         {
            $normalize[] = $this->webPath.self::DIRECTORY_SEPARATOR.$fileSrc;
         }
         return $normalize;
      }
      else
         return [];

   }

   protected function _linksNormalizeWitchSlug($array)
   {
      if(!empty($array))
      {
         $normalize = [];
         foreach($array as $fileSrc)
         {
            if(!preg_match("#^.+://.+$#isU", $fileSrc))
            $normalize[] = $this->assetsWebPath.self::DIRECTORY_SEPARATOR.$this->slug.self::DIRECTORY_SEPARATOR.$fileSrc;
            else
               $normalize[] = $fileSrc;
         }
         return $normalize;
      }
      else
         return [];

   }

   public static function getActiveThemeBundle()
   {
      $theme = Application::app()->getConfig('theme');
      return 'themes\\'.$theme.'\\'.ucfirst($theme).'Bundle';
   }
}