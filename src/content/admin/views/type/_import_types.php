<?php
/**
 * @var $this yii\web\View
 * @var $model \svsoft\yii\content\forms\import\TypeImport
 */
?>
<h4>
    Прочитанные типы
</h4>
<table class="table table-bordered dataTable">
    <thead>
    <tr>
        <th>Название</th>
        <th>Подпись</th>
        <th>Статус</th>
    </tr>
    </thead>
    <tbody>
    <?foreach($model->types as $typeKey=>$type):?>

        <tr>
            <td><?=$type->name?></td>
            <td><?=$type->label?></td>
            <td><?=$type->isNewRecord ? '<span class="badge bg-green">Будет добавлен</span>':'<span class="badge bg-orange">Будет перезаписан</span>'?></td>
        </tr>

        <?if ($type->hasErrors()):?>
            <tr>
                <td class="has-error" colspan="3">
                    <?foreach($type->errors as $errors):?>
                        <?foreach($errors as $error):?>
                            <div class="help-block"><?=$error?></div>
                        <?endforeach;?>
                    <?endforeach;?>
                </td>
            </tr>
        <?endif;?>

        <tr>
            <td colspan="3">
            <b>Свойства:</b>

            <table class="table table-striped table-bordered">
                <thead>
                <tr>
                    <th>Название</th>
                    <th>Подпись</th>
                    <th>Тип</th>
                    <th>Множественное</th>
                    <th>Статус</th>
                </tr>
                </thead>
                <tbody>
                <?=$this->render('_import_properties', ['properties' => $model->getPropertiesByTypeKey($typeKey), 'delete'=>false]);?>
                <?=$this->render('_import_properties', ['properties' => $model->getPropertiesForDeleteByKey($typeKey), 'delete'=>true]);?>
                </tbody>
            </table>
            </td>
        </tr>
    <?endforeach;?>
    </tbody>
</table>