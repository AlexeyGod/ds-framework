<?php
/**
 * Created by Digital-Solution.Ru web-studio.
 * https://digital-solution.ru
 * support@digital-solution.ru
 */

namespace framework\helpers;


class Logger
{
    private $log;

    public function setLog($class, $name, $value)
    {
        $this->log[$class][] = [$name => $value];
    }

    public function getLog()
    {
        return $this->log;
    }

    public function clearLog()
    {
        $this->log = [];
    }

    public function toSting()
    {

        return var_export($this->log, true);
    }
}