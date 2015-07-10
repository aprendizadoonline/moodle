<?php
	use yii\web\View;
	use kartik\select2\Select2;
?>

<?= $form->field($user, 'username')->textInput(['maxlength' => 25]) ?>
<?= $form->field($user, 'email')->textInput(['maxlength' => 255]) ?>
<?= $form->field($user, 'password')->passwordInput() ?>
<?= $form->field($user, 'newRole')->dropDownList($roles); ?>

<div>
	<div data-user-type="Manager">
		<p class="col-sm-9 col-sm-offset-3"><?= Yii::t('app', 'A Manager handles multiple Tutors, Cohorts and Students') ?></p>
		<?= $form->field($user, 'cohortsManager')->widget(Select2::classname(), [
			'data' => $cohorts,
			'options' => ['placeholder' => Yii::t('app', 'Select the cohort(s)'), 'multiple' => true],
			'pluginOptions' => ['allowClear' => true],
		]); ?>
	</div>
	
	<div data-user-type="Tutor">
		<p class="col-sm-9 col-sm-offset-3"><?= Yii::t('app', 'A Tutor can be a teacher or the student\'s mom/daddy. Assign with cohorts if he/she is a teacher.') ?></p>
		<?= $form->field($user, 'cohortsTutor')->widget(Select2::classname(), [
			'data' => $cohorts,
			'options' => ['placeholder' => Yii::t('app', 'Select the cohort(s)'), 'multiple' => true],
			'pluginOptions' => ['allowClear' => true],
		]); ?>
	</div>
	
	<div data-user-type="Student">
		<p class="col-sm-9 col-sm-offset-3"><?= Yii::t('app', 'A Student can be inserted in one cohort and have various tutors') ?></p>
		<?= $form->field($user, 'cohortsStudent')->widget(Select2::classname(), [
			'data' => $cohorts,
			'options' => ['placeholder' => Yii::t('app', 'Select the cohort'), 'multiple' => false],
			'pluginOptions' => ['allowClear' => false],
		]); ?>
		
		<p class="col-sm-9 col-sm-offset-3"><?= Yii::t('app', 'Select user\'s direct Tutors. Remember that Tutors assigned to the user cohort do not need to be included here.') ?></p>
		<?= $form->field($user, 'newTutors')->widget(Select2::classname(), [
			'data' => $tutors,
			'options' => ['placeholder' => Yii::t('app', 'Select the tutor(s)'), 'multiple' => true],
			'pluginOptions' => ['allowClear' => true],
		]); ?>
	</div>
</div>

<?php
	$this->registerJs("
		updateVisibleUserType = function(val) {
			$('div[data-user-type]').css('display', 'none');
			if (val) $('div[data-user-type=' + val + ']').css('display', 'block');
		};
		
		userRole = $('#user-newrole');
		userRole.change(function() { updateVisibleUserType($(this).val()) });
		updateVisibleUserType(userRole.val());
		"
		, View::POS_READY
		, 'formSwitcher'
	);
?>