<?php
/**
 * Created by Digital-Solution.Ru web-studio.
 * https://digital-solution.ru
 * support@digital-solution.ru
 */

namespace framework\components;


use framework\core\Application;
use framework\exceptions\AccessDeniedException;

class Controller
{
    public $view;
    public $layout;

    public $accepts = [];

    protected $viewPath = '';
    protected $layoutPath = '';

    protected $_moduleName;
    protected $_moduleClass;

    public function __construct($options = [])
    {
        /*
        if(isset($options['layout']))
            $this->layout = $options['layout'];
        else
            $this->layout = 'main';
        */
        // Модульность
        $this->_moduleClass = (isset($options['moduleClass']) ? $options['moduleClass'] : false);
        $this->_moduleName = (isset($options['moduleName']) ? $options['moduleName'] : false);

        if(!empty($this->accepts))
        {
            foreach($this->accepts as $accept)
                if(!Application::app()->identy->can($accept)) {
                    throw new AccessDeniedException("У Вас нет необходимых полномочий");
                }
        }

    }

    public function render($view_name, $variables = [])
    {
        return Application::app()->createObject('framework\\components\\View',
            [
                'controller' => static::getClassName(),
                'view' => $view_name,
                'variables' => $variables,
                'layout' => $this->layout,
                'layoutPath' => $this->layoutPath,
                'viewPath' => $this->viewPath,
                'moduleName' => $this->_moduleName,
                'moduleClass' => $this->_moduleClass,
            ]);
    }

    public function beforeAction()
    {

    }

    public function redirect($url)
    {
        header ("Location: ".$url);
        exit();
    }

    public function goHome($url = '/')
    {
        header ("Location: ".$url);
        exit();
    }

    public function goBack()
    {
        header ("Location: ".getenv("HTTP_REFERER"));
        exit();
    }

    public static function normalizeName($name)
    {
        preg_match_all('/[A-Z][^A-Z]*?/Us', $name, $matches);

       //exit(var_dump($matches));

       if(!empty($matches[0]))
           $name = implode("-", $matches[0]);


        return $name;
    }

    public static function getClassName()
    {
        $class =explode("\\", static::class);
        $class = $class[count($class)-1];
       
        return  strtolower(self::normalizeName(substr($class, 0, -10)));
    }

    public function notFound()
    {
        $this->redirect('/404');
        exit();
    }
}