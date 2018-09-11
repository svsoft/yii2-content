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
class ValueDate extends Value
{
    public $format = 'php:Y-m-d';

    function rules()
    {
        $rules = parent::rules();

        $rules['format'] = [['value'], 'date', 'format'=>$this->format];

        return $rules;
    }

    public function getValueField()
    {
        return 'value_date';
    }

    public function prepareSetValue($value)
    {
        return (string)$value;
    }

    public function changeType(Type $newType)
    {
        return $this->changeTypeBase($newType);
    }

    public function prepareGetValue($value)
    {
        if (!$value)
            return null;

        return \Yii::$app->formatter->asDate($value, $this->format);
    }
}
