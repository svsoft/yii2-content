<?php
namespace svsoft\yii\content\admin\widgets;

use svsoft\yii\content\models\Type;
use svsoft\yii\content\models\ValueFile;
use yii\grid\GridView;
use svsoft\yii\content\models\ItemObject;
use yii\helpers\Html;

/**
 * Выводит таблицу элементов с прикрепленными свойствами
 *
 * Class GridViewItems
 * @package svsoft\yii\content\admin\widgets
 */
class GridViewItems extends GridView
{
    /**
     * @var Type
     */
    public $type;

    public $showControls = true;

    function init()
    {
        $propertyColumns = [];
        $columns = [];

        if ($this->type)
        {
            $type = $this->type;

            foreach($type->properties as $property)
            {
                if ($property->getTypeName() == Type::TYPE_HTML || $property->getTypeName() == Type::TYPE_TEXT)
                    continue;

                $propertyColumns[] = [
                    'label'=>$property->label,
                    'attribute'=>$property->name,
                    'value'=> function(ItemObject $model) use ($property) {

                        $values = $model->getItemProperty($property->property_id)->getValues();

                        if ($property->getTypeName() == Type::TYPE_FILE)
                        {
                            foreach($values as $key=>&$value)
                            {
                                $ext = pathinfo($value, PATHINFO_EXTENSION);

                                /**
                                 * @var $valueModel ValueFile
                                 */
                                $valueModel = $model->getItemProperty($property->property_id)->getValueModel($key);

                                if (in_array($ext, ['png','jpg','jpeg','gif']))
                                    $value = Html::img($valueModel->getFileWebPath(), ['style'=>'max-width:50px; max-height:50px;']);
                            }
                            unset($value);
                        }

                        return is_array($values) ? implode(', ', $values) : $values;

                        //return Html::a('Просмотр', ['index','type_id'=>$property->type_id]);

                    },
                    'format'=>'html',
                ];
            }

            $columns = [
                ['class' => 'yii\grid\SerialColumn'],

                'item_id',
                'name',
                'slug',
            ];

            $columns = array_merge($columns, $propertyColumns);

            if ($this->showControls)
            {
                $columns[] = [
                    'class'          => \svsoft\yii\admin\components\ActionColumn::className(),
                    'template'       => '<div class="btn-group btn-sm-group-4">{create}{update}{delete}</div>',
                    'buttonOptions' => ['class'=>'btn btn-info btn-sm'],
                    'contentOptions' => ['class'=>'format-action'],
                    'headerOptions' => ['class'=>'format-action'],
                    'buttons'        => [
                        'create' => [

                            'url'=> function(ItemObject $model) { return \yii\helpers\Url::to(['create','type_id'=>$model->type_id], ['class' => 'btn btn-success btn-flat']); },
                            'content'=>'<i class="fa fa-plus"></i>',
                            'class'=>'btn btn-sm btn-default',
                            'appendOptions'=>[
                            ]
                        ],
                    ]
                ];
            }
        }

        $this->columns = $columns;

        parent::init();
    }
}
