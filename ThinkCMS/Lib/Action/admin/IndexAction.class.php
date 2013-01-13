<?php

/**
 * Encoding     :   UTF-8
 * Created on   :   2011-6-15 9:47:53 by ZhengYi , 13880273017@139.com
 */
class IndexAction extends GlobalAction {

    public function index() {
        if ($this->UserID > 0) {
            $this->redirect('Main/index');
        } else {
            $this->redirect('Public/login');
        }
    }

}