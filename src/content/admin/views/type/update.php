<?php

/* @var $this yii\web\View */
/* @var $model svsoft\yii\content\models\Type */

$this->title = 'Update Type: ' . $model->label;
$this->params['breadcrumbs'][] = ['label' => 'Types', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->label, 'url' => ['view', 'id' => $model->type_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="type-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
