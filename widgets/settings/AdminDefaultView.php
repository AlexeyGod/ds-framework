<?php
/**
 * Created by Digital-Solution.Ru web-studio.
 * https://digital-solution.ru
 * support@digital-solution.ru
 */

namespace framework\widgets\settings;


class AdminDefaultView
{
    public $model;

    public function __construct($model)
    {
        $this->model = $model;
    }

    public function getItems()
    {
        return [
            'index' => 'Состояние системы',
        ];
    }



    public function template()
    {
        $out = '<select name="configs['.$this->model->name.']" class="input">';

        $items= $this->getItems();

        foreach($items as $itemKey => $itemLabel)
            $out .= '<option value="'.$itemKey.'"'.($itemKey == $this->model->value ? ' selected' : '').'>'.$itemLabel.'</option>';

        $out .= '</select>';

        return $out;
    }


    public function run()
    {
        return $this->template();
    }
}