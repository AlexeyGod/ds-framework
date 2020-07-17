<?php
/**
 * Created by PhpStorm.
 * User: Алексей-дом
 * Date: 21.07.2019
 * Time: 18:18
 */

namespace framework\helpers\grid;


use framework\core\Application;

class DnDColumn
{
    public function templateView()
    {

        return '<span class="move icon icon-enlarge"></span>';
    }


    public function __toString()
    {
        return $this->templateView();
        //return var_export($this->_data, true);
    }

}