<?php
/**
 * Created by Digital-Solution.Ru web-studio.
 * https://digital-solution.ru
 * support@digital-solution.ru
 */

namespace framework\core;

use framework\components\Route;

class Application extends BaseApplication
{
    public static $_config; // Входные настройки
    public static $app; // Экземпляр приложения

    public function __construct($config = [])
    {
        self::$app = $this;
        parent::__construct($config);
        // Экземпляр приложения для реазизации app()
    }

    public function run()
    {
        return $this->route->run();
    }

    public static function app()
    {
        if(is_object(self::$app)) return self::$app;
        else
        {
            $stack ='<h1>Ошибка сборки App</h1><table>';
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
                                    $out .= $key.' =&gt; '.$value.', ';
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
            throw new \Exception("Приложение не запущено<hr>".$stack);
        }

    }

    public function version()
    {
        return '4.0.0';
    }

}