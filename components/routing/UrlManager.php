<?php
/**
 * Created by PhpStorm.
 * User: Алексей-дом
 * Date: 26.04.2020
 * Time: 0:51
 */

namespace framework\components\routing;


class UrlManager
{
    /**
     * Список правил-объектов UrlRule или массивов
     * со специфическими опциями
     * @var BaseUrlRule[]|array[]
     */

    protected $_rules = [];
    protected $_siteRules = [];

    public function __construct($options = [])
    {
        if(count($options) > 0)
        {
            foreach($options as $k => $v)
            {
                if($k == 'rules')
                {
                    $this->addSiteRules($v);

                    continue;
                }
                if(isset($this->$k)) $this->$k = $v;
            }
        }
    }

    public function addSiteRules($rules)
    {
        foreach ($rules as $pattern => $url)
        {
            $this->_siteRules[] = self::createRule ($pattern, $url);
        }
    }

    public function addRules($rules)
    {
        foreach ($rules as $pattern => $url)
        {
            $this->_rules[] = self::createRule ($pattern, $url);
        }
    }

    public function getRules()
    {
        return array_merge($this->_rules, $this->_siteRules);
    }

    public static function createRule ($pattern, $url)
    {
        if(!is_array($url))
            return new UrlRule($pattern, $url);

        return new $url['class']($url['pattern'], $url['url']);
    }

    public function parseUrl($pattern, $url)
    {
        //$rules = array_reverse($this->rules);
        $rules = $this->getRules();
        foreach ($rules as $rule)
        {
            $route = $rule->parseUrl($pattern, $_GET);

            if($route === false) continue;
            else
            {
                return [
                    'route' => $route,
                    'params' => $rule->getParams(),
                    'pattern' => $rule->pattern
                ];
            }
        }

        return false;
    }


}