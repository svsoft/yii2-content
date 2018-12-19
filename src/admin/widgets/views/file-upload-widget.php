<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $attribute string */
/* @var $files array */
/* @var $widget \svsoft\yii\content\admin\widgets\FileUploadWidget */
/* @var $model \yii\base\Model */

?>
<div class="file-upload-widget">

<? if($widget->multiple):?>

    <?= $form->field($model, $attribute.'[]')->input('file', ['multiple' => $widget->multiple, 'accept' => 'image/*', 'title'=>'Выберите файл', 'class'=>'file-input']) ?>
    <div class="file-upload-widget-img-items row">
    <?foreach($files as $key=>$value):?>
        <? if ($value instanceof \svsoft\yii\content\components\files\UploadedFile) continue;?>

            <div class="file-upload-widget-img-item col-lg-2 col-sm-3 col-xs-6">

                <img src="<?=$widget->webDirPath . DIRECTORY_SEPARATOR . $value?>"/>

                <?=Html::activeHiddenInput($model, $attribute.'['.$key.']', ['value'=>$value])?>
                <div class="checkbox">
                    <label>
                        <?=Html::checkbox(Html::getInputName($model, $attribute.'['.$key.']'), false, ['value'=>''])?> Удалить
                    </label>
                </div>
            </div>

    <?endforeach;?>
    </div>

<?else:?>
    <?= $form->field($model, $attribute)->input('file', ['multiple' => $widget->multiple, 'accept' => 'image/*', 'title'=>'Выберите файл', 'class'=>'file-input']) ?>
    <div class="file-upload-widget-img-items row">
        <? if ($files && is_string($files)):?>
            <div class="file-upload-widget-img-item col-lg-2 col-sm-3 col-xs-6">

                <img src="<?=$widget->webDirPath . DIRECTORY_SEPARATOR . $files?>"/>

                <?=Html::activeHiddenInput($model, $attribute, ['value'=>$files])?>
                <div class="checkbox">
                    <label>
                        <?=Html::checkbox(Html::getInputName($model, $attribute), false, ['value'=>''])?> Удалить
                    </label>
                </div>
            </div>
        <? endif;?>
    </div>
<?endif;?>

</div>


<?php
$js = <<< JS
//$('.bootstrap-file-input').bootstrapFileInput();
JS;
$this->registerJs($js);
?>