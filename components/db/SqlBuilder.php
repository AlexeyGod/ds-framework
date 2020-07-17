<?php
/**
 * Created by Digital-Solution.Ru web-studio.
 * https://digital-solution.ru
 * support@digital-solution.ru
 */

namespace framework\components\db;


use framework\core\Application;
use framework\exceptions\ErrorException;

class SqlBuilder
{
    protected $_db = false;
    protected $_action;
    protected $_table;
    protected $_fields;
    protected $_join;
    protected $_set;
    protected $_where;
    protected $_whereParams;
    protected $_orderBy;
    protected $_limit;
    protected $_sql = '';


    public function __construct($options = [])
    {
        if(!empty($options['db']))
            $this->_db = $options['db'];
    }

    public function getDb()
    {
        if(!is_object($this->_db))
        {

            if(is_object(Application::app()->db))
            {
                $this->_db = Application::app()->db;
            }
            else
            {
                throw new ErrorException("Не удается получить экземляр компонента DB при вызове SqlBuilder::getDb()");
            }
        }

        return $this->_db;
    }

    public function __toString()
    {
        return $this->build();
    }

    public function setFilter($condition)
    {
        $this->actionWhere('AND', $condition);

        return $this;
    }

    public function setParam($key , $val)
    {
        //if(empty($val)) $val = NULL;
        $this->_whereParams[$key] = $val;
    }

    public function getParams()
    {
        return (!empty($this->_whereParams) ? $this->_whereParams : []);
    }

    public function select($table)
    {
        $this->_action = 'select';
        $this->_table = $table;
        return $this;
    }

    public function insert($table)
    {
        $this->_action = 'insert';
        $this->_table = $table;
        return $this;
    }

    public function update($table)
    {
        $this->_action = 'update';
        $this->_table = $table;
        return $this;
    }

    public function delete($table)
    {
        $this->_action = 'delete';
        $this->_table = $table;
        return $this;
    }

    public function showColumns($table)
    {
        $this->_action = 'columns';
        $this->_table = $table;
        return $this;
    }

    public function fields($fields = '*')
    {
        if(!is_array($fields))
        {
            $str = $fields;
        }
        else
        {
            $str = implode (", ", $fields);
        }

        $this->_fields = $str;

        return $this;
    }


    public function whereNormalize($condition)
    {
        // Строковое представление условий
        $newWhere = '';
        // Условия
        $conditions = [];
        // ...И их значения
        $conditionVars = [];

        if(!is_array($condition))
        {
            // Строка
            $conditions[] = $condition;
        }
        else
        {
            // Массив
            foreach ($condition as $key => $value)
            {
                if(!is_array($value)) // Значения элемента не массив
                {
                    // Определяем размерность
                    $size = count($condition);
                    if($size == 1)
                    {
                        //  Ключ => значение
                        $conditions[] = $key." = :".$key;
                        $this->setParam($key, $value);
                    }
                    elseif($size == 3) // Три элемента ( ['like', 'user', 'Костя']
                    {
                        $conditions[] = $condition[1]." ".$condition[0]." :".$condition[1];
                        if(strtoupper($condition[0]) == 'LIKE')
                            $this->setParam($condition[1], '%'.$condition[2].'%');
                        else
                            $this->setParam($condition[1], $condition[2]);
                        break;
                    }
                    else
                    {
                        throw new ErrorException("SqlBuilder error in function WHERE: very many params: " . $size);
                    }
                }
                else
                {
                    $w = $this->whereNormalize($value);
                    $conditions[] = "(". join(" AND ", $w['where']). ")";
                    $conditionVars = array_replace_recursive($conditionVars, $w['params']);
                }
            }

        }

        return [
            'where' => $conditions,
            'params' => $conditionVars
        ];
    }

    public function actionWhere ($action = 'AND', $condition)
    {
        $action = trim($action);

        $where = $this->whereNormalize($condition);

        $operator = 'AND';

        if($action  == 'OR')
            $operator = 'OR';

        if($this->_where == '')
        {
            $this->_where = join("\n".$operator." ", $where['where']);
        }
        else // Приписываем если ранее что-то было
        {
            $this->_where .= "\n".$operator." (".join(' AND ', $where['where']).')';
        }
    }

