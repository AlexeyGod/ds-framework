<?php

/**
 * Created by Digital-Solution.Ru web-studio.
 * https://digital-solution.ru
 * support@digital-solution.ru
 *
 * Данный класс является компонентом системы
 *
 */

namespace framework\components;

use framework\core\Application;
use framework\exceptions\ErrorException;
use framework\components\Bundle;

class AssetManager
{
    // Настройки
    const DIRECTORY_SEPARATOR = '/';
    public  $autoReload = false; // Автоматическая чистка и публикация ресурсов
    public  $versionAppend = false; // Автоматическая подстановка версионирования (/file.css?v=3242342342)
    public  $normalizeSeparator = true; // Замена обратных слешей на обычные в заголове и футере
    public  $time = 0; // Временная метка

    protected  $_publishPath = '@root/assets';
    protected  $_publishWebPath = '@web/assets';
    public  $registerBundles = [];

    // Свойства менеджера ресурсов
    protected  $_bundles = []; // Массив необработанных пакетов
    protected  $_bundles_stack = []; // Массив классов обработанных пакетов

    protected  $_sourcePath = []; // Массив исходных путей для копирования
    protected  $_css = [];
    protected  $_js_head = [];
    protected  $_js_footer = [];
    protected  $_js_code = [];

    // Константы позиционирования
    const POS_HEAD = 'POS_HEAD';
    const POS_FOOTER = 'POS_FOOTER';

    public function __construct($options = [])
    {
        // Автоматическая чистка и публикация ресурсов
        if(isset($options['autoReload']))
            $this->autoReload = $options['autoReload'];

        // Автоматическая подстановка версионирования (/file.css?v=3242342342)
        if(isset($options['versionAppend']))
            $this->versionAppend = $options['versionAppend'];

        $this->time = time();

        if($this->autoReload)
        {
            //Application::app()->addLog(static::class, 'Assets', 'очищены');
            $this->clearAssets();
        }
    }

    protected function _clearAssets ($path)
    {
        foreach (glob($path.'/*') as $file)
        {
            if(is_dir($file)) {
                $this->_clearAssets($file);
                rmdir($file);
            }
            else
                unlink($file);
        }
    }

    public function clearAssets()
    {
        //exit('method run!');
        return $this->_clearAssets(Application::app()->getRealPath($this->_publishPath));
    }


/**
 * Устанавливает пакет
 * @param $className
 * @throws ErrorException
 */

    public function setBundle($className)
    {
        if(!class_exists($className)) throw new ErrorException("Попытка установить несуществующий класс $className в AssetManager");
        if(!isset($this->_bundles[$className])) //throw new ErrorException("Пакет $className уже включен в AssetManager");
        {
            $this->_bundles[$className] = Application::app()->createObject($className);
            $slug = Application::app()->getRealPath($this->_publishWebPath.self::DIRECTORY_SEPARATOR.$this->_bundles[$className]->slug);
            $this->registerBundles[$className] = $slug;
            Application::app()->addLog(static::class, $className.' is register in AssetManager', 'slug: '.$slug);
            return $slug;
        }
        else
            return false;
    }

    protected function _mergeWitchBundle($bundle)
    {
        $class= get_class($bundle);
        // Проверка на дубликаты
        if(!in_array($class, $this->_bundles_stack))
        {
            //echo "[[Зависимость: $class включена в пакет]]";
            // Requires/Depends
            if(!empty($bundle->depends))
                $this->_useDepends($bundle->depends);

            // CSS
            $this->_css = array_merge($this->_css, $bundle->css);
            // JS
            if($bundle->jsOptions['position'] == self::POS_HEAD)
                $this->_js_head = array_merge($this->_js_head, $bundle->js);
            else
                $this->_js_footer = array_merge($this->_js_footer, $bundle->js);
            // Other

            // ... to future

            // Add to stack
            $this->_bundles_stack[] = $class;
            $this->registerBundles[$class] = Application::app()->getRealPath($this->_publishWebPath.self::DIRECTORY_SEPARATOR.$bundle->slug);
        }
    }

