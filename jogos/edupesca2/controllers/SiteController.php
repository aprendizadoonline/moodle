<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\Game;
use app\models\Level;
use app\models\Filter;

class SiteController extends Controller {
    public function actions() {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionIndex() {
		// Encontra a lista de jogos
		$games = Game::find()->all();

		// Rederiza a página inicial
		return $this->render('index', [
			'games' => $games
		]);
    }

	public function actionPlay($game, $level = null) {
		// Encontra o jogo
		$game = Game::findOne($game);
		if (!$game)
			return $this->redirect(['index']);

		// Informa��es do usu�rio
		$user = Yii::$app->user->identity;
		if (!$user)
			return $this->redirect(['user/login']);

        $userId = $user->id;
		$userCohorts = ArrayHelper::getColumn($user->cohorts, 'id');

		// Encontra os n�veis do jogador e o atual
		// - Todos os que forem destinados � turma | N�veis
		// - Todos os que forem destinados a ele   | Meus n�veis
		// - Todos os sem restrição                | Outros n�veis
		$levelsIterator = Level::find()
			->joinWith('filters')
			->where([
				'{{%filter}}.target_id' => $userId,
				'{{%filter}}.target_type' => Filter::TARGET_STUDENT
			])
			->orWhere([
				'{{%filter}}.target_id' => $userCohorts,
				'{{%filter}}.target_type' => Filter::TARGET_COHORT
			])
			->orWhere([
				'{{%filter}}.target_id' => NULL,
				'{{%filter}}.target_type' => NULL
			])
			->andWhere([
				'{{%level}}.game_id' => $game->id
			])
			->orderBy('{{%level}}.position')
			->each();

		$levels = [
			'cohort' => [],
			'my' => [],
			'other' => []
		];

		$actualLevel = null;

		foreach ($levelsIterator as $_level) {
			if (count($_level->cohortFilters) > 0) {
				$levels['cohort'][] = $level;
			} elseif (count($_level->studentFilters) > 0) {
				$levels['my'][] = $_level;
			} else {
				$levels['other'][] = $_level;
			}

			if (($actualLevel === null) && (!$_level->scoredBy($userId)))
				$actualLevel = $_level;

			if ($level && ($_level->id == $level)) {
				$actualLevel = $_level;
			}
		};

		$noCohortLevels = empty($levels['cohort']);
		$noMyLevels = empty($levels['my']);
		$noOtherLevels = empty($levels['other']);
		$noLevels = $noCohortLevels && $noMyLevels && $noOtherLevels;

		if (!$noLevels && !$actualLevel) {
			if (!$noCohortLevels) {
				$actualLevel = $levels['cohort'][0];
			} elseif (!$noMyLevels) {
				$actualLevel = $levels['my'][0];
			} else {
				$actualLevel = $levels['other'][0];
			}
		}

		// Rederiza a p�gina de jogo
		return $this->render('play', [
			'game' => $game,
			'levels' => $levels,
			'actualLevel' => $actualLevel,
			'noLevels' => $noLevels,

		]);
	}
}
