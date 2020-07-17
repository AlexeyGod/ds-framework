<?php
/**
 * Created by Digital-Solution.Ru web-studio.
 * https://digital-solution.ru
 * support@digital-solution.ru
 */

namespace framework\helpers;


class FileManager
{
    public static function copyDir($source, $destination)
    {
        if(!is_dir($destination))
            mkdir($destination);
       foreach (glob($source.'/*') as $file)
       {
           if(is_dir($file)) self::copyDir($file, $destination.DIRECTORY_SEPARATOR.basename($file));
           else
               copy($file, $destination.DIRECTORY_SEPARATOR.basename($file));
       }
    }
}