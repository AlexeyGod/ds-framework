<?php
/**
 * Created by Digital-Solution.Ru web-studio.
 * https://digital-solution.ru
 * support@digital-solution.ru
 */

namespace framework\helpers;


class ArrayHelper
{
    public static function map($objects, $key, $val)
    {
        $new = [];

        foreach ($objects as $object)
        {
            $new[$object->{$key}] = $object->{$val};
        }

        return $new;

    }
}