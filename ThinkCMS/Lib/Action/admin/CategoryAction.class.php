<?php

/**
 * 栏目管理
 */
class CategoryAction extends GlobalAction {
    /*
     * 初始化
     */

    public function _initialize() {
        $Dao = D('Category');
        $rs = $Dao->getClassName(0);
        $this->assign('categoryTree', $rs);

        $Dao = M('Model');
        $rs = $Dao->field('`modelid`,`modelname`')->where('`status`=1')->select();
        $this->assign('modelTree', $rs);

        if (!$this->Page['orderField'])
            $this->Page['orderField'] = 'listorder';
        if (!$this->Page['orderDirection'])
            $this->Page['orderDirection'] = 'asc';
        parent::_initialize();
    }

    /*
     * 添加
     */

    public function add() {
        $this->assign('vo', array('parentid' => intval($_REQUEST['parentid'])));
        $this->display('dataTable');
    }

    /*
     * 修改
     */

    public function edit() {
        $ID = intval($_REQUEST['id']);
        $Dao = D('Category');
        $vo = $Dao->where('`catid`=' . $ID)->find();
        $this->assign('vo', $vo);
        $this->display('dataTable');
    }

    /*
     * 删除
     */

    public function delete() {
        $Dao = D('Category');
        $ids = '';
        $ex = explode(',', $_REQUEST['id']);
        foreach ($ex as $v) {
            $chId = $Dao->getClassID($v);
            if ($chId !== $v)
                $ids.= $chId . ',';
            $ids = str_repeat(',,', ',', $ids);
        }
        $ids = str_replace(',,', ',', trim($_REQUEST['id']) . ',' . $ids);
        if (substr($ids, -1, 1) == ',')
            $ids = substr($ids, 0, strlen($ids) - 1);
        $ex = explode(',', $ids);
        $nId = array_unique($ex);
        $r = $Dao->where(array('catid' => array('in', $nId)))->data(array('isdelete' => 1))->save();
        ($r) ? $rm = '成功' : $rm = '失败';
        $this->uiReturn($r, '删除' . $rm);
        //$this->deleteItems('Category', $ids, 'catid');
    }

    /*
     * 保存
     */

    public function save() {
        $ID = intval($_REQUEST['catid']);
        $Dao = D('Category');
        $where = array();
        $where['parentid'] = intval($_REQUEST['parentid']);
        $where['catname'] = trim($_REQUEST['catname']);
        if ($ID > 0) {
            $act = 'edit';
            $where['catid'] = array('neq', $ID);
        } else {
            $act = 'add';
        }
        $rs = $Dao->field('catid')->where($where)->count();
        if ($rs > 0) {
            //添加时有重复
            $this->uiReturn(false, '在同一栏目下已有一个相同的栏目，请重新设置。');
        } else {
            $data = $_POST;
            $data['lanid'] = $this->choseLanguage;
            if ($data = $Dao->create($data)) {
                $Re = ($ID > 0) ? $Dao->data($data)->where('catid=' . $ID)->save() : $Dao->add();
                if ($Re) {
                    $ID = ($act == 'add') ? $Dao->getLastInsID() : $ID;
                    $this->uiReturn(true, '保存成功', 'tab-' . MODULE_NAME . '-listing', array('id' => $ID, 'text' => $_POST['catname'], 'iconCls' => $_POST['icon'], 'act' => $act));
                } else {
                    $this->uiReturn(false, '保存失败：' . $Dao->getError());
                }
            } else {
                //dump($_REQUEST);
                $this->uiReturn(false, $Dao->getError());
            }
        }
    }

    /*
     * 列表
     */

