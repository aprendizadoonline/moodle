<?php

namespace app\models;
use Yii;
use app\query\UserQuery;
use yii\helpers\ArrayHelper;
use dektrium\user\models\User as BaseUser;

class User extends BaseUser {
	public $cohortsManager;
	public $cohortsTutor;
	public $cohortsStudent;
	public $newTutors;
	private $oldTutors;
	public $newRole;

	/**
	 * @inheritdoc
	 */
	public static function find() {
        return new UserQuery(get_called_class());
    }

    public function scenarios() {
        $scenarios = parent::scenarios();

		$scenarios['create'][]   = 'newRole';
		$scenarios['create'][]   = 'cohortsManager';
		$scenarios['create'][]   = 'cohortsTutor';
		$scenarios['create'][]   = 'cohortsStudent';
		$scenarios['create'][]   = 'newTutors';

		$scenarios['update'][]   = 'newRole';
		$scenarios['update'][]   = 'newTutors';
		$scenarios['update'][]   = 'cohortsManager';
		$scenarios['update'][]   = 'cohortsTutor';
		$scenarios['update'][]   = 'cohortsStudent';

        return $scenarios;
    }

	public function attributeLabels() {
		$attributeLabels = parent::attributeLabels();
		$attributeLabels['cohortsManager'] = Yii::t('app', 'Cohorts');
		$attributeLabels['cohortsTutor'] = Yii::t('app', 'Cohorts');
		$attributeLabels['cohortsStudent'] = Yii::t('app', 'Cohorts');
		$attributeLabels['newRole'] = Yii::t('app', 'Role');
		$attributeLabels['newTutors'] = Yii::t('app', 'Tutors');
		return $attributeLabels;
	}

    public function rules() {
        $rules = parent::rules();
		$rules['roleRequired'] = ['newRole', 'required'];
		$rules['cohortsStudentValidateCohort'] = [['cohortsStudent'], 'validateCohort', 'skipOnEmpty' => false];
        return $rules;
    }

	/**
	 * Faz a validação da turma
	 *
	 * @param string $attribute
	 * @param array $params
	 */
	public function validateCohort($attribute, $params) {
		// Só é necessária para estudantes
		if ($this->newRole == 'Student') {
			// Tenta achar a turma
			$cohort = Cohort::findOne($this->$attribute);

			// Se não encontrou
			if ($cohort === null) {
				// Retorna o erro
				$this->addError($attribute, Yii::t('app', 'You should select one cohort.'));
			}
		}
	}

	public function hasRole($roleName) {
		$roles = Yii::$app->authManager->getRolesByUser($this->id);

		if (is_array($roleName)) {
			foreach ($roles as $role)
				if (in_array($role->name, $roleName)) return true;
		} else {
			foreach ($roles as $role)
				if ($role->name == $roleName) return true;
		}

		return false;
	}

	/**
     * @return \yii\db\ActiveQuery
     */
    public function getUserCohorts() {
        return $this->hasMany(UserCohort::className(), ['user_id' => 'id']);
    }

	/**
     * @return \yii\db\ActiveQuery
     */
    public function getCohorts() {
		return $this->hasMany(Cohort::className(), ['id' => 'cohort_id'])->viaTable('{{%user_cohort}}', ['user_id' => 'id']);
    }

	/**
     * @return \yii\db\ActiveQuery
     */
    public function getCohort() {
		if ($this->role == 'Student') {
			return $this->hasOne(Cohort::className(), ['id' => 'cohort_id'])->viaTable('{{%user_cohort}}', ['user_id' => 'id']);
		} else {
			return $this;
		}
    }

	/**
     * @return \yii\db\ActiveQuery
     */
    public function getUserTutors() {
         return $this->hasMany(UserTutor::className(), ['user_id' => 'id']);
    }

	/**
     * @return \yii\db\ActiveQuery
     */
    public function getUserTutees() {
         return $this->hasMany(UserTutor::className(), ['tutor_id' => 'id']);
    }

	/**
     * @return \yii\db\ActiveQuery
     */
    public function getTutors() {
		return $this->hasMany(User::className(), ['id' => 'tutor_id'])->viaTable('{{%user_tutor}}', ['user_id' => 'id']);
    }

	/**
     * @return \yii\db\ActiveQuery
     */
    public function getTutees() {
		return $this->hasMany(User::className(), ['id' => 'user_id'])->viaTable('{{%user_tutor}}', ['tutor_id' => 'id']);
    }

	/**
	 * @return null|string
	 */
	public function getRole() {
		$roles = Yii::$app->authManager->getRolesByUser($this->id);
		if (empty($roles)) return null;
		return array_values($roles)[0]->name;
	}

