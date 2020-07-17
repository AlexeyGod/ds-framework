<?php
/**
 * Created by Digital-Solution.Ru web-studio.
 * https://digital-solution.ru
 * support@digital-solution.ru
 */

namespace framework\widgets\redactor;


use framework\core\Application;
use framework\helpers\ActiveForm;

class RedactorWidget
{
    protected $model, $field, $options, $identity;

    public function __construct($model, $field, $options = [])
    {
        $this->model = $model;
        $this->field = $field;
        $this->options = $options;
        $this->identity = mt_rand(0,99999999);
        $this->options['options']['id'] = ActiveForm::getElementIdentity($model, $field);
    }



    public function template()
    {
        return '<textarea name="'.$this->model->modelName().'['.$this->field.']" '.ActiveForm::options2attributes($this->options['options']).' rows="10" cols="80">'.$this->model->{$this->field}.'</textarea>'."\n";
    }


    public function run()
    {
        $identity = $this->options['options']['id'];
        $js = <<<JS
$(document).ready(function(){
 CKEDITOR.replace('$identity');
});
JS;

        RedactorBundle::register();
        Application::app()->assetManager->setJsCode($js);
        return $this->template();
    }
}