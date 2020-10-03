<?php


namespace frontend\modules\treemanager\models;
use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class TreeManagerAssset extends AssetBundle
{

    public $basePath = '@webroot/tree';
    public $baseUrl = '@web/tree';
    public $css = [
//        'css/github-dark.css',
        'css/stylesheet.css',
    ];
    public $js = [
        'js/jquery-sortable-lists-mobile.min.js',
        'js/jquery-sortable-lists.min.js',
    ];

    public $depends = [
      'yii\web\JqueryAsset',
    ];

}