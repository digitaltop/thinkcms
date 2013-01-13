<?php

/*
 * 内容管理模块
 */

class ArticleAction extends GlobalAction {

    protected $ModelId = 'b1a9de09-5199-11e2-83b4-f0def10abaa2';

    /*
     * 初始化
     */

    public function _initialize() {
        if (!$this->Page['orderField'])
            $this->Page['orderField'] = 'listorder';
        if (!$this->Page['orderDirection'])
            $this->Page['orderDirection'] = 'asc';
        $P = M('Position');
        $this->assign('position', $P->order('`listorder` ASC')->select());
        parent::_initialize();
    }

    /*
     * 添加
     */

    public function add() {
        $Dao = M('Category');
        $cat = $Dao->field('`catid`,`catname`')->where('`catid`=' . intval($_REQUEST['catid']))->find();
        $this->assign('cat', $cat);

        $defaultData = array();
        $defaultData['inputtime'] = time();
        $defaultData['updatetime'] = $defaultData['inputtime'];
        $defaultData['username'] = $this->UserNickName;
        $defaultData['copyfrom'] = C('APP_TITLE');
        $defaultData['hits'] = 0;
        $defaultData['paginationtype'] = 0;
        $defaultData['maxcharperpage'] = 10000;
        $defaultData['status'] = 99;
        $this->assign('vo', $defaultData);
        $this->display('dataTable');
    }

    /*
     * 修改
     */

    public function edit() {
        $ID = intval($_REQUEST['id']);
        $vo = M('Article')->where('`id`=' . $ID)->find();
        $con = M('Article_data')->where('`id`=' . $ID)->field('content')->find();
        $vo['content'] = $con['content'];
        $pos = M('Position_data')->field('`posid`')->where('`modelid`=\'b1a9de09-5199-11e2-83b4-f0def10abaa2\' and `tableid`=' . $ID)->select();
        $position = array();
        foreach ($pos as $k => $v) {
            $position[$v['posid']] = true;
        }
        $vo['position'] = $position;
        $this->assign('vo', $vo);


        $Dao = M('Category');
        $cat = $Dao->field('`catid`,`catname`')->where('`catid`=' . $vo['catid'])->find();
        $this->assign('cat', $cat);

        $this->display('dataTable');
    }

    /*
     * 删除
     */

    public function delete() {
        $Dao = D('Article');
        $ids = $_REQUEST['id'];
        $ex = explode(',', $ids);
        $res = array();
        $res['base'] = $this->_del('Article', 'id|' . $ids, 1); //基本信息
        $res['content'] = $this->_del('ArticleData', 'id|' . $ids, 1); //内容
        $res['keywords'] = $this->_del('ArticleKeywords', 'newsid|' . $ids, 1); //关键词
        $res['position'] = $this->_del('PositionData', array('tableid' => array('in', $ids), 'modelid' => $this->ModelId), 1); //推荐位
        //更新栏目统计
        foreach ($ex as $newsId) {
            $res['category'][] = M('Category')->where('`catid`=' . getNewsCategory($newsId))->setDec('rocords');
            $res['relation'][] = $this->articleRelation($newsId); //删除关联文章
        }
        $this->uiReturn(true, '删除任务提交成功！');
    }

    /*
     * 保存
     */

