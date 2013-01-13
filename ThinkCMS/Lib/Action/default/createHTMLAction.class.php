<?php

/**
 * 生成静态HTML
 */
class createHTMLAction extends GlobalAction {
    /*
     * 生成首页
     */

    public function index() {
        $filePath = str_replace('//', '/', $_SERVER['DOCUMENT_ROOT']);
        $fileAction = A('Index:index');
        $fileAction->buildHtml('index', $filePath, 'Index:index');
    }

    /*
     * 修正URL
     */

    public function changeURL() {
        $page = intval($_GET['page']) + 1;
        $totalPage = max($_GET['total'], 1);
        if ($page <= $totalPage) {
            echo('正在执行第' . $page . '页</br>');
            $Dao = D('NewsCatView');
            $totalPage = ceil(($Dao->count()) / 100);
            $this->assign('page', $page);
            $this->assign('totalPage', $totalPage);
            $this->display();
        }
    }

    /*
     * 执行修正URL
     */

    public function ch() {
        $page = intval($_GET['page']) + 1;
        $totalPage = max($_GET['total'], 1);
        if ($page <= $totalPage) {
            $Dao = D('NewsCatView');
            $totalPage = ceil(($Dao->count()) / 100);
            $rs = $Dao->page($page, 100)->select();
            $N = M('News');
            foreach ($rs as $v) {
                $data = array();
                $data['url'] = $v['caturl'] . date('Y', $v['inputtime']) . '/' . date('md', $v['inputtime']) . '/' . $v['id'] . '.html';
                $data['id'] = $v['id'];
                $a = $N->save($data);
            }
            echo('OK');
        }
    }

    /*
     * 生成二级域名配置信息
     */

    public function createHostConfig() {
        $Dao = M('Category');
        $rs = $Dao->where('parentid=0')->field('url,catid,modelid,child')->select();
        foreach ($rs as $v) {
            $s = explode('.', str_replace('http://', '', $v['url']));
            $cc.='   \'' . $s[0] . '\' => array(\'default/Index\',\'a=category&catid=' . $v['catid'] . '&modelid=' . $v['modelid'] . '&catname=' . $s[0] . '&child=' . $v['child'] . '\'),<br/>';
            $host.='127.0.0.1   ' . str_replace('/', '', str_replace('http://', '', $v['url'])) . '<br/>';
        }
        echo($host);
    }

}