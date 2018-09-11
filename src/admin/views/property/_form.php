<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use svsoft\yii\content\models\Type;

/* @var $this yii\web\View */
/* @var $model svsoft\yii\content\models\Property */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="property-form box box-primary">
    <?php $form = ActiveForm::begin(['enableClientValidation' => false]); ?>
    <div class="box-body table-responsive">

        <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'label')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'type_id')->dropDownList(Type::getTypeList(null)) ?>

        <?= $form->field($model, 'multiple')->checkbox() ?>

    </div>
    <div class="box-footer">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success btn-flat']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
