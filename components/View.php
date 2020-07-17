<?php
/**
 * Created by Digital-Solution.Ru web-studio.
 * https://digital-solution.ru
 * support@digital-solution.ru
 */

namespace framework\components;


use framework\core\Application;
use framework\exceptions\ErrorException;

class View
{
    protected $identity;

    protected $viewPath;
    protected $layoutPath;
    protected $layout_file;
    protected $view_file;
    protected $_variables;
    protected $_controllerName;
    protected $_defaultCharset;
    protected $_theme;
    protected $_asset;

    protected $_moduleName;
    protected $_moduleClass;

    public $assetManager;

    const DEFAULT_THEME = 'basic';
    const DEFAULT_LAYOUT = 'main';
    const DEFAULT_VIEW = 'index';
    const DEFAULT_VIEW_PATH = '@webroot/themes/';
    const DEFAULT_LAYOUT_PATH = '@webroot/themes/';

    public $breadcrumbs = [];

    public $title;


    public function __construct($options = [])
    {
        $this->identity = mt_rand(0,9999999999999);

        // Заполнение из DI-Контейнера
        if(isset($options['controller']))
            $this->_controllerName = $options['controller'];
        else
        {
            throw new ErrorException("Не указан контроллер для вида");
        }

        // Модульность
        $this->_moduleClass = (isset($options['moduleClass']) ? $options['moduleClass'] : false);
        $this->_moduleName = (isset($options['moduleName']) ? $options['moduleName'] : false);

        // Получение AssetManager
        $this->assetManager = Application::app()->assetManager;

        // Папка с представлениями
        $this->viewPath = (!empty($options['viewPath']) ? $options['viewPath'] : self::DEFAULT_VIEW_PATH);
        $this->layoutPath = (!empty($options['layoutPath']) ? $options['layoutPath'] : self::DEFAULT_LAYOUT_PATH);
        Application::app()->addLog(static::class, '__constructor_layoutPath', $this->layoutPath);

        // Определение темы
        $this->_theme = (Application::app()->getConfig('theme') == '' ? self::DEFAULT_THEME : Application::app()->getConfig('theme'));
        $this->_variables = (isset($options['variables']) ? $options['variables'] : []);
        $this->_defaultCharset = (isset($options['defaultCharset']) ? $options['defaultCharset'] : 'utf-8');

        // Нормализация
        $this->layout_file = $this->layoutFileNameNormalize(!empty($options['layout']) ? $options['layout'] : self::DEFAULT_LAYOUT);
        $this->view_file = $this->viewFileNameNormalize(!empty($options['view']) ? $options['view'] : self::DEFAULT_VIEW);

        $this->title = '';


        //Вывод
        $this->run();
    }

    /**
     * @param $fileName
     * @return string
     */
    public function layoutFileNameNormalize($fileName)
    {
        if(substr($fileName, -4) != '.php')
            $fileName = $fileName.'.php';

        if($this->layoutPath == self::DEFAULT_LAYOUT_PATH)
            $fullPath = Application::getRealPath($this->layoutPath.DIRECTORY_SEPARATOR.$this->_theme.DIRECTORY_SEPARATOR.'layouts'.DIRECTORY_SEPARATOR.$fileName);
        else
            $fullPath = Application::getRealPath($this->layoutPath.DIRECTORY_SEPARATOR.$fileName);

        Application::app()->addLog(static::class, 'layout', $fullPath);
        return $fullPath;
    }

    /**
     * @param $fileName
     * @return mixed
     */
    public function viewFileNameNormalize($fileName)
    {
        if(substr($fileName, -4) != '.php')
            $fileName = $fileName.'.php';

        if(!$this->_moduleName)
        {
            $fullPath = Application::getRealPath($this->viewPath.DIRECTORY_SEPARATOR.$this->_theme.DIRECTORY_SEPARATOR.$this->_controllerName.DIRECTORY_SEPARATOR.$fileName);
        }
        else
        {
            $fullPath = Application::getRealPath($this->viewPath.DIRECTORY_SEPARATOR.$this->_theme.DIRECTORY_SEPARATOR.$this->_moduleName.DIRECTORY_SEPARATOR.$this->_controllerName.DIRECTORY_SEPARATOR.$fileName);
            if(!is_file($fullPath))
            {
                $fullPath = Application::getRealPath('@modules'.DIRECTORY_SEPARATOR.$this->_moduleName.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.$this->_controllerName.DIRECTORY_SEPARATOR.$fileName);
            }
        }

        Application::app()->addLog(static::class, 'view', $fullPath);

        return $fullPath;
    }

    public function renderGeneral()
    {
        // Проверка на существавание представлений
        if(!is_file($this->layout_file))
            throw new ErrorException("renderGeneral: Layout не найден в ".$this->layout_file);

        if(!is_file($this->view_file))
            throw new ErrorException("renderGeneral: Представление не найдено в ".$this->view_file." (Модуль: ".($this->_moduleName ? $this->_moduleName : 'Нет').")");

        extract($this->_variables);

        // Буферизация контента
        ob_start();
        require ($this->view_file);
        $content = ob_get_clean();
        ob_end_clean();

        // Вывод слоя с контентом $content
        header("Content-type: text/html; charset=".$this->_defaultCharset);
        ob_start();
            require ($this->layout_file);
            $___buffer_page___ = ob_get_clean();
        ob_end_clean();

        // Синхронизация с AssetManager
        Application::app()->assetManager->run();
        $aMarkers = Application::app()->assetManager->getAssetMarkers();

        exit(str_replace(
            array_merge(array_keys($aMarkers), [
                $this->head(),
                $this->footer(),
            ]),
            array_merge($aMarkers, [
                Application::app()->assetManager->head()."\n"
                .'<title>'.$this->title.'</title>'."\n",
                Application::app()->assetManager->footer()
            ]),
            $___buffer_page___));
    }

    public function run()
    {
        return $this->renderGeneral();
    }

    public function render($file, $variables = [])
    {
        $file = $this->viewFileNameNormalize($file);
        if(!is_file($file))
           echo "render: Представление не найдено в ".$file."";

        extract($variables);

        // Буферизация контента
        ob_start();
        require ($file);
        echo ob_get_clean();
    }

    public function setAsset($object)
    {
        $this->_asset = $object;
    }

    public function head()
    {
        return '<!-- [~~<HEAD:'.$this->identity.'>~~] -->';
    }

    public function footer()
    {
        return '<!-- [~~<FOOTER:'.$this->identity.'>~~] -->';
    }

}