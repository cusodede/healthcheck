<?php

declare(strict_types = 1);

/**
 * @var View $this
 * @var Users $model
 */

use app\models\Users;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Modal;
use yii\web\View;

$modelName = $model->formName();
?>
<?php
Modal::begin([
	'id' => "{$modelName}-modal-edit-{$model->id}",
	'size' => Modal::SIZE_LARGE,
	'title' => 'edit',
	'footer' => $this->render('../subviews/editPanelFooter', [
		'model' => $model,
		'form' => "{$modelName}-modal-edit"
	]),//post button outside the form
	'options' => [
		'tabindex' => false, // important for Select2 to work properly
		'class' => 'modal-dialog-large'
	]
]); ?>
<?php
$form = ActiveForm::begin(
	[
		'id' => "{$modelName}-modal-edit",
		'enableAjaxValidation' => true,

	]
)
?>
<?= $this->render('../subviews/editPanelBody', compact('model', 'form')) ?>
<?php
ActiveForm::end(); ?>
<?php
Modal::end(); ?>
