<?php

//文章列表
class ArticleViewModel extends ViewModel {

    public $viewFields = array(
        'Category' => array('catname'),
        'Article'=>array('id','title','username','updatetime','listorder', '_on' => 'Article.`catid`=Category.`catid`')
    );

}