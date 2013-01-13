<?php

/**
 * 文章排行视图模型
 */
class HitsViewModel extends ViewModel {

    public $viewFields = array(
        'News' => array('id', 'catid', 'title', 'style', 'thumb', 'keywords', 'description', 'url'),
        'Hits' => array('views', 'yesterdayviews', 'dayviews', 'weekviews', 'monthviews', '_on' => 'Hits.`newsid`=News.`id`')
    );

}