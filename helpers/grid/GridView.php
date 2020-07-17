<?php
/**
 * Created by Digital-Solution.Ru web-studio.
 * https://digital-solution.ru
 * support@digital-solution.ru
 */

namespace framework\helpers\grid;

use framework\core\Application;
use framework\helpers\grid\assets\GridViewBundle;
use framework\helpers\grid\assets\DnDViewBundle;

class GridView
{
    const DEFAULT_COLUMN_CLASS = 'framework\\helpers\\grid\\Column';
    static $emptyValue = '(Пусто)';

    public static function createColumnObject($class, $model, $data)
    {
        return new $class($model, $data);
    }

    public static function registerAssets()
    {
        GridViewBundle::register();
    }


    public static function widget($objects, $options = [], $childrenLevel = 0){
        // Проверка на пустой массив
        if(empty($objects))
        {
            if(isset($options['treeView']))
                return false;
                else
                    return 'Данных нет';
        }

        // Подключаем ресурсы
        self::registerAssets();

        // Подготовка данных
        $columns = [];
        if(!empty($options['emptyValue']))
            self::$emptyValue = $options['emptyValue'];

        $iconTreeOpen = '<span class="icon icon-folder"></span>';
        $iconTreeDefault = '<span class="icon icon-folder-outline"></span>';

        $columns = (isset($options['columns']) ? $options['columns'] : $objects[0]->getFields());

        // Drag And Drop
        if($options['draggable'])
        {
            // Регистрируем бандл
            DnDViewBundle::register();

            // Переменные
            $identyField = isset($options['identy']) ? $options['identy'] : $objects[0]->getPrimaryKey();
            $sortField = isset($options['sort_field']) ? $options['sort_field'] : false;

            $action = (isset($options['action']) ? $options['action'] : '');
        }



        $tableCssClass = (!empty($options['tableCssClass']) ? $options['tableCssClass'] : 'grid-table');

        if($options['draggable'])
            $tableCssClass .= ' dragAndDrop';


        if(!empty($options['columns']))
        {
            $columns = $options['columns'];
        }
        else
        {
            foreach ($objects[0]->getFields() as $column)
            {
                $columns[] = ['attribute' => $column, 'name' => $objects[0]->label($column)];
            }

            if($options['draggable'])
                $columns[] = [
                    'name' => 'Перемещ.',
                    'columnClass' => '\\framework\\helpers\\grid\\DnDColumn',
                ];

            $columns[] = [
                'name' => 'Действия',
                'columnClass' => '\\framework\\helpers\\grid\\ActiveColumn',
            ];
        }

        if($options['develop'])
        {
            $output = '<pre>\'columns\' =>'.PHP_EOL;

            foreach($columns as $column)
            {
                $output .= "\t[\n";
                foreach ($column as $key => $value)
                {
                    $output .= "\t\t'".$key."' => '".$value."',\n";
                }
                $output .= "\t],\n";
            }

            $output .= "".',</pre>';

            return $output;
        }


        // Формируем таблицу

        if($childrenLevel == 0)
        {
            $st = "<table class=\"$tableCssClass\">\n";
            // Заголовки
            $st .= "\t<tr>\n";

            // Древовидное представление
            if(isset($options['treeView']))
            {
                $st.= "\t\t<th>Иеархия</th>";
            }

            foreach ($columns as $column)
            {
                if(empty($column)) continue;

                $st .=  "\t\t".'<th title="'.(isset($column['attribute']) ? $column['attribute'] : $column) .'">'
                    .(empty($column['name']) ? (is_array($column) ? $objects[0]->label($column['attribute']) : $objects[0]->label($column)) : $column['name'])
                    .'</th>'."\n";
            }

            $st .= "</tr>\n";
        }

        // Заполнение
            foreach ($objects as $object)
            {

                $st .= "\t<tr";
                if($options['draggable'])
                {
                    $st .= ' draggable="true"'.PHP_EOL;
                    $st .= ' class="item"'.PHP_EOL;
                    $st .= ' data-identy="'.$object->$identyField.'"'.PHP_EOL;
                    $st .= ' data-sort="'.$object->$sortField.'"'.PHP_EOL;
                }

                if($childrenLevel > 0)
                {
                    $childrenLevelCss = $childrenLevel;
                    if($childrenLevel > 3) $childrenLevelCss = 3;
                    $st .= ' class="child child'.$childrenLevelCss.'"';

                    $st .= ' data-parent="'.$object->{$options['treeView']['relation_field']}.'"';
                    $st .= ' data-identy="'.$object->getIdentity().'"';
                }
                $st .= ">\n";

                // Древовидное представление
                if(isset($options['treeView']))
                {
                    $tree = static::widget(
                        call_user_func(
                            [
                                $object,
                                $options['treeView']['function']
                            ]),
                        $options,
                        $childrenLevel+1);
                    if($tree !== false) $tree = "\r\n".'<!-- TREE -->'."\r\n".$tree."\r\n".'<!-- /TREE -->'."\r\n";

                    $activeElement = '';
                    if($tree !== false)
                        $activeElement .= '<a href="#" class="tree-link" data-target="'.$object->getIdentity().'" id="tree-span-'.$object->getIdentity().'">'.$iconTreeOpen.'</a></td>';
                    else
                        $activeElement .= $iconTreeDefault;

                    $upStr = '';
                    if($childrenLevel > 0)
                    {
                        for($i=1; $i<=$childrenLevel; $i++)
                            $upStr .= '&nbsp;&nbsp;&nbsp;';
                        $upStr .= '';
                    }

                    if(!empty($upStr)) $upStr .= ' ';


                        $st .= '<td>'.$upStr.$activeElement.'</td>';
                }

                    foreach ($columns as $column)
                    {
                        if(empty($column)) continue;

                        if(!is_array($column))
                            $column = ['attribute' => $column];

                        if(empty($column['value']))
                        {
                            $value = (!empty($column['columnClass']) ? (self::createColumnObject($column['columnClass'], $object, $column)) : (self::createColumnObject(self::DEFAULT_COLUMN_CLASS, $object, $column)));
                        }
                        else
                        {
                            if(is_callable($column['value']))
                                $value = call_user_func($column['value'], $object, $column);
                            else
                                $value = $column['value'];
                        }

                        $style = '';

                        if(!empty($column['style']))
                        {
                            $style = ' style="'.$column['style'].'"';
                        }

                        $st .= "\t\t".'<td'.$style.'>'
                            .((empty($value) || $value == NULL) ? self::$emptyValue : $value)
                                .'</td>'."\n";
                    }
                $st .= "</tr>\n";

                // Древовидное представление
                if(isset($options['treeView']))
                {
                    $columnCount = count($columns);
                    if(isset($options['treeView'])) $columnCount++;

                    if($tree !== false)
                    {
                        $st .= $tree;
                    }
                }
            }
        // */
        if($childrenLevel == 0) $st .= "</table>\n";


        if($options['draggable'])
        {
            $jsCode = <<<JSCODE

        console.log('work 5!');

        // Контейнер для выбранного элемента
        var dragNewElement;
         // Контейнер для замещаемого элемента
        var dragOldElement;

        // Перетаскивание
        $('.dragAndDrop').on('drag', '.item', function(){
        // текущий элемент
        dragElement = $(this);
        dragNewElement = $(this);
        // Делаем элемент призрачным
        dragElement.addClass('ghost');

            console.log('Drag start on '+dragElement.attr('data-identy'));
        });

        // Наведение
        $('.dragAndDrop').on('dragover', '.item', function(event){
            console.log('Наведение');
            event.preventDefault();
           dragElement = $(this);
           dragElement.addClass('selected');
        });

        // Уход с элемента
        $('.dragAndDrop').on('dragleave', '.item', function(){
             console.log('LEAVE');
             dragElement = $(this);
             dragElement.removeClass('selected');
        });

         // Окончание
        $('.dragAndDrop').on('dragend', '.item', function(){
            console.log('Окончание');
            dragElement = $(this);
            $(this).removeClass('ghost');
            $('.dragAndDrop .item').removeClass('selected');
            $('.dragAndDrop .item').removeClass('selected');
        });

        // Дроп
         $('.dragAndDrop').on('drop', '.item', function(){
             dragElement = $(this);

             if(dragElement.attr('data-identy') != dragNewElement.attr('data-identy'))
             {
                // Запоминаем старый элемент
                dragOldElement = $(this);

                console.log('DROP: new='+dragNewElement.attr('data-identy')+', old='+dragOldElement.attr('data-identy'));

                $.ajax({
                    url: '$action',
                    type: 'GET',
                    data: 'action=sort&element1='+dragNewElement.attr('data-identy')+'&element2='+dragOldElement.attr('data-identy'),
                    dataType: 'text',
                    success: function(data){
                        console.log('response: '+data);
                    },
                    error: function(data){
                        console.log('error '+data);
                    }
                });

                // Снимаем эффект со перетаскиваемого элемента
                dragNewElement.removeClass('ghost');
                //Вставляем новый после выбранного
                dragElement.after(dragNewElement.prop('outerHTML'));

                // Снимаем эффект на наведенном элементе
                dragElement.removeClass('selected');
                 //Вставляем старый взамен нового
                dragNewElement.after(dragElement.prop('outerHTML'));

                // Удаляем копии
                dragNewElement.remove();
                dragOldElement.remove();

                dragElement.removeClass('selected');
             }

        });


JSCODE;

            \framework\core\Application::app()->assetManager->setJsCode($jsCode, [
                'identy' => 'DnDTableController',
                'depends' => [\framework\assets\jquery\JqueryBundle::class]
            ]);
        }

        if($options['treeView'])
        {
            $jsCode = <<<JS

            // Свернуть дерево
            function closeTr(idTarget)
            {
                console.log("Старт функции. Поиск подчиненных элементов (data-parent="+oTarget+")");
                // Поиск элементов дочек
                children = $("tr[data-parent="+idTarget+"]");

                if(children.length > 0)
                {
                    console.log("Найдено: "+children.length);
                    console.log("Перебор элементов");

                    children.each (function(index, element)
                    {
                        jElement = $(element);
                        console.log(" Элемент №"+index+": "+jElement.attr("data-identy"));

                        console.log("Поиск иконки...");
                        eSpan = jElement.find('span');
                        if(eSpan.length == 1)
                        {
                            console.log("Span найден. Меняем иконку");
                            eSpan.attr('class', 'icon icon-folder');
                        }
                        else
                        {
                            console.log("Span не найден");
                        }

                        // Скрываем элемент
                        jElement.hide();

                        // Проверяем элемент на дочки
                        closeTr(jElement.attr('data-identy'));
                    });
                }
            }


             console.log('Tree view controller active');
             $('.child').hide();

             $('.grid-table').on('click', '.tree-link', function(e){
                 e.preventDefault();
                 oTarget = $(this).attr('data-target');
                 eSpan = $(this).find("span");
                 console.log("span: "+eSpan.attr('class'));

                 if(eSpan.hasClass('icon-folder')) // Открыть
                 {
                    $("tr[data-parent="+oTarget+"]").show();
                    eSpan.removeClass('icon-folder');
                    eSpan.addClass('icon-folder-open');
                 }
                 else // Закрыть
                 {

                    eSpan.removeClass('icon-folder-open');
                    eSpan.addClass('icon-folder');
                    closeTr(oTarget);

                 }


                 console.log('tree link click');
             });
JS;

            \framework\core\Application::app()->assetManager->setJsCode($jsCode, [
                'identy' => 'treeViewController',
                'depends' => [\framework\assets\jquery\JqueryBundle::class]]);

        }

        return $st;
    }
}