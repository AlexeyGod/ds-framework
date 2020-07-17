<?php
/**
 * Created by Digital-Solution.Ru web-studio.
 * https://digital-solution.ru
 * support@digital-solution.ru
 */

namespace framework\components;


use framework\core\Application;
use framework\exceptions\ErrorException;

class Request
{
    public $_session;
    public $_sessionName;
    private $_currentUri;
    private $_backwardUri;

    public $routing = [];

    public $log;

    const CURRENT_URI = '_CURRENT_URI';
    const BEFORE_URI = '_BEFORE_URI';

    public function __construct($options = [])
    {
        // Заполнение из DI-Контейнера
        $this->_sessionName = (isset($options['sessionName']) ? $options['sessionName'] : 'ds-framework');

        // Запуск сессий
        $this->_session = $this->sessionStart();

        // История пользователя
        $this->_currentUri = $this->requestNormalize(getenv('REQUEST_URI'));
        Application::app()->addLog(static::class, 'uri', $this->uri());
        $this->_backwardUri = $_SESSION[self::BEFORE_URI];
        $_SESSION[self::BEFORE_URI] = $this->_currentUri;
    }

    public function requestNormalize($request){
        $request = trim($request , '/');
        if(strpos($request, '?'))
        {
            $request = substr($request, 0, strpos($request, '?'));
        }

        return $request;
    }

    public function registerRouting($array)
    {
        $this->routing = $array;
    }

    public function getRouting()
    {
        return $this->routing;
    }

    public function getCurrent()
    {
        return $this->_currentUri;
    }

    public function uri()
    {
        return $this->getCurrent();
    }

    public function getBackward()
    {
        return $this->_backwardUri;
    }

    public function goBack()
    {
        return $this->getBackward();
    }

    public function sessionStart()
    {
        ini_set('session.use_only_cookies', '1');

        session_name ($this->_sessionName);
        session_start();
    }

    public function setVar($name , $val)
    {
        $_SESSION[$name] = $val;
    }

    public function getVar($name)
    {
        if(isset($_SESSION[$name]))
            return $_SESSION[$name];
        else
            return null;
    }

    public function unSetVar($name)
    {
        if(isset($_SESSION[$name]))
            unset($_SESSION[$name]);
        else
            return false;
    }

    public function setFlash($name, $message)
    {
        $_SESSION['flashes'][] = ['key' => $name, 'message' => $message];
    }

    public function getAllFlashes()
    {
        $return =  $_SESSION['flashes'];
        unset($_SESSION['flashes']);
        return $return;
    }

    public function post($param = false)
    {
        if ($param) {
            if (isset($_POST[$param])) {
                if (!is_array($_POST[$param]))
                    return $_POST[$param];//self::normalize($_POST[$param]);
                else
                    return $_POST[$param];//self::arrayNormalize($_POST[$param]);
            } else
                return false;
        } else {
            if (empty($_POST))
                return false;
            else
                return $_POST; //self::arrayNormalize($_POST);
        }


    }

    public function get($param = false)
    {
        if ($param) {
            if (isset($_GET[$param])) {
                if (!is_array($_GET[$param]))
                    return $_GET[$param];//self::normalize($_GET[$param]);
                else
                    return $_GET[$param];//self::arrayNormalize($_GET[$param]);
            } else
                return false;
        } else {
            if (empty($_GET))
                return false;
            else
                return $_GET;//self::arrayNormalize($_GET);
        }


    }

    public function makeUrl($address = '', $params = [])
    {
        if(empty($address))
            $address = '/'.$this->getCurrent();


        $query = '';

        if(!empty($params))
            foreach ($params as $key => $param) {
                $query .= $key.'='.$param;
            }

        return $address.($query == '' ? '' : '?'.$query);

    }

    public static function normalize($str)
    {
        return htmlspecialchars(stripslashes($str));
    }

    public static function arrayNormalize($array)
    {
        foreach ($array as $key => $item)
        {
            if(is_array($item))
                $newArray[$key] = static::arrayNormalize($item);
            else
                $newArray[$key] = static::normalize($item);
        }

        return $newArray;
    }
}