<?php

/**
 * Created by Digital-Solution.Ru web-studio.
 * https://digital-solution.ru
 * support@digital-solution.ru
 */
namespace framework\models;

use framework\components\db\ActiveRecord;
use framework\components\db\SqlBuilder;

class DbSettings extends ActiveRecord
{
    public function getTypes()
    {
        return [
            'string' => 'Строка',
            'list' => 'Список',
            'flag' => 'Индикатор'
        ];
    }
    public static function getConfig($configName) {
        $config =  static::findOne(['name' => $configName]);
        if($config == null)
        {
            // Предопределенные значения
            switch($configName):

                case 'secretKey':
                    return 'secret';
                break;

                case 'theme':
                    return 'basic';
                break;

                case 'defaultAdminView':
                    return 'default';
                break;




                endswitch;
            return false;
        }
        else
            return $config->value;
    }

    public static function getAllSettings($simpleOutput = true)
    {
        $sql = new SqlBuilder();
        $sql->select(self::tableName());
        $data = $sql->row();

        $settings = [];

        if(!empty($data))
            foreach ($data as $item)
            {
                if($simpleOutput) $settings[$item['name']] = $item['value'];
                else
                    $settings[$item['name']] = $item;
            }

        return $settings;
    }

    public static function updateSettings($settingsArray)
    {
        foreach ($settingsArray as $name => $value)
        {
            $sql = new SqlBuilder();
            $sql->update(self::tableName());
            $sql->set(['value' => $value]);
            $sql->where(['name' => $name]);
            //exit($sql->build().','.var_export($settingsArray, true));
            $sql->execute();
            unset($sql);
        }
    }


}
