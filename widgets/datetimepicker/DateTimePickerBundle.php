<?php
/**
 * Created by Digital-Solution.Ru web-studio.
 * https://digital-solution.ru
 * support@digital-solution.ru
 */

namespace framework\widgets\datetimepicker;


use framework\components\Bundle;
use framework\core\Application;


class DateTimePickerBundle extends Bundle
{
    public  $depends = ['framework\\assets\\jquery\\JqueryBundle'];
    public  $sourcePath = '@framework/widgets/datetimepicker/src';
    public $js = [
        'jquery.datetimepicker.full.min.js'
    ];

    public $css = [
        'jquery.datetimepicker.min.css'
    ];
}