    public function save() {
        $ID = intval($_REQUEST['id']);
        $catid = trim($_REQUEST['catid']);
        $ca = explode(',', $catid);
        if (count($ca) < 1 || trim($_REQUEST['catid']) == '') {
            $this->uiReturn(false, '您一个栏目也没有选择哦！');
        }
        if (trim($_REQUEST['title']) == '') {
            $this->uiReturn(false, '您的文章标题都没有输入呢！');
        }
        if (trim($_REQUEST['content']) == '') {
            $this->uiReturn(false, '您的文章内容都没有输入呢！');
        }
        $data = array();
        //标题样式
        (intval($_REQUEST['bold'] == 1)) ? $bold = 'bold' : $bold = '';
        $data['style'] = trim($_REQUEST['color']) . ';' . $bold;
        //标题图片
        $thumb = '';
        if (intval($_REQUEST['auto_thumb']) == 1) {
            $str = $_REQUEST['content'];
            $auto_thumb_no = intval($_REQUEST['auto_thumb_no']);
            $pattern = "/<[img|IMG].*?[src|SRC]=[\'|\"](.*?(?:[\.gif|\.jpg|\.png|\.bmp]))[\'|\"].*?[\/]?>/";
            preg_match_all($pattern, $str, $match);
            $match = array_unique($match[0]); //去除数组中重复的值
            if ($auto_thumb_no <= count($match)) {
                $pattern = "/<[img|IMG].*?[src|SRC]=[\'|\"](.*?(?:[\.gif|\.jpg|\.png|\.bmp]))[\'|\"].*?[\/]?>/";
                preg_match_all($pattern, $match[$auto_thumb_no - 1], $match);
                $thumb = $match[1];
            } else {
                $this->uiReturn(FALSE, '您一共在文章内容中添加了【' . count($match) . '】张图片，不能提取到您设置的第【' . $auto_thumb_no . '】张图片，请重新设置！');
            }
        }
        (trim($_REQUEST['thumb']) == '') ? $data['thumb'] = $thumb : $data['thumb'] = trim($_REQUEST['thumb']);
        //摘要
        if (intval($_REQUEST['add_introduce']) == 1 && trim($_REQUEST['description']) == '') {
            $data['description'] = str_cut(strip_tags($_REQUEST['content']), intval($_REQUEST['introcude_length']), '');
        }
        //修正时间
        $data['inputtime'] = strtotime($_REQUEST['inputtime']);
        $data['updatetime'] = strtotime($_REQUEST['updatetime']);
        //默认值
        $data['keywords'] = str_replace('，', ',', str_replace('　', ',', str_replace(' ', ',', str_replace('|', ',', trim($_REQUEST['keywords'])))));
        $data['isdelete'] = 0;
        $data['listorder'] = intval($_REQUEST['listorder']);
        $data['islink'] = intval($_REQUEST['islink']);
        $data['lanid'] = $this->choseLanguage;
        $modelid = $this->ModelId; //文章的模块ID
        //URL链接
        if (intval($_REQUEST['islink']) == 1) {
            $data['url'] = trim($_REQUEST['url']);
        } else {
            switch (C('SEO.url_model')) {
                case 1:
                    $data['url'] = C('BASE_URL') . 'Index-content-id-[[NEWSID]].' . C('URL_HTML_SUFFIX');
                    break;
                case 2:
                    $data['url'] = C('BASE_URL') . '?s=Index/content/id/[[NEWSID]].' . C('URL_HTML_SUFFIX');
                    break;
                case 3:
                    $data['url'] = C('BASE_URL') . 'Index/content/id/[[NEWSID]].' . C('URL_HTML_SUFFIX');
                    break;
                case 4:
                    $data['url'] = '[[CATEGORYURL]]' . date('Y') . '/' . date('md') . '/[[NEWSID]].' . C('URL_HTML_SUFFIX');
                    break;
                default:
                    $data['url'] = C('BASE_URL') . 'index.php?m=Index&a=content&id=[[NEWSID]].' . C('URL_HTML_SUFFIX');
                    break;
            }
            $data['url'] = str_replace('..', '.', $data['url']);
        }
        $resu = array(); //每一项运行的结果
        if ($ID > 0) {
            $resu['base'] = $this->_update('Article', $data, 1); //更新文章基本信息
            //添加文章内容
            $resu['data'] = $this->articleContent($ID);
            //添加文章关键词
            $resu['keywords'] = $this->articleKeywords($ID, $data['keywords'], $data['url'], intval($_REQUEST['listorder']));
            //更新推荐位
            $postion = $_REQUEST['position'];
            if (count($postion) > 0) {
                $resu['position'] = $this->aritclePosition($ID, $postion, $catid, $modelid, $data['thumb'], intval($_REQUEST['listorder']));
            }
        } else {
            $s = 0;
            foreach ($ca as $newsNum => $catid) {
                if (0 === $this->CATEGORYS[$catid]['ischild']) {//没有子栏目的目录才能添加文章
                    $s++;
                    $data['url'] = str_replace('[[CATEGORYURL]]', $this->CATEGORYS[$catid]['url'], $data['url']);
                    $data['catid'] = $catid;
                    $newsId = $this->_add('Article', $data, 1);
                    $resu[$newsNum]['baseAdd'] = $newsId;
                    if (intval($_REQUEST['islink']) !== 1) {
                        //更新文章URL链接
                        $upDate = array();
                        $upDate['id'] = $newsId;
                        $upDate['url'] = str_replace('[[NEWSID]]', $newsId, $data['url']);
                        $up = $this->_update('Article', $upDate, 1, 0);
                        $resu[$newsNum]['update'] = $up;
                    } else {
                        $resu[$newsNum]['baseUpdate'] = true;
                    }
                    //添加文章内容
                    $resu[$newsNum]['data'] = $this->articleContent($newsId);
                    //添加文章关键词
                    $resu[$newsId]['keywords'] = $this->articleKeywords($newsId, $data['keywords'], str_replace('[[NEWSID]]', $newsId, $data['url']), intval($_REQUEST['listorder']));
                    //更新推荐位
                    $postion = $_REQUEST['position'];
                    if (count($postion) > 0) {
                        $resu['position'] = $this->aritclePosition($newsId, $postion, $catid, $modelid, $data['thumb'], intval($_REQUEST['listorder']));
                    }
                    //更新栏目的文章数量
                    $Cate = M('Category');
                    $resu[$newsId]['category'] = $Cate->where('`catid`=' . $catid)->setInc('rocords');
                }
            }
            $this->uiReturn(TRUE, '成功将文章添加至【<font color="#f00">' . $s . '</font>】个栏目中！');
        }
        $this->uiReturn(false, '添加失败！');
    }

