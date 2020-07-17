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
use framework\helpers\captcha\Captcha;
use framework\helpers\validators\FileValidator;

class ActiveRecord extends Model
{
    protected $_tableName;
    protected $_primaryKey;
    protected $_columns;
    protected $_fields;
    protected $_fieldsType;
    protected $_errors = [];

    public $scenario = 'default';

    const ENCODING = 'UTF-8';
    const SHOW_DEBUG_ERRORS = false;
    //const SHOW_DEBUG_ERRORS = true;

    const SCENARIO_SYSTEM = 'system';
    const SCENARIO_DEFAULT = 'default';

    public function __construct(array $options = [])
    {
        parent::__construct($options);
        // Имя таблицы
        $this->_tableName = static::tableName();

        // Статус записи (новая/старая)
        if(isset($options['isNewRecord']))
            $this->isNewRecord = $options['isNewRecord'];

        // Экспорт полей из БД
        $this->setFields();
    }


    public static function tableName()
    {
        //$name = strtolower(basename(static::class));
        $class = static::class;
        $name = explode('\\', $class);
        $name = $name[count($name)-1];
        $arr = preg_split('/(?<=[a-z])(?=[A-Z])/u',$name);

        return strtolower(implode('_', $arr));
    }

    public function __set($name, $value)
    {
        $getMethod = 'set' . ucfirst($name);
        if ( method_exists($this, $getMethod) ) {
            return $this->$getMethod($value);
        }



        if(in_array($name, $this->_fields))
        {
            $this->_oldAttributes[$name] = $this->_attributes[$name];
            $this->_attributes[$name] = $value;

            return true;
        }
        else
        {
            throw new ErrorException(static::className()." не может установить переменную ".$name);
        }
    }

    public function __get($name)
    {
        $getMethod = 'get' . ucfirst($name);
        if ( method_exists($this, $getMethod) ) {
            return $this->$getMethod();
        }
        else
            if(in_array($name, $this->_fields))
        {
            return $this->_attributes[$name];
        }
        else
        {
            if(property_exists($this, $name))
                return $this->{$name};
            else
                throw new ErrorException("Обращение к несществующему свойству $name В объекте ".static::className()."");
        }
    }

    public function __isset($name)
    {
        $getMethod = 'get' . ucfirst($name);
        if ( method_exists($this, $getMethod) ) {
            return true;
        }
        else
            if(in_array($name, $this->_fields))
            {
                return true;
            }
            else
            {
                if(property_exists($this, $name))
                {
                    return true;
                }
                else
                {
                    return false;
                }
            }
    }

    public function getAttributes()
    {
        return array_merge($this->_attributes, $this->getObjectParams());
    }

    public function isDbField($field)
    {
        return isset($this->_fieldsType[$field]);
    }

    public function getIdentity()
    {
        return $this->_attributes[$this->_primaryKey];
    }

    public function getPrimaryKey()
    {
        return $this->_primaryKey;
    }

    public function getColumns()
    {
        return $this->_columns;
    }

    public function getFields()
    {
        return $this->_fields;
    }

    public function getErrors()
    {
        return $this->_errors;
    }

    public function getErrorField($field)
    {
        return (!empty($this->_errors[$field]) ? $this->_errors[$field] : false);
    }

    public function load($data = [])
    {
        if(isset($data[$this->modelName()]))
        {
            foreach ($data[$this->modelName()] as $field => $value)
            {
                if(isset($this->$field))
                    $this->$field = $value;
            }

            return true;
        }
        else
            return false;
    }

    public function loadFromData($data)
    {
        //$objectParams = $this->getObjectParams();
        //$data = array_merge($objectParams, $data);
        $this->_attributes = $data;
        $this->_oldAttributes = $data;
        //exit(var_dump($data));
    }

    public function addError($attribute, $errorString)
    {
        if(!self::SHOW_DEBUG_ERRORS)
        {
            if(substr($attribute, 0, 2) == '__')
                return true;
        }
        $this->_errors[$attribute] = $errorString;
    }

    public function hasErrors()
    {
        if(count($this->_errors) == 0) return false;
        else
            return true;
    }

    protected function setFields()
    {
        $sqlBuilder = new ActiveSqlBuilder();
        $sqlBuilder->showColumns(static::tableName());
        $columns = $sqlBuilder->row();

        foreach ($columns as $column)
        {
            $this->_fields[] = $column['Field'];
            $this->_fieldsType[$column['Field']] = $column['Type'];

            if($column["Key"] == 'PRI')
                $this->_primaryKey = $column['Field'];
        }

        $this->_columns = $columns;
    }

