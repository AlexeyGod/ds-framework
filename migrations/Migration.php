<?php

namespace framework\migrations;

class Migration {

    public $db = false;

    public function __construct ($options = [])
    {
        if(isset($options['db']) AND is_object($options['db']))
        {
            $this->db = $options['db'];
        }
    }

    public function up() {}
    public function down() {}

}