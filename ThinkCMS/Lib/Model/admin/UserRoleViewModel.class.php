<?php

/**
 * Encoding     :   UTF-8
 * Created on   :   2011-7-8 14:31:23 by Expression author is undefined on line 4, column 41 in Templates/Scripting/EmptyPHP.php. , Expression email is undefined on line 4, column 53 in Templates/Scripting/EmptyPHP.php.
 */
class UserRoleViewModel extends ViewModel {

    public $viewFields = array(
        'Role' => array('role_name'),
        'User_role' => array('role_id','user_id', '_on' => 'Role.`role_id`=User_role.`role_id`')
    );

}