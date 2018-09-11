<?php

use yii\helpers\Html;
use svsoft\yii\content\models\ItemProperty;
use svsoft\yii\content\models\ItemObject;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $itemProperty ItemProperty */
/* @var $model ItemObject */

?>
<div class="form-group">

    <?if ($model->isNewRecord):?>
        <div>Возможно добавить после создания</div>
        <?else:?>


        <?$itemList = [];?>
        <?foreach($itemProperty->property->type->items as $item):?>
            <?$itemList[$item->item_id] = $item->name ? $item->name : $item->item_id ;?>
        <?endforeach;?>

        <?=$form->field($itemProperty, 'value', [
            'template'=>"{label} <a href=\"javascript:\" onclick=\"$(this).parent().find('select').toggleClass('hide')\">Показать привязку</a> \n {input}\n{hint}\n{error}",
            'inputOptions'=>['class'=>"hide form-control"]])->dropDownList($itemList, ['multiple'=>$itemProperty->property->multiple])?>

        <? if($itemProperty->valueModels):?>
            <?$models = [];?>
            <?foreach($itemProperty->valueModels as $value):?>
                <? if (!$value->valueItem) continue;?>
                <?$models[$value->valueItem->item_id] = $value->valueItem;?>
            <?endforeach;?>

            <?= \svsoft\yii\content\admin\widgets\GridViewItems::widget([
                'type'=>$itemProperty->property->type,
                'dataProvider' => new \yii\data\ArrayDataProvider([
                    'allModels'=>$models,
                ]),
                'layout' => "{items}",
            ]); ?>
        <?endif;?>
        <div>
            <?=Html::a('<i class="fa fa-plus"></i> Добавить',['create', 'type_id'=>$itemProperty->property->type_id, 'parent_item_id'=>$model->item_id, 'parent_property_id'=>$itemProperty->property->property_id])?>
        </div>

    <?endif;?>
</div>

