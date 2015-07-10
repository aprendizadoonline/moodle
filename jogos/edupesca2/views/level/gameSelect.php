<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Level */

$this->title = Yii::t('app', 'Create Level - Game Selection');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Levels'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="level-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= Html::beginForm('#', 'POST') ?>
		<div class="form-group">
			<p class="text-center"><label for="gameId"><?= Yii::t('app', 'Select the game') ?></label></p>
			<?= Html::hiddenInput('step', 2) ?>
			<?= Html::dropDownList('gameId', null, $games, ['id' => 'gameId', 'class' => 'form-control']) ?>
		</div>
		<button type="submit" class="btn btn-default"><?= Yii::t('app', 'Next') ?></button>
	<?= Html::endForm('#', 'POST') ?>
</div>
