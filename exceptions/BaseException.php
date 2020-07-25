<?php
/**
 * Created by Digital-Solution.Ru web-studio.
 * https://digital-solution.ru
 * support@digital-solution.ru
 */

namespace framework\exceptions;


use framework\core\Application;
use framework\models\Settings;

class BaseException extends \Exception {

    public function __construct($message = '', $errorLevel = 0, $errorFile = '', $errorLine = 0) {
        parent::__construct($message, $errorLevel);
        $this->file = $errorFile;
        $this->line = $errorLine;
    }

    public function exceptionName()
    {
        return basename(static::class);
    }

    public static function errorHandler($exception)
    {
        if(method_exists($exception, 'asPage'))
        {
            return $exception->asPage();
        }
        else
        {
            exit('(ReWork BaseException)Не обработанное исключение в классе '.get_class($exception)
                .'<br>'.
                $exception->getMessage()." in file \n\n<pre>".var_export($exception, true).'</pre>');
        }
    }

    /**
     * Старый обработчик - deprecated
    */
    public static function errorHandlerBackup($exception)
    {
        $stack = ''; // Инициализируем стек
        $template = false; // Выключаем вывод шаблона ошибки по умолчанию


        if(!method_exists($exception, 'exceptionName'))
        {
            //$stack .=
            exit('(ReWork BaseException)Не обработанное исключение в классе '.get_class($exception)
                .'<br>'.
                $exception->getMessage()." in file \n\n<pre>".var_export($exception, true).'</pre>');
        }
        else
        {
            // Формируем название файла шаблона
            $file = $exception->exceptionName();

            // Поиск шаблонов ошибок
            switch($file)
            {
                case 'NotFoundHttpException':
                    $file = '404';
                    $template = true;
                    break;
            }
        }

        // Формирование текста ошибки
        $content = ($exception->getMessage() != '' ? $exception->getMessage() :  'Описание ошибки отсутствует');

        // Существование приложения
        if(is_object(Application::app()))
        {
            $exceptionFile = Application::app()->getRealPath('@root/themes/'.Settings::getConfig('theme').'/exceptions/'.$file.'.php');
            if(is_file($exceptionFile))
            {
                require ($exceptionFile);
                exit();
            }
            else
            {
                if($template)
                    exit("Не найден шаблон исключения: ".$exceptionFile);
            }
        }

        $stack .= '<hr>';
        //$stack .= var_export(debug_backtrace(), true);
        $stack .= '<table border="1">';
        foreach (array_reverse(debug_backtrace()) as $trace)
        {
            $stack .= '<tr>';
            foreach ($trace as $label => $data)
            {
                if(is_object($data))
                    //$data = '[Object to Sting (ReWork Later)]';
                    $data = get_class($data);
                if(is_array($data))
                {
                    $out = '';
                    if(!empty($data))
                        foreach ($data as $key => $value)
                        {
                            $out .= '<p>'.$key.' =&gt; '.str_replace('#', '<br>#', $value).'</p><br><br>';
                        }
                    else
                    {
                        $out = 'EMPTY';
                    }
                    $data = 'array('.$out.')';
                }

                $stack .= '<td>'.$label.' =&gt; '.$data.'</td>';
            }
            $stack .= '</tr>';
        }
        $stack .='</table>';

        $stack .= '<br>Приложение<br><pre>'.var_export(Application::app()->getComponentList(), true)
            //."\n-\nПользователь\n"
            //."name: <b>".Application::app()->identy->shortName."</b>\n"
            //."auth: <b>".(Application::app()->identy->isAuth() ? 'yeas' : 'no')."</b>\n"
            //."\n-\nРазрешения пользователя:\n"
            //.var_export(Application::app()->identy->getAccepts(), true)
            ."\n- - -\n"
            ."ROUTING INFO:\n".
            var_export(Application::app()->request->getRouting(), true)
            .'</pre>';

        exit('<html>
        <head>
            <style>
                pre {
                background: #e6e6e6;
                color: darkred;
                padding: 5px;
                margin: 5px;
                }
                table {
                width: 100%;
                border-spacing: unset;
                }
                table td {
                padding: 5px;
                }
            </style>
        </head>
        <body>
        Произошла ошибка: ' .$content.$stack.'
        </body>');
    }
}