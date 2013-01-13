<?php

class CategoryModel extends Model {

    /**
     * 设置子字段
     * @param string  
     */
    public static $subIdField = 'parentid';

    /**
     * 设置父字段
     * @param string  
     */
    public static $parentIdField = 'catid';

    /**
     * 设子字段值的键值  
     * @param string  
     */
    public static $subField = 'children';

    /**
     * 是否重新分配KEY  
     * @param boolean 
     */
    public static $reSortKey = true;

    /**
     * 读取深度
     */
    public static $lev = 0;
    public static $setLev = 0;
    protected $padding = array(
        0 => '',
        1 => '&nbsp;&nbsp;├',
        2 => '&nbsp;&nbsp;&nbsp;&nbsp;├',
        3 => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;├',
        4 => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;├',
        5 => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;├');
    protected $Color = array(
        0 => 'FFA00D',
        1 => '55555',
        2 => '999999',
        3 => 'FFA00D',
        4 => '55555',
        5 => '999999');
    private $list = array();
    private $IDS = '';

    //取得大类，递归 $status 1没有被删除的栏目 2已删除的栏目 3都显示
    protected function _getThisClassName($ID, $status = 1) {
        $condition = array();
        $condition['parentid'] = $ID;
        switch ($status) {
            case 1:
                $condition['isdelete'] = 0;
                break;
            case 2:
                $condition['isdelete'] = 1;
            default :
                break;
        }
        $Q = $this->where($condition)->order('`listorder` asc,`catid` asc')->select();

        if (!$Q) {
            return;
        } else {
            foreach ($Q as $cat) {
                $listorder = 0;
                if ($cat['parentid'] > 0) {
                    $UPID = $cat['parentid'];
                    while (!($UPID == 0)) {
                        $listorder++;
                        $where = array();
                        $where['catid'] = $UPID;
                        switch ($status) {
                            case 1:
                                $where['isdelete'] = 0;
                                break;
                            case 2:
                                $where['isdelete'] = 1;
                            default :
                                break;
                        }
                        $cate = $this->where($where)->select();
                        $UPID = $cate[0]['parentid'];
                    }
                }
                $cat['fulltitle'] = $this->padding[$listorder];
                $cat['color'] = $this->Color[$listorder];
                $this->list[] = $cat;
                $this->_getThisClassName($cat['catid'], $status);
            }
        }
    }

    //取得大类下所有子类
    //参数：上级菜单ID，是否显示所有数据，$status 1没有被删除的栏目 2已删除的栏目 3都显示
    //返回：包括子类的所有数据
    public function getClassName($class_ID, $status = 1) {
        $class_ID = intval($class_ID);
        $status = intval($status);
        $this->list = array(); //首先清除原数据
        $this->_getThisClassName($class_ID, $status);
        $list = &$this->list;
        return $list;
    }

    //取得ID子集，递归
    //参数：上级菜单ID，$status 1没有被删除的栏目 2已删除的栏目 3都显示
    protected function _getThisClassID($ID, $status = 1) {
        $condition = array();
        $condition['parentid'] = $ID;
        switch ($status) {
            case 1:
                $condition['isdelete'] = 0;
                break;
            case 2:
                $condition['isdelete'] = 1;
            default :
                break;
        }
        $Q = $this->where($condition)->field('`catid`')->order('`listorder` asc,`catid` asc')->select();
        if (false === $Q)
            return false;
        foreach ($Q as $id) {
            $thisid = $id['catid'];
            $this->IDS.=$thisid . ',';
            $this->_getThisClassID($thisid, $status);
        }
    }

    //取得子类下所有ID
    //返回：子类
    //参数：上级菜单ID，是否显示所有数据，默认显示，是否包含自身，默认不包含
    public function getClassID($action_title, $status = 1, $myself = 0) {
        //exit(dump($action_title));
        $condition = array();
        is_numeric($action_title) ? $condition['catid'] = $action_title : $condition['catname'] = $action_title;
        switch ($status) {
            case 1:
                $condition['isdelete'] = 0;
                break;
            case 2:
                $condition['isdelete'] = 1;
            default :
                break;
        }
        $Q = $this->where($condition)->field('`catid`')->find();
        if ($Q) {
            $id = $Q['catid'];
            if ($myself == 1) {
                $this->IDS.=$id . ',';
            }
            //exit(dump($id));
            $this->_getThisClassID($id, $status);
        }
        if ($this->IDS !== '') {
            $this->IDS = substr($this->IDS, 0, strlen($this->IDS) - 1);
        }//exit(dump($this->IDS));
        return $this->IDS;
        //exit(dump($this->IDS));
    }

    /**
     * 处理分级数组并返回  
     * @param array $array  string pid【父ID】 int dataType【返回的数据类型：0所有数组数据，1JSON对象】 int $setLev 读取深度，默认全部
     * @return array
     */
    public static function toSub($pid = 0, $dataType = 0, $setLev = 0) {
        self::$setLev = $setLev;
        $Dao = D('Category');
        $condition = array();
        if ($pid > 0) {
            $condition['catid'] = array('in', $Dao->getClassID($pid));
        }

        $array = $Dao->where($condition)->order('`parentid` asc,`listorder` asc,`catid` asc')->select();

        if (is_array($array)) {
            $proarr = array();
            foreach ($array as $row) {
                if ($dataType == 0) {
                    //返回所有字段
                    $proarr [$row [self::$parentIdField]] = $row;
                    $proarr [$row [self::$subIdField]] [self::$subField] [$row [self::$parentIdField]] = $row;
                } else {
                    //返回JSON字段
                    $chi = ($row['child'] == 0) ? false : true;
                    $jsonData = array();
                    $jsonData['id'] = $row['parentid'] . ':' . $row['catdir'] . ':' . $row['catid'];
                    $jsonData['pId'] = $row['parentid'];
                    $jsonData['text'] = $row['catname'];
                    $jsonData['leaf'] = ($chi) ? FALSE : TRUE;
                    if ($chi)
                        $jsonData['isexpand'] = FALSE;
                    $proarr [$row [self::$parentIdField]] = $jsonData;
                    $proarr [$row [self::$subIdField]] [self::$subField] [$row [self::$parentIdField]] = $jsonData;
                }
            }

            $proarr = self::search_sub($proarr, $pid);

            if (self::$reSortKey) {
                $proarr = self::re_sort_key($proarr);
            }
            if ($dataType == 0) {
                return $proarr;
            } else if ($dataType == 1) {
                return $proarr;
                //return json_encode($proarr);
            } else {
                return self::$jsTree;
            }
        } else {
            if ($dataType == 0) {
                return $array;
            } else {
                return $array;
                //return json_encode($array);
            }
        }
    }

    /**
     * 关键算法函数  
     * @param array $array  
     * @param string $key 
     * @return array
     */
    private static function search_sub(array $array, $key) {
        self::$lev++;
        $return = array();
        $subs = isset($array [$key] [self::$subField]) ? $array [$key] [self::$subField] : array();
        if (self::$setLev > 0 && self::$lev > self::$setLev)
            return $return;
        foreach ($subs as $k => $v) {
            $temp = $v;
            $temp[self::$subField] = self::search_sub($array, $k);
            $return [$k] = $temp;
        }
        return $return;
    }

    /**
     * 根据菜单ID，返回菜单的后台操作名称
     * @param int $pid
     * #return string
     */
    private function getParentName($id) {
        if (is_numeric($id)) {
            $Dao = D('Category');
            $rs = $Dao->where('`catid`=' . $id)->field('catname')->find();
            return ($rs) ? base64_encode($rs['catname']) : 'NULL';
        } else {
            return 'NULL';
        }
    }

    /**
     * 重新排序KEY
     * @param array $array
     * @return array
     */
    private static function re_sort_key($array) {
        $array = array_values($array);
        foreach ($array as $k => $v) {
            if (is_array($v[self::$subField]) && !empty($v[self::$subField])) {
                $array[$k][self::$subField] = self::re_sort_key($v[self::$subField]);
            }
        }
        return $array;
    }

}