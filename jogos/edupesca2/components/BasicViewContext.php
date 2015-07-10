<?php

namespace app\components;
use yii\base\ViewContextInterface;

class BasicViewContext implements ViewContextInterface {
	private $path;
	
	public function __construct($path) {
		$this->path = $path;
	}
	
	public function getViewPath() {
		return $this->path;
	}
}
