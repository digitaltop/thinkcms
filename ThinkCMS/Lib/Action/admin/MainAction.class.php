<?php

/*
 * 主页
 */

class MainAction extends GlobalAction {
    /*
     * 基本框架
     */

    public function index() {
        $info = array(
            '操作系统' => PHP_OS,
            '运行环境' => $_SERVER["SERVER_SOFTWARE"],
            'PHP运行方式' => php_sapi_name(),
            '软件版本' => APP_VERSION . ' [ <a href="http://thinkcms.digitaltop.com.cn" target="_blank">查看最新版本</a> ]',
            '上传附件限制' => ini_get('upload_max_filesize'),
            '执行时间限制' => ini_get('max_execution_time') . '秒',
            '服务器时间' => date("Y年n月j日 H:i:s"),
            '北京时间' => gmdate("Y年n月j日 H:i:s", time() + 8 * 3600),
            '服务器域名/IP' => $_SERVER['SERVER_NAME'] . ' [ ' . gethostbyname($_SERVER['SERVER_NAME']) . ' ]',
            '剩余空间' => round((@disk_free_space(".") / (1024 * 1024)), 2) . 'M',
            'register_globals' => get_cfg_var("register_globals") == "1" ? "ON" : "OFF",
            'magic_quotes_gpc' => (1 === get_magic_quotes_gpc()) ? 'YES' : 'NO',
            'magic_quotes_runtime' => (1 === get_magic_quotes_runtime()) ? 'YES' : 'NO',
        );
        $this->assign('info', $info);

        //调取菜单
        //调取菜单大类
        $condition = array();
        $Dao = D('Menu');
        $condition['menu'] = 1;
        $condition['status'] = 1;
        $condition['menu_title'] = array('neq', '');
        $condition['parentid'] = 0;
        $rs = $Dao->where($condition)->order('`listorder` asc,`menu_id` asc')->select();
        $tree = array();
        foreach ($rs as $i => $v) {
            //echo('开始认证菜单ID='.$v['menu_id'].'的权限<br/>');
            if (true == checkAction($this->UserID, $v['menu_id'])) {
                //echo('<font color="red">显示了</font><br/>'.$i);
                $tree[$i]['model_name'] = $v['menu_name'];
                $tree[$i]['model_id'] = $v['menu_id'];
                $tree[$i]['model_title'] = $v['menu_title'];
                $tree[$i]['iconCls'] = $v['icon'];
                //echo('<font color="red">显示了</font><br/>'.$i);
            } else {
                //echo('<font color="red">没显示</font><br/>');
            }
        }
        // exit();
        $this->assign('rootTree', $tree);

        cookie(null, 'p_');
        $Tree = D('TreeMenu');
        $tree = $Tree->toSub($this->UserID, 0, 1, $this->UserID, 0);
        $this->uiTree($tree);
        $this->display();
    }

    /**
     * UI菜单权限
     */
    protected function uiTree($tree) {
        foreach ($tree as $k => $v) {
            if ($v['attributes']['children'] == 1) {
                $this->uiTree($v['children']);
            } else {
                $x = explode(':', $v['id']);
                $m = md5($x[0]);
                $pn = 'p_' . md5('digitaltop_ecmos_thinkoa_' . $m);
                $oldValue = $_COOKIE[$pn];
                ($oldValue == '') ? $prf = '' : $prf = md5('.|.');
                cookie($pn, $oldValue . $prf . md5($x[1]), array('expire' => 86400));
            }
        }
    }

