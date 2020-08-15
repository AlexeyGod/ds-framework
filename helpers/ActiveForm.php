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

class ActiveForm
{

    public static function begin($options = [])
    {
        ob_start();

        echo '<form method="post"'.(!empty($options['options']) ? self::options2attributes($options['options'], true) : '').'>';

        return new static();
    }

    public static function submit($options = [])
    {
        echo '<input type="submit" '.(!empty($options['options']) ? self::options2attributes($options['options'], true) : '').' name="formSubmit" value="'.($options['value'] == '' ? 'Отправить' : $options['value']).'">'."\n";
    }

    public static function button($name, $options = [])
    {
        echo '<button'.self::options2attributes($options).'>'.$name.'</button>'."\n";
    }

    public static function agreement($disableElementSelector = false)
    {
        $st = '<label><input type="checkbox" id="__agreement" name="__agreement" value="true"> Я даю согласие на <a href="/agreement">обработку персональных данных</a>.</label>';

        if($disableElementSelector)
        {
            $jsCode = <<<JS
$('$disableElementSelector').prop('disabled', true);
$('#__agreement').on('change', function(){
if($(this).is(':checked')) $('$disableElementSelector').prop('disabled', false);
else
$('$disableElementSelector').prop('disabled', true);
})
JS;
            Application::app()->assetManager->setJsCode($jsCode, ['depends' => ['framework\\assets\\jquery\\JqueryBundle']]);

            return $st;
        }
    }

    public static function end()
    {
        echo ob_get_clean()."\n</form>";
    }

    public function hiddenInput($model, $field, $options= [])
    {
        return '<input name="'.$model->modelName().'['.$field.']" type="hidden" value="'.self::prepareValue($model->{$field}).'"'.self::options2attributes($options['options']).'>'."\n";
    }

    public function textarea($model, $field, $options= [])
    {
        if(!isset($options['options']['rows']))
            $options['options']['rows'] = 5;

        if($model->getErrorField($field))
        {
            $error = '<p class="help-box error">'.$model->getErrorField($field).'</p>'."\n";
            $labelClass = 'error';
            $options['options']['class'] = 'error';
        }


        return  '<label class="'.$labelClass.'">'.$model->label($field).'</label>'
        .'<textarea name="'.$model->modelName().'['.$field.']" '.self::options2attributes($options['options']).'>'.self::prepareValue($model->{$field}).'</textarea>'."\n"
        .$error;
    }

    public function readonlyInput($model, $field, $options= [])
    {
        return $this->input($model, $field, array_merge_recursive(['options' => ['readonly' => 'readonly']], $options));
    }

    public function input($model, $field, $options= [])
    {
        $error = '';
        $labelClass = 'label';
        $inputClass = 'input';

        if($model->getErrorField($field))
        {
            $error = '<p class="help-box error">'.$model->getErrorField($field).'</p>'."\n";
            $labelClass .= ' error';
            $inputClass .= ' error';
        }

        if(!isset($options['options']['class']))
            $options['options']['class'] = $inputClass;



        if(!isset($options['options']['type']))
            $options['options']['type'] = 'text';

        if(!isset($options['label']))
            $label = $field;
        else
            $label = $options['label'];

        if(!isset($options['value']))
            $value = $model->{$field};
        else
            $value = $options['value'];


        $st = '';
        if($label != '') $st .= '<label class="'.$labelClass.'">'.$model->label($label).'</label>';
        $st .= '<input name="'.$model->modelName().'['.$field.']" value="'.self::prepareValue($value).'"'.self::options2attributes($options['options']).'>'."\n".$error;

        return $st;
    }

    public function checkbox($model, $field, $options= [])
    {
        $error = '';
        if($model->getErrorField($field))
        {
            $error = '<p class="help-box error">'.$model->getErrorField($field).'</p>'."\n";
        }
        //exit("Field: ".$field);
        $identy = 'field-'.ucfirst($field);
        $checked = '';
        if($model->{$field} > 0) $checked = ' checked';

        return '<label>'
        .'<input type="hidden" name="'.$model->modelName().'['.$field.']" value="0"/>'
        .'<input id="'.$identy.'" name="'.$model->modelName().'['.$field.']" type="checkbox"  value="1"'.$checked.self::options2attributes($options['options']).'>'."\n".
        $model->label($field).'</label>'.$error;
    }



    public function select($model, $field, $items, $options= [])
    {
        $html = '<label>'.$model->label($field).'</label>'."\n".
            '<select name="'.$model->modelName().'['.$field.']"  id="field'.ucfirst($field).'"'.self::options2attributes($options['options']).'>'."\n";

        foreach ($items as $key => $val)
        {
            $html .= '<option value="'.$key.'"'.($model->{$field} == $key ? 'selected' : '').'>'.htmlspecialchars(stripslashes($val)).'</option>'."\n";
        }

        $html .= "</select>\n";

        return $html;
    }

