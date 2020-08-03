<?php

/**
 * Created by Digital-Solution.Ru web-studio.
 * https://digital-solution.ru
 * support@digital-solution.ru
 */

namespace framework\assets\bootstrap;

use framework\components\Bundle;

class BootstrapBundle extends Bundle
{
   /*
    * Все нижеприведнные переменные должны быть массивом
    * */
   public  $webPath = '';
   public  $sourcePath = '@framework/assets/bootstrap/src';

   public  $css = [
      'css/bootstrap.min.css'
   ];

   public $js = [
      'js/bootstrap.min.js'
   ];

   public $depends = [
       'framework\\assets\\jquery\\JqueryBundle'
   ];
}