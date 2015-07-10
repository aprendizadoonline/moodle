<?php

use yii\web\View;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Game;
use app\models\Filter;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model app\models\Level */
/* @var $form yii\widgets\ActiveForm */

$appendCounter = 0;

function generateInputField($form, $model, $key, $field, $label) {	
	$inputField = $form->field(
		$model, 
		$model->getFieldName($key), 
		['labelOptions' => [
			'label' => $label
		]]
	);
	
	$value = $model->getFieldValue($key);
	
	if (isset($field['hint']))
		$inputField->hint($field['hint']);

	switch ($field[0]) {
	case Game::FIELD_STRING:
		$options = $model->isNewRecord ? [] : ['value' => $value];
		if (isset($field['password']) && $field['password']) {
			$inputField->passwordInput($options);
		} else {
			$inputField->textInput($options);
		}
		break;
		
	case Game::FIELD_BOOL:
		$inputField->checkbox(['label' => $label, 'uncheck' => !$value]);
		break;
		
	case Game::FIELD_SELECT:
		if (isset($field['multiple']) && $field['multiple']) {
			$inputField->checkboxList($field['options']);
		} else {
			$inputField->radioList($field['options']);
		}
		
		break;
		
	case Game::FIELD_CUSTOM:
		if ((!$model->isNewRecord) && isset($field['insertValue']))
			call_user_func_array($field['insertValue'], [&$field['data'], $value]);
			
		$inputField->widget($field['class'], $field['data']);
		
		break;
		
	case Game::FIELD_FILE:
		$inputField->fileInput();
		break;
	}
	
	return $inputField;
}
?>

<div class="level-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
	
	<?= $form->errorSummary($model) ?>
	
	<?= Html::hiddenInput('step', 2) ?>
	<?= Html::hiddenInput('gameId', $game->id) ?>
	
	<?= $form->field($model, 'title')->textInput() ?>
	
	<?php foreach ($game->levelFields as $key => $field): ?>
		<?php 
			$label = $game->getLevelAttributeLabel($key);
			
			// Se houverem vários tipos
			if (is_array($field[0])) {
				$total = count($field[0]);
				$count = $total;
				// Para cada tipo...
				foreach ($field[0] as $fieldId => $_field) {
					// Gera o inputfield com label somente no primeiro
					echo generateInputField($form, $model, $key . '[' . $fieldId . ']', $_field, ($count == $total ? $label : ''));
					// Mostra "ou" se não for o último
					if (--$count > 0)
						echo Html::tag('p', Yii::t('app', 'Or'), ['class' => 'text-center']);
				}
			// Se houver apenas um tipo
			} else {
				echo generateInputField($form, $model, $key, $field, $label);
			}
		?>
	<?php endforeach; ?>
	
	<?= Html::tag('label', Yii::t('app', 'Restriction'), ['class' => 'control-label']) ?>
	<div class="hint-block"><?= Yii::t('app', 'Adding a restriction will allow only selected students/cohorts to see this level.') ?></div>
		
	<div id="restrictData">
		<a href="#restrictData" id="addRestriction"><?= Yii::t('app', 'Add new restriction...') ?></a>
		
		<?php if (!$model->isNewRecord): ?>
			<?php foreach ($model->filters as $filter): ?>
				<div class="restriction" id="restriction-<?= ++$appendCounter ?>">
					<?= $form->field($model, 'restrictions[type][]', ['labelOptions' => ['label' => '']])
						->dropDownList(
							[
								Filter::TARGET_COHORT => Yii::t('app', 'Cohort'), 
								Filter::TARGET_STUDENT => Yii::t('app', 'Student')
							],
							['class' => 'form-control type-control', 'value' => $filter->target_type]
						); 
					?>
					<?= $form->field($model, 'restrictions[target][]', ['labelOptions' => ['label' => '']])
						->dropDownList( 
							($filter->target_type == Filter::TARGET_COHORT) ? $userCohorts : $userTutees,
							['class' => 'form-control target-control', 'value' => $filter->target_id]
						);
						?>
					<span class="close">&times;</span>
				</div>
			<?php endforeach; ?>
		<?php endif; ?>
	</div>
	
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
	$typeModel = $form->field($model, 'restrictions[type][]', ['labelOptions' => ['label' => '']])
		->dropDownList(
			[
				Filter::TARGET_COHORT => Yii::t('app', 'Cohort'), 
				Filter::TARGET_STUDENT => Yii::t('app', 'Student')
			],
			['class' => 'form-control type-control']
		);

	$targetModel = $form->field($model, 'restrictions[target][]', ['labelOptions' => ['label' => '']])
		->dropDownList( 
			$userCohorts,
			['class' => 'form-control target-control']
		);
	
	$this->registerJs("
		levelRestricted = $('#addRestriction');
		restrictData = $('#restrictData');
		
		FormData = {
			UserCohorts: " . json_encode($userCohorts) . ",
			UserTutees:  " . json_encode($userTutees ) . ",
			TypeModel:   " . json_encode($typeModel->__toString()) . ",
			TargetModel: " . json_encode($targetModel->__toString()) . ",
			Types:       " . json_encode(['Cohort' => Filter::TARGET_COHORT, 'Student' => Filter::TARGET_STUDENT]) . ",
		};
		
		appendCounter = " . json_encode($appendCounter) . ";
		
		function changeOptions(dropdown, options) {
			dropdown.empty();
			$.each(options, function(value,key) {
				dropdown.append(
					$('<option></option>')
						.attr('value', value)
						.text(key)
				);
			});
		}
		
		levelRestricted.click(function() {
			appendCounter++;
			
			content = $('<div class=\"restriction\" id=\"restriction-' + appendCounter + '\">');
			type = $(FormData.TypeModel);
			target = $(FormData.TargetModel);
			close = $('<span class=\"close\">&times;</span>');
			
			content.append(type);
			content.append(target);
			content.append(close);
			
			restrictData.append(content);
		}); 
		
		
		$(document).on('change', '.type-control', function() {
			\$this = $(this);
			_target = \$this.parent().parent().find('.target-control');
			if (\$this.val() == FormData.Types.Cohort) {
				changeOptions(_target, FormData.UserCohorts);
			} else if (\$this.val() == FormData.Types.Student) {
				changeOptions(_target, FormData.UserTutees);
			}
		});
			
		$(document).on('click', '.close', function() {
			$(this).parent().remove();
		});
		"
		, View::POS_READY
		, 'levelRestricted'
	);
?>