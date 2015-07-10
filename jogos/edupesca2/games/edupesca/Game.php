<?php

namespace app\games\edupesca;

use Yii;
use app\models\Game as BaseGame;
use yii\web\UploadedFile;
use yii\helpers\Html;
use gabteles\bootstrap\tokenfield\Tokenfield;

class Game extends BaseGame {
	const IMAGE_NONE = 0;
	const IMAGE_URL  = 1;
	const IMAGE_FILE = 2;

	public function getAssetBundle() {
		return 'app\games\edupesca\GameAsset';
	}

	public function levelAttributeLabels() {
		return [
			'instruction' => Yii::t($this->languageId, 'Instruction'),
			'answer' => Yii::t($this->languageId, 'Answer'),
			'opts' => Yii::t($this->languageId, 'Options'),
			'image' => Yii::t($this->languageId, 'Image'),
		];
	}

	public function getValidActions() {
		return [
			'catch'
		];
	}

	public function getLevelFields() {
		return [
			'instruction' => [BaseGame::FIELD_STRING],
			'answer' => [
				BaseGame::FIELD_STRING,
				'hint' => Yii::t($this->languageId, 'The answer has to be in the options below. Capital letters are different from lowercase')
			],
			'opts' => [
				BaseGame::FIELD_CUSTOM,
				'class' => Tokenfield::className(),
				'data' => [
					'pluginOptions' => [
						'createTokensOnBlur' => true
					]
				],
				'insertValue' => function(&$data, $value) {
					$data['value'] = array_values($value);
					$data['overwriteValue'] = true;
				},
				'hint' => Yii::t($this->languageId, 'Separate the options with commas.')
			],
			'image'	=> [[
				'file' => [
					BaseGame::FIELD_FILE,
					'hint' => Yii::t($this->languageId, 'Select one image')
				],
				'str' => [
					BaseGame::FIELD_STRING,
					'hint' => Yii::t($this->languageId, 'Enter one URL')
				]
			]],
		];
	}

	/**
	 * Valida os dados de entrada de um nível e gera os dados que serão armazenados no banco de dados
	 */
	public function levelInputCallback($model, $inputData, &$result) {
		// Informação não precisa de validação
		$result->output['instruction'] = $inputData['instruction'];

		// Verifica se foram inseridas opções
		$opts = explode(',', $inputData['opts']);
		if (!empty($opts)) {
			// Verifica se a resposta é valida (está dentro das opções)
			$tmpAnswer = $inputData['answer'];
			foreach ($opts as $opt) {
				str_replace($opt, "", $tmpAnswer);
				if (empty($tmpAnswer)) break;
			}

			if (empty($tempAnswer)) {
				$result->output['answer'] = $inputData['answer'];
				$result->output['opts'] = $opts;
			} else {
				$result->errors[] = Yii::t($this->languageId, 'The answer has to be in the options.');
			}
		} else {
			$result->errors[] = Yii::t($this->languageId, 'You should enter at least one option.');
		}


		// A imagem já existe?
		$imgExist = (isset($result->output['imageType']) && ($result->output['imageType'] != static::IMAGE_NONE));

		// Verifica a imagem (dá preferência ao link)
		$checkImg = true;
		if (!empty($inputData['image']['str'])) {
			$checkImg = !@getimagesize($inputData['image']['str']);
			if (!$checkImg) {
				// Se a imagem existia e era um arquivo
				if ($imgExist && $result->output['imageType'] == static::IMAGE_FILE) {
					// Deleta o arquivo
					$filepath = Yii::getAlias('@webroot') . '/' . Yii::$app->params['uploadsDir'] . $result->output['image'];
					@unlink($filepath);
				}

				$result->output['image'] = $inputData['image']['str'];
				$result->output['imageType'] = static::IMAGE_URL;
			}
		}

		// Se não encontrou informações no link, checa o arquivo
		if ($checkImg) {
			$file = UploadedFile::getInstance($model, $model->getFieldName('image[file]'));
			if ($file) {
				if ((@getimagesize($file->tempName) !== null)) {
					$destinationFilename = hash_file(Yii::$app->params['filehashAlgo'], $file->tempName) . '.' . $file->extension;
					$destinationPath = Yii::getAlias('@webroot') . '/' . Yii::$app->params['uploadsDir'] . $destFilename;

					// Se copia o arquivo se o arquivo de destino não existir e se não existirem erros
					if (!file_exists($destinationPath) && empty($errors)) {
						// Se a imagem existia e era um arquivo
						if ($imgExist && $result->output['imageType'] == static::IMAGE_FILE) {
							// Deleta o arquivo
							$filepath = Yii::getAlias('@webroot') . '/' . Yii::$app->params['uploadsDir'] . $result->output['image'];
							@unlink($filepath);
						}

						$file->saveAs($destinationPath);
						$result->output['image'] = $destinationFilename;
						$result->output['imageType'] = static::IMAGE_FILE;
					}
				} else {
					$result->errors[] = Yii::t($this->languageId, 'Send a valid image or link.');
				}
			} else {
				// Se a imagem existia, não altera
				if (!$imgExist) {
					$result->output['image'] = '';
					$result->output['imageType'] = static::IMAGE_NONE;
				}
			}
		}
	}

	public function loadLevelDataCallback($inputData, &$outputData) {
		$outputData['instruction'] = $inputData['instruction'];
		$outputData['answer'] = $inputData['answer'];
		$outputData['opts'] = $inputData['opts'];
		$outputData['image[file]'] = '';
		$outputData['image[str]']  = ($inputData['imageType'] == static::IMAGE_NONE ? '' : $inputData['image']);
	}

	public function getViewData($level) {
		$info = $level->unserializedData;

		$data = [
			'basePath' => $this->assetsPath,
			'levelTitle' => $level->title,
			'levelInfo' => $info['instruction'],
			'levelWord' => $info['answer'],
			'levelData' => $info['opts'],
		];

		switch ($info['imageType']) {
		case static::IMAGE_URL:
			$data['levelImg'] = $info['image'];
			break;
		case static::IMAGE_FILE:
			$data['levelImg'] = Yii::getAlias('@webroot') . '/' . Yii::$app->params['uploadsDir'] . $info['image'];
			break;
		case static::IMAGE_NONE:
			$data['levelImg'] = '';
			break;
		}

		return $data;
	}

	public function getDetailViewAttributes($level) {
		$model = $this->getViewData($level);
		$labels = $this->levelAttributeLabels();

		$start = '<span class="label label-primary">';
		$end = '</span>&nbsp;';
		$optsValue = $start . implode($end . $start, $model['levelData']) . $end;
		$imageValue = (empty($model['levelImg']) ? '' : Html::img($model['levelImg']));

		return  [
			'model' => $model,
			'attributes' => [
				[
					'attribute' => 'levelInfo',
					'label' => $labels['instruction'],
				],
				[
					'attribute' => 'levelWord',
					'label' => $labels['answer'],
				],
				[
					'label' => $labels['opts'],
					'value' => $optsValue,
					'format' => 'html'
				],
				[
					'label' => $labels['image'],
					'value' => $imageValue,
					'format' => 'html'
				]
			]
		];
	}

	/**
	 * @inheritdoc
	 */
	public function getUserFriendlyAction($action, $data) {
		switch ($action) {
			case 'catch':
				return Yii::t($this->languageId, 'Picked up "{word}"', ['word' => $data['word']]);
				break;
			default:
				return false;
		};
	}

	/**
	 * @inheritdoc
	 */
	public function calculateScore($session) {
		return parent::calculateScore($session);
	}
}
