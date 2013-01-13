<?php

/*
 * 全局方法
 */

class GlobalAction extends Action {
    public $CATEGORYS;

    public function _initialize() {
        set_time_limit(600);
        //检测是否暂停使用该系统
        if (C('STATUS') == 2 && C('STOPD') != '') {
            echo (C('STOPD'));
            exit();
        }
        $this->assign('SEO', C('SEO'));
        $this->CATEGORYS = getCacheCategory();
        $this->assign('CATEGORYS', $this->CATEGORYS);
    }

}