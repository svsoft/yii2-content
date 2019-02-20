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
 * @property ItemObject $valueItem - Модель элемента для комплексного типа свойств
 */
class ValueItem extends Value
{
    function rules()
    {
        $rules = parent::rules();

        $rules[] = [
            ['value'],
            'exist',
            'skipOnError' => true,
            'targetClass' => Item::className(),
            'targetAttribute' => ['value' => 'item_id']
        ];

        return $rules;
    }

    /**
     * @return string
     */
    public function getValueField()
    {
        return 'value_item_id';
    }

    /**
     * @param $value
     *
     * @return int
     */
    public function prepareSetValue($value)
    {
        return $value ? (int)$value : null;
    }

    /**
     * @param Type $newType
     *
     * @return false|int
     */
    public function changeType(Type $newType)
    {
        return $this->delete();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getValueItem()
    {
        return $this->hasOne(ItemObject::className(), ['item_id' => 'value_item_id']);
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        // При изменении значения (ид элемента), старый элемент удаляем
        // Пока убрал удаление сязанного элемента
//        $oldValue = ArrayHelper::getValue($changedAttributes, $this->getValueField());
//        if ($oldValue && $oldValue != $this->value)
//        {
//            if ($item = ItemObject::findOne($oldValue))
//                $item->delete();
//        }
    }

    public function afterDelete()
    {
        parent::afterDelete();

        // Пока убрал удаление сязанного элемента
//        $itemId = $this->value;
//
//        if ($item = ItemObject::findOne($itemId))
//            $item->delete();
    }
}
