<?php
/**
 * Created by PhpStorm.
 * User: Алексей-дом
 * Date: 28.07.2019
 * Time: 20:31
 */

namespace framework\helpers;

use framework\core\Application;

class Pager
{
    public $currentPage = 1;
    public $pages = 1;
    public $options = [];

    public function __construct($options)
    {
        $this->page = intval($_REQUEST['page']);

        if(isset($options['pages']))
            $this->pages = $options['pages'];

        if($this->page < 1)
            $this->page = 1;

        if($this->page > $this->pages)
            $this->page = $this->pages;

        if(isset($options['options']))
            $this->options = $options['options'];
    }

    public function widget()
    {
        $st = '<ul class="pagination">'."\n";

        for($i =1; $i <= $this->pages; $i++)
        {
            $st .= "\t".'<li'.($i == $this->page ? ' class="active"' : '').'>'.($i == $this->page ? $i : '<a href="'.Application::app()->request->makeUrl('', ['page' => $i]).'">'.$i.'</a>').'</li>'."\n";
        }

        $st .= "</ul>\n";

        return $st;
    }


}