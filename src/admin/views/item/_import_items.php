<?php
/**
 * @var $this yii\web\View
 * @var $items \svsoft\yii\content\models\ItemObject[]
 * @var $type \svsoft\yii\content\models\Type
 */
?>
<table class="table table-bordered dataTable">
    <thead>
    <tr>
        <th>Название</th>
        <th>Тип</th>
        <th>Уникальный код</th>
        <?foreach($type->properties as $property):?>
            <th><?=$property->name?></th>
        <?endforeach;?>
        <th>Статус</th>
    </tr>
    </thead>
    <tbody>
    <?foreach($items as $itemKey=>$item):?>

        <tr>
            <td><?=$item->name?></td>
            <td><?=$item->getTypeName()?></td>
            <td><?=$item->slug?></td>
            <?foreach($item->getItemProperties() as $itemProperty):?>
                <td>
                    <?=implode(', ', $itemProperty->values)?>
                </td>
            <?endforeach;?>
            <td><?=$item->isNewRecord ? '<span class="badge bg-green">Будет добавлен</span>':'<span class="badge bg-orange">Будет перезаписан</span>'?></td>
        </tr>

        <?if ($item->hasErrors()):?>
            <tr>
                <td class="has-error" colspan="3">
                    <?foreach($item->errors as $errors):?>
                        <?foreach($errors as $error):?>
                            <div class="help-block"><?=$error?></div>
                        <?endforeach;?>
                    <?endforeach;?>
                </td>
            </tr>
        <?endif;?>
    <?endforeach;?>
    </tbody>
</table>