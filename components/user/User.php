<?php

/**
 * Created by Digital-Solution.Ru web-studio.
 * https://digital-solution.ru
 * support@digital-solution.ru
 */
namespace framework\components\user;

class User  implements UserInterface
{
    public function __construct($options = []){}
    static function auth($username, $password){ return false;}
    public function isAuth() { return false; }
    public function can($acceptName){ return false;}
    public function getShortName(){ return 'SomeName';}
    public function logout(){ return true;}

}