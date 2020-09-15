<?php

/**
 * Created by Digital-Solution.Ru web-studio.
 * https://digital-solution.ru
 * support@digital-solution.ru
 */
namespace framework\components\user;

use framework\core\Application;
use framework\components\Model;

class User extends Model  implements UserInterface
{
    /**
     * Внимание! Функция isAuth() должна получить id больше 0
     *
     */
    static public $users = [
        [
          // гость - состояние не авторизированного пользователя
        ],
        [
            'username' => 'developer',
            'password' => 'dev123',
            'access' => [
                'system',
                'developer',
                'manager', // Доступ к панели управления
                'admin', // Доступ к администрированию

                // Модуль "content"
                'c-manager'
            ]
        ],
        [
            'username' => 'admin',
            'password' => 'admin',
            'access' => [
                'manager', // Доступ к панели управления
                'admin' // Доступ к администрированию
            ]
        ]
    ];

    protected $_auth = false;
    public $isNewRecord = true;


    protected $_attributes    = [];
    protected $_oldAttributes = [];


    // Активные/Не активные пользователи
    const STATUS_ACTIVE = 1;
    const STATUS_DEACTIVATED = 0;

    const DEFAULT_PASSWORD = 'init_1234';
    const DEFAULT_IMAGE = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAOEAAADhCAYAAAA+s9J6AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyBpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMC1jMDYwIDYxLjEzNDc3NywgMjAxMC8wMi8xMi0xNzozMjowMCAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNSBXaW5kb3dzIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOkVCNTExRTZFMjNBNjExRTNCMEIyQTE2OTlCREQ1QUJCIiB4bXBNTTpEb2N1bWVudElEPSJ4bXAuZGlkOkVCNTExRTZGMjNBNjExRTNCMEIyQTE2OTlCREQ1QUJCIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6RUI1MTFFNkMyM0E2MTFFM0IwQjJBMTY5OUJERDVBQkIiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6RUI1MTFFNkQyM0E2MTFFM0IwQjJBMTY5OUJERDVBQkIiLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz6goTpMAAAZGklEQVR42uydCZRkVXnH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';

    public function __construct($options = [])
    {
        // Автологин
        if($options['autoLogin'] == true)
        {
            $this->__autoLogin();
        }

        parent::__construct($options);
    }

    protected function __autoLogin()
    {
        $identy = Application::app()->request->getVar('_identy');

        if($identy > 0)
        {
            $this->id = $identy;
            $this->_auth = true;
            $this->isNewRecord = false;
            $this->_attributes = static::$users[$identy];
        }
    }

    public static function auth($username, $password)
    {
       foreach (static::$users as $userID => $data)
       {
           if($data['username'] == $username AND $data['password'] == $password)
           {
               Application::app()->request->setVar('_identy', $userID);
               return true;
           }
       }
        return false;
    }

    public function isAuth() { return $this->_auth; }

    public function getIdentity(){
        return $this->id;
    }

    public function can($acceptName){

        if(isset($this->_attributes['access']))
        {
            if(in_array($acceptName, $this->_attributes['access']))
                return true;
        }
        return false;
    }
    public function getShortName(){ return isset(static::$users[$this->id]['users']) ? static::$users[$this->id]['users'] : 'Unnamed user';}
    public function logout(){
       return Application::app()->request->unSetVar('_identy');
    }
}