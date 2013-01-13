<?php

class MemberAction extends GlobalAction {
    /*
     * 初始化
     */

    public function _initialize() {
        parent::_initialize();
    }

    /*
     * 客户组管理
     */

    public function memberGroup() {
        switch ($_GET['s']) {
            case 'json'://显示组列表
                $listorder = ($_POST['listorder']) ? $_POST['listorder'] : 'inputtime';
                $order = ($_POST['order']) ? $_POST['order'] : 'desc';
                $Dao = D('Membergroup');
                //显示列表
                $page = intval($_POST['page']); //当前页
                if ($page == 0)
                    $page = 1;
                $rows = intval($_POST['rows']); //每页显示多少页
                if ($rows == 0)
                    $rows = 10;
                $count = $Dao->count();
                $rs = $Dao->order($listorder . ' ' . $order)->page($page . ',' . $rows)->select();
                $datagrid = array('total' => $count, 'rows' => $rs);
                exit(json_encode($datagrid));
                break;
            case 'billjson'://显示关联表
                $Dao = D('MemberGroupView');
                $count = $Dao->count();
                $rs = $Dao->select();
                $datagrid = array('total' => $count, 'rows' => $rs);
                exit(json_encode($datagrid));
                break;
            case 'billTypeJson'://获取计费列表
                R('AdSetting/Billing', array('s' => 'json'));
                break;
            case 'save'://保存
                $Dao = D('Membergroup');
                if (!$Dao->create()) {
                    exit(json_encode($Dao->getError()));
                } else {
                    (intval($_POST['id']) > 0) ? $S = $Dao->save() : $S = $Dao->add();
                    if ($S) {
                        exit(json_encode(array('success' => true)));
                    } else {
                        exit(json_encode(array('errorMsg' => '保存失败' . $Dao->getError())));
                    }
                }
                break;
            case 'delete'://删除
                $Dao = M('Membergroup');
                if (is_array($_POST['id'])) {
                    $condition['id'] = array('in', $_POST['id']);
                } else {
                    $condition['id'] = intval($_POST['id']);
                }
                $exec = $Dao->where($condition)->delete();
                if ($exec) {
                    exit(json_encode(array('success' => true)));
                } else {
                    exit(json_encode(array('errorMsg' => '删除失败：' . $Dao->getError())));
                }
                break;
            default:
                $this->display();
                break;
        }
    }

}