<?php

namespace app\components;

use yii\web\View as BaseView;

class View extends BaseView {
	public function renderGameView($path, $view, $params = []) {
		$context = new BasicViewContext($path);
		$result = $this->render($view, $params, $context);
		unset($context);
		return $result;
	}
}