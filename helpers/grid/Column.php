<?php
/**
 * Created by PhpStorm.
 * User: Алексей-дом
 * Date: 21.07.2019
 * Time: 18:18
 */

namespace framework\helpers\grid;


class Column
{
    protected $_data;
    protected $_model;

    public $emptyValue = '';

    public function __construct($model, $data)
    {
        $this->_data = $data;
        $this->_model = $model;
    }

    public function __toString()
    {
        $object = $this->_model;
        $variable = $this->_data['attribute'];

        if(is_object($object))
        {
            if(isset($object->$variable))
                return (!empty($object->$variable) ? $object->$variable : '');
            else
                return $this->emptyValue;
        }
        else
            return $this->emptyValue;

    }
}