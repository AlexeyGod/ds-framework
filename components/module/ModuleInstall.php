<?php

/**
 * Created by Digital-Solution.Ru web-studio.
 * https://digital-solution.ru
 * support@digital-solution.ru
 */

namespace framework\components\module;

use framework\components\db\SqlBuilder;
use framework\components\rbac\AcceptObject;
use framework\components\rbac\Permission;
use framework\components\rbac\Role;
use framework\core\Application;
use framework\exceptions\ErrorException;


class ModuleInstall
{
   const INSTALL_SUCCESS = 'ok';
   const INSTALL_ERROR = 'error';

   public static function addRole($slug = 'Метка роли', $name = 'Название роли', $description = 'Описание роли')
   {
      $r = AcceptObject::addRole($slug, $name, $description);
      return $r->id;
   }

   public static function addPermission($slug = 'Метка полномочия', $name = 'Название', $description = 'Описание')
   {
      $p = AcceptObject::addPermission($slug, $name, $description);
     return $p->id;
   }

   public static function addPermissionInRole($idRole = 0, $idPermission = 0)
   {
      return AcceptObject::addRelation($idRole, $idPermission, 1);

   }

   public static function setUserRole($roleID)
   {
      if($roleID < 1) return false;

      Application::app()->identy->setRole($roleID);
   }

   public static function createTable()
   {

   }

   public static function sqlImportWitchFile($path)
   {
      if(!is_file($path)) throw new ErrorException("При установке модуля не найден дамп: ".$path);

      $file = file($path);
      $sqlStr = '';
      $sql = [];

      foreach ($file as $str)
      {
         $sqlStr .= $str;
         if(substr(trim($str),-1) == ';')
         {
            Application::app()->db->getPdo()->query($sqlStr);
            $sql[] = trim($sqlStr);
            $sqlStr = '';
         }
      }
      //exit ("<pre>SQL:\n".var_export($sql, true)."\nDB:\n".var_export( [], true)."</pre>");
      //exit("Попытка ипорта из: ".$path."<br>".$txt);

   }
}