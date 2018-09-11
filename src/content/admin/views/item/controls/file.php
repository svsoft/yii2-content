<?php

use svsoft\yii\content\models\ItemProperty;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $itemProperty ItemProperty */

?>

<?=\svsoft\yii\content\admin\widgets\FileUploadWidget::widget([
    'model' => $itemProperty,
    'attribute' => 'value',
    'form'   => $form,
    'multiple'=>$itemProperty->property->multiple,
    'files'=> $itemProperty->value,
    'webDirPath'=>$itemProperty->module->webDirPath,
])?>


