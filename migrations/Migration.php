<?php

namespace framework\migrations;

use framework\core\Application;

class Migration {

    public $db = false;

    public function __construct ($options = [])
    {
        if(is_object(Application::app()))
        {
            if(is_object(Application::app()->app()->db))
                $this->db = Application::app()->db;
        }
        else
        if(isset($options['db']) AND is_object($options['db']))
        {
            $this->db = $options['db'];
        }
    }

    public function up() {}
    public function down() {}

}