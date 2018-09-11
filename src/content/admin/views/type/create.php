<?php


/* @var $this yii\web\View */
/* @var $model svsoft\yii\content\models\Type */

$this->title = 'Create Type';
$this->params['breadcrumbs'][] = ['label' => 'Types', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="type-create">

    <?= $this->render('_form', [
    'model' => $model,
    ]) ?>

</div>
