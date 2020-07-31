<?php
/**
 * Created by Digital-Solution.Ru web-studio.
 * https://digital-solution.ru
 * support@digital-solution.ru
 */

namespace framework\exceptions;


use framework\components\db\DataBase;

class NotInstallFrameworkException extends \Exception {

    public function __construct($message = "", $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);

        //return $this->asPage();
    }

    public function asPage()
    {
        /**
         * Конфигурации
         *
         */

        $root = getenv("DOCUMENT_ROOT");
        $configFile = $root.'/config/config.php';


        header('content-type: text/html; charset=utf-8');
?>
        <html>
            <head>
                <title>Установка приложения</title>
                <style>
                    * {
                        margin: 0;
                        padding: 0;
                        box-sizing: border-box;
                    }
                    body {
                        font-family: Arial;
                        font-size: 16px;
                        line-height: 1.6;
                    }
                    hr {
                        border: 0;
                        border-top: 1px solid rgba(190, 190, 190, 0.68);
                        margin: 10px 5px;
                    }
                    p {
                        
                    }
                    code {
                        background: #f4433617;
                        color: red;
                        padding: 3px 5px;
                        border-radius: 5px;
                    }
                    pre {
                        background: #c8ec7e26;
                        color: darkgreen;
                        padding: 15px;
                        border: 1px dashed #8bc34a45;
                    }
                    header, footer {
                        background: #3c3f41;
                        color: #ffffff;
                        text-align: center;
                        padding: 20px;
                    }
                    header {
                        min-height: 100px;
                    }
                    header h1 {
                        width: 100%;
                        text-align: left;
                    }
                    section {
                        min-height: 800px;
                        padding: 20px;
                    }

                    article table {
                        width: 100%;
                        border: 1px solid #bebebe;
                        border-spacing: 0;
                    }
                    article table td,  article table th {
                        text-align: left;
                        border: 1px solid #bebebe;
                        padding: 5px;
                    }
                    footer {
                        min-height: 85px;
                    }
                </style>
            </head>
        <body>
            <header>
                <h1>Установка системы</h1>
            </header>
            <section>
            <?php
            /**
             * Определение конфигурации
             */
            $config = require($configFile);
            $db = new DataBase($config['components']['db']['options']);

            /*
            <article>
                <pre>DB: <?=var_export($config['components']['db'], true)?></pre>
            </article>
               */ ?>
            <?
            /**
             * SQL Dump's in directory install
             */

            $sqlDumpDirectory = getenv("DOCUMENT_ROOT").'/migrations';

            $sqlFiles = [];

            foreach (glob ($sqlDumpDirectory.'/*') as $file)
            {
                $sqlFiles[] = [
                    'file' => $file,
                    'name' => basename($file),
                    'migration' => substr(basename($file), 0, -4)
                ];
            }
            ?>
            <!-- SQL Dumps-->
            <article>
                <h2>Миграции</h2>
                <p>Каталог миграций: <code><?=$sqlDumpDirectory?></code></p>
                <hr>
                <?php

                if(count($sqlFiles) > 0)
                {
                    echo '<table>';

                    echo '<tr>'
                        .'<th>'
                        .'№'
                        .'</th>'
                        .'<th>'
                        .'Название'
                        .'</th>'
                        .'<th>'
                        .'Путь'
                        .'</th>'
                        .'<th>'
                        .'Установка'
                        .'</th>'
                        .'</tr>';

                    foreach ($sqlFiles as $tNumber => $item) {
                        echo '<tr>'
                            .'<td>'
                            .($tNumber+1)
                            .'</td>'
                            .'<td>'
                            .$item['migration']
                            .'</td>'
                            .'<td>'
                            .$item['file']
                            .'</td>';


                        require_once ($item['file']);

                        $className = 'migrations\\'.$item['migration'];
                        $m = new $className($db);

                    if($this->status)
                        echo 'success';
                        else
                        {
                            foreach ($m->errors as $error)
                                echo '<p>'.htmlspecialchars(stripslashes($error)).'</p>';
                        }
                        echo '<td></td>';

                        echo '</tr>';
                    }

                    echo '</table>';
                }
                else
                    echo 'Migrations not found';

                ?>
            </article>

            </section>
            <footer>
                Powered by <b>DS-Framework</b>
            </footer>

        </body>
        </html>
<?
    }
}