    protected function _useDepends($dependClasses)
    {
        //Перебор зависимостей
        foreach ($dependClasses as $dependBundle)
        {
            // Проверка на правильность класса
            if(!class_exists($dependBundle))
                throw new ErrorException("AssetManager не может найти класс $dependBundle установленный как зависимость в пакете ресурсов. ".__FILE__." в строке ".__LINE__);
            // Включаем пакет
            $this->_mergeWitchBundle(Application::createObject($dependBundle));
        }
    }

    public function run()
    {
        if(empty($this->_bundles)) return false;

        foreach($this->_bundles as $bundle)
        {
            // Если есть зависимости включаем в их первостепенно
            if(!empty($bundle->depends))
            {
                $this->_useDepends($bundle->depends);
            }
            // Включаем нужный пакет
            $this->_mergeWitchBundle($bundle);
        }

        //Application::app()->addLog(static::class, 'REGISTER ASSETS', var_export($this->registerBundles, true));
        return true;
    }

    public function setJsCode($jsCode, $options = []){
        if(!empty($options['depends']) AND is_array($options['depends']))
            $this->_useDepends($options['depends']);

        $codeIdenty = isset($options['identy']) ? $options['identy'] : 'Code_'.count($this->_js_code);

        if(!isset($this->_js_code[$codeIdenty]))
            $this->_js_code[$codeIdenty] = $jsCode;
        else
            $this->_js_code[$codeIdenty] = $jsCode;
    }

    public function head()
    {
        $output = '';
        // CSS
        if(!empty($this->_css))
            foreach($this->_css as $file)
            {
                $version = '';
                $version = ($this->versionAppend ? '?v='.$this->time : '');
                if(preg_match("#^.+://.+$#isU", $file)) $version = '';

                $output .= '<link rel="stylesheet" href="'.$file.$version.'">'."\n";
            }
        // JS
        if(!empty($this->_js_head))
            foreach($this->_js_head as $file)
            {
                $output .= '<script type="text/javascript" src="'.$file.($this->versionAppend ? '?v='.$this->time : '').'"></script>'."\n";
            }

        return $output;
    }

    public function footer()
    {
        $output = "<!-- footer tags -->\n";
        // JS
        if(!empty($this->_js_footer))
            foreach($this->_js_footer as $file)
            {
                $output .= '<script type="text/javascript" src="'.$file.($this->versionAppend ? '?v='.$this->time : '').'"></script>'."\n";
            }
        // JS Code
        if(!empty($this->_js_code))
        {
            $output .= '<script type="text/javascript">'."\n";
            foreach($this->_js_code as $identy => $script)
            {
                $output .= "<!-- ".$identy." -->\n".$script."\n";
            }
            $output .= "</script>\n";
        }

        // Debug
        if(Application::app()->getConfig('debug') === true)
            $output .= $this->debugCode();

       return ($this->normalizeSeparator ? preg_replace("#\\\#", "/", $output) : $output);
    }

    public function debugCode()
    {
        $return  = '<div style="position: fixed;
        bottom: 0px;
        right: 0px;
        Background: #c1c1c1ad;
        color: darkred;
        height: 30vh;
        width: 100%;
        padding: .5em;
        overflow-y: scroll;
        ">';

        $log = Application::app()->getLog();

        if(count($log) > 0)
        {
            $return .= '<table style="width: 100%; border: 1px solid darkslategrey; border-collapse: collapse; ">';

            foreach ($log as $class => $array) {
                $return .= '<tr><td>'.$class.'</td><td>';

                $return .= '<table style="width: 100%; border: 1px solid darkred; border-collapse: collapse; ">';
                foreach($array as $item)
                    foreach ($item as $key => $value)
                    {
                        $return .= '<tr><td>'.$key.'</td><td>'.$value.'</td></tr>';
                    }
                $return .= '</table>';

                $return .= '</td></tr>';
            }

            $return .= '</table>';
        }



        $return .= '</div>';
        return $return;
    }

    public function getAssetMarkers()
    {
        foreach ($this->registerBundles as $key => $val)
            $markers['[~(@asset:'.$key.')]'] = $val;

        return (is_array($markers) ? $markers : []);
    }

    public function getBundlePath($class)
    {
        return '[~(@asset:'.$class.')]';
    }

}