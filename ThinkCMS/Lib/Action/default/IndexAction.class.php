<?php

/*
 * 首页
 */

class IndexAction extends GlobalAction {

    public function _initialize() {
        //搜索条上方
        $Dao = M('Category');
        $condition = array();
        $condition['istop'] = 1;
        $topSearch = $Dao->where($condition)->field('`catid`,`catname`,`url`')->limit(7)->order('`listorder` ASC')->select();

        //页脚导航
        $Dao = M('Category');
        $footerNav = $Dao->field('`catid`,`catname`,`url`')->limit(15)->order('`listorder` ASC')->select();

        $this->assign('topSearch', $topSearch);
        $this->assign('footerNav', $footerNav);
        parent::_initialize();
    }

    /*
     * 首页
     */

    public function index() {
        //导航
        $Dao = M('Category');
        $treeMenu = array();
        $condition = array();
        $condition['isleft'] = 1;
        $condition['child'] = 1;
        $tree = $treeMenu = $Dao->field('`catid`,`catname`,`url`,`css`,`ico`,`style`,`shortname`')->where($condition)->limit(24)->order('listorder ASC')->select();
        foreach ($tree as $k => $v) {
            $condition = array();
            $condition['isleft'] = 1;
            $condition['parentid'] = $v['catid'];
            $childTree = $Dao->field('`catid`,`catname`,`url`,`css`,`ico`,`style`,`shortname`')->where($condition)->limit(20)->order('listorder ASC')->select();
            $treeMenu[$k]['childTree'] = $childTree;
        }

        //焦点图片
        $Dao = D('PositionView');
        $condition = array();
        $condition['posid'] = 1;
        $condition['status'] = 99;
        $condition['thumb'] = array('neq', '');
        $positionPic = $Dao->where($condition)->order('`updatetime` DESC,`inputtime` DESC,News.`listorder` DESC')->limit(5)->select();

        //健康头条
        $Dao = D('PositionView');
        $condition = array();
        $condition['posid'] = 2;
        $condition['status'] = 99;
        $positionTop = getNewsList($Dao->where($condition)->order('`updatetime` DESC,`inputtime` DESC,News.`listorder` DESC')->limit(46)->select(), 20, 14);

        //健康排行
        $Dao = D('HitsView');
        $condition = array();
        $condition['status'] = 99;
        $TopList = getNewsList($Dao->where($condition)->order('`views` DESC')->limit(46)->select(), 20, 14);

        //病症相关
        $Dao = D('PositionView');
        $condition = array();
        $condition['posid'] = 3;
        $condition['status'] = 99;
        $positionJiBing = getNewsList($Dao->where($condition)->order('`updatetime` DESC,`inputtime` DESC,News.`listorder` DESC')->limit(46)->select(), 20, 14);

        //生活保健
        $Dao = D('PositionView');
        $condition = array();
        $condition['posid'] = 4;
        $condition['status'] = 99;
        $positionBaoJian = getNewsList($Dao->where($condition)->order('`updatetime` DESC,`inputtime` DESC,News.`listorder` DESC')->limit(46)->select(), 20, 14);

        //疯狂贴图
        $Dao = D('PositionView');
        $condition = array();
        $condition['posid'] = 12;
        $condition['status'] = 99;
        $condition['thumb'] = array('neq', '');
        $positionTieTu = $Dao->where($condition)->order('`updatetime` DESC,`inputtime` DESC,News.`listorder` DESC')->limit(46)->select();

        //文章
        $catId = array(
            array('id' => 1458, 'catname' => '两性与爱'),
            array('id' => 1768, 'catname' => '健康图谱'),
            array('id' => 22, 'catname' => '妇科疾病'),
            array('id' => 496, 'catname' => '男科疾病'),
            array('id' => 1606, 'catname' => '女性保健'),
            array('id' => 1607, 'catname' => '男性保健'),
            array('id' => 217, 'catname' => '糖尿病'),
            array('id' => 421, 'catname' => '肿瘤'),
            array('id' => 1604, 'catname' => '婴幼儿保健'),
            array('id' => 1610, 'catname' => '老年人保健'),
            array('id' => 935, 'catname' => '中医'),
            array('id' => 841, 'catname' => '偏方'),
            array('id' => 1316, 'catname' => '母婴'),
            array('id' => 1223, 'catname' => '心理'),
            array('id' => 6, 'catname' => '不孕不育'),
            array('id' => 92, 'catname' => '肝病'),
            array('id' => 1915, 'catname' => '八卦'),
            array('id' => 1644, 'catname' => '饮食'),
        );
        foreach ($catId as $x) {
            $Dao = M('News');
            $condition = array();
            $condition['status'] = 99;
            $condition['catid'] = array('in', subCat($x['id'], 1));
            $News[$x['id']] = getThumbNewsList($Dao->where($condition)->order('`thumb` DESC,`updatetime` DESC,`inputtime` DESC,`listorder` DESC')->limit(21)->select(), 10, 5);
        }
        $this->assign('treeMenu', $treeMenu);
        $this->assign('positionPic', $positionPic);
        $this->assign('positionTop', $positionTop);
        $this->assign('topList', $TopList);
        $this->assign('positionJiBing', $positionJiBing);
        $this->assign('positionBaoJian', $positionBaoJian);
        $this->assign('positionTieTu', $positionTieTu);
        $this->assign('news', $News);
        $htmlpath = str_replace('//', '/', $_SERVER['DOCUMENT_ROOT'] . '/');
        //$this->buildHtml('index', $htmlpath);
        $this->display();
    }

