<?php
/**
 * Created by Digital-Solution.Ru web-studio.
 * https://digital-solution.ru
 * support@digital-solution.ru
 */

namespace framework\helpers\html;


use framework\exceptions\ErrorException;

class Html
{
    static $closeTags = [
        'div',
        'label',
        'select',
        'textarea',
        'frame',
        'iframe'
    ];


    public static function tag($tagName, $attributes = [])
    {
        switch($tagName):
            case 'checkbox':
                $realTag = 'input';
                $attributes['type'] = 'checkbox';
                break;
            default:
                $realTag = $tagName;
                break;
        endswitch;

        return '<'.$realTag.static::attributes($tagName, $attributes).'>';
    }

    public static function closeTag($tagName, $attributes = [])
    {
        $output = static::tag($tagName, $attributes);
        if(!isset($attributes['items']))
            $output .= $attributes['value'];
        else {
            $defaultValue = (isset($attributes['defaultValue']) ? $attributes['defaultValue'] : 'Выберите...');
            $output .= PHP_EOL;
            $output .= static::closeTag('option', ['value' => $defaultValue]).PHP_EOL;
            foreach ($attributes['items'] as $item)
            {
                $optionAttr = [];
                $optionAttr['value'] = $item;
                if($item == $attributes['value'])
                    $optionAttr['selected'] = 'true';

                $output .= static::closeTag('option', $optionAttr).PHP_EOL;
            }
        }
        $output .= '</'.$tagName.'>';
        return $output;
    }

    public static function smartTag($tagName, $attributes = [])
    {
        $tagName = strtolower($tagName);

        if(in_array($tagName, static::$closeTags))
            return static::closeTag($tagName, $attributes);
        else
            return static::tag($tagName, $attributes);
    }

    public static function normalize ($st = ''){
        if(is_array($st))
            $st = 'array: ('.implode(",", $st).')';


        return (empty($st) ? '' : htmlspecialchars($st));
    }

    public static function attributes($tag = '', $attributes = [])
    {
        if(empty($attributes)) return '';

        foreach ($attributes as $key => $value)
        {

            if(in_array($tag, static::$closeTags) && $key == 'value') continue;

            $output[] = $key.'="'.(!empty($value) ? static::normalize($value) : '').'"';
        }

        return (empty($output) ? "" : " ".join(" ", $output));
    }

    public function input($attr)
    {
        return static::tag($attr);
    }

    /**
     * Ниже будет удалено в сл. версиях

    public static function tags($tags = [])
    {
        $st = '';

        if(!empty($tags))
        {
            foreach ($tags as $attribute => $value)
            {
                $st .= ' '.$attribute.'="'.$value.'"';
            }
        }

        return $st;
    }



    public static function textInput($name, $value = '', $options = [])
    {
        return '<input name="'.$name.'" value="'.$value.'" '.static::tags($options).'>';
    }

    public static function checkbox($name, $value = '', $checked = false, $options = [])
    {
        return '<input name="'.$name.'" value="'.$value.'" type="checkbox" '.($checked ? ' checked' : '').' '.static::tags($options).'>';
    }

    public static function radio($name, $value = '', $checked = false, $options = [])
    {
        return '<input name="'.$name.'" value="'.$value.'" type="radio" '.($checked ? ' checked' : '').' '.static::tags($options).'>';
    }

    public static function hiddenInput($name, $value = '', $options = [])
    {
        return static::textInput($name, $value, array_merge(['type' => 'hidden'], $options)).'>';
    }

    public static function select($name, $items, $selected = false,  $options = [])
    {
        $st = '<select name="'.$name.'"'.static::tags($options).'>';

        foreach ($items as $key => $value)
        {
            $st .= '<option value="'.$key.'">'.$value.'</option>';
        }

        $st .= '</select>';

        return $st;
    }

    public static function button($name, $options = [])
    {
        return '<button'.static::tags($options).'>'.$name.'</button>';
    }

     */

}