    public function where($condition)
    {
        $this->actionWhere('AND', $condition);

        return $this;
    }

    public function orWhere($condition)
    {
        $this->actionWhere('OR', $condition);

        return $this;
    }

    public function join($tableName, $fields)
    {
        // Перенос если включение уже было объявлено
        if(!empty($this->_join))
            $this->_join .= "\n";

        // Имя таблицы
        if(is_array($tableName))
        {
            $table = array_keys($tableName)[0].' as '.$tableName[array_keys($tableName)[0]];
        }
        else
            $table = $tableName;

        // Поля
        $stringFields = [];
        foreach($fields as $fieldKey => $field)
        {
            if(is_array($field))
                $stringFields[] = array_keys($field)[0]."=".$field[0]."";
            else
                $stringFields[] = $fieldKey."=".$field."";
        }

        $this->_join .= "JOIN ".$table." ON ".join(" AND ", $stringFields);

        return $this;
    }

    public function orderBy($order = false)
    {
        if(!$order)
            $this->_orderBy = '';
        else
        {
            if(!is_array($order))
                $this->_orderBy .= $order;
            else
            {
                $orderBy = [];
                // Массив
                foreach ($order as $key => $value)
                {
                    if(!is_array($value)) {
                        // Одинарный
                        $this->_orderBy .= $key . ' ' . $value;
                        break;
                    }
                    else
                    {
                        foreach ($value as $field => $sort)
                            $orderBy[] = $field.' '.$sort;
                    }
                }
                $this->_orderBy .= join(', ', $orderBy);
            }
        }

        return $this;
    }

    public function limit($on = null, $rows = null)
    {
        if(empty($on) AND empty($rows)) $this->_limit = '';
        else
            $this->_limit = ''.intval($on).', '.intval($rows);

        return $this;
    }

    public function set($arrayKeysAndValues = [])
    {
        if(!empty($arrayKeysAndValues))
        foreach ($arrayKeysAndValues as $key => $value)
        {
            $this->_set[] = '`'.$key.'` = :'.$key;
            $this->setParam($key, $value);
        }

        return $this;
    }

    public function build()
    {
        // Действия
        switch ($this->_action):
            case 'select':
                $this->_sql = 'SELECT '.($this->_fields == '' ? '*' : $this->_fields).' FROM '.$this->_table;
                break;
            case 'insert':
                if(!is_array($this->_set))
                    throw new ErrorException("SqlBuilder не может сформировать параметры вставки для INSERT т.к. параметры вставки (->set(array) ) не сформированы");

                $this->_sql = 'INSERT INTO '.$this->_table.' SET '.join(",\n", $this->_set);
                break;
            case 'update':
                $this->_sql = 'UPDATE '.$this->_table.' SET '.join(",\n", $this->_set);
                break;
            case 'delete':
                $this->_sql = 'DELETE FROM '.$this->_table;
                break;
            case 'columns':
                $this->_sql = 'SHOW COLUMNS FROM '.$this->_table;
                break;

            endswitch;

        // JOIN

        if(!empty($this->_join))
        {
            $this->_sql .= "\n".$this->_join;
        }

        // Условия WHERE
        if(!empty($this->_where))
            $this->_sql .= "\nWHERE \n".$this->_where;

        // Сортировка
        if(!empty($this->_orderBy) AND $this->_action == 'select')
            $this->_sql .= "\nORDER BY ".$this->_orderBy;

        // Ограничения
        if(!empty($this->_limit))
            $this->_sql .= "\nLIMIT ".$this->_limit;


        //exit(var_dump($this->_sql, $this->getParams()));

        return $this->_sql; //.' (debug: action: '.$this->_action.', fields: '.$this->_fields.', table: '.$this->_table.')';
    }

    public function row()
    {
        $db = $this->getDb();

        return $db->row($this->build(), $this->getParams());
    }

    public function column()
    {
        $db = $this->getDb();

        return $db->column($this->build(), $this->getParams());
    }

    public function query()
    {
        $db = $this->getDb();

        return $db->query($this->build(), $this->getParams());
    }

    public function execute()
    {
        return $this->query();
    }

}