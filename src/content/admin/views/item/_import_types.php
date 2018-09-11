<?php
/**
 * @var $this yii\web\View
 * @var $model \svsoft\yii\content\forms\import\ItemImport
 */
?>
    <?foreach($model->types as $typeKey=>$type):?>
        <h4>
            Прочитанные элементы типа <?=$type->name?>(<?=$type->label?>)
        </h4>
        <?=$this->render('_import_items', ['items' => $model->getItemsByTypeKey($typeKey), 'type'=>$type, 'delete'=>false]);?>
    <?endforeach;?>
