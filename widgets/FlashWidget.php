<?php
/**
 * Created by Digital-Solution.Ru web-studio.
 * https://digital-solution.ru
 * support@digital-solution.ru
 */

namespace framework\widgets;


use framework\core\Application;

class FlashWidget extends Widget
{
    public static function asBlocks(){
        $flashes = Application::app()->request->getAllFlashes();
        $st = '';

        if(is_array($flashes))
        {
            if(count($flashes) > 0)
                foreach($flashes as $flash)
                {
                    $st .= '<div class="notify '.$flash['key'].'">';

                    if(is_array($flash['message']))
                        //$flashBody = var_export($flashBody, true);
                        $flash['message'] = implode('<br>', $flash['message']);

                    $st .= $flash['message'].'</div>';
                }
        }

        return $st;
    }
}