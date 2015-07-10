<?php
/* @var $this yii\web\View */
use yii\helpers\Html;

$this->title = Yii::t('app', 'Highscores: {gamename}', ['gamename' => $game->name]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Highscores'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

function buildHighscoresTable($bestScores) {
    ?>
        <table class="table">
                <?php if (empty($bestScores)): ?>
                    <tr>
                        <td class="text-center" colspan="3"><?= Yii::t('app', 'No highscores! :(') ?></td>
                    </tr>
                <?php else: ?>
                    <tr>
                        <td class="text-center">#</td>
                        <td><strong><?= Yii::t('app', 'User') ?></strong></td>
                        <td class="text-right"><strong><?= Yii::t('app', 'Points') ?></strong></td>
                    </tr>

                    <?php foreach($bestScores as $index => $score): ?>
                        <tr>
                            <td class="text-center">#<?= $index + 1 ?></td>
                            <td><?= $score->user->username ?></td>
                            <td class="text-right"><?= $score->sumPoints ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
        </table>
    <?php
}
?>
<h1><?= $this->title ?></h1>

<div class="highscore-index">
    <div class="row">
        <?php foreach ($game->levels as $level): ?>
            <div class="col-md-6">
                <div class="panel panel-primary">
                    <div class="panel-heading text-center"><strong><?= $level->title ?></strong></div>
                    <?php buildHighscoresTable($level->bestScores); ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
