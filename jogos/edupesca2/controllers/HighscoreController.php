<?php

namespace app\controllers;
use yii\web\Controller;
use app\models\Game;
use app\models\Score;

class HighscoreController extends Controller {
    public function actionIndex() {
        // Encontra todos os jogos
        $games = Game::find()->all();

        // Encontra os jogadores que mais pontuaram
        $bestScores = Score::find()
            ->select(['user_id', 'SUM(points) AS sumPoints'])
            ->distinct('user_id')
			->addSelect(['user_id', 'points'])
			->groupBy('user_id')
			->orderBy('SUM(points) DESC')
            ->limit(10)
            ->all();

        return $this->render('index', [
            'games' => $games,
            'bestScores' => $bestScores
        ]);
    }

    public function actionLevel($gameId) {
        $game = Game::findOne($gameId);

        if ($game == null)
            throw new NotFoundHttpException('The requested page does not exist.');

        return $this->render('level', ['game' => $game]);
    }

    protected function findModel($id)
    {
        if (($model = Cohort::findOne($id)) !== null) {
            return $model;
        } else {

        }
    }
}
