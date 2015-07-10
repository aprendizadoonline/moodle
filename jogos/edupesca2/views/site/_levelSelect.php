<?php

use yii\helpers\Html;

function generateLevelList($levels, $actual) {
	$data = "<ul class='list-group'>";
	
	if (empty($levels)) {
		$data .= "<li class='list-group-item text-center'>" . Yii::t('app', 'No levels here') . '</li>';
	} else {
		foreach ($levels as $level) {
			$data .= "<li class='list-group-item text-center'>";
			$data .= Html::a($level->title, ['site/play', 'game' => $level->game_id, 'level' => $level->id]);
			$data .= "</li>";
		}
	}
	
	$data .= "</ul>";
	return $data;
}
?>

<div class="row">
	<div class="col-md-4">
		<div class="panel panel-default">
			<div class="panel-heading text-center"><?= Yii::t('app', 'Cohort Levels') ?></div>
			<ul class="list-group">
				<?= generateLevelList($levels['cohort'], $actualLevel) ?>
			</ul>
		</div>
	</div>
	
	<div class="col-md-4">
		<div class="panel panel-default">
			<div class="panel-heading text-center"><?= Yii::t('app', 'My Levels') ?></div>
			<ul class="list-group">
				<?= generateLevelList($levels['my'], $actualLevel) ?>
			</ul>
		</div>
	</div>
	
	<div class="col-md-4">
		<div class="panel panel-default">
			<div class="panel-heading text-center"><?= Yii::t('app', 'Other Levels') ?></div>
			<ul class="list-group">
				<?= generateLevelList($levels['other'], $actualLevel) ?>
			</ul>
		</div>
	</div>
</div>