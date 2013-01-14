<?php

/*
 * 公共方法
 */

class PublicAction extends Action {
    /*
     * 初始化数据
     */

    public function _initialize() {
        
    }

    /*
     * 登录
     */

    public function login() {
        $this->display();
    }

    /**
     * 菜单
     */
    public function menu() {
        //调取菜单
        $Tree = D('TreeMenu');
        $tree = $Tree->toSub(Session::get('USER_ID'), 0, 1);
        $this->assign('tree', toUIaccordion($tree));
        $this->display();
    }

    /*
     * 退出
     */

    public function logout() {
        unset($_SESSION);
        Session::destroy();
        $this->redirect('Public/login');
    }

    /*
     * 验证登录
     */

    public function checkLogin() {
        $username = trim($_POST ['username']);
        $password = trim($_POST ['password']);
        $verify = trim($_POST['verify']);
        if (md5($verify) !== Session::get('verify')) {
            uiReturn(FALSE, '验证码输入错误！');
        }
        if (strlen($username) > 0 && strlen($password) > 0) {
            $condtion = array();
            $condtion ['user_name'] = $username;
            $condtion ['password'] = passWd($password);
            $condtion ['status'] = 1;
            $Dao = D('User');
            $AccountsInfo = $Dao->where($condtion)->find();
            if (FALSE == $AccountsInfo) {
                uiReturn(FALSE, '用户名或密码错误！');
            } else {
                $UserID = intval($AccountsInfo ['user_id']); // 用户ID号
                if (checkUserIP($UserID)) { // 检查是否有IP访问限制
                    // 用户对应部门
                    $Dao = D('User_department');
                    $depts = '';
                    $D = $Dao->where('`user_id`=' . $UserID)->field('`dept_id`')->select();
                    foreach ($D as $K => $V) {
                        $depts .= $V ['dept_id'] . ',';
                    }
                    $depts = substr($depts, 0, strlen($depts) - 1); // 所属于部门ID

                    $USIPANDTIME = updataLogin($UserID);
                    $USER_THEMES = $AccountsInfo ['themes'];
                    $USER_NICKNAME = $AccountsInfo ['nickname'];
                    $USER_LOGINCOUNT = $AccountsInfo ['login_count'];
                    // 验证完成，保存Session
                    Session::set('USER_ID', $UserID); // 用户ID
                    Session::set('USER_NAME', $username); // 用户名(英文)
                    Session::set('USER_DEPT_ID', $depts); // 用户主部门ID
                    Session::set('USER_NICKNAME', $USER_NICKNAME); // 用户真实姓名
                    Session::set('USER_THEMES', $USER_THEMES); // 用户自定义主题
                    Session::set('USER_LOGINCOUNT', $USER_LOGINCOUNT); // 登录次数
                    uiReturn(TRUE, '登录成功！', U('Main/index'));
                } else {
                    uiReturn(FALSE, 'IP限制或禁止登录');
                }
            }
        } else {
            uiReturn(FALSE, '用户名或密码错误！');
        }
    }

    /*
     * 验证码
     */

    public function verify() {
        import('ORG.Util.Image');
        Image::buildImageVerify();
    }

    /*
     * login_logo.jpg
     */

    public function login_logo() {
        $basePath = str_replace('//', '/', $_SERVER['DOCUMENT_ROOT']);
        $canvas = new Imagick(str_replace('//', '/', $basePath . '/' . APP_PATH . '/Tpl/' . GROUP_NAME . '/Public/images/login_logo.gif'));
        $draw = new ImagickDraw();
        $draw->setFontSize(20); //设置字体大小
        //$draw->setTextUnderColor(new ImagickPixel('red')); //设置背景色
        $draw->setFillColor(new ImagickPixel('white')); //设置字体颜色
        $draw->setGravity(Imagick::GRAVITY_SOUTHEAST); //设置水印位置
        $draw->setFillAlpha(0.5);
        $draw->annotation(0, 0,  C('APP_TITLE') . '_后台管理系统');

        $canvas->drawImage($draw);
        //$canvas->writeImage('1.fill.jpg');
        header("Content-Type: image/jpg");
        echo $canvas;

//        (intval($_GET['p']) > 0) ? $r = array(1, 1, 1) : $r = array(255, 255, 255);
//        import('@.ORG.BuildImageText');
//        $IMG = new BuildImageText();
//
//        $IMG->img(str_replace('//', '/', $basePath . '/' . APP_PATH . '/Tpl/' . GROUP_NAME . '/Public/images/login_logo.gif'), C('APP_TITLE') . '_后台管理系统', $basePath . '/Public/ttf/msyhbd.ttf', 20, 0, $r[0], $r[1], $r[2], 60, 30);
    }

    /*
     * 动态生成JS
     */

    public function js() {
        C('TMPL_CONTENT_TYPE', 'application/javascript');
        C('TMPL_TEMPLATE_SUFFIX', '.js');
//        $v = explode('/', str_replace('http://', '', $_SERVER['HTTP_REFERER']));
//        $viewURL = $v[1] . '/' . explode('.', $v[2])[0];
        $t = explode('-', trim($_GET ['src']));
        if (count($t) == 2) {
            $this->display($t [0] . ':' . '/js/' . $t [1]);
        }
    }

    /*
     * 生成css
     */

    public function css() {
        for ($i = 1; $i <= 515; $i++) {
            ($i < 10) ? $li = '0' . $i : $li = $i;
            $css.='.icon-tabicons' . $li . '{
	background:url(\'icons/tabicons/tabicons_' . $li . '.png\') no-repeat;
}<br/>';
        }
        echo($css);
    }

}