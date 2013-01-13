<?php

/**
 * Encoding     :   UTF-8
 * Created on   :   2011-7-5 23:51:12 by ZhengYi , 13880273017@139.com
 */
class TreeMenuModel extends Model {

    /**
     * 设置子字段
     * @param string  
     */
    public static $subIdField = 'parentid';

    /**
     * 设置父字段
     * @param string  
     */
    public static $parentIdField = 'menu_id';

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
     * 处理分级数组并返回  
     * @param array $array  string pid【父ID】 int dataType【返回的数据类型：0所有数组数据，1JSON对象】 $perSer 是否判断权限
     * @return array
     */
    public static function toSub($UserID, $pid = 0, $dataType = 0, $perSer = 0, $isMenu = 1) {
        $Dao = D('Menu');
        $condition = array();
        if ($pid > 0) {
            $condition['menu_id'] = array('in', D('Menu')->getClassID($pid, 1));
        }
        $condition['status'] = 1;
        if ($isMenu == 1) {
            $condition['menu'] = 1;
        }

        $array = $Dao->where($condition)->order('`parentid` asc,`listorder` asc,`menu_id` asc')->select();

        if (is_array($array)) {
            $proarr = array();
            foreach ($array as $row) {
                if ($perSer !== 1) {
                    $userSecurity = checkAction($UserID, $row['menu_id']);
                } else {
                    $userSecurity = true;
                }
                if (true === $userSecurity) {
                    if ($dataType == 0) {
                        //返回所有字段
                        $proarr [$row [self::$parentIdField]] = $row;
                        $proarr [$row [self::$subIdField]] [self::$subField] [$row [self::$parentIdField]] = $row;
                    } else {
                        //返回JSON字段
                        $chi = _verChildrenAction($row['menu_id']);
                        $jsonData = array();
                        $jsonData['id'] = self::getParentName($row['parentid']) . ':' . $row['menu_name'] . ':' . $row['menu_id'];
                        $jsonData['state'] = 'open'; //节点状态，'open' or 'closed'，默认为'open'。当设置为'closed'时，拥有子节点的节点将会从远程站点载入它们。
                        $jsonData['text'] = $row['menu_title'];
                        $jsonData['checked'] = FALSE; //表明节点是否被选择。
                        //$jsonData['url'] = U('Main/getUserMenus', array('itemId' => $vo['menu_id']));//动态树时使用
                        $jsonData['attributes']['children'] = ($chi) ? 1 : 0;
                        if (strlen($row['icon']) > 0) {
                            $jsonData['iconCls'] = $row['icon'];
                        }
                        if (strlen($row['outurl']) > 0) {//外部链接
                            $jsonData['attributes']['outurl'] = $row['outurl'];
                        }
                        $proarr [$row [self::$parentIdField]] = $jsonData;
                        $proarr [$row [self::$subIdField]] [self::$subField] [$row [self::$parentIdField]] = $jsonData;
                    }
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
        $return = array();
        $subs = isset($array [$key] [self::$subField]) ? $array [$key] [self::$subField] : array();
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
            $Dao = D('Menu');
            $rs = $Dao->where('`menu_id`=' . $id)->field('menu_name')->find();
            return ($rs) ? $rs['menu_name'] : 'NULL';
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