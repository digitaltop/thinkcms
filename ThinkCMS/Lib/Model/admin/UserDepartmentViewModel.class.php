<?php

/**
 * Encoding     :   UTF-8
 * Created on   :   2011-7-8 16:51:01 by Expression author is undefined on line 4, column 41 in Templates/Scripting/EmptyPHP.php. , Expression email is undefined on line 4, column 53 in Templates/Scripting/EmptyPHP.php.
 */
class UserDepartmentViewModel extends ViewModel {

    public $viewFields = array(
        'Department' => array('dept_name'),
        'User_department' => array('dept_id', '_on' => 'Department.`dept_id`=User_department.`dept_id`')
    );

}