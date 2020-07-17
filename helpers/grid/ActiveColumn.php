<?php
/**
 * Created by PhpStorm.
 * User: Алексей-дом
 * Date: 21.07.2019
 * Time: 18:18
 */

namespace framework\helpers\grid;


use framework\core\Application;

class ActiveColumn
{
    protected $_data;
    protected $_model;
    public $template = '{VIEW} {UPDATE} {DELETE}';
    public $buttons = [];


    public $current;

    public function __construct($model, $data)
    {
        $this->_data = $data;
        $this->_model = $model;

        $this->current = Application::app()->request->uri();

        if(isset($data['buttons']))
            $this->buttons = $data['buttons'];

        if(isset($data['template']))
            $this->template = $data['template'];
    }

    public function templateView()
    {
        preg_match_all("~{([^}]+)}~", $this->template, $buttons);

        $buttons = $buttons[1];
        foreach($buttons as $button)
        {

            $button = strtolower($button);
            $replace = '{'.$button.'}';

            if(!empty($this->buttons[$button]))
            {
                $replace = call_user_func($this->buttons[$button], $this->_model, $this->_data);
            }
            else
            {
                if(method_exists($this, 'button_'.$button))
                {
                    $method = 'button_'.$button;
                    $replace = $this->$method();
                }

            }

            $this->template = preg_replace("~{".$button."}~isU", $replace, $this->template);
            unset($button, $replace);
        }



        return $this->template;
    }


    public function __toString()
    {
        return $this->templateView();
        //return var_export($this->_data, true);
    }

    public function button_update()
    {
        return '<a href="/'.$this->current.'/update/'.$this->_model->getIdentity().'">Редактировать</a>';
    }

    public function button_delete()
    {
        return '<a href="/'.$this->current.'/delete/'.$this->_model->getIdentity().'" onClick="if(confirm(\'Действительно удалить?\')) return true; else return false;">Удалить</a>';
    }

    public function button_view()
    {
        return '<a href="/'.$this->current.'/view/'.$this->_model->getIdentity().'">Посмотреть</a>';
    }
}