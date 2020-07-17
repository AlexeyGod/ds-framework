<?php
/**
 * Created by Digital-Solution.Ru web-studio.
 * https://digital-solution.ru
 * support@digital-solution.ru
 */

namespace framework\helpers\grid;


use framework\core\Application;
use framework\helpers\grid\assets\BlockViewBundle;

class BlockView
{

    const DRAGGABLE_TAG = '<span class="move icon icon-enlarge"></span>';
    const DRAGGABLE_TH_TAG = '<span class="move icon icon-tab"></span>';
    public function widget($objects = [], $options = [])
    {
        // Регистрируем бандл
        BlockViewBundle::register();

        $html = '<table class="table grid-table dragAndDrop">';

        if(count($objects) == 0)
        {
            $html .= '<tr><td>Нет элементов для отображения</td></tr>';
        }
        else
        {
            // Умолчания
            $identyField = isset($options['identy']) ? $options['identy'] : $objects[0]->getPrimaryKey();
            $sortField = isset($options['sort_field']) ? $options['sort_field'] : false;
            $columns = (isset($options['columns']) ? $options['columns'] : $objects[0]->getFields());
            $action = (isset($options['action']) ? $options['action'] : '');

            // Заголовки
            $html .= '<tr>'.PHP_EOL;
            foreach($columns as $column)
            {
                $html .= '<th>'.PHP_EOL;
                $html .= htmlspecialchars($objects[0]->label($column)).PHP_EOL;
                $html .= '</th>'.PHP_EOL;
            }

            // Add a dragable column
            if($sortField)
                $html .= '<th>'.self::DRAGGABLE_TH_TAG.'</th>'.PHP_EOL;

            $html .= '</tr>'.PHP_EOL;

            foreach ($objects as $object)
            {
                $html .= '<tr class="item"';
                if($sortField)
                    $html .= 'draggable="true"'.PHP_EOL
                        .'data-identy="'.$object->$identyField.'"'.PHP_EOL
                        .'data-sort="'.$object->$sortField.'"'.PHP_EOL;

                    $html .= '>'.PHP_EOL;



                foreach ($columns as $column)
                {
                    $html .= '<td>';
                    $html .= htmlspecialchars($object->$column);
                    $html .= '</td>';

                }

                // Add a dragable column
                if($sortField)
                    $html .= '<td align="center">'.self::DRAGGABLE_TAG.'</td>'.PHP_EOL;

                $html .= '</tr>'.PHP_EOL;
            }
        }


        $html .= '</table>';



        return $html;
    }
}