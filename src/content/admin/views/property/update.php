<?php

/* @var $this yii\web\View */
/* @var $model svsoft\yii\content\models\Property */

$this->title = 'Update Property: ' . $model->label;
$this->params['breadcrumbs'][] = ['label' => 'Properties', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->label, 'url' => ['view', 'id' => $model->property_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="property-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
