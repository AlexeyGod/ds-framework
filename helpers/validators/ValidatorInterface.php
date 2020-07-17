<?php

/**
 * Created by Digital-Solution.Ru web-studio.
 * https://digital-solution.ru
 * support@digital-solution.ru
 */
namespace framework\helpers\validators;

Interface ValidatorInterface
{
    public function __construct($options);
    public function validate($object, $field); // Return false OR value
    public function getError();
}