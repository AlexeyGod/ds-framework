<?php

/**
 * Created by Digital-Solution.Ru web-studio.
 * https://digital-solution.ru
 * support@digital-solution.ru
 */

namespace framework\assets\jquery;

use framework\components\Bundle;

class JqueryBundle extends Bundle
{
   /*
    * Все нижеприведнные переменные должны быть массивом
    * */
   public  $webPath = '';
   public  $sourcePath = '@framework/assets/jquery/src';

   public  $js = [
      'jquery.min.js',
      //'jquery-ui.min.js'
   ];
}