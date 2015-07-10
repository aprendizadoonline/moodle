<?php

namespace app\controllers;

use Yii;
use app\models\Game;
use app\models\Level;
use app\models\LevelSearch;
use app\models\Cohort;
use app\models\User;
use app\models\GameSession;
use app\models\GameSessionStep;
use app\models\Score;
use app\components\UUID;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

/**
 * LevelController implements the CRUD actions for Level model.
 */
class LevelController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Level models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new LevelSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Level model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Level model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
		$request = Yii::$app->request->post();
		$step = isset($request['step']) ? ((int)$request['step']) : 1;
		$gameId = isset($request['gameId']) ? ((int)$request['gameId']) : null;

		if (($gameId == null) or ($step < 1))
			$step = 1;

		if ($step > 2)
			$step = 2;

		// Seleção do jogo
		if ($step == 1) {
			$games = ArrayHelper::map(Game::find()->all(), 'id', 'name');
			return $this->render('gameSelect', [
				'games' => $games
			]);
		// Criação do nível
		} else {
			// Encontra o jogo
			$game = Game::findOne($gameId);
			if (!$game)
				return $this->redirect(['create']);

			// Encontra e mapeia as turmas e alunos no usuário
			if (Yii::$app->user->identity->hasRole('Administrator')) {
				$userCohorts = Cohort::find()->all();
				$userTutees  = User::find()->withRole('Student')->all();
			} else {
				$userCohorts = Yii::$app->user->identity->cohorts;
				$userTutees  = Yii::$app->user->identity->tutees;
			}

			$userCohorts = ArrayHelper::map($userCohorts, 'id', 'name');
			$userTutees  = ArrayHelper::map($userTutees, 'id', 'username');

			$model = new Level();
			$model->game_id = $game->id;
			$model->levelInputCallback = array($game, 'levelInputCallback');
			$model->created_on = time();
			$model->created_by = Yii::$app->user->identity->id;
			$model->position = Level::find()->max('`position`') + 1;

			if ($model->load($request) && $model->save()) {
				return $this->redirect(['view', 'id' => $model->id, 'game_id' => $model->game_id]);
			} else {
				return $this->render('create', [
					'model' => $model,
					'game' => $game,
					'userCohorts' => $userCohorts,
					'userTutees'  => $userTutees
				]);
			}
		}
    }

    /**
     * Updates an existing Level model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @param integer $game_id
     * @return mixed
     */
    public function actionUpdate($id) {
		// Encontra e mapeia as turmas e alunos no usuário
		if (Yii::$app->user->identity->hasRole('Administrator')) {
			$userCohorts = Cohort::find()->all();
			$userTutees  = User::find()->withRole('Student')->all();
		} else {
			$userCohorts = Yii::$app->user->identity->cohorts;
			$userTutees  = Yii::$app->user->identity->tutees;
		}

		$userCohorts = ArrayHelper::map($userCohorts, 'id', 'name');
		$userTutees  = ArrayHelper::map($userTutees, 'id', 'username');

        $model = $this->findModel($id);
		$game = $model->game;
		$model->levelInputCallback = array($game, 'levelInputCallback');
		$model->loadLevelDataCallback = array($game, 'loadLevelDataCallback');
		$model->loadLevelFields();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
				'game' => $game,
				'userCohorts' => $userCohorts,
				'userTutees' => $userTutees,
            ]);
        }
    }

    /**
     * Deletes an existing Level model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

	/**
	 * Move um nível para cima em relação aos demais do mesmo jogo
	 * @param integer $id
     * @return mixed
	 */
	public function actionMoveup($id) {
		// Encontra o nível
		$level = $this->findModel($id);
		$game_id = $level->game_id;

		// Encontra o nível mais próximo para cima
		$upperLevel = Level::find()
			->where(['game_id' => $game_id])
			->andFilterWhere(['<', 'position', $level->position])
			->limit(1)
			->orderBy('position DESC')
			->one();

		// Se encontrou
		if ($upperLevel) {
			// Troca as posições
			$tmp = $level->position;
			$level->position = $upperLevel->position;
			$upperLevel->position = $tmp;

			// Salva
			$upperLevel->save();
			$level->save();
		}

		// Redireciona para o índice
		return $this->redirect(['index']);
	}

	/**
	 * Move um nível para baixo em relação aos demais do mesmo jogo
	 * @param integer $id
     * @return mixed
	 */
	public function actionMovedown($id) {
		// Encontra o nível
		$level = $this->findModel($id);
		$game_id = $level->game_id;

		// Encontra o nível mais próximo para baixo
		$lowerLevel = Level::find()
			->where(['game_id' => $game_id])
			->andFilterWhere(['>', 'position', $level->position])
			->limit(1)
			->orderBy('position ASC')
			->one();

		// Se encontrou
		if ($lowerLevel) {
			// Troca as posições
			$tmp = $level->position;
			$level->position = $lowerLevel->position;
			$lowerLevel->position = $tmp;

			// Salva
			$lowerLevel->save();
			$level->save();
		}

		// Redireciona para o índice
		return $this->redirect(['index']);
	}

	/**
	 * Registra uma solicitação via ajax de iniciar uma sessão de jogo
	 * @param int $user_id Identificador de usuário
	 * @param int $game_id Identificador de jogo
	 * @param int $level_id Identificador de nível
     * @return mixed
	 */
	public function actionAjaxRegisterGameStart($user_id, $game_id, $level_id) {
		if (!Yii::$app->request->isAjax) {
            return $this->redirect(['site/index']);
        }
		Yii::$app->response->format = Response::FORMAT_JSON;

		// Verifica se o jogo existe (se existir, consequentemente o jogo existe)
		$level = Level::findOne(['id' => $level_id, 'game_id' => $game_id]);
		if (!$level) {
            return ['error' => Yii::t('app', 'Level not found.')];
        }

		// Verifica se o usuário existe
		$user = User::findOne($user_id);
		if (!$user) {
            return ['error' => Yii::t('app', 'User not found.')];
        }

        // Cria a nova sessão
        $sess = new GameSession();
		$sess->id = UUID::v4();
		$sess->user_id = $game_id;
		$sess->game_id = $game_id;
		$sess->level_id = $level_id;
		$continue = $sess->save();

		if ($continue) {
			$request = Yii::$app->request;

			$step = new GameSessionStep();
			$step->session_id = $sess->id;
			$step->time = time();
			$step->action = 'startPlay';
			$step->data = serialize([
				'ip' => $request->userIP,
				'ua' => $request->userAgent
			]);

			$continue = $step->save();
		}

		if (!$continue) {
            return ['error' => Yii::t('app', 'Could not create session.')];
		}

        return ['session' => $sess->id];
	}

	/**
	 * Recebe uma solicitação via ajax para registrar um passo de progresso para o servidor
	 * @param string $session_id Identificador da sessão de jogo
	 * @param string $step Passo de progresso do jogo
	 * @param array $data Informações necessárias
     * @return mixed
	 */
	public function actionAjaxSendProgress($session_id, $step, array $data) {
		if (!Yii::$app->request->isAjax) {
            return $this->redirect(['site/index']);
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

		// Verifica se o passo é válido, inicialmente
		if (($step == GameSessionStep::ACTION_STARTPLAY) or ($step == GameSessionStep::ACTION_ENDPLAY)) {
			return ['error' => Yii::t('app', 'Invalid step.')];
		}

		// Verifica se a sessão existe
		$session = GameSession::findOne($session_id);

		// Se existir e estiver ativa
		if (!($session && $session->active)) {
            return ['error' => Yii::t('app', 'Session not found.')];
        }
		// Verifica se o passo é definitivamente válido
		if (!in_array($step, $session->game->validActions)) {
			return ['error' => Yii::t('app', 'Invalid step.')];
		}

		// Adiciona o novo passo
		$_step = new GameSessionStep();
		$_step->session_id = $session->id;
		$_step->time = time();
		$_step->action = $step;
		$_step->data = serialize($data);

		if (!$_step->save()) {
            return ['error' => Yii::t('app', 'Could not register progress.')];
        }

		return ['success' => true];
	}

	/**
	 * Registra o final de uma sessão de jogo
	 * @param string $session_id Identificador da sessão de jogo
     * @return mixed
	 */
	public function actionAjaxRegisterGameEnd($session_id) {
		if (!Yii::$app->request->isAjax) {
            return $this->redirect(['site/index']);
        }

		Yii::$app->response->format = Response::FORMAT_JSON;

		// Verifica se a sessão existe
		$session = GameSession::findOne($session_id);

		// Se existir e estiver ativa
		if (!$session || !$session->active) {
            return ['error' => Yii::t('app', 'Session not found.')];
        }

		// Encontra a requisição
		$request = Yii::$app->request;

		// Adiciona o novo passo
		$_step = new GameSessionStep();
		$_step->session_id = $session->id;
		$_step->time = time();
		$_step->action = GameSessionStep::ACTION_ENDPLAY;
		$_step->data = serialize([
			'ip' => $request->userIP,
			'ua' => $request->userAgent
		]);

		if (!$_step->save())
            return ['error' => Yii::t('app', 'Could not register game end.')];

		$score = new Score();
        $score->user_id = $session->user_id;
        $score->game_id = $session->game_id;
        $score->level_id = $session->level_id;
        $score->points = $session->game->calculateScore($session);
        $score->date = time();

		if (!$score->save())
			return ['error' => Yii::t('app', 'Could not register level score.')];

        return ['success' => true];
	}

    /**
     * Finds the Level model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Level the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Level::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
