<?php

/* @var $this yii\web\View */
/* @var $model svsoft\yii\content\models\Property */

$this->params['breadcrumbs'][] = ['label' => 'Properties', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="property-create">

    <?= $this->render('_form', [
    'model' => $model,
    ]) ?>

</div>