    public static function find($condition = '')
    {
        $sqlBuilder = new ActiveSqlBuilder();
        $sqlBuilder->setTargetClass(static::class);
        $sqlBuilder->select(static::tableName());
        $sqlBuilder->where($condition);

        return $sqlBuilder;
    }

    public static function findOne($condition)
    {
        $sqlBuilder = static::find($condition);

        return $sqlBuilder->one();
    }

    public static function findAll($condition = '')
    {
        $sqlBuilder = static::find($condition);

        return $sqlBuilder->all();
    }

    public function rules()
    {
        return [
            [$this->getFields(), 'safe', ['on' => self::SCENARIO_DEFAULT]]
        ];
    }

    public function _validate($attributes)
    {
        // Проверка Правил
        if(count($this->rules()) == 0) {
            $this->addError('__emptyRules', 'Ошибка валидации - правила проверки пусты');
            return false;
        }

        //exit(var_dump($this->rules()));
        //exit(var_dump($attributes));

        // Массив проверенных данных
        $successData = [];

        // Проверка данных на соответствие правилам
        foreach ($attributes as $field => $value)
        {
            //exit(var_dump($updateFields));

            // перечисление правил
            foreach($this->rules() as $idRule => $rule)
            {
                if(self::SHOW_DEBUG_ERRORS)
                {
                    $this->addError("__NOTIFY_--------- START ITERATION -----", 'Rule #'.$idRule.", field: ".$field);
                }

                // Если указано несколько полей
                if(
                    (is_array($rule[0]) AND in_array($field, $rule[0])) // Если указан в перечислении полей
                    OR
                    (!is_array($rule[0]) AND $field == $rule[0]) // Поле указано явно
                )
                {
                    //exit("Field $field validated");

                    //Определяем текущий сценарий
                    if(!isset($rule[2]['on']))
                        $currentScenario = ['default'];
                    else
                        $currentScenario = (is_array($rule[2]['on']) ? $rule[2]['on'] : [$rule[2]['on']]);

                    if(self::SHOW_DEBUG_ERRORS)
                    {
                        $this->addError("__NOTIFY_".$idRule."_SCENARIO_FOR_".$field, "Текущий сценарий: ".$this->scenario." сравнение с: ".(is_array($currentScenario) ? implode(", ", $currentScenario) : $currentScenario));
                    }

                    // Если не необходим по сценарию
                    if(!in_array($this->scenario, $currentScenario))
                    {
                        $this->addError("__NOTIFY_".$idRule."_SKIP_FOR_".$field, "Поле: ".$field." пропущено, т.к. не входит в сценарий валидации");
                        continue;
                    }

                      // Применяем валидатор
                        switch($rule[1]){
                            case 'safe':
                                    $successData[$field] = $value;
                                break;

                            case 'length':
                                if(!isset($rule[2]['range']))
                                    throw new ErrorException("ActiveRecord не может применить валидатор ".$rule[1]."  для поля ".$field.", т.к. не указан параметр RANGE [min, max] в правилах класса ".static::class);
                                $length = mb_strlen($value, self::ENCODING);
                                if($length < $rule[2]['range'][0] OR $length > $rule[2]['range'][1])
                                    $this->addError($field, "Длинна должна быть от ".$rule[2]['range'][0]." до ".$rule[2]['range'][1]." символов.");
                                else
                                    $successData[$field] = $value;
                                break;

                            case 'required':
                                if(trim($value) == '' OR $value == null)
                                    $this->addError($field, "Поле обязательное для заполнения.");
                                else
                                    $successData[$field] = $value;
                                break;

                            case 'mask':
                                if(preg_match($rule[2]['mask'], $value))
                                    $this->addError($field, $rule[2]['error']);
                                else
                                    $successData[$field] = $value;
                                break;

                            case '>':
                            case '<':
                                $logic = false;

                                if($rule[1] =='>') $logic = ($value > $rule[2]['value']);
                                if($rule[1] =='<') $logic = ($value < $rule[2]['value']);

                                if(!$logic)
                                    $this->addError($field, "Значение должно быть ".$rule[1].$rule[2]['value']);
                                else
                                    $successData[$field] = $value;
                                break;

                            case 'range':
                                if(!isset($rule[2]['range']))
                                    throw new ErrorException("ActiveRecord не может применить валидатор ".$rule[1]."  для поля ".$field.", т.к. не указан параметр RANGE [min, max] в правилах класса ".static::class);

                                $length = mb_strlen($value, safe::ENCODING);

                                if($length < $rule[2]['range'][0] OR $length > $rule[2]['range'][1])
                                    $this->addError($field, "число должна находиться в диапозоне ".$rule[2]['range'][0]." - ".$rule[2]['range'][1].".");
                                else
                                    $successData[$field] = $value;
                                break;

                            case 'email':
                                if (preg_match("/^(?:[a-z0-9]+(?:[-_.]?[a-z0-9]+)?@[a-z0-9_.-]+(?:\.?[a-z0-9]+)?\.[a-z]{2,5})$/i", $value))
                                    $successData[$field] = $value;
                                else
                                    $this->addError($field, "Проверьте правильность ввода E-Mail");
                                break;

                            case 'unique':
                                if(!isset($rule[2]['targetClass']))
                                    throw new ErrorException("ActiveRecord не может применить валидатор ".$rule[1]."  для поля ".$field.", т.к. не указан параметр targetClass в правилах класса ".static::class);
                                if(!isset($rule[2]['targetField']))
                                    throw new ErrorException("ActiveRecord не может применить валидатор ".$rule[1]."  для поля ".$field.", т.к. не указан параметр targetField в правилах класса ".static::class);

                                if ($rule[2]['targetClass']::find()->where([$rule[2]['targetField'] => $value])->one())
                                    $this->addError($field, 'Это значение уже используется в системе');
                                else
                                    $successData[$field] = $value;
                                break;

                            case 'file':

                                $validator = Application::createObject(FileValidator::class, $rule[2]);
                                $result = $validator->validate($this, $field);

                                //exit("TEST Drive of ".var_export($validator, true));

                                if(!$result)
                                {
                                    //if(!isset($rule[2]['skipOnEmpty']))
                                    //    $rule[2]['skipOnEmpty'] = true; // пропуск пустых по умолчанию

                                    // Не загружен
                                    //if($rule[2]['skipOnEmpty'] == false)
                                    //{
                                    //    $this->addError($field, $validator->getError());
                                    //}
                                    $this->addError($field, $validator->getError());
                                }
                                else
                                    $successData[$field] = $result;
                                break;

                            case 'repeat':
                                if(!isset($rule[2]['repeat']))
                                {
                                    throw new ErrorException("ActiveRecord валидатор ".$rule[1]." указанный в правилах класса ".static::class." не получил параметр repeat");
                                }

                                if($value != $this->{$rule[2]['repeat']})
                                {
                                    $this->addError($field, 'Зачения полей не совпадают');
                                    $this->addError($rule[2]['repeat'], 'Значения полей не совпадают');
                                    break;
                                }
                                else
                                {
                                    $successData[$field] = $value;
                                }

                                break;

                            case 'captcha':
                                if(empty($value))
                                {
                                    $this->addError($field, 'Обязательное поле');
                                    break;
                                }

                                $captchaValidator = Application::app()->createObject(Captcha::class);
                                if(!$captchaValidator->check($value))
                                {
                                    $this->addError($field, 'Не верный код с картинки ');//.md5($value).' || '.$_COOKIE['ds-captcha'].' || '.$captchaValidator->getVerifyCodeName().' || '.$captchaValidator->getVerifyCode());
                                }
                                break;

                            default:
                                if(!class_exists($rule[1]))
                                {
                                    $validatorMethod = $rule[1].'Validator';
                                    if(method_exists($this, $validatorMethod))
                                    {
                                        $result = $this->{$validatorMethod}($value);
                                        if($result)
                                        {
                                            $successData[$field] = $result;
                                        }
                                        else
                                        {
                                            $this->addError($field, "Некорректное значение");
                                        }
                                    }
                                    else
                                        throw new ErrorException("ActiveRecord не может найти валидатор ".$rule[1]." указанный в правилах класса ".static::class);
                                }
                                else
                                {
                                    $validator = Application::createObject($rule[1], $rule[2]);
                                    $result = $validator->validate($this, $field);

                                    if(!$result)
                                        $this->addError($field, $validator->getError());
                                    else
                                        $successData[$field] = $result;
                                }
                                break;
                        }
                }
                else
                {
                    $this->addError('__NOTIFY_'.$idRule.'_SKIP_noValidator_for_field_'.$field, "Не определен валидатор для поля $field. Список сравнения [ ".(is_array($rule[0]) ? implode(", ", $rule[0]) : $rule[0]). ']');
                }
            }
        }


        if(!$this->hasErrors())
            return $successData;
        else
        {
            $this->addError('__falseReturn', 'Валидация завершилась с ошибками');
            return false;
        }
    }




