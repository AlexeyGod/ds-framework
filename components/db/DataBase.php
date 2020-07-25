<?php
/**
 * Created by Digital-Solution.Ru web-studio.
 * https://digital-solution.ru
 * support@digital-solution.ru
 */

namespace framework\components\db;


use framework\exceptions\ErrorException;
use framework\exceptions\NotInstallException;

class DataBase
{
    public $enable = false; // Состояние сконфигурированного объекта
    private $pdo;

    public function __construct($options = [])
    {
        if(empty($options['host']) ||
        !isset($options['username']) ||
        !isset($options['password']) ||
        empty($options['database']))
            throw new NotInstallException("Для работы компонента DataBase необходимо указать host, username, password и database");

        $defaultCharset  = (isset($options['defaultCharset']) ? $options['defaultCharset'] : 'utf-8');
        try {
            $this->pdo = new \PDO('mysql:host=' . $options['host'] . ';dbname=' . $options['database'], $options['username'], $options['password'],
                [\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"]);
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        }
        catch (\PDOException $e)
        {
            exit("Ошибка при создании компонента DB (host: ".$options['host'].", u: ".$options['username'].", p: ".$options['password'].", db: ".$options['database'].") | ".$e->getMessage());
        }
    }

    public function getPDO()
    {
        return $this->pdo;
    }

    public function query($sql, $params = [])
    {
        $query = $this->pdo->prepare($sql);
        if(!empty($params)){
            try
            {
                foreach ($params as $key => $val)
                {
                    $query->bindValue(':'.$key, $val);
                }
            }
            catch (\Exception $e)
            {
                exit($e->getMessage().' | Ошибка при пропытке забиндить '.var_export($key, true).' = '.var_export($val, true));
            }

        }
        $query->execute();

        return $query;
    }

    public function row($sql, $params = [])
    {
        $query = $this->query($sql, $params);

        $data = $query->fetchAll(\PDO::FETCH_ASSOC);

        return $data;
    }

    public function column($sql, $params = [])
    {
        return $this->query($sql, $params)->fetchColumn();
    }

    public static function current_datetime()
    {
        return date('Y-m-d H:i:s');
    }

    public static function unixTime2date($unixTime)
    {
        return date("d.m.Y", $unixTime);
    }

    public static function dateNormalize($date)
    {
        return date('d.m.y в H:i', strtotime($date));
    }

    public static function normalizeRub($rub, $label = 'руб.')
    {
        return number_format($rub, 2, ',', ' ').' '.$label;
    }

}