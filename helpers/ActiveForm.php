<?php
/**
 * Created by Digital-Solution.Ru web-studio.
 * https://digital-solution.ru
 * support@digital-solution.ru
 */

namespace framework\helpers;


use framework\assets\jquery\JqueryBundle;
use framework\core\Application;
use framework\exceptions\ErrorException;
use framework\helpers\html\Html;

class ActiveForm
{
    protected $_tag = 'input',
    $_widgetHtml = '';

    public $template = '{label}{input}',
        $default_template = '{label}{input}',
        $theme = 'bootstrap',
        $model,
        $field,
        $label,
        $options = [],
        $default_options = [
        'input' => [],
        'label' => [],
    ];

    public function __construct($options = [])
    {
        $this->setTheme($this->theme);

        return $this;
    }

    public function setDefaultOptions(){
        $this->options = $this->default_options;
    }

    // Themes
    public function setTheme($theme)
    {
        $options = [];

        switch($theme){
            case 'bootstrap':
                $this->default_template = '<div class="form-group">'."\n"
                    ."\t{label}\n"
                    ."\t{input}\n"
                    ."</div>\n";
                $options = [
                    'input' => [
                        'class' => 'form-control'
                    ],
                    'label' => [],
                    'button' => [
                        'class' => 'btn btn-success'
                    ],
                ];
                break;
        }

        $this->default_options = array_replace_recursive($this->default_options, $options);
        $this->template = $this->default_template;

        return $this;
    }

    public function addOptions($section, $options = [])
    {
        $this->options[$section] = array_replace_recursive($this->options[$section], $options);
    }

    public function getHtmlId()
    {
        return 'field-'.strtolower($this->model->modelName().'-'.$this->field);
    }

    public function getFieldName()
    {
        return ucfirst($this->model->modelName()).'['.$this->field.']';
    }

    public static function begin($options = [])
    {
        ob_start();

        echo "\n<!-- ActiveFrom -->\n"
            .Html::tag('form', [
            'method' => 'post'
        ])."\n";

        //echo '<form method="post"'.(!empty($options['options']) ? self::options2attributes($options['options'], true) : '').'>';

        return new static();
    }

    public function label($label = '')
    {
        $this->label = $label;

        return $this;
    }

    public function field($model, $field, $options = [])
    {
        //Reset previous
        $this->setDefaultOptions();
        $this->template = $this->default_template;
        $this->_tag = 'input';
        $this->label = '';
        $this->_widgetHtml = '';

        // Use model
        $this->model = $model;
        $this->field = $field;
        // Use custom options
        $this->addOptions('input', $options);
        $this->label = $this->model->label($field);
        return $this;
    }

    public function checkbox()
    {
        $this->_tag = 'checkbox';
        $this->options['input']['class'] = 'checkbox';
        $this->template = '<div class="checkbox">'
            .'<label for="'.ucfirst($this->model->modelName()).'['.$this->field.']'.'">'
            .PHP_EOL
            .'{input} {labelText}'.PHP_EOL
            .'</label>'.PHP_EOL
            .'</div>'.PHP_EOL;
        return $this;
    }

    public function select($items = [], $defaultValue = 'Выберите...')
    {
        $this->_tag = 'select';
        $this->addOptions('input', [
            'items' => $items,
            'selected' => ($this->model->isNewRecord ? 'false' : $this->model->{$this->field}),
            'defaultValue' => $defaultValue
        ]);
        return $this;
    }

    public function number()
    {
        $this->_tag = 'input';
        $this->addOptions('input', ['type' => 'number']);
        return $this;
    }

    public function hidden(){
        $this->addOptions('input', ['type' => 'hidden']);
        $this->label = '';
        return $this;
    }

    public function textarea()
    {
        $this->_tag = 'textarea';
        $this->addOptions('input', ['cols' => 20, 'rows' => 5]);
        return $this;
    }

    // support widgets
    public function widget($class, $widgetOptions = []){
        // check class
        if(!class_exists($class)) throw new ErrorException("ActiveForm::widget не можен найти указанный класс $class");
        $widget = new $class($this, $widgetOptions);

        //run widget
        $this->_widgetHtml = $widget->run();

        return $this;
    }

    // debug to future
    public function printDebug()
    {
        return Html::closeTag('pre', ['value' => var_export($this->options, true)]);
    }

    // finaly
    public static function end()
    {
        echo ob_get_clean()
            ."\n<!--/ActiveFrom-->\n";
    }
    protected function _inputPrint()
    {
        return Html::smartTag($this->_tag,
            array_replace_recursive([
                'name' => $this->getFieldName(),
                'value' => $this->model->{$this->field},
                'id' => $this->getHtmlId()
            ],
                $this->options['input'])
        );
    }

    protected function _labelPrint()
    {
        return (empty($this->label) ? '' : Html::closeTag('label',
            array_merge([
                'value' => $this->label,
                'for' => ucfirst($this->model->modelName()).'['.$this->field.']'
            ],
                $this->options['label'])
        ));
    }

    public function __toString()
    {
        if(empty($this->_widgetHtml))
            return str_replace(
                [
                    '{input}',
                    '{label}',
                    '{labelText}',
                ],
                [
                    $this->_inputPrint(),
                    $this->_labelPrint(),
                    $this->label,
                ],
                $this->template);
        else
            return "<!-- AF: use widget -->\n".$this->_widgetHtml."\n";
    }


    // -------------------------------------------
    //Add to support a old version
    public function input($model, $field, $options = [])
    {
        return '(deprecated method: input for field: '.$field.')'.$this->field($model, $field, $options);
    }

    public function inputFile($model, $field, $options = [])
    {
        return '(deprecated method: input for field: '.$field.')'.$this->field($model, $field, $options);
    }

    public static function submit($options = [])
    {
        return Html::closeTag('button', [
            'value' => (isset($options['value']) ? $options['value'] : 'Отправить')
        ]);
    }

}