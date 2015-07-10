<?php
namespace app\games\edupesca;

use yii\web\AssetBundle;

class GameAsset extends AssetBundle {
    public $sourcePath = '@app/games/edupesca/assets';
	
    public $js = [
		'js/crafty-min.js',
		'js/Box2dWeb-2.1.a.4.js',
		'js/box2d.js',
		'js/bootstrap-dialog.js',
		'js/game.js',
	];

	public $css = [
		'css/bootstrap-dialog.css',
		'css/style.css',
	];

	public $depends = [
		'app\assets\GameAsset',
		'yii\bootstrap\BootstrapPluginAsset',
		'yii\web\JqueryAsset'
	];
}