    /**
     * 内容显示
     */
    public function content() {
        $u = $_GET['_URL_'];
        if (is_array($u)) {
            $m = $u[count($u) - 1];
            $a = explode('.', $m);
            $a = explode('-', $a[0]);
            if (count($a) == 2) {
                $page = $a[1];
            }
            $page = max($page, 1);

            if (count($u) >= 3 && is_numeric($a[0]) && is_numeric($u[count($u) - 2]) && is_numeric($u[count($u) - 3])) {
                $newsId = $a[0];
                $urlURI = str_replace('.' . C('URL_HTML_SUFFIX'), '', $_SERVER['REQUEST_URI']);
                $m = explode('.', $urlURI);
                $URL = $_SERVER['HTTP_HOST'] . $m[0];
                $m = explode('/', $URL);
                $m = array_slice($m, 0, count($m) - 3);
                $caturl = 'http://';
                foreach ($m as $v) {
                    $caturl.=$v . '/';
                }
                $newsPath = $caturl . $u[count($u) - 3] . '/' . $u[count($u) - 2] . '/' . $newsId;
                $newsRoot = str_replace('http://' . $_SERVER['HTTP_HOST'], '', $caturl);
                $pageType = 'page';
            } else {
                $caturl = 'http://' . $_SERVER['HTTP_HOST'] . $urlURI;
                if (substr($caturl, strlen($caturl) - 1, 1) !== '/')
                    $caturl.='/';
            }
            $Dao = M('Category');
            $rs = $Dao->field('`catid`,`child`')->where('`url`=\'' . $caturl . '\'')->find();
            if ($rs) {
                if (!($pageType)) {
                    ($rs['child'] == 0) ? $pageType = 'list' : $pageType = 'category';
                }
                $catid = $rs['catid'];
            } else {
                $this->redirect('Index/error', array('errorId' => '403'), 5, '页面跳转中...');
            }
        } else {
            $pageType = 'category';
            $catid = intval($_GET['catid']);
        }
        switch ($pageType) {
            case 'page'://内容页
                $Dao = M('Category');
                $r = $Dao->where('`catid`=' . $catid)->field('`modelid`')->find();
                $modelId = $r['modelid'];
                $modelName = getModelName($modelId);
                setNewsHits($modelId, $newsId, $catid); //更新记数
                //读取文章
                $Dao = D('NewsDataView');
                $news = $Dao->where('News.`id`=' . $newsId)->find();
                if (isset($news['paginationtype'])) {
                    $paginationtype = $news['paginationtype'];
                    $maxcharperpage = $news['maxcharperpage'];
                }
                $pages = $titles = '';
                $content = $news['content'];
                if ($news['paginationtype'] == 1) {
                    //自动分页
                    if ($maxcharperpage < 10)
                        $maxcharperpage = 500;
                    import('@.ORG.Contentpage');
                    $contentpage = new Contentpage();
                    $news['content'] = $contentpage->get_data($content, $maxcharperpage);
                }
                if ($news['paginationtype'] != 0) {
                    //手动分页
                    $CONTENT_POS = strpos($content, '[page]');
                    if ($CONTENT_POS !== false) {
                        $contents = array_filter(explode('[page]', $content));
                        $pagenumber = count($contents);
                        if (strpos($content, '[/page]') !== false && ($CONTENT_POS < 7)) {
                            $pagenumber--;
                        }
                        for ($i = 1; $i <= $pagenumber; $i++) {
                            $pageurls[$i] = array($newsPath . '-' . $i . C('URL_HTML_SUFFIX'), $newsRoot);
                        }
                        $END_POS = strpos($content, '[/page]');
                        if ($END_POS !== false) {
                            if ($CONTENT_POS > 7) {
                                $content = '[page]' . $title . '[/page]' . $content;
                            }
                            if (preg_match_all("|\[page\](.*)\[/page\]|U", $content, $m, PREG_PATTERN_ORDER)) {
                                foreach ($m[1] as $k => $v) {
                                    $p = $k + 1;
                                    $titles[$p]['title'] = strip_tags($v);
                                    $titles[$p]['url'] = $pageurls[$p][0];
                                }
                            }
                        }
                        //当不存在 [/page]时，则使用下面分页
                        $pages = content_pages($pagenumber, $page, $pageurls);
                        //判断[page]出现的位置是否在第一位 
                        if ($CONTENT_POS < 7) {
                            $content = $contents[$page];
                        } else {
                            if ($page == 1 && !empty($titles)) {
                                $content = $title . '[/page]' . $contents[$page - 1];
                            } else {
                                $content = $contents[$page - 1];
                            }
                        }
                        if ($titles) {
                            list($title, $content) = explode('[/page]', $content);
                            $content = trim($content);
                            if (strpos($content, '</p>') === 0) {
                                $content = '<p>' . $content;
                            }
                            if (stripos($content, '<p>') === 0) {
                                $content = $content . '</p>';
                            }
                        }
                    }
                    $news['keywords'] = str_replace(',', ' ', $news['keywords']);
                    if (str_replace(' ', '', $news['keywords']) == '欢迎访问PHPCMS网站管理系统')
                        $news['keywords'] = '';
                    $PAGESEO['title'] = $news['title'] . '_' . $this->CATEGORYS[$catid]['catname'] . '_';
                    $PAGESEO['keywords'] = $news['keyword'];
                    $PAGESEO['description'] = $news['description'];
                    $news['keywords'] = explode(' ', $news['keywords']);
                    $news['relation'] = getRelation($news['relation'], $catid, 10, $news['keywords']); //如果没有设置相关文章，默认取10条相关文章
                    $news['content'] = $content;
                    $news['titles'] = $titles;
                    $news['pages'] = $pages;
                }
                $this->assign('news', $news);

                //分类信息
                $Dao = M('Category');
                $condition = array();
                $condition['isleft'] = 1;
                $condition['child'] = 1;
                $rs = $Dao->field('`catid`,`catname`,`url`')->where($condition)->order('`listorder` ASC')->limit(10)->select();
                $newsDao = M('News');
                foreach ($rs as $key => $value) {
                    $where = array();
                    $where['status'] = 99;
                    $where['catid'] = array('in', subCat($value['catid'], 1));
                    $where['thumb'] = array('neq', '');
                    $rs[$key]['catNews'] = $newsDao->field('`title`,`style`,`thumb`,`inputtime`,`description`')->where($where)->order('`updatetime` DESC,`inputtime` DESC,`listorder` DESC,`thumb` DESC')->limit(13)->select();
                }
                $this->assign('catNews', $rs);

                $this->assign('FocusList', getTopNews(11, 'w')); //焦点关注
                $pid = $this->CATEGORYS[$catid]['parentid']; //同栏目的数据
                break;
            case 'list'://列表
                $pid = $this->CATEGORYS[$catid]['parentid']; //同栏目的数据
                break;
            case 'category'://分类首页
                $catid = intval($_GET['catid']);
                $modelId = intval($_GET['modelid']);
                $modelName = getModelName($modelId);

                //首页栏目推荐
                $Dao = D('PositionView');
                $condition = array();
                $condition['catid'] = $catid;
                $condition['posid'] = 10;
                $condition['status'] = 99;
                $positionCategory = getNewsList($Dao->where($condition)->order('`updatetime` DESC,`inputtime` DESC,News.`listorder` DESC')->limit(20)->select(), 10, 5);
                $this->assign('positionCategory', $positionCategory);

                //滚动图片
                $Dao = D('PositionView');
                $condition = array();
                $condition['catid'] = $catid;
                $condition['thumb'] = array('neq', '');
                $condition['posid'] = 13;
                $condition['status'] = 99;
                $positionCategoryPic = $Dao->where($condition)->order('`updatetime` DESC,`inputtime` DESC,News.`listorder` DESC')->limit(5)->select();
                $this->assign('positionCategoryPic', $positionCategoryPic);

                //子栏目
                $Dao = M('Category');
                $subCats = $Dao->field('`catid`,`url`,`catname`')->where('`parentid`=' . $catid)->select();
                $this->assign('subCats', $subCats);
                $catNewsList = array();
                $Dao = M('News');
                foreach ($subCats as $v) {
                    $condition = array();
                    $condition['catid'] = $v['catid'];
                    $condition['thumb'] = array('neq', '');
                    $condition['status'] = 99;
                    $catNewsList[$v['catid']]['topOneNews'] = $Dao->where($condition)->order('`updatetime` DESC,`inputtime` DESC,News.`listorder` DESC')->limit(1)->select();

                    $condition = array();
                    $condition['catid'] = $v['catid'];
                    $condition['id'] = array('neq', $catNewsList[$v['catid']]['topOneNews']['id']);
                    $condition['status'] = 99;
                    $catNewsList[$v['catid']]['newsList'] = $Dao->where($condition)->order('`updatetime` DESC,`inputtime` DESC,News.`listorder` DESC')->limit(22)->select();
                }
                $this->assign('catNewsList', $catNewsList);

                $this->assign('FocusList', getTopNews(11, 'w')); //焦点关注
                $pid = $catid; //同栏目的数据
                break;
            default:
                break;
        }
        //搜索条上方快速导航
        $Dao = M('Category');
        $topNav = $Dao->field('`catname`,`url`')->where('`parentid`=0')->order('`listorder` ASC')->limit(100)->select();
        $this->assign('topNav', $topNav);

        //导航
        $Dao = M('Category');
        $treeMenu = array();
        $condition = array();
        $condition['parentid'] = ($pid > 0) ? $pid : $this->CATEGORYS[$catid]['parentid'];
        $tree = $treeMenu = $Dao->field('`catid`,`catname`,`url`')->where($condition)->limit(10)->order('listorder ASC')->select();
        foreach ($tree as $k => $v) {
            $condition = array();
            $condition['parentid'] = $v['catid'];
            $childTree = $Dao->field('`catid`,`catname`,`url`')->where($condition)->limit(30)->order('listorder ASC')->select();
            $treeMenu[$k]['childTree'] = $childTree;
        }
        $this->assign('treeMenu', $treeMenu);

        $this->assign('catid', $catid);
        $this->assign('PAGESEO', $PAGESEO);
        $this->display($pageType . '-' . $modelName);
    }

}