<?php
namespace app\components;

class LevelCallbackData {
	public $output;
	public $errors;
	
	public function success() {
		return (count($this->errors) == 0);
	}
}