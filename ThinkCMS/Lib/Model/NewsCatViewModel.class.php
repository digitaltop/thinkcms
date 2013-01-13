<?php

/**
 * 文章栏目视图模型
 */
class NewsCatViewModel extends ViewModel {

    public $viewFields = array(
        'News' => array('id', 'url','inputtime'),
        'Category' => array('url'=>'caturl','_on' => 'Category.`catid`=News.`catid`')
    );

}