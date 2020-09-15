<?php
/**
 * Created by Digital-Solution.Ru web-studio.
 * https://digital-solution.ru
 * support@digital-solution.ru
 */

namespace framework\exceptions;


use application\controllers\DefaultController;
use framework\components\rbac\Role;
use framework\core\Application;

class AccessDeniedException extends BaseException {

    public function asPage($error)
    {
        $defaultController = new DefaultController();

        return $defaultController->render('error-no-access', [
            'error_msg' => $error
        ]);
    }
}