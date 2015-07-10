<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Level */

$this->title = Yii::t('app', 'Create Level');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Levels'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="level-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
		'game' => $game,
		'userCohorts' => $userCohorts,
		'userTutees'  => $userTutees
    ]) ?>

</div>
