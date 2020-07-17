<?php

/**
 * Created by Digital-Solution.Ru web-studio.
 * https://digital-solution.ru
 * support@digital-solution.ru
 */

namespace framework\assets\icons;

use framework\components\Bundle;

class IconsBundle extends Bundle
{
   /*
    * Все нижеприведнные переменные должны быть массивом
    * */
   public  $webPath = '';
   public  $sourcePath = '@framework/assets/icons/src';

   public  $css = [
      'css/icons.css'
   ];
}