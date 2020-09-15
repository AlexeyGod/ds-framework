<?php
/**
 * Created by Digital-Solution.Ru web-studio.
 * https://digital-solution.ru
 * support@digital-solution.ru
 */

namespace framework\widgets\ckeditor;


use framework\core\Application;
use framework\helpers\ActiveForm;


class CkeditorWidget
{
    public $activeForm;
    protected $model, $field, $options, $identity, $htmlId;

    public function __construct(ActiveForm $activeForm, $options = [])
    {
        $this->activeForm = $activeForm;
        $this->model = $activeForm->model;
        $this->field = $activeForm->field;
        $this->options = $activeForm->options['input'];
        $this->identity = mt_rand(0,99999999);
        $this->htmlId=  $activeForm->getHtmlId();
    }



    public function template()
    {
        return $this->activeForm->textarea();
    }


    public function run()
    {
        $identity = $this->htmlId;
        $js = <<<JS
<!-- Redactor JS ($identity) -->
$(document).ready(function(){
 CKEDITOR.replace('$identity');
});
JS;

        CkeditorBundle::register();
        Application::app()->assetManager->setJsCode($js);
        return $this->template();
    }
}