    public function save()
    {
        $changes = [];
        $attributes = [];


        foreach ($this->getAttributes() as $key => $val)
        {
            // Пропускаем автоинкрементное поле
            if($key == $this->_primaryKey) continue;

            // Пропускаем поля, которые не изменились (если запись не новая)
            if(isset($this->_oldAttributes[$key]) and $this->_oldAttributes[$key] == $val and !$this->isNewRecord) continue;

            $attributes[$key] = $val;
        }

        if(method_exists($this, 'beforeValidate'))
            $attributes = $this->beforeValidate($attributes);

        //exit(var_dump($attributes).'///beforeValidate-function');


        // Проверка полей
        $attributes = $this->_validate($attributes);

        //exit(var_dump($attributes).'///afterValidate');

        if(method_exists($this, 'afterValidate'))
            $attributes = $this->afterValidate($attributes);

        //exit(var_dump($attributes).'///afterValidate-function');

        // Формируем поля обновления
        $updateFields = [];


        if(!empty($attributes))
        {
            if(!$this->isNewRecord)
            {
                foreach ($attributes as $key => $val)
                {
                    if($key == $this->_primaryKey) continue;
                    if(!isset($this->_fieldsType[$key])) continue;

                    if($this->_oldAttributes[$key] != $val)
                    {
                        $updateFields[$key] = $val;
                    }
                }
            }
            else
            {
                $updateFields = $attributes;
            }
        }

        // Если нечего обновлять
        if(empty($updateFields))
        {
            $this->addError('__noChanges', 'Сохранение не выполнено, т.к. ни одного поля не было изменено');
            return false;
        }
        else
        {
            foreach ($updateFields as $key => $value)
            {
                if($this->isDbField($key)) {
                    $changes[$key] = ['before' => $this->_oldAttributes[$key], 'after' => $value];
                    $updates[$key] = $value;
                }

            }

            if(method_exists($this, 'beforeSave'))
                $updates = $this->beforeSave($updates);

            //exit(var_dump($updates).'///beforeSave-function');

            if (!$this->isNewRecord)
            {
                // Обновление
                // Запрос в базу
                $sqlBuilder = new ActiveSqlBuilder();
                $sqlBuilder->update($this->_tableName)->set($updates)->where([$this->_primaryKey => $this->_attributes[$this->_primaryKey]]);

                if(method_exists($this, 'beforeUpdateQuery'))
                    $this->beforeUpdateQuery($sqlBuilder);

                $sqlBuilder->query();

                $this->_oldAttributes = array_merge($this->_oldAttributes, $updateFields);
            }
            else
            {
                // Новый
                // Запрос в базу
                $sqlBuilder = new ActiveSqlBuilder();
                //exit(var_dump($updates).'_T:'.$this->_tableName);

                $sqlBuilder->insert($this->_tableName)->set($updates);
                if(method_exists($this, 'beforeInsertQuery'))
                    $this->beforeInsertQuery($sqlBuilder);

                $sqlBuilder->query();
                unset($sqlBuilder);

                // Дополняем ID
                $sqlBuilder = new ActiveSqlBuilder();
                $sqlBuilder->select($this->_tableName)->fields('MAX(' . $this->_primaryKey . ')');

                $this->_attributes[$this->_primaryKey] = $sqlBuilder->column();
                $this->_oldAttributes = $this->_attributes;
                $this->isNewRecord = false;
            }

            if (method_exists($this, 'afterSave'))
                $this->afterSave($changes);

            return true;
        }
    }

