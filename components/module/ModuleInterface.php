<?php
/**
 * Created by Digital-Solution.Ru web-studio.
 * https://digital-solution.ru
 * support@digital-solution.ru
 */

namespace framework\components\module;


interface ModuleInterface
{
    public function __construct();

    public function moduleName();

    public function contextMenu();

    public static function install();
    public static function unInstall();
}