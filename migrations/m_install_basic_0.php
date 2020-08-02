<?php

namespace framework\migrations;


class m_install_basic_0 extends Migration
{
    public $description = 'Install basic sql-tables';

    public function up(){

        $this->db->beginTransaction();

        $this->db->query("CREATE TABLE IF NOT EXISTS settings ("
        ."id int not null auto_increment,"
        ."name text,"
        ."value text,"
        ."system int DEFAULT 0,"
        ."handler text,"
        ."description text,"
        ."PRIMARY KEY(id))");

        $this->db->query("INSERT INTO settings (name, value, system, handler, description)"
        ."VALUES"
        ."('theme', 'basic', 1, 'framework\\widgets\\settings\\ThemeSelect', 'Тема сайта'),"
        ."('secretKey', 'you_secret_key', 1, '', 'Секретный ключ для шифрования'),"
        ."('defaultAdminView', 'index', 1, 'framework\\widgets\\settings\\AdminDefaultView', 'Стартовая страница панели управления'),"
        ."('title', 'DS-Content Manager System', 1, '', '')");


        $this->db->commit();
    }
}