<?php
/**
 * Created by Digital-Solution.Ru web-studio.
 * https://digital-solution.ru
 * support@digital-solution.ru
 */

namespace framework\components\db;

use framework\components\Model;
use framework\core\Application;
use framework\exceptions\ErrorException;

class ARModel extends Model
{
    public static $sql;
    protected $_tableName;
    protected $_columns;
    protected $_fields;
    protected $_userFields;
    protected $db;
    protected $_primaryKey;

    public function __construct(array $options = [])
    {
        $this->db = Application::app()->db;
        $this->_tableName = static::tableName();
        $this->setColumns();

        parent::__construct($options);
    }

    public static function tableName()
    {
        return basename(static::class);
    }

    public static function findOne($condition)
    {
        if (!is_array($condition)) {
            if (!is_numeric($condition))
                throw new ErrorException("ARModel: не допускается использование нечислвого значение при запросе findOne через единственный параметр в классе " . static::tableName());

            $data = Application::app()->db->row("SELECT * FROM " . static::tableName() . " WHERE id=:id", ['id' => $condition]);
        } else {
            $where = [];
            foreach ($condition as $key => $val) {
                $where[] = '`' . $key . '` = :' . $key;
                $params[$key] = $val;
            }
            $data = Application::app()->db->row("SELECT * FROM " . static::tableName() . " WHERE " . implode(' AND ', $where), $params);
        }
        if (!empty($data)) {
            $new = new static();
            return $new->createExistingModel($data[0]);
        } else
            return null;
    }

    public static function findAll($condition = false, $order = false, $limit = false)
    {
        $where = '';
        if($condition) $where .= ' WHERE '.$condition;
        $orderStr = '';
        if($order) $orderStr .= ' ORDER BY '.$order;
        $limitStr = '';
        if($limit) $limitStr .= ' LIMIT '.$limit;

        $finalSql = "SELECT * FROM " . static::tableName().$where.$orderStr.$limitStr;

        //exit("sql: ".$finalSql);

        $data =  Application::app()->db->row($finalSql);

        if(empty($data)) return null;
        else
        {
            $objects = [];

            foreach ($data as $item)
            {
                $obj = new static();
                $obj->setDefaultAttributes($item);
                $objects[] = $obj;
            }

            return $objects;
        }
    }

    public function setColumns()
    {
        $_columns = $this->db->row("SHOW COLUMNS FROM " . $this->_tableName);
        foreach ($_columns as $item) {
            $this->_fields[] = $item['Field'];
            if ($item['Key'] != 'PRI') {
                $this->_userFields[] = $item['Field'];
            } else {
                $this->_primaryKey = $item['Field'];
            }
            $this->_columns[$item['Field']] = $item;
        }
    }

    public function setEmptyAttributes()
    {
        if(empty($this->_columns)) return false;

        foreach (array_keys($this->_columns) as $item) {
           $this->_oldAttributes[$item] = '';
           $this->_attributes[$item] = '';
        }
    }

    public function createExistingModel($data)
    {
        $this->setDefaultAttributes($data);
        return $this;
    }

    public function getFields()
    {
        return $this->_fields;
    }
    public function getUserFields()
    {
        return $this->_userFields;
    }

    public function getColumns()
    {
        return $this->_columns;
    }

    public function save()
    {
        if(method_exists($this, 'beforeSave'))
            $this->beforeSave();

        // Формируем поля обновления
        $updateFields = [];
        $updateParams= [];

        foreach ($this->_attributes as $key => $val)
        {
            if($key == $this->_primaryKey) continue;
            if(!isset($this->_columns[$key])) continue;

            if($this->_oldAttributes[$key] != $val)
            {
                $updateFields[] = $key.' = :'.$key;
                $updateParams[$key] = $val;
            }
        }
        //exit(var_dump(['oldAttr' => $this->_oldAttributes, 'Attr' => $this->_attributes, 'updateParams' => $updateParams]));
        if(!$this->isNewRecord)
        {// Обновление
            // Если нечего обновлять
            if(empty($updateFields))
                return false;

            $sql = "UPDATE ".$this->_tableName." SET ".implode(', ', $updateFields)." WHERE ".$this->_primaryKey." = ".$this->getAttribute($this->_primaryKey);
            //exit($sql);
            $this->db->query($sql, $updateParams);
        }
        else
        {// Новый
            // Если нет никаких данных
            if(empty($updateFields))
                return false;

            $sql = "INSERT INTO ".$this->_tableName." SET ".implode(', ', $updateFields);
            $this->db->query($sql, $updateParams);
        }

        if(method_exists($this, 'afterSave'))
            $this->afterSave();
    }

    public function beforeSave()
    {
        //
    }

    public function afterSave()
    {
        if($this->isNewRecord)
        {
            $this->_attributes['id'] = $this->db->column("SELECT MAX(id) FROM ".static::tableName());
            $this->isNewRecord = false;
        }
    }


    public function delete()
    {
       $this->db->query("DELETE FROM ".$this->_tableName." where ".$this->_primaryKey.' = '.$this->getAttribute('id'));
    }

    public function hasMany($class, $condition, $limit = false)
    {
        return $class::findAll($condition, $limit);
    }

    public function hasOne($class, $condition)
    {
        return $class::findOne($condition);
    }

    public function __set($name, $value)
    {
        $getMethod = 'set' . ucfirst($name);
        if ( method_exists($this, $getMethod) ) {
            return $this->$getMethod($name, $value);
        }

        if(isset($this->_columns[$name]))
        {
            $this->_oldAttributes[$name] = $this->_attributes[$name];
            $this->_attributes[$name] = $value;
        }
    }

    public function lastId()
    {
        return $this->db->column("SELECT MAX(id) FROM ".static::tableName());
    }


}