	/**
	 * @inheritdoc
	 */
	public function beforeSave($insert) {
		// Se a rotina da classe pai falhou, essa tamb�m falha
		if (!parent::beforeSave($insert))
			return false;

		// Se for um usu�rio existente
		if (!$insert) {
			//=== PERMISS�ES

			// O papel mudou
			if ($this->newRole != $this->role) {
				// Encontra o gerenciador de autentica��o
				$authManager = Yii::$app->authManager;

				// Encontra o papel novo
				$role = $authManager->getRole($this->newRole);

				// Se n�o encontrou o papel, a rotina falha
				if (!$role) return false;

				// Se o papel anterior era de estudante, remove todos os tutores
				if ($this->role == 'Student') {
					foreach ($this->userTutors as $userTutor)
						$userTutor->delete();
				}

				// Remove todos os papeis
				$authManager->revokeAll($this->id);

				// Atribui o novo papel
				$authManager->assign($role, $this->id);
			}

			//=== TUTORES
			// Se o usu�rio � um estudante e os tutores mudaram
			if (($this->role == 'Student') and ($this->newTutors != $this->oldTutors)) {
				// Remove todos os tutores anteriores
				foreach ($this->userTutors as $userTutor)
					$userTutor->delete();

				// Adiciona todos os novos tutores
				if (!empty($this->newTutors))
					foreach ($this->newTutors as $newTutor) {
						$rel = new UserTutor();
						$rel->user_id = $this->id;
						$rel->tutor_id = (int)$newTutor;
						$rel->save();
					}
			}

			//=== TURMAS
			// Remove as turmas anteriores
			foreach ($this->userCohorts as $userCohort)
				$userCohort->delete();

			switch ($this->newRole) {
			//case 'Administrator':
			case 'Manager':
			case 'Tutor':
			case 'Student':
				$cohortKey = 'cohorts' . $this->newRole;
				break;
			default:
				return false;
			};

			// Adiciona todas as novas turmas
			if (is_array($this->$cohortKey)) {
				foreach ($this->$cohortKey as $cohort) {
					$rel = new UserCohort();
					$rel->user_id = $this->id;
					$rel->cohort_id = (int)$cohort;
					$rel->save();
				}
			} else {
				$rel = new UserCohort();
				$rel->user_id = $this->id;
				$rel->cohort_id = (int)$this->$cohortKey;
				$rel->save();
			}
		}

		return true;
	}

	/**
	 * @inheritdoc
	 */
	public function afterSave($insert, $changedAttributes) {
		// Se for um usu�rio novo
		if ($insert) {
			//=== PERMISS�ES

			// Encontra o gerenciador de autentica��o
			$authManager = Yii::$app->authManager;

			// Encontra o papel
			$role = $authManager->getRole($this->newRole);

			// Se n�o encontrou o papel, a rotina falha
			if (!$role) return false;

			// Atribui o novo papel
			$authManager->assign($role, $this->id);

			//=== TUTORES
			// Adiciona todos os novos tutores
			if (!empty($this->newTutors))
				foreach ($this->newTutors as $newTutor) {
					$rel = new UserTutor();
					$rel->user_id = $this->id;
					$rel->tutor_id = (int)$newTutor;
					$rel->save();
				}

			//=== TURMAS
			switch ($this->newRole) {
			//case 'Administrator':
			case 'Manager':
			case 'Tutor':
				$cohortKey = 'cohorts' . $this->newRole;
				break;
			case 'Student':
				break;
			default:
				return false;
			};
			if ($this->newRole == 'Student') {
				$rel = new UserCohort();
				$rel->user_id = $this->id;
				$rel->cohort_id = (int)$this->cohortsStudent;
				$rel->save();
			} else {
				// Adiciona todas as novas turmas
				foreach ($this->$cohortKey as $cohort) {
					$rel = new UserCohort();
					$rel->user_id = $this->id;
					$rel->cohort_id = (int)$cohort;
					$rel->save();
				}
			}
		}

		return true;
	}

	/**
	 * @inheritdoc
	 */
	public function afterFind() {
		$this->newRole = $this->role;
		$this->newTutors = ArrayHelper::getColumn($this->userTutors, 'tutor_id');
		$this->oldTutors = $this->newTutors;

		if ($this->role != 'Administrator') {
			$cohortKey = 'cohorts' . $this->role;
			$this->$cohortKey = ArrayHelper::getColumn($this->userCohorts, 'cohort_id');
		}
	}

	/**
	 * @inheritdoc
	 */
	public function beforeDelete() {
		if (!parent::beforeDelete())
			return false;

		try {
        	$this->unlinkAll('tutors', true);
			$this->unlinkAll('tutees', true);
			return true;
		} catch (Exception $e) {
			return false;
		}
	}
}
