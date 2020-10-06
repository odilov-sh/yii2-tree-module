<?php


namespace odilov\treemodule\assets;
use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class TreeInputAssset extends AssetBundle
{

    public $sourcePath = __DIR__."/treeinput" ;

    public $css = [
        'css/jquerysctipttop.css',
        'css/style.css',
    ];
    public $js = [
        'js/comboTreePlugin.js',
    ];

    public $depends = [
      'yii\bootstrap\BootstrapPluginAsset',
    ];

}