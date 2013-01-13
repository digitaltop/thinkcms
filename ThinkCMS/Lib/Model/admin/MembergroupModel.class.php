<?php

class MembergroupModel extends Model {

    protected $_validate = array(
        array('groupname', 'require', '必须输入组名！'),
    );
    protected $_auto = array(
        array('inputtime', 'getNowTime', 1, 'function'),
    );

}