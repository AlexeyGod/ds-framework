<?php
/**
 * Created by Digital-Solution.Ru web-studio.
 * https://digital-solution.ru
 * support@digital-solution.ru
 */

namespace framework\helpers\html;


class Html
{
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

}