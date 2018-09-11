<?php

use yii\helpers\Html;
use yii\grid\GridView;
use svsoft\yii\content\models\Property;

/* @var $this yii\web\View */
/* @var $searchModel svsoft\yii\content\models\PropertySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $type \svsoft\yii\content\models\Type */

$this->title = 'Properties';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="property-index box box-primary">
    <div class="box-header with-border">
        <?= Html::a('Create Property', ['create','type_id'=>$type->type_id], ['class' => 'btn btn-success btn-flat']) ?>
    </div>
    <div class="box-body table-responsive no-padding">
        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'layout' => "{items}\n{summary}\n{pager}",
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                'property_id',
                'name',
                'label',
                [
                    'attribute'=>'type.name',
                ],
                [
                    'attribute' => 'multiple',
                    'value' => function(Property $model) { return $model->multiple ? Html::tag('i', '', ['class'=>'glyphicon glyphicon-ok text-success']) : ''; },
                    'format' => 'html',
                    'contentOptions' => ['class'=>'format-status'],
                ],
                // 'slug',

                ['class' => 'yii\grid\ActionColumn'],
            ],
        ]); ?>
    </div>
</div>
