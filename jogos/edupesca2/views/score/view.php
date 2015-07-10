<?php
/* @var $this yii\web\View */
use yii\grid\GridView;
use yii\helpers\Url;

$this->title = Yii::t('app', 'Scores: {username}', [
    'username' => $user->username,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Scores'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $user->username;

?>

<div class="score-index">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'gameName',
            'levelTitle',
            'date:datetime',
            'points',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
                'urlCreator' => function($action, $model, $key, $index) {
                    return Url::to(['details', 'id' => $model->id]);
                }
            ],
        ],
    ]) ?>
</div>
