<?php
/**
 * Created by PhpStorm.
 * User: Алексей-дом
 * Date: 26.04.2020
 * Time: 0:51
 */

namespace framework\components\routing;


abstract class BaseUrlRule
{
    public $route;
    public $pattern;
    public $params = [];

    // Ниже пока не используются
    public $urlSuffix = '';
    public $caseSensitive = false;
    public $defaultParams = [];
    public $routePattern;

    public function __construct($pattern, $route) {
        $this->route = trim($route,'/');
        $this->pattern = $pattern;
        $this->routePattern = $this->_patternNormalize($pattern);
        $this->defaultParams = $this->_setParams($pattern);
    }

    /** Функция преобразует псевдо-паттерн в паттерн RegExp */
    protected function _patternNormalize($pattern)
    {
        if($pattern != '')
        {
            // Преобразование записей вида <id:\d+> в (?P<id>\d+)
            return preg_replace('#<([A-Za-z_-]+):\s*?(.+)>#isU', '(?P<$1>$2)', $pattern);

        }
        else
            return $pattern;
    }

    protected function _setParams($pattern)
    {
        preg_match_all('#<(.+):.+>#U', $this->pattern, $matches);
        return [
            'patterns' => $matches[0],
            'variables' => $matches[1],
        ];
    }


    abstract public function parseUrl($route, $pathInfo);
    abstract public function createUrl($route, $params);

    public function getParams()
    {
        return array_merge($this->params, $_GET);
    }

    public static function clearNumericKeys($matches) {
        $clean = [];

        foreach ($matches as $key=>$value)
        {
            if (!is_int($key)) $clean[$key] = $value;
        }

        return $clean;
    }

    /**
     * Разбирает адрес $route или адрес с параметрами $route, $pathinfo
     * @param $url
     * @return string $routeResult или false
     */
    protected function _parseUrl($route, $pathInfo)
    {
        $routeResult = false;

        // Соответствие шаблона
        if(preg_match('#^'.$this->routePattern.'$#', $route, $matches))
        {
            // Совпадение найдено - добавляем параметры
            if(count($matches) > 0)
                $this->params = self::clearNumericKeys($matches);



            $routeResult = $this->route;
        }

        if(!$routeResult)
        {
            if($route == $this->route AND count(array_diff($this->defaultParams['variables'], array_keys($pathInfo))) == 0)
                $routeResult = $this->route;
        }

        if($routeResult)
        {
            // Проверяем наличие псевдоэлементов <...> в $this->route
            preg_match_all('#<(.+)>#U', $this->route, $elements);
            //exit('<pre>'.htmlspecialchars(var_export(['route' => $this->route, 'matches' => $elements], true)).'</pre>');
            if(count($elements[1]) > 0)
            {
                foreach ($elements[1] as $element)
                {
                    if(isset($this->params[$element]))
                    {
                        if(strpos($routeResult, '<'.$element.'>')!==false)
                        {
                            $routeResult = str_replace('<'.$element.'>', $this->params[$element], $routeResult);
                            unset($this->params[$element]);
                        }
                    }
                }
            }
        }


        return $routeResult;
    }

    protected function _createUrl($route, $params = []) {
        if($route == $this->route AND count(array_diff($this->defaultParams['variables'], array_key($params))) == 0)
        {
            $url = $this->pattern;

            if(count($params) > 0)
                foreach ($this->defaultParams['patterns'] as $key => $pattern)
                {
                    $url = str_replace($pattern, $params[$this->defaultParams['variables'][$key]], $url);
                }

            return $url;
        }
        else
            return false;
    }

}