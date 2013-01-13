<?php

/**
 * 文章推荐视图模型
 */
class PositionViewModel extends ViewModel {

    public $viewFields = array(
        'News' => array('id', 'catid', 'title', 'style', 'thumb', 'keywords', 'description', 'url'),
        'Position_data' => array('_on' => 'Position_data.`id`=News.`id`')
    );

}