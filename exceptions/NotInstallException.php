<?php
/**
 * Created by Digital-Solution.Ru web-studio.
 * https://digital-solution.ru
 * support@digital-solution.ru
 */

namespace framework\exceptions;


class NotInstallException extends \Exception {

    public function asPage()
    {
        header("Content-type: text/html; charset=utf-8");
        header("HTTP/1.0 503 Temporary unavailable");


        echo '<html>';
        echo '<head></head>';
        echo '<body>';

        echo "<h1>Установка Digital Solution Content Manager System</h1>";

        echo '</body></html>';
        exit();
    }

}