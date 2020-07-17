<?php
/**
 * Created by PhpStorm.
 * User: Алексей-дом
 * Date: 01.05.2020
 * Time: 20:57
 */

namespace framework\helpers\debug;


use framework\components\module\ModuleComponent;

class DebugModule extends ModuleComponent
{
    public function __construct(array $options = [])
    {
        parent::__construct($options);
    }
}