<?php
/**
 * Created by PhpStorm.
 * User: GODINSKIJ_AI
 * Date: 31.07.2020
 * Time: 9:34
 */

namespace framework/migrations;


class m_install_basic_0
{
    public $status = true;
    public $errors = [];

    public $description = 'Install basic sql-tables';

    public function __construct($db)
    {
        $rows = explode(";", self::$table_Settings);

        //exit(var_export($rows));
        $db->beginTransaction();
        foreach ($rows as $row)
        {
            if(empty(trim($row))) continue;
            try {
                $db->query(trim($row));
            }
            catch (PDOException $e)
            {
                $this->errors[] = $e->getMessage();
                $this->status = false;
            }
        }
        $db->commit();
    }

    public static $table_Settings = <<<SQL
CREATE TABLE IF NOT EXISTS settings (
id int not null auto_increment,
name text,
value text,
system int DEFAULT 0,
handler text,
description text,
PRIMARY KEY(id));

INSERT INTO settings (name, value, system, handler, description)
VALUES
('theme', 'basic', 1, 'framework\\widgets\\settings\\ThemeSelect', 'Тема сайта'),
('secretKey', 'you_secret_key', 1, '', 'Секретный ключ для шифрования'),
('defaultAdminView', 'index', 1, 'framework\\widgets\\settings\\AdminDefaultView', 'Стартовая страница панели управления'),
('title', 'DS-Content Manager System', 1, '', '');
SQL;



}