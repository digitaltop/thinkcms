<?php

/**
 * 文章内容视图模型
 */
class NewsDataViewModel extends ViewModel {

    public $viewFields = array(
        'News' => array('id', 'inputtime', 'title', 'style', 'thumb', 'keywords', 'description', 'url'),
        'News_data' => array('copyfrom','content','paginationtype','maxcharperpage','relation','_on' => 'News_data.`id`=News.`id`')
    );

}