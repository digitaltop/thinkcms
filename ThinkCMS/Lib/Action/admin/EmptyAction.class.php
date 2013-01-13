<?php

//空操作
class EmptyAction extends Action {

    public function index() {
        $this->redirect('Public/login');
    }

}