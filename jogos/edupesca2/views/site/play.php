<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
$this->title = $game->name . ' | ' . Yii::$app->params['appName'];
$game->registerAssets($this);
$this->registerJs('Score.initialize(' . json_encode(Url::to(['/level'])) . ')',  \yii\web\View::POS_LOAD, 'scoreInitialize');
?>

<div class="site-index">
	<div class="row">
		<div class="col-md-12 game-container">
			<?= $this->renderGameView($game->viewsPath, 'index', ['game' => $game, 'level' => $actualLevel, 'user' => Yii::$app->user->identity]) ?>
		</div>
		<div class="col-md-12 levelSelect">
			<?= $this->render('_levelSelect', ['game' => $game, 'levels' => $levels, 'actualLevel' => $actualLevel]) ?>
		</div>
	</div>
</div>