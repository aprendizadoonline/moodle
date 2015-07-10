<?php
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use app\assets\GameAsset;

/* @var $this \yii\web\View */
/* @var $content string */

GameAsset::register($this);
AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>

<?php $this->beginBody() ?>
    <div class="wrap">
        <?php
            NavBar::begin([
                'brandLabel' => Yii::$app->params['appName'],
                'brandUrl' => Yii::$app->homeUrl,
                'options' => [
                    'class' => 'navbar-inverse navbar-fixed-top',
                ],
            ]);

            echo Nav::widget([
                'options' => ['class' => 'navbar-nav navbar-right'],
                'items' => [
                    ['label' => Yii::t('app', 'Home'), 'url' => ['/site/index']],
                    ['label' => Yii::t('app', 'Users'), 'url' => ['/user/admin'], 'visible' => (Yii::$app->user->isGuest ? false : Yii::$app->user->identity->hasRole(['Administrator', 'Manager']))],
					['label' => Yii::t('app', 'Cohorts'), 'url' => ['/cohort/index'], 'visible' => (Yii::$app->user->isGuest ? false : Yii::$app->user->identity->hasRole(['Administrator', 'Manager']))],
					['label' => Yii::t('app', 'Levels'), 'url' => ['/level/index'], 'visible' => (Yii::$app->user->isGuest ? false : Yii::$app->user->identity->hasRole(['Administrator', 'Manager', 'Tutor']))],
                    ['label' => Yii::t('app', 'Scores'), 'url' => ['/score/index'], 'visible' => (Yii::$app->user->isGuest ? false : Yii::$app->user->identity->hasRole(['Administrator', 'Manager', 'Tutor']))],
                    ['label' => Yii::t('app', 'Highscores'), 'url' => ['/highscore/index']],
                    Yii::$app->user->isGuest ?
						['label' => Yii::t('app', 'Sign in'), 'url' => ['/user/security/login']] :
						['label' => Yii::t('app', 'Sign out ({username})', ['username' => Yii::$app->user->identity->username]),
							'url' => ['/user/security/logout'],
							'linkOptions' => ['data-method' => 'post']],
						['label' => Yii::t('app', 'Register'), 'url' => ['/user/registration/register'], 'visible' => Yii::$app->user->isGuest]
                ],
            ]);
            NavBar::end();
        ?>

        <div class="container">
            <?= Breadcrumbs::widget([
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            ]) ?>
            <?= $content ?>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <p class="pull-left">&copy; Aprendizado Online <?= date('Y') ?></p>
        </div>
    </footer>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
