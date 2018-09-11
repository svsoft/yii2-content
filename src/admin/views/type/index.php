<?php

use yii\helpers\Html;
use yii\grid\GridView;
use svsoft\yii\content\models\Type;

/* @var $this yii\web\View */
/* @var $searchModel svsoft\yii\content\models\TypeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Types';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="type-index box box-primary">
    <div class="box-header with-border">
        <?= Html::a('Create Type', ['create'], ['class' => 'btn btn-success btn-flat']) ?>
    </div>
    <div class="box-body table-responsive no-padding">
        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'layout' => "{items}\n{summary}\n{pager}",
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                'type_id',
                'name',
                'label',
                [
                    'label' => 'Элементы',
                    'value' => function(Type $model) { return $model->simple ? '' : Html::a('Просмотр',['item/index','type_id'=>$model->type_id]); },
                    'format' => 'html',
                ],
                [
                    'label' => 'Свойства',
                    'value' => function(Type $model) { return $model->simple ? '' : Html::a('Просмотр',['property/index','type_id'=>$model->type_id]); },
                    'format' => 'html',
                ],

                [
                    'class'          => \svsoft\yii\admin\components\ActionColumn::class,
                    'template'       => '<div class="btn-group btn-sm-group-3">{export-json}{create}{update}{delete}</div>',
                    'buttonOptions' => ['class'=>'btn btn-info btn-sm'],
                    'contentOptions' => ['class'=>'format-action'],
                    'headerOptions' => ['class'=>'format-action'],
                    'buttons' => [
                        'export-json' => [
                            //'url'=> function(Item $model) { return \yii\helpers\Url::to(['create','type_id'=>$model->type_id], ['class' => 'btn btn-success btn-flat']); },
                            'content'=>'Json',
                            'class'=>'btn btn-sm btn-default',
                            'appendOptions'=>[]
                        ],
                    ]
                ],
            ],
        ]); ?>
    </div>
</div>