    public function listing() {
        if (trim($_GET['act']) == 'listTree') {
            $Dao = M('Category');
            $condition = array();
            $condition['parentid'] = intval($_REQUEST['id']);
            $condition['isdelete'] = 0;
            $condition['lanid'] = $this->choseLanguage;
            $clist = $Dao->where($condition)->field('`catid` as `id`,`catname` as `text`,`ico` as `iconCls`')->order('`listorder` ASC')->select();
            $tvo = array();
            foreach ($clist as $k => $v) {
                $where = array();
                $where['parentid'] = $v['id'];
                $where['isdelete'] = 0;
                $where['lanid'] = $this->choseLanguage;
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
                $vo[0]['text'] = '根栏目';
                $vo[0]['iconCls'] = 'icon-tabicons24';
                $vo[0]['state'] = 'open';
                $vo[0]['children'] = $tvo;
            } else {
                $vo = $tvo;
            }
            exit(json_encode($vo));
        }
        if (trim($_GET['act']) == 'search') {
            $condition = array();
            $condition['parentid'] = intval($_REQUEST['parentid']);
            $condition['isdelete'] = 0;
            $condition['lanid'] = $this->choseLanguage;
            $catName = trim($_REQUEST['keywords']);
            if (strlen($catName) > 0) {
                $k = array();
                $k['catname'] = array('like', '%' . $catName . '%');
                $k['catdir'] = array('like', '%' . $catName . '%');
                $k['description'] = array('like', '%' . $catName . '%');
                $k['_logic'] = 'or';
                $condition['_complex'] = $k;
                $this->assign('keywords', $catName);
            }
            $this->search('CategoryModelView', $condition);
        }
        $this->display();
    }

    /**
     * Excel导入
     */
    public function inport() {
        Vendor('PHPExcel.PHPExcel');
        $PHPExcel = new PHPExcel();
        $Dao = M('Category');
        $filePath = 'category.xlsx';
        $PHPReader = new PHPExcel_Reader_Excel2007();
        if (!$PHPReader->canRead($filePath)) {
            $PHPReader = new PHPExcel_Reader_Excel5();
            if (!$PHPReader->canRead($filePath)) {
                $this->uiReturn(FALSE, '打开Excel文件失败！');
            }
        }
        $PHPExcel = $PHPReader->load($filePath);
        /*         * 读取excel文件中的第一个工作表 */
        $currentSheet = $PHPExcel->getSheet(0);
        /*         * 取得最大的列号 */
        $allColumn = $currentSheet->getHighestColumn();
        /*         * 取得一共有多少行 */
        $allRow = $currentSheet->getHighestRow();
        /*         * 从第二行开始输出，因为excel表中第一行为列名 */

        for ($currentRow = 2; $currentRow <= $allRow; $currentRow++) {
            $condition = array();
            $condition2 = array();
            $data = array();
            $data2 = array();
            $condition['catname'] = str_replace('_richTextElements', '', $PHPExcel->getActiveSheet()->getCell("A" . $currentRow)->getValue() . _richTextElements);
            $data['catname'] = $condition['catname'];
            $condition['catdir'] = trim($PHPExcel->getActiveSheet()->getCell("B" . $currentRow)->getValue());
            $data['catdir'] = trim($PHPExcel->getActiveSheet()->getCell("B" . $currentRow)->getValue());
            $data['listorder'] = $PHPExcel->getActiveSheet()->getCell("C" . $currentRow)->getValue();
            $data['modelid'] = $PHPExcel->getActiveSheet()->getCell("D" . $currentRow)->getValue();
            $data['viewlocation'] = $PHPExcel->getActiveSheet()->getCell("E" . $currentRow)->getValue();
            $data['target'] = $PHPExcel->getActiveSheet()->getCell("F" . $currentRow)->getValue();
            $data['ico'] = $PHPExcel->getActiveSheet()->getCell("G" . $currentRow)->getValue();
            $data['description'] = $PHPExcel->getActiveSheet()->getCell("H" . $currentRow)->getValue();
            $data['lanid'] = $this->choseLanguage;
            $data['parentid'] = 0;
            $data['url'] = 'http://' . $data['catdir'] . '.kx168.cn/';
            $data['isdelete'] = 0;
            $data['hits'] = 0;
            $data['rocords'] = 0;

            $condition2['catname'] = str_replace('_richTextElements', '', $PHPExcel->getActiveSheet()->getCell("I" . $currentRow)->getValue() . _richTextElements);
            $data2['catname'] = $condition2['catname'];
            $condition2['catdir'] = trim($PHPExcel->getActiveSheet()->getCell("J" . $currentRow)->getValue());
            $data2['catdir'] = trim($PHPExcel->getActiveSheet()->getCell("J" . $currentRow)->getValue());
            $data2['listorder'] = $PHPExcel->getActiveSheet()->getCell("K" . $currentRow)->getValue();
            $data2['modelid'] = $PHPExcel->getActiveSheet()->getCell("L" . $currentRow)->getValue();
            $data2['viewlocation'] = $PHPExcel->getActiveSheet()->getCell("M" . $currentRow)->getValue();
            $data2['target'] = $PHPExcel->getActiveSheet()->getCell("N" . $currentRow)->getValue();
            $data2['ico'] = $PHPExcel->getActiveSheet()->getCell("O" . $currentRow)->getValue();
            $data2['description'] = $PHPExcel->getActiveSheet()->getCell("P" . $currentRow)->getValue();
            $data2['lanid'] = $this->choseLanguage;
            $data2['parentid'] = 0;
            $data2['url'] = 'http://' . $data['catdir'] . '.kx168.cn/';
            $data2['isdelete'] = 0;
            $data2['hits'] = 0;
            $data2['rocords'] = 0;
            //一级栏目


            $one = $Dao->where($condition)->field('`catid`')->find();
            if (!$one) {
                $Dao->data($data)->add();
                $data2['parentid'] = $Dao->getLastInsID();
            } else {
                $data2['parentid'] = $one['catid'];
            }
            $data2['url'] = 'http://' . $data['catdir'] . '.kx168.cn/' . $data2['catdir'] . '/';
            //二级栏目
            $one = $Dao->where($condition2)->field('`catid`')->find();
            if (!$one) {
                $Dao->data($data2)->add();
            }
        }
        $this->uiReturn(TRUE, '导入完毕！');
    }

