<?php
/**
 * Created by Digital-Solution.Ru web-studio.
 * https://digital-solution.ru
 * support@digital-solution.ru
 */

namespace framework\widgets\datetimepicker;

use framework\core\Application;
use framework\helpers\ActiveForm;
use framework\widgets\datetimepicker\DateTimePickerBundle;

class DateTimePickerWidget
{
    /*
     * Опции enableTime = false
     */
    public $enableTime = 'false';
    public $cssClass = 'input ds-dateTimePicker';
    protected $model, $field, $options, $identity;

    public function __construct($model, $field, $options = [])
    {
        $this->model = $model;
        $this->field = $field;
        $this->options = $options;
        $this->identity = mt_rand(0,99999999);

        if($options['enableTime'])
            $this->enableTime = 'true';

        $this->options['options']['id'] = ActiveForm::getElementIdentity($model, $field);
        $this->options['options']['class'] = (empty($this->options['options']['class']) ? $this->cssClass : $this->cssClass.' '.$this->options['options']['class']);
        if(empty($this->options['options']['type']))
            $this->options['options']['type'] = 'text';
    }



    public function template()
    {
        $error = '';
        $labelClass = '';
        if($this->model->getErrorField($this->field)){
            $error = '<p class="help-box error">'.$this->model->getErrorField($this->field).'</p>'."\n";
            $this->options['options']['class'] .= ' error';
            $labelClass .= ' error';
        }
        return '<label class="'.$labelClass.'">'.$this->model->label($this->field).'</label>'."\n"
        .'  <div class="input-inline">'
        .'  <input name="'.$this->model->modelName().'['.$this->field.']" '.ActiveForm::options2attributes($this->options['options']).' value="'.$this->model->{$this->field}.'">'."\n"
        .'  <span class="input-icon"><span class="icon-calendar"></span></span>'
        .'</div>';
    }


    public function run()
    {
        $identity = $this->options['options']['id'];
        // Включение времени
        $enableTime = $this->enableTime;

        if($enableTime == 'true')
        {
            $format = 'd.m.Y H:i';
        }
        else
        {
            $format = 'd.m.Y';
        }

        $js = <<<JS
        jQuery.datetimepicker.setLocale('ru');

        jQuery('#$identity').datetimepicker({

         timepicker: $enableTime,
         format:'$format',
         defaultTime: '08:00'
        });
JS;

        DateTimePickerBundle::register();
        Application::app()->assetManager->setJsCode($js);
        return $this->template();
    }


    public static function widget($options = [])
    {
        $labelClass = $options['labelClass'] ? $options['labelClass'] : '';
        $inputName = $options['name'] ? $options['name'] : 'datetimepicker';
        $label = $options['label'] ? $options['label'] : ucfirst($inputName);
        $value = $options['value'] ? $options['value'] : '';
        $enableTime = $options['enableTime'] ? $options['enableTime'] : 'false';

        $options['class'] = 'input ds-dateTimePicker';

        if($enableTime == 'true')
        {
            $format = 'd.m.Y H:i';
        }
        else
        {
            $format = 'd.m.Y';
        }

        $identity = 'dateTimePicker';

        $js = <<<JS
        jQuery.datetimepicker.setLocale('ru');

        jQuery('#$identity').datetimepicker({

         timepicker: $enableTime,
         format:'$format',
         defaultTime: '08:00'
        });
JS;
        DateTimePickerBundle::register();
        Application::app()->assetManager->setJsCode($js);


         return '<label class="'.$labelClass.'">'.$label.'</label>'."\n"
        .'  <div class="input-inline">'
        .'  <input name="'.$inputName.'" '.ActiveForm::options2attributes($options['options']).' value="'.$value.'">'."\n"
        .'  <span class="input-icon"><span class="icon icon-calendar"></span></span>'
        .'  </div>';
    }
}