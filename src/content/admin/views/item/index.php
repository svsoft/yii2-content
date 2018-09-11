<?php

use yii\helpers\Html;
use svsoft\yii\content\models\Type;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel svsoft\yii\content\models\ItemSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $type \svsoft\yii\content\models\Type */

$this->params['breadcrumbs'][] = $this->title;
?>
<div class="item-index box box-primary">
    <div class="box-header with-border">
        <?= Html::a('Create Item', ['create','type_id'=>$type->type_id], ['class' => 'btn btn-success btn-flat']) ?>
        <div class="btn-group">
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <?=Html::encode($type->label)?> <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
                <?foreach(Type::getTypeList() as $typeId=>$name):?>
                    <li><a href="<?=Url::to(['index','type_id'=>$typeId])?>"><?=Html::encode($name)?></a></li>
                <?endforeach;?>
            </ul>
        </div>
    </div>
    <div class="box-body table-responsive no-padding">

        <?= \svsoft\yii\content\admin\widgets\GridViewItems::widget([
            'type'=>$type,
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'layout' => "{items}\n{summary}\n{pager}",
        ]); ?>
    </div>
</div>
