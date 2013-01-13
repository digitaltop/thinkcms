<?php

class CategoryModelViewModel extends ViewModel {

    public $viewFields = array(
        'Category' => array('catid','catname','viewlocation','catdir','modelid','target'),
        'Model' => array('modelname', '_on' => 'Model.`modelid`=Category.`modelid`')
    );

}