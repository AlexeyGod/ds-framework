<?php
/**
 * Created by Digital-Solution.Ru web-studio.
 * https://digital-solution.ru
 * support@digital-solution.ru
 */

namespace framework\widgets;


use framework\core\Application;
use framework\helpers\ActiveForm;

class CaptchaInputWidget
{
    protected $model, $field, $options, $identity;

    public function __construct($model, $field, $options = [])
    {
        $this->model = $model;
        $this->field = $field;
        $this->options = $options;
        $this->identity = mt_rand(0,99999999);
        if(empty($this->options['class']))
            $this->options['class'] = 'input';
        $this->options['options']['id'] = ActiveForm::getElementIdentity($model, $field);
    }



    public function template()
    {
        return '<label>'.$this->model->label($this->field).'</label>'
        .'<div class="captcha-block">'
        .'<img src="/captcha" alt="Код с картинки">'
        .'</div>'
        .'<input name="'.$this->model->modelName().'['.$this->field.']" type="text" '.ActiveForm::options2attributes($this->options['options']).' value="'.$this->model->{$this->field}.'">'."\n";
    }


    public function run()
    {
        $identity = $this->options['options']['id'];
        return $this->template();
    }
}