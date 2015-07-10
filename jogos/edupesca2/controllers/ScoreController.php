<?php

namespace app\controllers;
use Yii;
use yii\web\Controller;
use yii\db\ActiveQuery;
use yii\data\ActiveDataProvider;
use dektrium\user\Finder;
use dektrium\user\models\UserSearch;
use app\models\User;
use app\models\Score;
use app\models\GameSessionStep;
use app\models\ScoreSearch;

class ScoreController extends Controller {
    public function actionIndex() {
        // Identificar os alunos que o usuário atual poderá verificar a pontuação
        $user = Yii::$app->user->identity;

        // Objeto para busca
        $finder = new Finder();
        // Se o usuário é um tutor ou gerente, os usuários devem ser filtrados
        if ($user->hasRole(['Manager', 'Tutor'])) {
            $finder->setUserQuery($user->getTutees());
        // Caso contrário, é possível ver todos os estudantes
        } else {
            $finder->setUserQuery(User::find()->withRole('Student'));
        }

        // Modelo de busca e dataProvider
        $searchModel = new UserSearch($finder);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        // Renderiza a página
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    public function actionView($id) {
        // Encontra o usuário
        $user = $this->find(1, User::classname());//$id);

        // Modelo de busca e dataProvider
        $searchModel = new ScoreSearch($user->id);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        // Renderiza a página
        return $this->render('view', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'user' => $user,
        ]);
    }

    public function actionDetails($id) {
        // Encontra o usuário
        $score = $this->find($id, Score::classname());

        // Modelo de busca e dataProvider
        $query = GameSessionStep::find()
            ->orderBy('time ASC')
            ->where(['session_id' => $score->session_id]);

        $dataProvider = new ActiveDataProvider(['query' => $query]);

        // Renderiza a página
        return $this->render('details', [
            'dataProvider' => $dataProvider,
            'session' => $score->session
        ]);
    }

    private function find($id, $klass) {
        if (($model = $klass::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
