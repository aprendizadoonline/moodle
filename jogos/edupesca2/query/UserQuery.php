<?php

namespace app\query;
use yii\db\ActiveQuery;

class UserQuery extends ActiveQuery {
    /**
	 * Seleciona os usuários de determinado papel
	 *
	 * @param string $role
	 * @return $this
	 */
	public function withRole($role) {
		return $this
			->join('LEFT JOIN','auth_assignment','auth_assignment.user_id = id')
			->andWhere(['auth_assignment.item_name' => $role]);
	}
}