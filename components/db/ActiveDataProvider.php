<?php
/**
 * Created by PhpStorm.
 * User: Алексей-дом
 * Date: 27.07.2019
 * Time: 20:04
 */

namespace framework\components\db;


use framework\core\Application;
use framework\helpers\Pager;
use framework\exceptions\ErrorException;

class ActiveDataProvider
{
    public $modelClass;
    public $items = 0;
    public $onPage = 5;
    public $pages = 1;
    public $currentPage = 1;
    public $on = 0;
    public $all = 0;
    public $order = false;
    public $where = false;

    // Поиск
    public $search = false;
    public $fields = [];

    // Навигация
    public $pager;


    public function __construct($options = [])
    {
        // Объектов на страницу
        if(isset($options['onPage']))
        {
            $this->onPage = $options['onPage'];
        }

        // Сортировка
        if(isset($options['order']))
        {
            $this->order = $options['order'];
        }

        // Условие
        if(isset($options['where']))
        {
            $this->where = $options['where'];
        }

        // Инициируем поиск
        if(isset($options['search']) AND !empty($options['search']['query']))
        {
            $this->search = $options['search']['query'];
            //exit("query: ".$this->search);

            if(empty($options['search']['fields'])) throw new ErrorException("ActiveDataProvider не был передан список полей (fields) для поиска (вызов в ".get_called_class().")");

            foreach($options['search']['fields'] as $field)
            {
                $this->fields[] = ['like', $field, $this->search];
            }
        }

        // Класс для работы с объектами AR
        if(isset($options['class']))
        {
            $this->modelClass = $options['class'];
            $this->items = $this->_getAllWithAR();
        }

        // Всего страниц
        if(!$this->search) $this->_setPages();
        else
            $this->pages = 1;

        $this->pager = new Pager([
            'pages' => $this->pages
        ]);

        $this->currentPage = $this->pager->page;

        // Ограничение запроса
        $this->on = $this->currentPage*$this->onPage - $this->onPage;

    }

    protected function _setPage($page = 1)
    {
        if($page == 'last') $page = $this->pages;

        if($page < 1) $page = 1;
        if($page > $this->pages) $page = $this->pages;

        return $page;
    }

    protected function _setPages()
    {
        $this->pages = ceil($this->items/$this->onPage);

        if($this->pages < 1)
            $this->pages = 1;

    }

    protected function _getAllWithAR()
    {
        $sql = new SqlBuilder();
        $table = $this->modelClass;
        $table = $table::tableName();

        $sql->fields('COUNT(*)');
        $sql->select($table);

        if(!empty($this->fields))
            $sql->orWhere($this->fields);

        if($this->where)
            $sql->where($this->where);
        //exit('SQL: '.$sql->build().' | '. var_export($sql->getParams(), true));

        $this->all = $sql->column();

        return $this->all;
    }

    public function buildSQL($show = false)
    {
        $class = $this->modelClass;
        $sql = $class::find();

        if($this->where)
            $sql->where($this->where);

        if($this->order)
            $sql->orderBy($this->order);

        if(!empty($this->fields))
            $sql->orWhere($this->fields);

        $sql->limit($this->on, $this->onPage);
        //exit("SQL:". $sql->build());
        //
        if($show)
            return $sql->build().' | params: '.var_export($sql->getParams(), true);
        else
            return $sql;
    }

    public function getModels()
    {
        $sql = $this->buildSQL();
        return $sql->all();
    }

    public function getNavigation($template = 'Страница {currentPage} из {pages} (Записи с {on} по {off} из {all})')
    {
        $off = ($this->on+$this->onPage);

        if($off > $this->all) $off = $this->all;
        return str_replace(
            ['{currentPage}', '{pages}', '{on}', '{off}', '{all}'],
            [$this->currentPage, $this->pages, $this->on, $off, $this->all],
            $template
        );
    }

    public function searchWidget($options = [])
    {
        $template = '<div class="field">'."\n".
        "\t".'<form method="post">'."\n".
        "\t\t".'<input type="text" name="search" placeholder="Поиск..." class="input search-input" autofocus>'.
        "\t\t".'<input type="submit" style="display: none">'."\n".
        "\t</form>\n".
        "</div>\n";

        if(!empty($options['template']))
            $output = $options['template'];
        else
            $output = $template;

        if($this->search)
        {
            $output .= '<div class="search-results">'."\n"
                ."\t".'<p>По запросу <b>&laquo;'.htmlspecialchars(stripslashes($this->search)).'&raquo</b> '.($this->items > 0 ? 'найдено '.$this->items.' совпадений' : 'ничего не найдено').'</p>'."\n"
                ."\t".'<p><a href="/'.Application::app()->request->getCurrent().'">Сброс результатов</a></p>'
                .'</div>';
        }

        return $output;
    }


}