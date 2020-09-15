<?php
/**
 * Created by Digital-Solution.Ru web-studio.
 * https://digital-solution.ru
 * support@digital-solution.ru
 */

namespace framework\widgets;


use framework\core\Application;

class BreadCrumbsWidget extends Widget
{
    public static function widget($breadcrumbsArray, $homeLink = false){
        $st = '';

        //exit($st);

        if(count($breadcrumbsArray) > 0)
        {
            $st .= "<ul class=\"breadcrumb\">\n";

            if(!empty($homeLink))
                $st .= "\t".
                    '<li><a href="'.$homeLink['url'].'"><span class="icon icon-home"></span> '.$homeLink['name'].'</a></li>'
                    ."\n";

            foreach($breadcrumbsArray as $crumb)
            {
                if(!empty($crumb['url']))
               $st .= "\t".
                   '<li><a href="'.$crumb['url'].'">'.$crumb['name'].'</a></li>'
                   ."\n";
                else
                    $st .=  "\t".
                        '<li class="active">'.$crumb['name'].'</li>'
                        ."\n";
            }
            $st .= "</ul>\n";
        }

        return $st;
    }
}