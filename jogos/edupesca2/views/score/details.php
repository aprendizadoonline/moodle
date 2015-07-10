<?php
/* @var $this yii\web\View */
use yii\grid\GridView;
use yii\helpers\Url;

$this->title = Yii::t('app', 'Score Details: {username} in game {gamename} (Level {leveltitle})', [
    'username' => $session->user->username,
    'gamename' => $session->game->name,
    'leveltitle' => $session->level->title
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Scores'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $session->user->username, 'url' => ['view', 'id' => $session->user->id]];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="score-details">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'time:datetime',
            'userFriendlyAction',
        ],
    ]) ?>
</div>
