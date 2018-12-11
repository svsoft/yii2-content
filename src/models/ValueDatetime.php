<?php

namespace svsoft\yii\content\models;

/**
 * This is the model class for table "{{%svs_content_value}}".
 *
 * @property integer $value_id
 * @property integer $item_id
 * @property integer $property_id
 * @property integer $value_item_id
 * @property string $value_string
 * @property string $value_text
 * @property integer $value_int
 * @property double $value_float
 * @property mixed $value
 *
 * @property Property $property
 * @property Item $item
 * @property Item $valueItem - Модель элемента для комплексного типа свойств
 */
class ValueDatetime extends ValueDate
{
    public $format = 'php:Y-m-d H:i:s';

    public function getValueField()
    {
        return 'value_datetime';
    }
}
