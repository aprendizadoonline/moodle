<?php
/* @var $this yii\web\View */
use yii\grid\GridView;

$this->title = Yii::t('app', 'Scores');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="score-index">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'username',
            [
                'attribute' => 'cohort.name',
                'label' => Yii::t('app', 'Cohort'),
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
            ],
        ],
    ]) ?>
</div>
