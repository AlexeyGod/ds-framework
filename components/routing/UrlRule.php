<?php
/**
 * Created by PhpStorm.
 * User: Алексей-дом
 * Date: 26.04.2020
 * Time: 0:51
 */

namespace framework\components\routing;


class UrlRule extends BaseUrlRule
{
    public function parseUrl($route, $pathInfo)
    {
        return $this->_parseUrl($route, $pathInfo);
    }

    public function createUrl($route, $params)
    {
        return $this->_createUrl($route, $params);
    }
}