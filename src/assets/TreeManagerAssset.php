<?php


namespace odilov\treemodule\assets;
use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class TreeManagerAssset extends AssetBundle
{

    public $sourcePath = __DIR__."/treeview" ;

    public $css = [
        'css/stylesheet.css',
    ];
    public $js = [
        'js/tree-view.js',
        'js/jquery-sortable-lists-mobile.min.js',
        'js/jquery-sortable-lists.min.js',
    ];

    public $depends = [
      'yii\bootstrap\BootstrapPluginAsset',
    ];

}