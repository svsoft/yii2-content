<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
/**
 * @var $this yii\web\View
 * @var $model \svsoft\yii\content\forms\import\TypeImport
 */

$this->title = 'Импорт типов';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="type-import box box-primary">
    <div class="box-header with-border">
    </div>
    <?php $form = ActiveForm::begin(['enableClientValidation' => false, 'action' => ['import']]); ?>
    <div class="box-body table-responsive">

        <?= $form->field($model, 'content')->textarea(['maxlength' => true,'rows'=>20]) ?>

        <?if($model->types):?>
            <?=$this->render('_import_types', ['model' => $model])?>
        <?endif;?>
    </div>
    <div class="box-footer">
        <?= Html::submitButton('Прочитать', ['class' => 'btn btn-info btn-flat', 'name'=>'read', 'value'=>1]) ?>
        <?if($model->types):?>
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success btn-flat', 'name'=>'save', 'value'=>1, 'disabled'=>$model->hasErrors()?true:false]) ?>
        <?endif;?>
    </div>
    <?php ActiveForm::end(); ?>

</div>
