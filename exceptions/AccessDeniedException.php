<?php
/**
 * Created by Digital-Solution.Ru web-studio.
 * https://digital-solution.ru
 * support@digital-solution.ru
 */

namespace framework\exceptions;


use framework\components\rbac\Role;
use framework\core\Application;

class AccessDeniedException extends BaseException {

    public function asPage($error)
    {
        return Application::app()->route->redirect('default/error');
    }
}