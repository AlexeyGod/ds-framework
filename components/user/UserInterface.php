<?php

/**
 * Created by Digital-Solution.Ru web-studio.
 * https://digital-solution.ru
 * support@digital-solution.ru
 */
namespace framework\components\user;

Interface UserInterface
{
    public function __construct($options);

    static function auth($username, $password);
    public function isAuth();
    public function can($permissionName);
    public function getShortName();
    public function getIdentity();
    public function logout();
}