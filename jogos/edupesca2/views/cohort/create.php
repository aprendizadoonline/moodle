<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Cohort */

$this->title = Yii::t('app', 'Create Cohort');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Cohorts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cohort-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
