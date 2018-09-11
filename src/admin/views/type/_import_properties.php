<?php
/**
 * @var $this yii\web\View
 * @var $model \svsoft\yii\content\forms\import\TypeImport
 * @var $properties \svsoft\yii\content\models\Property[]
 * @var $delete boolean
 */
?>
<?foreach($properties as $property):?>

    <tr>
        <td><?=$property->name?></td>
        <td><?=$property->label?></td>
        <td><?=$property->getTypeName()?></td>
        <td><?=$property->multiple?></td>
        <td>
            <?=$delete ?
                '<span class="badge bg-red">Будет удалено</span>' :
                ($property->isNewRecord ? '<span class="badge bg-green">Будет добавлено</span>':'<span class="badge bg-orange">Будет перезаписано</span>')
            ?>
        </td>
    </tr>
    <?if ($property->hasErrors()):?>
        <tr>
            <td class="has-error" colspan="5">
                <?foreach($property->errors as $errors):?>
                    <?foreach($errors as $error):?>
                        <div class="help-block"><?=$error?></div>
                    <?endforeach;?>
                <?endforeach;?>
            </td>
        </tr>
    <?endif;?>
<?endforeach;?>
