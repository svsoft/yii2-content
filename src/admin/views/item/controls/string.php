<?php

use unclead\multipleinput\MultipleInput;
use svsoft\yii\content\models\ItemProperty;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $itemProperty ItemProperty */
?>


<? if($itemProperty->property->multiple):?>
    <?=$form->field($itemProperty, 'value')->widget(MultipleInput::className(), [
        //'max'               => 100,
        'min'               => 1, // should be at least 2 rows
        'allowEmptyList'    => false,
        'enableGuessTitle'  => false,
        'addButtonPosition' => MultipleInput::POS_FOOTER, // show add button in the header

    ]);?>
<?else:?>
    <?= $form->field($itemProperty, 'value')->textInput(['maxlength' => true]) ?>
<?endif;?>