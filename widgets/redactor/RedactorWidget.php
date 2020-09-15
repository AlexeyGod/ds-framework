<?php
/**
 * Created by Digital-Solution.Ru web-studio.
 * https://digital-solution.ru
 * support@digital-solution.ru
 */

namespace framework\widgets\redactor;


use framework\core\Application;
use framework\helpers\ActiveForm;
use framework\helpers\html\Html;


class RedactorWidget
{
    public $activeForm;
    protected $model, $field, $options, $identity, $htmlId,
    $_formatBlock = false,
    $_buttonBlock = 'bold, underline, italic, link, unlink, indent, outdent, source';

    public function __construct(ActiveForm $activeForm, $options = [])
    {
        $this->activeForm = $activeForm;
        $this->model = $activeForm->model;
        $this->field = $activeForm->field;
        $this->options = $activeForm->options['input'];
        $this->identity = mt_rand(0,99999999);
        $this->htmlId =  $activeForm->getHtmlId();
        $this->frameId =  'frame-'.$this->htmlId;

        $this->_formatBlock = '';
        //$this->_buttonBlock = 'bold, underline, italic, link, indent, outdent';

        RedactorBundle::register();
    }


    public function createControlElement($item)
    {
        $item = trim($item);
        if(!isset(RedactorCollection::$buttons[$item]))
            return $item;

        $element =  RedactorCollection::$buttons[$item];

        if($element['type'] == 'button')
            return '<span title="'.$element['title'].'" data-command="'.$element['command'].'">'.$element['name'].'</span>';

        if($element['type'] == 'image')
            return '<span title="'.$element['title'].'" data-command="'.$element['command'].'"><img src="'.$element['img'].'" alt="'.$element['name'].'"/></span>';

        return 'error in RedactorWidget::createControlElement';

    }

    public function template()
    {
        $html = "<!-- JS Editor begin (#".$this->htmlId.")-->\n";

        $html .= "<div class=\"ds-redactor\" id=\"rb-".$this->htmlId."\">\n";

        $html .= "\t<label for=\"".$this->activeForm->getFieldName()."\">".$this->model->label($this->field)."</label>\n";

        // Start Visual Redactor
        $html .= "<!-- Visual redactor -->\n"
            ."\t<div id=\"vr-".$this->htmlId."\" style=\"display: block\">\n";

        $html .= "\t\t<div class=\"controls\">\n";

        if($this->_formatBlock)
        {
            $html .= "\t\t\t<div class='\"format\"'>\n";

            foreach (explode(',', $this->_formatBlock) as $item)
            {
                $html .= "\t\t\t\t".$this->createControlElement($item)."\n";
            }

            $html .= "\t\t\t</div>\n";
        }

        if($this->_buttonBlock)
        {
            $html .= "\t\t\t<div class='\"buttons\"'>";

            foreach (explode(',', $this->_buttonBlock) as $item)
            {
                $html .= "\t\t\t\t".$this->createControlElement($item)."\n";
            }

            $html .= "\t\t\t</div>\n";
        }

        $html .= "\t\t</div><!-- /End of controls -->\n";

        $html .= "\t\t".Html::smartTag('iframe', [
            'id' => $this->frameId,
            'class' => 'ds-redactor-frame'
        ])
            ."\n"
            ."\t\t<div class=\"footer\">\n"
            ."\t\t..."
            ."\t\t</div>\n"
            ."</div>\n"
            ."<!-- / End of Visual redactor -->\n"
            ."\t<div class=\"source-block\" id=\"source-".$this->htmlId."\" style=\"display: none\">"
            ."\t<div class=\"controls\">"
            ."\t\t".$this->createControlElement('source')."\n"
            ."\t</div>"
            ."\t\t".$this->activeForm->textarea()->label()//->hidden();
            ."\t</div>";

        //$html .= "<div class=\"footer\">.footer</div>\n";

        //depr: $html .= "</div>\n";
        // depr: End of Visual Redactor
        $html .= "</div>\n";

        return $html;
    }


    public function run()
    {
        $defaultText = ($this->model->{$this->field} == '' ? 'Текст' : $this->model->{$this->field});
        $css = 'iHTML += "<link rel=\'stylesheet\' href=\'/assets/ca4c50b905/css/bootstrap.min.css\'/>";'."\n";
        $css .= 'iHTML += "<link rel=\'stylesheet\' href=\'/assets/f17aaabc20/css/style.css\'/>";';

        $jsCode = <<<JSCODE
console.log("DS-Redactor Active (id: #{$this->frameId})");
// Обработчики
document.addEventListener("DOMContentLoaded", function(){ createEditor("{$this->htmlId}"); });






JSCODE;
        Application::app()->assetManager->setJsCode($jsCode);
        return $this->template();
    }


}