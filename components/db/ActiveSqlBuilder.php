<?php
/**
 * Created by Digital-Solution.Ru web-studio.
 * https://digital-solution.ru
 * support@digital-solution.ru
 */

namespace framework\components\db;


use framework\core\Application;
use framework\exceptions\ErrorException;

class ActiveSqlBuilder extends SqlBuilder
{
    protected $_targetClass;
    protected $_model;

    public function setTargetClass($className)
    {
        if(!class_exists($className))
        {
            throw new ErrorException("ActiveSqlBuilder::setTargetClass вызван с указанием не сществующего класса ".$className);
        }

        $this->_targetClass = $className;
        $this->_model = Application::createObject($this->_targetClass);
    }


   public function one()
   {
       // For Debug
       //exit(var_dump($this->build(), $this->getParams(), $this->_where));

       if(empty($this->_where)) return null;

       $data = $this->row();

       //exit(var_dump($data[0]));

       if(!empty($data[0]))
       {
           $this->_model->isNewRecord = false;
           $this->_model->loadFromData($data[0]);

           //exit(var_dump($this->_model));
           return $this->_model;
       }
       else
           return null;
   }

    public function all()
    {
        $data = $this->row();
        $objects = [];

        foreach ($data as $item)
        {
            $new = Application::createObject($this->_targetClass);
            $new->isNewRecord = false;
            $new->loadFromData($item);

            $objects[] = $new;
        }

        return $objects;
    }

    public function actionWhere($action = 'AND', $condition)
    {
        if(!is_array($condition) AND !empty($condition))
            if(!strpos(trim($condition), ' ') AND ($condition - $condition) == 0)
                $condition = [$this->_model->primaryKey => $condition];

        parent::actionWhere($action, $condition); // TODO: Change the autogenerated stub
    }


}