    public function widget($class, $model, $field, $options = []){
        if(!class_exists($class)) throw new ErrorException("ActiveForm::widget не можен найти указанный класс $class");

        $widget = new $class($model, $field, $options);

        $html = $widget->run();

        if($model->getErrorField($field))
        {
            $html .= '<p class="help-box error">'.$model->getErrorField($field).'</p>'."\n";
        }

        return $html;
    }

    public function inputFile($model, $field, $options= [])
    {
        $text = '☁ <B>Выберите файл</B><i> или перетащите его в эту область</i>';

        if(!is_object($model)) throw new ErrorException("Не передана модель в ActiveForm");

        $identy = 'field'.ucfirst($field);
        $fieldName = $model->modelName().'['.$field.']';
        if($options['multiple']) $fieldName .= '[]';

        $multiple = '';
        if($options['multiple']) $multiple = ' multiple="multiple"';

        $attributes = ''.self::options2attributes($options['options']);


        if($model->getErrorField($field))
        {
            $error = '<p class="help-box error">'.$model->getErrorField($field).'</p>'."\n";
            $labelClass .= ' error';
            $inputClass .= ' error';
        }

        $dropZoneId = $field.'DropZone';

        $html = <<<HTML
<div class="fileInput">
<label for="$identy" class="ar-chous $labelClass" id="$dropZoneId">$text</label>
<input type="file" class="ar-input-file $inputClass" id="$identy" name="$fieldName"$multiple $attributes />
</div>
$error
HTML;

if($options['multiple'])
    $selectedFile = "'Выбрано файлов: ' + $(this)[0].files.length";
        else
        $selectedFile = "'Файл: ' + $(this).val()";

$js = <<<JS
if (typeof(window.FileReader) == 'undefined') {
    console.log('Drag and Drop Не поддерживается браузером!');
}

$dropZoneId = $("#$dropZoneId");
{$dropZoneId}[0].ondragover = function() {
    $dropZoneId.addClass('hover');
    return false;
};

{$dropZoneId}[0].ondragleave = function() {
    $dropZoneId.removeClass('hover');
    return false;
};

{$dropZoneId}[0].ondrop = function(event) {
    event.preventDefault();
    {$dropZoneId}.removeClass('hover');
    {$dropZoneId}.addClass('drop');
    let files = event.dataTransfer.files;
    $('#{$identy}').prop('files', files);
};


$('.ar-input-file').change(function() {
    if ($(this).val() != '')
    $(".ar-chous").text($selectedFile);
    else
    $(this).prev().html('$text');
});


JS;

$themes['white'] = <<<CSS
<style>
.fileInput {
margin-bottom: 20px;
}
.ar-chous {
    height: 100px !important;
    border-radius: 4px;
    padding-top: 20px;
    text-align: center;
    margin: 0px;
    cursor: pointer;
    display: block;
    transition: all 0.18s ease-in-out;
    border: 1px dashed #fbeedb;
    background: linear-gradient(to top right, #1a53c9, #6789ea 20%, rgba(255, 255, 255, 0) 80%, rgba(255, 255, 255, 0)) top right/500% 500%;
    color: #fbeedb;
    line-height: 50px !important;
}

.ar-chous:hover, .ar-chous.hover {
    color: white;
    background-position: bottom left;
}

.ar-input-file {
  width: 0.1px !important;
  height: 0.1px !important;
  opacity: 0;
  overflow: hidden;
  position: absolute;
  z-index: -1;
}
</style>
CSS;

$themes['black'] = <<<CSS
<style>
.fileInput {
margin-bottom: 20px;
}
.ar-chous {
    height: 50px !important;
    border-radius: 4px;
    padding-left: 15px;
    margin: 0px;
    cursor: pointer;
    display: block;
    transition: all 0.18s ease-in-out;
    border: 1px dashed #000000;
    background: linear-gradient(to top right, #1a53c9, #6789ea 20%, rgba(255, 255, 255, 0) 80%, rgba(255, 255, 255, 0)) top right/500% 500%;
    color: #000000;
    line-height: 50px !important;
}

.ar-chous:hover {
    color: white;
    background-position: bottom left;
}

.ar-input-file {
  width: 0.1px !important;
  height: 0.1px !important;
  opacity: 0;
  overflow: hidden;
  position: absolute;
  z-index: -1;
}
</style>
CSS;
        if(!empty($options['theme']))
            $css = $themes[$options['theme']];
        else
            $css = $themes['black'];

        Application::app()->assetManager->setJsCode($js, ['depends' =>  ['framework\\assets\\jquery\\JqueryBundle']]);
        return $css."\n".$html."\n";


    }



    public static function options2attributes($options, $noDefault = false)
    {
        $st = '';

        if(!isset($options['class']))
            $options['class'] = 'input';

        if(!empty($options))
            foreach ($options as $key => $val)
                $st .= ' '.$key.'="'.$val.'"';

            return $st;
    }

    public static function getElementIdentity($model, $field)
    {
        return $model->modelName().'-'.$field.'-field';
    }

    public static function prepareValue($value = '')
    {
        if(is_array($value))
            $value = var_export($value, true);

       if($value != '')
           $value = htmlspecialchars(stripslashes($value));

        return $value;
    }


}