    /**
     * 修改资料
     */
    public function profile() {
        if ($_POST['doAction']) {
            $data = array();
            //对表单提交处理进行处理或者增加非表单数据
            if (md5($_POST['verify']) != $_SESSION['verify']) {
                $this->uiReturn(false, '验证码错误！');
            }
            if (trim($_POST['password']) !== '') {
                if (trim($_POST['oldpassword']) == '') {
                    $this->uiReturn(false, '修改密码前必须输入旧密码！');
                }

                if (trim($_POST['password']) !== trim($_POST['repassword'])) {
                    $this->uiReturn(false, '两次输入的密码不一致！');
                }

                if ('' == (trim($_POST['password']) || trim($_POST['repassword']))) {
                    $this->uiReturn(false, '新密码不能为空！');
                }

                $data['password'] = passWd($_POST['password']);
            }
            $map = array();
            $map['password'] = passWd($_POST['oldpassword']);
            $map['user_id'] = Session::get('USER_ID');
            //检查用户
            $User = D('User');
            if ($User->where($map)->count() < 1) {
                $this->uiReturn(false, '旧密码不符或者用户名错误！');
            } else {
                $ch = $User->data($data)->where($map)->save();
                ($ch) ? $this->uiReturn(TRUE, '修改成功，重新登录时生效！') : $this->uiReturn(FALSE, '修改失败:' . $User->getDbError());
            }
        } else {
            $User = D('User');
            $vo = $User->getById($this->UserID);
            $this->assign('vo', $vo);
            $this->display();
        }
    }

    /*
     * 上传组件
     */

    public function Upload() {
        $tablename = trim($_REQUEST['module']);
        $tableId = intval($_REQUEST['moduleid']);
        $this->display();
    }

    /*
     * 保存附件
     */

    public function saveUploadFile() {
        $filePath = str_replace('//', '/', $_SERVER['DOCUMENT_ROOT'] . '/Attachments/' . $this->UserName . '/' . date('Y') . '/' . date('m') . '/');
        if (!file_exists($filePath)) {
            mk_dirs($filePath);
        }
        import("ORG.Net.UploadFile");
        $upload = new UploadFile();
        $upload->maxSize = 31457280; //30M
        $upload->allowExts = array('jpg', 'gif', 'png', 'jpeg', 'rar', 'doc', 'docx', 'xls', 'xlsx');
        $upload->savePath = $filePath;
        $upload->saveRule = 'uniqid';
        if (!$upload->upload()) {
            //捕获上传异常
            echo('<script language="javascript">alert("' . $upload->getErrorMsg() . '");</script>');
        } else {
            //上传成功
            $uploadList = $upload->getUploadFileInfo();
            $fileName = $uploadList[0]['name'];
            $newUpFileName = $uploadList[0]['savename'];

            //保存附件
            $Dao = D('Attachments');
            $data = array();
            $data['attachments_name'] = $fileName; //原始文件名
            $data['filename'] = str_replace($_SERVER['DOCUMENT_ROOT'], '', $filePath . $newUpFileName); //修改后的文件名
            $data['user_id'] = $this->UserID;
            $data['size'] = $uploadList[0]['size'];
            $data['create_time'] = date('Y-m-d H:i:s');
            $data['listorder'] = 0;
            $Dao->data($data)->add();
            $instId = $Dao->getLastInsID();

            //回调
            echo "<script language=javascript>parent.UploadSaved('" . $instId . "');history.back()</script>";
        }
    }

    /*
     * 获取指定模块的所有子模块及操作
     */

    public function getUserMenus() {
        $pid = intval($_GET['itemId']);
        if ($pid > 0) {
            $Tree = D('TreeMenu');
            $tree = $Tree->toSub($this->UserID, $pid, 1);
            exit(json_encode($tree));
        } else {
            //调取菜单
            $Tree = D('TreeMenu');
            $tree = $Tree->toSub($this->UserID, 0, 1);
            exit(json_encode($tree));
        }
    }

    /**
     * 返回附件列表 —— json 方式
     */
    public function getAttachements_json() {
        $attIds = $_REQUEST['attIds'];
        $attachments = getAttachments($attIds, $this->UserID);
        if (count($attachments) > 0) {
            $result = array(
                'count' => count($attachments),
                'items' => $attachments
            );

            echo(json_encode($result));
        } else {
            echo('{count:0;items:[]}');
        }
    }
    
    /*
     * 测试
     */
    public function test(){
        echo(date('Y-m-d H:i:s',1358040428));
    }

}