<?php

/**
 * Created by Digital-Solution.Ru web-studio.
 * https://digital-solution.ru
 * support@digital-solution.ru
 */

namespace framework\helpers\grid\assets;

use framework\components\Bundle;

class DnDViewBundle extends Bundle
{
   /*
    * Все нижеприведнные переменные должны быть массивом
    * */
   public  $webPath = '';
   public  $sourcePath = '@framework/helpers/grid/assets/src';

   public $css = [
      'block-view.css'
   ];

  // public  $js = [
  //    'jquery.min.js',
  //    //'jquery-ui.min.js'
  // ];
}