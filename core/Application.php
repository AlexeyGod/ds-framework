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
    }

    public function run()
    {
        return $this->route->run();
    }

    public static function app()
    {
        if(is_object(self::$app)) return self::$app;
        else
            throw new ErrorExtension('Ошибка сборки App');
    }

    public function getVersion()
    {
        return '0.1.0';
    }

    public function getCopyright()
    {
        return '© 2020 DS-framework';
    }

}