    /**
     * 对excel里的日期进行格式转化
     * 显示格式为  “月/日/年”
     */
    protected function GetData($val) {
        $jd = GregorianToJD(1, 1, 1970);
        $gregorian = JDToGregorian($jd + intval($val) - 25569);
        return $gregorian;
    }

    /*
     * 导出到Excel
     */

    public function export() {
        print_r($this->CATEGORYS[2]['ischild']);
    }

    /*
     * 回收站
     */

    public function recycle() {
        if (trim($_GET['act']) == 'search') {
            $condition = array();
            $condition['isdelete'] = 1;
            $condition['lanid'] = $this->choseLanguage;
            $condition['Model.status'] = 1;
            $catName = trim($_REQUEST['keywords']);
            if (strlen($catName) > 0) {
                $k = array();
                $k['catname'] = array('like', '%' . $catName . '%');
                $k['catdir'] = array('like', '%' . $catName . '%');
                $k['description'] = array('like', '%' . $catName . '%');
                $k['_logic'] = 'or';
                $condition['_complex'] = $k;
                $this->assign('keywords', $catName);
            }
            $this->search('CategoryModelView', $condition);
        }
        if (trim($_GET['act']) == 'delete') {
            $Dao = D('Category');
            $ids = '';
            $ex = explode(',', $_REQUEST['id']);
            $nId = array_unique($ex);
            $this->deleteItems('Category', $nId, 'catid');
        }
        if (trim($_GET['act']) == 'recycle') {
            $Dao = D('Category');
            $ids = '';
            $ex = explode(',', $_REQUEST['id']);
            $nId = array_unique($ex);
            $r = $Dao->where(array('catid' => array('in', $nId)))->data(array('isdelete' => 0))->save();
            ($r) ? $rm = '成功' : $rm = '失败';
            $this->uiReturn($r, '还原' . $rm);
        }
        $Dao = D('Category');
        $rs = $Dao->getClassName(0, 2);
        $this->assign('categoryTree', $rs);
        $this->display();
    }

}