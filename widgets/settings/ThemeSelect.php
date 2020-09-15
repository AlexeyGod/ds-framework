<?php
/**
 * Created by Digital-Solution.Ru web-studio.
 * https://digital-solution.ru
 * support@digital-solution.ru
 */

namespace framework\widgets\settings;


use framework\core\Application;
use framework\helpers\ActiveForm;

class ThemeSelect
{
    public $model;

    public function __construct($model)
    {
        $this->model = $model;
    }

    public function getItems()
    {
        return [
            'index' => 'Состояние системы',
        ];
    }

    public function getThemes()
    {
        return Application::app()->getRealPath('@themes');
    }



    public function template()
    {
        $out = '<select class="input" name="configs['.$this->model->name.']" value="'.$this->model->value.'">'."\n";

        $themes = $this->getThemes();

        foreach(glob($themes.'/*') as $path)
            if(is_dir($path)) $out .= '<option value="'.basename($path).'"'.(basename($path) == $this->model->value ? ' selected' : '').'>'.basename($path).'</option>';

        $out .= '</select>';

        return $out;
    }


    public function run()
    {
        return $this->template();
    }
}