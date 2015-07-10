<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
$this->title = Yii::$app->params['appName'];
?>
<div class="site-index">
	<div class="row">
		<?php foreach ($games as $game): ?>
			<div class="col-md-4">
				<div class="panel panel-primary">
					<?php $game->registerAssets($this); ?>
					<div class="panel-heading"><?= $game->name ?></div>
					<div class="panel-body">
						<div class="text-center">
							<img src="<?= $game->imageUrl ?>"/>
							<hr/>
							<?= Html::a(Yii::t('app', 'Play'), ['play', 'game' => $game->id], ['class' => 'btn btn-primary']) ?>
						</div>
					</div>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</div>