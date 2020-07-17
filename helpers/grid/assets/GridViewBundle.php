<?php

/**
 * Created by Digital-Solution.Ru web-studio.
 * https://digital-solution.ru
 * support@digital-solution.ru
 */

namespace framework\helpers\grid\assets;

use framework\components\Bundle;

class GridViewBundle extends Bundle
{
   /*
    * Все нижеприведнные переменные должны быть массивом
    * */
   public  $webPath = '';
   public  $sourcePath = '@framework/helpers/grid/assets/src';

   public $css = [
      'grid.css'
   ];

  // public  $js = [
  //    'jquery.min.js',
  //    //'jquery-ui.min.js'
  // ];
}