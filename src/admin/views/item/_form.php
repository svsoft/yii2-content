<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use svsoft\yii\content\models\ItemObject;

/* @var $this yii\web\View */
/* @var $model ItemObject */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="item-form box box-primary">
    <?php $form = ActiveForm::begin(['enableClientValidation' => false]); ?>
    <div class="box-body table-responsive">

        <?= $form->field($model, 'slug')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'sort')->textInput(['maxlength' => true]) ?>

        <?foreach($model->getItemProperties() as $propertyId=>$itemProperty):?>
            <?if($itemProperty->property->type->simple):?>
                <?=$this->render('controls/simple_type', ['form' => $form, 'itemProperty' => $itemProperty]);?>
            <?else:?>
                <?=$this->render('controls/complex_type', ['form' => $form, 'itemProperty' => $itemProperty, 'model'=>$model]);?>
            <?endif;?>
        <?endforeach;?>

    </div>
    <div class="box-footer">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success btn-flat']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
