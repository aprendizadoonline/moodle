<?php
/* @var $this yii\web\View */
use yii\helpers\Html;

$this->title = Yii::t('app', 'Highscores');
$this->params['breadcrumbs'][] = $this->title;

function buildHighscoresTable($bestScores, $appendRows = '') {
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
                <?= $appendRows ?>
        </table>
    <?php
}
?>
<h1 class="text-center"><?= $this->title ?></h1>

<div class="highscore-index">
    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <div class="panel panel-primary">
                <div class="panel-heading text-center">
                    <strong><?= Yii::t('app', 'Users who most scored') ?></strong>
                </div>
                <?php buildHighscoresTable($bestScores) ?>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 text-center">
            <h2><?= Yii::t('app', 'Highscores by game') ?></h2>
        </div>

        <?php foreach ($games as $game): ?>
            <div class="col-md-6">
                <div class="panel panel-primary">
                    <div class="panel-heading text-center"><strong><?= $game->name ?></strong></div>
                    <?php buildHighscoresTable($game->bestScores,
                            Html::tag('tr',
                                Html::tag('td',
                                    Html::a(
                                        Yii::t('app', 'See Highscores by Level'),
                                        ['level', 'gameId' => $game->id],
                                        ['class' => 'btn btn-primary']
                                    ),
                                    ['colspan' => 3, 'class' => 'text-center']
                                )
                            )
                        );
                    ?>

                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
