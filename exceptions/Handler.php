<?php
/**
 * Created by Digital-Solution.Ru web-studio.
 * https://digital-solution.ru
 * support@digital-solution.ru
 */

namespace framework\exceptions;


use framework\core\Application;
use framework\models\Settings;

class Handler {

    public static function errorHandler($exception)
    {

        $trace = debug_backtrace();
        $bTrace = $trace[0]['args'][0];

        $traceHeader = substr($bTrace, 0, strpos($bTrace, "\n"));

        preg_match_all ("/#[0-9]{1,10}\s(?<file>.+)\s(?<code>.+)\n/isU", $bTrace, $matches);

        if(Application::$appConfig['app'] == 'terminal')
        {
            var_export($matches, true);
            exit();
        }

        if(method_exists($exception, 'asPage'))
            return $exception->asPage();

        header ("Content-type: text/html; charset=utf-8");
        ?>
        <html>
        <head>
            <title>Произошла ошибка</title>
            <style>
                * {
                    margin:0;
                    padding:0;
                }
                body {
                    font-size:16px;
                    line-height: 1.6;
                    background: #f1f1f1;
                    font-family: Arial;
                }
                div.header {
                    padding: 25px;
                    color: red;
                    background: rgba(255, 5, 14, 0.43);
                    border-bottom: 2px solid red;
                }
                div.header h1 {
                    font-size: 18px;
                }
                div.content {
                    padding: 20px;
                    min-height: 675px;
                }
                div.content p {
                    font-size: 18px;
                    font-weight:bold;
                    margin-bottom: 20px;
                }
                div.content table {
                    border; 1px solid black;
                    width: 100%;
                    border-spacing: 0;
                }
                div.content table tr {


                }
                div.content table tr:hover  {
                    background: #81ff865c;
                }
                div.content table td  {
                    padding: 15px;
                    color: #535353;
                    border-bottom: 1px dotted rgba(205, 205, 205, 1);
                }
                div.content table tr:last-child td {
                    border: 0;
                }

                div.content table code {
                    color: #e91e63;
                }
                footer {
                    background: #bebebe;
                    border-top: 2px solid black;
                    padding: 20px;
                    font-weight: bold;
                }
            </style>
        </head>
        <body>
            <div class="header">
                <h1><?=$traceHeader?></h1>
            </div>
            <div class="content">
                <?
                    if(count($matches['file']) > 0) {
                        echo '<table>';
                        foreach ($matches['file'] as $number => $file) {
                            echo '<tr>'
                                .'<td><b>#'.($number+1).'</b></td>'
                                .'<td>'.$file.' '
                                .'<code>'.$matches['code'][$number].'</code>'
                                .'</td></tr>';
                        }
                        echo '</table>';
                    }
                ?>
            </div>
            <footer>
                DS-Framework
            </footer>
        </body>
        </html>
        <?
    }
}