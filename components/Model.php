<?php
/**
 * Created by Digital-Solution.Ru web-studio.
 * https://digital-solution.ru
 * support@digital-solution.ru
 */

namespace framework\components;

class Model
{
    protected $_oldAttributes = [];
    protected $_attributes = [];
    protected $_attributeLabels = [];
    protected $_errors = [];
    public $isNewRecord = true;

    public function __construct($options = [])
    {
        $this->_attributeLabels = $this->attributeLabels();
    }

    public static function className()
    {
        return static::class;
    }

    public function modelName()
    {
        $names = explode("\\", static::className());

        return $names[count($names)-1];
    }

    public function setDefaultAttributes($array)
    {
        $this->_attributes = array_replace_recursive($this->_attributes, $array);
        $this->_oldAttributes = $this->_attributes;
        $this->isNewRecord = false;
    }

    public function __get($name)
    {
        $getMethod = 'get' . ucfirst($name);
        if ( method_exists($this, $getMethod) ) {
            return $this->$getMethod();
        }

        if(isset($this->_attributes[$name]))
            return $this->_attributes[$name];
        else
            return null;
    }

    public function __set($name, $value)
    {
        $getMethod = 'set' . ucfirst($name);
        if ( method_exists($this, $getMethod) ) {
            return $this->$getMethod($name, $value);
        }

        if(isset($this->_attributes[$name]))
        {
            $this->_oldAttributes[$name] = $this->_attributes[$name];
            $this->_attributes[$name] = $value;
        }
    }

    public function getObjectParams()
    {
        $reflect = new \ReflectionClass($this);
        $props = $reflect->getProperties(\ReflectionProperty::IS_PUBLIC);
        if(!is_array($props))
            $objectParams = [];
        else
        {
            foreach ($props as $prop)
                $objectParams[$prop->getName()] = $this->{$prop->getName()};
        }

        return $objectParams;
    }

    public function getAttributes()
    {
        return array_merge($this->getObjectParams());
    }

    public function getAttribute($key)
    {
        return $this->_attributes[$key];
    }

    public function setAttributes($array = [])
    {
        $this->_attributes = $array;
    }

    public function label($key)
    {
        if($this->_attributeLabels[$key] == '')
            return ucfirst($key);
        else
            return $this->_attributeLabels[$key];
    }

    public function attributeLabels()
    {
        return [

        ];
    }

    public function validate()
    {
        return true;
    }

    public function getErrorField($field)
    {
        return $this->_errors[$field];
    }

    public function save()
    {
       if(method_exists($this, 'beforeSave'))
           $this->beforeSave();

        if(method_exists($this, 'afterSave'))
            $this->afterSave();
    }

    public function load($array)
    {
        if(!is_array($array) OR empty($array))
            return false;

        $changed = array_replace($this->_attributes, $array[ucfirst($this->modelName())]);
        $changes = array_diff($changed, $this->_attributes);

        if(count($changes) > 0)
        {
            $this->_attributes = $changed;
            return true;
        }
        else
            return false;
    }

    protected function beforeValidate($attributes)
    {
        return $attributes;
    }
}