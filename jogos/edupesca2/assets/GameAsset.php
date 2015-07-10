<?php

namespace app\assets;
use yii\web\AssetBundle;

class GameAsset extends AssetBundle {
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [];
    public $js = [
		'js/score.js'
    ];
    public $depends = [
		'yii\web\JqueryAsset'
	];
}
