<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\LevelSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Levels');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="level-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Create Level'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
	
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
       // 'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            [
				'attribute' => 'game.name',
				'label' => Yii::t('app', 'Game name'),
			],
			'title',
            //'position',
            [
				'attribute' => 'createdBy.username',
				'label' => Yii::t('app', 'Created By'),
			],
			'created_on:datetime',
            //'created_on',
            // 'data',

            [
				'class' => 'yii\grid\ActionColumn',
				'template' => '{moveup} {movedown} {view} {update} {delete}',
				'buttons' => [
					'moveup'   => function($url, $model, $key) {
						return Html::a(Html::tag('span', '', ['class' => 'glyphicon glyphicon-arrow-up', 'title' => Yii::t('app', 'Move Up'), 'aria-label' => Yii::t('app', 'Move Up')]), $url);
					},
					'movedown' => function($url, $model, $key) {
						return Html::a(Html::tag('span', '', ['class' => 'glyphicon glyphicon-arrow-down', 'title' => Yii::t('app', 'Move Down'), 'aria-label' => Yii::t('app', 'Move Down')]), $url);
					},
				]
			],
        ],
    ]); ?>

</div>