    public function delete()
    {
        if (method_exists($this, 'beforeDelete'))
            $this->beforeDelete();

        if($this->isNewRecord) return false;

        $sqlBuilder = new ActiveSqlBuilder();
        $sqlBuilder->delete($this->tableName())->where([$this->_primaryKey => $this->_attributes[$this->_primaryKey]]);
        $sqlBuilder->query();

        if (method_exists($this, 'afterDelete'))
            $this->afterDelete();

        return true;
    }



    // Отношения

    public function hasOne($class, $condition)
    {
        $filed = array_keys($condition)[0];
        $sql = $class::find([$filed => $this->_attributes[$condition[$filed]]]);
        //if($condition['id'] != 'id_project') exit('sql: '.var_export($sql->build(), TRUE).' | param: '.var_export($sql->getParams(), true).' | condition: '. var_export($condition, true));
        //exit($sql->build().' | '.var_export($sql->getParams(), true));
        return $sql->one();

    }

    public function hasMany($class, $condition)
    {
        $filed = array_keys($condition)[0];
        $sql = $class::find([$filed => $this->_attributes[$condition[$filed]]]);
        //$sql =  $class::find($condition);

       //exit(var_dump($sql->build(), $sql->getParams(), var_export($condition, true), ['value' => $value]));

        return $sql->all();
    }

    // Поведения

    public function beforeSave($attributes)
    {
        return $attributes;
    }

    public function afterSave($changes = [])
    {
        return $changes;
    }

    public function afterValidate($attributes)
    {
        return $attributes;
    }

}