    /**
     * 更新文章关键词
     * @param type $newsId 文章ID
     * @param type $keywords 关键词
     * @param type $url 文章URL
     */
    protected function articleKeywords($newsId, $keywords, $url, $listorder = 0) {
        $Dao = M('ArticleKeywords');
        $resu = array();
        $resu['delete'] = $Dao->where('`newsid`=' . $newsId)->delete();
        //添加文章关键词
        $ky = explode(',', $keywords);
        foreach ($ky as $k => $v) {
            if ($v !== '') {
                $keyData = array();
                $keyData['keywords'] = $v;
                $keyData['newsid'] = $newsId;
                $keyData['url'] = $url;
                $keyData['listorder'] = $listorder;
                $ak = $this->_add('ArticleKeywords', $keyData, 1);
                $resu[$k] = $ak;
            } else {
                $resu[$k] = true;
            }
        }
        return $resu;
    }

    /**
     * 更新文章内容
     * @param type $newsId 文章Id
     */
    protected function articleContent($newsId) {
        $Dao = M('ArticleData');
        $resu = array();
        $resu['delete'] = $Dao->where('`id`=' . $newsId)->delete();
        //添加文章内容
        $data['id'] = $newsId;
        $resu['add'] = $ad = $this->_add('ArticleData', $data, 1);
        return $resu;
    }

    /**
     * 更新文章推荐位
     * @param type $newsId 文章ID
     * @param type $posId 推荐位
     * @param type $catid 栏目ID
     * @param type $modelid 模型ID
     * @param type $thumb 是否有标题图片
     * @param type $listorder 排序号
     */
    protected function aritclePosition($newsId, $posId, $catid, $modelid, $thumb, $listorder) {
        ($thumb !== '') ? $thumb = 1 : $thumb = 0;
        $Dao = M('PositionData');
        $condition = array();
        $res = array();
        $condition['modelid'] = $modelid;
        $condition['tableid'] = $newsId;
        $res['delete'] = $Dao->where($condition)->delete();

        foreach ($posId as $k => $pos) {
            $data = $condition;
            $data['posid'] = $pos;
            $data['catid'] = $catid;
            $data['thumb'] = $thumb;
            $data['listorder'] = $listorder;
            $res['save'][$k] = $Dao->data($data)->add();
        }
        return $res;
    }

    /**
     * 清除文章关联
     * @param type $sid 源ID
     */
    public function articleRelation($sid) {
        return $this->_del('ArticleRelation', array('sourceid' => $sid), 1);
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
                    $tvo[$k]['state'] = 'open';
                } else {
                    $tvo[$k]['state'] = 'closed';
                }
                $tvo[$k]['id'] = $v['id'];
                $tvo[$k]['text'] = $v['text'];
                $tvo[$k]['iconCls'] = $v['iconCls'];
            }
            if (intval($_REQUEST['id']) == 0) {
                $vo[0]['id'] = 0;
                $vo[0]['text'] = '根栏目';
                $vo[0]['iconCls'] = 'icon-tabicons24';
                $vo[0]['state'] = 'open'; //默认不展开栏目，以加快前台
                $vo[0]['children'] = $tvo;
            } else {
                $vo = $tvo;
            }
            exit(json_encode($vo));
        }
        if (trim($_GET['act']) == 'searchCategory') {
            $this->display('searchCategory');
            exit();
        }
        if (trim($_GET['act']) == 'search') {
            $condition = array();
            $catid = intval($_REQUEST['catid']);
            if ($catid > 0)
                $condition['Article.catid'] = intval($_REQUEST['catid']);
            $condition['Article.isdelete'] = 0;
            $condition['Article.lanid'] = $this->choseLanguage;
            $catName = trim($_REQUEST['keywords']);
            if (strlen($catName) > 0) {
                $k = array();
                $k['Article.keywords'] = array('like', '%' . $catName . '%');
                $k['Article.description'] = array('like', '%' . $catName . '%');
                $k['_logic'] = 'or';
                $condition['_complex'] = $k;
                $this->assign('keywords', $catName);
            }
            if ($catid > 0 || $catName !== '') {
                $this->search('ArticleView', $condition);
            } else {
                exit(json_encode(array('total' => 0, 'rows' => array())));
            }
        }
        $this->display();
    }

    /*
     * 导出到Excel
     */

    public function export() {
        
    }

    /**
     * 关联文章
     */
    public function relation() {
        $sourceId = intval($_REQUEST['sourceId']);
        $targentIds = M('Article_relation')->where('`sourceid`=' . $sourceId)->select();
        $list = array();
        if ($targentIds) {
            $condition = array();
            $condition['id'] = array('in', $targentIds);
            $condition['status'] = array('neq', array(50, 0));
            $this->search('Article', $condition, '`id`,`title`');
        }
        if (is_null($list))
            $list = '';
        exit(json_encode(array('total' => 0, 'rows' => $list)));
    }

}