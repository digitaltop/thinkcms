<?php

class MenuAction extends GlobalAction {
    /*
     * 初始化
     */

    public function _initialize() {
        $Dao = D('Menu');
        $this->assign('modeltree', $Dao->getClassName(0));
        if (!$this->Page['orderField'])
            $this->Page['orderField'] = 'listorder';
        if (!$this->Page['orderDirection'])
            $this->Page['orderDirection'] = 'asc';
        parent::_initialize();
    }

    /*
     * 添加模块
     */

    public function add() {
        $this->assign('vo', array('parentid' => intval($_REQUEST['parentId'])));
        $this->display('dataTable');
    }

    /*
     * 修改模块
     */

    public function edit() {
        $ID = intval($_REQUEST['id']);
        $Dao = D('Menu');
        $CLIST = $Dao->where('`menu_id`=' . $ID)->find();
        $this->assign('vo', $CLIST);
        $this->display('dataTable');
    }

    /*
     * 删除模块
     */

    public function delete() {
        $Dao = D('Menu');
        $ids = '';
        if ($_REQUEST['id'] == 0 || !$_REQUEST['id']) {
            $this->uiReturn(FALSE, '请选择需要操作的记录！');
        }
        $ex = explode(',', $_REQUEST['id']);
        foreach ($ex as $v) {
            $th = $Dao->getClassName($v);
            $thisId = '';
            foreach ($th as $x) {
                $thisId.=$x['menu_id'] . ',';
            }
            $ids.=$thisId . ',';
        }
        $ids = str_replace(',,', ',', trim($_REQUEST['id']) . ',' . $ids);
        $ex = explode(',', $ids);
        $ids = array_unique($ex);
        $this->deleteItems('Menu', $ids, 'menu_id');
    }

    /*
     * 模块列表
     */

    public function listing() {
        switch (trim($_GET['act'])) {
            case 'search'://普通搜索
                $keywords = trim($_REQUEST['keywords']);
                $pid = intval($_REQUEST['parentid']);
                $this->assign('parentid', $pid);

                //模块
                $condition = array();
                if ($keywords) {
                    $k = array();
                    $k['menu_title'] = array('like', '%' . $keywords . '%');
                    $k['menu_name'] = array('like', '%' . $keywords . '%');
                    $k['description'] = array('like', '%' . $keywords . '%');
                    $k['_logic'] = 'or';
                    $condition['_complex'] = $k;
                    $this->assign('keywords', $keywords);
                }
                if ($pid > 0) {
                    $Dao = D('Menu');
                    $condition['menu_id'] = array('in', $Dao->getClassID($pid, 0, 0));
                }
                $this->search('Menu', $condition);
                break;
            case 'listTree':
                $Dao = D('Menu');
                $condition = array();
                $condition['parentid'] = intval($_REQUEST['id']);
                $clist = $Dao->where($condition)->field('`menu_id` as `id`,`menu_title` as `text`,`icon` as `iconCls`')->order('`listorder` ASC')->select();
                $tvo = array();
                foreach ($clist as $k => $v) {
                    $where = array();
                    $where['parentid'] = $v['id'];
                    if ($Dao->where($where)->count() < 1) {
                        //$tvo[$k]['state'] = 'open';
                    } else {
                        $tvo[$k]['state'] = 'closed';
                        //必须有子栏目才显示
                        $tvo[$k]['id'] = $v['id'];
                        $tvo[$k]['text'] = $v['text'];
                        $tvo[$k]['iconCls'] = $v['iconCls'];
                    }
                }
                if (intval($_REQUEST['id']) == 0) {
                    $vo[0]['id'] = 0;
                    $vo[0]['text'] = '根菜单';
                    $vo[0]['iconCls'] = 'icon-tabicons24';
                    $vo[0]['state'] = 'open';
                    $vo[0]['children'] = $tvo;
                } else {
                    $vo = $tvo;
                }
                exit(json_encode($vo));
                break;
            default:
                break;
        }
        $this->display();
    }

    /*
     * 保存模块
     */

    public function save() {
        $ID = intval($_REQUEST['menu_id']);
        $Dao = D('Menu');
        $where = array();
        $where['parentid'] = intval($_REQUEST['parentid']);
        $where['menu_name'] = trim($_REQUEST['menu_name']);
        if ($ID > 0)
            $where['menu_id'] = array('neq', $ID);
        $rs = $Dao->field('menu_id')->where($where)->count();
        if ($rs > 0) {
            //添加时有重复
            $this->uiReturn(false, '已有一个相同的模块名称，请重新设置。');
        } else {
            if ($data = $Dao->create()) {
                $Re = ($ID > 0) ? $Dao->data($data)->where('menu_id=' . $ID)->save() : $Dao->add();
                if ($Re) {
                    $this->uiReturn(true, '保存成功', 'tab-' . MODULE_NAME . '-listing');
                } else {
                    $this->uiReturn(false, '保存失败：' . $Dao->getError());
                }
            } else {
                //dump($_REQUEST);
                $this->uiReturn(false, $Dao->getError());
            }
        }
    }

    /**
     * 一键授权给自己
     */
    public function oneKeyToMeRole() {
        $Dao = D('Menu');
        $a = $Dao->where('user_id=' . $this->UserID)->delete();
        $T = D('User_menu_permis');
        foreach ($Dao->select() as $v) {
            $T->data(array('menu_id' => $v['menu_id'], 'user_id' => $this->UserID))->add();
        }
        $this->uiReturn(TRUE, '授权完成');
    }

}
