<?php
namespace app\controllers\user;

use dektrium\user\controllers\AdminController as BaseAdminController;
use app\models\User;
use dektrium\rbac\models\Search;
use yii\filters\AccessControl;
use yii\rbac\Item;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use app\models\Cohort;

class AdminController extends BaseAdminController {
	/**
	 * @inheritdoc
	 */
	public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
				'only' => ['create', 'update'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['create', 'update'],
                        'roles' => ['Administrator', 'Manager'],
                    ],
                ],
            ],
        ];
    }
	
	/**
	 * @inheritdoc
	 */
    public function actionCreate() {
		list($roles, $cohorts, $tutors) = $this->getFormParams();
		$user = Yii::createObject([
            'class'    => User::className(),
            'scenario' => 'create',
        ]);

        $this->performAjaxValidation($user);

        if ($user->load(Yii::$app->request->post()) && $user->create()) {
            Yii::$app->getSession()->setFlash('success', Yii::t('user', 'User has been created'));
            return $this->redirect(['update', 'id' => $user->id]);
        }

        return $this->render('create', [
            'user' => $user,
			'roles' => $roles,
			'cohorts' => $cohorts,
			'tutors' => $tutors,
        ]);
    }
	
	/**
     * Updates an existing User model.
     * @param  integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        Url::remember('', 'actions-redirect');
        $user = $this->findModel($id);
        $user->scenario = 'update';

        $this->performAjaxValidation($user);

        if ($user->load(Yii::$app->request->post()) && $user->save()) {
            Yii::$app->getSession()->setFlash('success', Yii::t('user', 'Account details have been updated'));
            return $this->refresh();
        }
		
		list($roles, $cohorts, $tutors) = $this->getFormParams($id);
		return $this->render('_account', [
            'user'    => $user,
			'roles'	=> $roles,
			'cohorts' => $cohorts,
			'tutors' => $tutors,
        ]);
    }
	
	/**
	 * Seleciona informação para determinada sessão
	 * @param int Id do usuário editado
	 */
	protected function getFormParams($userId = null) {
		// Informações do usuário
		$user = Yii::$app->user->identity;
		$isAdmin = $user->hasRole('Administrator');
		
		// Informações dos papeis
		$roleFilterModel = new Search(Item::TYPE_ROLE);
		$searchParams = [];
		$roleDataProvider = $roleFilterModel->search($searchParams);
		$rolesModels = $roleDataProvider->getModels();
		$roles = ArrayHelper::map($rolesModels, 'name', 'name');
		
		// Não é possível cadastrar outro administrador
		unset($roles['Administrator']);
		// Se o usuário é um gerente, outro gerente
		if (!$isAdmin) unset($roles['Manager']);
		
		// Seleciona as turmas visíveis pelo usuário
		
		// Se o usuário é um administrador, pode ver todos os cursos
		if ($isAdmin) {
			$_cohorts = Cohort::find()->all();
		// Se for gerente, pode ver apenas os cursos atribuídos a si
		} else {
			$_cohorts = $user->cohorts;
		}
		
		$cohorts = ArrayHelper::map($_cohorts, 'id', 'name');
		
		// Seleciona os tutores
		$tutorInstancesQuery = User::find()->withRole('Tutor');
		if ($userId != null) 
			$tutorInstancesQuery->andWhere('id != :id', ['id' => $userId]);
			
		$tutorInstances = $tutorInstancesQuery->all();
		$tutors = ArrayHelper::map($tutorInstances, 'id', 'username');
		
		// Retorna os valores necessários
		return [$roles, $cohorts, $tutors];
	}
}