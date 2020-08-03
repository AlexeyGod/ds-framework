<?php

namespace framework\migrations;


class m_install_basic_0 extends Migration
{
    public $description = 'Install basic sql-tables';

    public function up(){

        $this->db->beginTransaction();

        try {
            // global_settings
            $this->db->query("CREATE TABLE global_settings ("
                . "id int not null auto_increment,"
                . "name text,"
                . "value text,"
                . "system int DEFAULT 0,"
                . "handler text,"
                . "description text,"
                . "PRIMARY KEY(id))");

            $this->db->query("INSERT INTO global_settings (name, value, system, handler, description)"
                . "VALUES"
                . "('theme', 'basic', 1, 'framework\\\\widgets\\\\settings\\\\ThemeSelect', 'Тема сайта'),"
                . "('secretKey', 'you_secret_key', 1, '', 'Секретный ключ для шифрования'),"
                . "('defaultAdminView', 'index', 1, 'framework\\\\widgets\\\\settings\\\\AdminDefaultView', 'Стартовая страница панели управления'),"
                . "('title', 'DS-Content Manager System', 1, '', '')");

            // global_modules
            $this->db->query("CREATE TABLE global_modules ("
                . "id int not null auto_increment,"
                . "name text,"
                . "class text,"
                . "icon text,"
                . "priority int DEFAULT 0,"
                . "status int DEFAULT 1,"
                . "system int DEFAULT 0,"
                . "PRIMARY KEY(id))");

            $this->db->query("INSERT INTO global_modules (name, class, icon, priority, status, system)"
                . "VALUES"
                . "('user', 'application\\\\models\\\\User', 'icon-users', 0, 0, 0),"
                . "('content', 'modules\\\\content\\\\ContentModule', 'icon-folder-plus', 0, 1, 1),"
                . "('manager', 'modules\\\\manager\\\\ManagerModule', 'icon-folder-plus', 0, 1, 1)"
            );
            $this->db->commit();
        }
        catch (\PDOException $e)
        {
            return 'Ошибка: '.$e->getMessage();
        }

        return "Created table: global_settings, global_modules";
    }

    public function down(){

        $this->db->beginTransaction();
        $this->db->query("DROP TABLE global_settings");
        $this->db->query("DROP TABLE global_modules");
        $this->db->commit();

        return "Deleted table: global_settings, global_modules";
    }
}