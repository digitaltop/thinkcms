<?php

/**
 * Encoding     :   UTF-8
 * Created on   :   2011-6-29 12:19:06 by ZhengYi , 13880273017@139.com
 */
class RoleModel extends Model {

    protected $trueTableName = 'system_role';
    protected $_validate = array(
        array('role_name', 'require', '角色名称必填！'),
    );
    protected $_auto = array(
        array('create_time', 'getNowTime', 1, 'function'),
    );

}