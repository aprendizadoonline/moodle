<?php

namespace app\components;

use kartik\select2\Select2 as BaseSelect2;
use yii\helpers\Html;

class Select2 extends BaseSelect2 {
    public $overwriteValue;
	private $hasModelHack;
	
	/**
     * @inheritdoc
     */
    public function init() {
		
		if ($this->overwriteValue) {
			$this->hasModelHack = true;
			$this->name = empty($this->options['name']) ? Html::getInputName($this->model, $this->attribute) : $this->options['name'];
		} else {
			$this->hasModelHack = false;
		}
		parent::init();
		$this->hasModelHack = false;
    }

    public function hasModel() {
		return ($this->hasModelHack ? false : parent::hasModel());
	}
}