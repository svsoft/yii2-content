<?php

use svsoft\yii\content\models\ItemProperty;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $itemProperty ItemProperty */
?>


<? if($itemProperty->property->multiple):?>
    Множественный тип не предусмотрен
<?else:?>
    <?= $form->field($itemProperty, 'value')->widget(\kartik\widgets\DatePicker::class,[
        'layout' => '{picker}{input}',
        'pluginOptions' => [
            'autoclose'=>true,
            'format' => 'yyyy-mm-dd',
        ]
    ]) ?>
<?endif;?>