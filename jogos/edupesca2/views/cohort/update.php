<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Cohort */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Cohort',
]) . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Cohorts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="cohort-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
