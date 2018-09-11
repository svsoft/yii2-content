<?php

use dosamigos\ckeditor\CKEditor;
use mihaildev\elfinder\ElFinder;
use svsoft\yii\content\models\ItemProperty;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $itemProperty ItemProperty */

?>


<? if($itemProperty->property->multiple):?>
    Множественный тип не предусмотрен
<?else:?>
    <?= $form->field($itemProperty, 'value')->widget(CKEditor::className(),[
        'options' => ['rows' => 6],
        'preset' => 'full',
        'clientOptions'=>ElFinder::ckeditorOptions(['elfinder']) + [
                'allowedContent' => 'a pre blockquote img em p i h1 h2 h3 div span table tbody thead tr th td ul li ol(*)[*]; br hr strong;',
                'height'=>250
            ]
    ])?>
<?endif;?>