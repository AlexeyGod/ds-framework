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
    public static $app; // Экземпляр приложения

    public function __construct($config = [])
    {
        self::$app = $this;  // Экземпляр приложения для реазизации app()
        parent::__construct($config);
    }

    public function run()
    {
        return $this->route->run();
    }

    public static function app()
    {
        if(is_object(self::$app)) return self::$app;
        else
            throw new \Exception("Приложение не запущено");
    }

    public function version()
    {
        return '0.1.0';
    }

}