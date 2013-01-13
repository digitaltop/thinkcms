<?php

//插件模块
class PluginAction extends GlobalAction {
    /*
     * 初始化
     */

    public function _initialize() {
//        if (!$this->Page['orderField'])
//            $this->Page['orderField'] = 'listorder';
//        if (!$this->Page['orderDirection'])
//            $this->Page['orderDirection'] = 'asc';
        parent::_initialize();
    }

    /*
     * 添加
     */

    public function add() {
        $this->display('dataTable');
    }

    /*
     * 修改
     */

    public function edit() {
        $ID = trim($_REQUEST['id']);
        $Dao = M('Model');
        $vo = $Dao->where('`modelid`=\'' . $ID . '\'')->find();
        $this->assign('vo', $vo);
        $this->display('dataTable');
    }

    /*
     * 删除
     */

    public function delete() {
        $Dao = M('Category');
        $M = M('Model');
        $Md = M('Model_field');
        $ids = '';
        $unDelete = '';
        $ex = explode(',', $_REQUEST['id']);
        $fex = $ex;
        foreach ($fex as $k => $v) {
            if ($Dao->where('`modelid`=\'' . $v . '\'')->count() > 0) {
                $ex = array_splice($ex, $k + 1, 1);
                $r = $M->where('`modelid`=\'' . $v . '\'')->field('`modelname`')->find();
                $unDelete.=$r['modelname'] . '\n';
            }
        }
        //删除模块字段
        foreach ($ex as $k => $v) {
            $Md->where('`modelid`=\'' . $v . '\'')->delete();
        }
        //删除模块自身
        $M->where('`modelid`=\'' . $v . '\'')->delete();
        if (strlen($unDelete) > 0) {
            $rState = false;
            $message = '以下模块未被删除，其它模块均被删除：' . $unDelete;
        } else {
            $rState = true;
            $message = '删除成功！';
        }
        $this->uiReturn($rState, $message);
    }

    /*
     * 保存
     */

    public function save() {
        
    }

    /*
     * 列表
     */

    public function listing() {
        if (trim($_GET['act']) == 'listModelField') {
            
        }
        if (trim($_GET['act']) == 'search') {
            $Dao = M('Model');
            $condition = array();
            $keywords = trim($_REQUEST['keywords']);
            if (strlen($keywords) > 0) {
                $condition['modelname'] = array('like', '%' . $keywords . '%');
                $condition['tablename'] = array('like', '%' . $keywords . '%');
                $condition['description'] = array('like', '%' . $keywords . '%');
                $condition['_logic'] = 'OR';
                $this->assign('keywords', $keywords);
            }
            $this->search('Model', $condition);
        }
        
        $this->display();
    }

    /*
     * 导出到Excel
     */

    public function export() {
        
    }

}