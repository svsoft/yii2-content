<?php

namespace svsoft\yii\content\models;

/**
 * This is the ActiveQuery class for [[Value]].
 *
 * @see Value
 */
class ValueQuery extends \yii\db\ActiveQuery
{
    public function init()
    {
        parent::init();

        // Получем название типа через джоины таблицы свойств и типов
        $table = call_user_func([$this->modelClass,'tableName']);
        $this->leftJoin(['p'=>Property::tableName()], 'p.property_id = ' . $table . '.property_id');
        $this->leftJoin(['t'=>Type::tableName()], 't.type_id = p.type_id');
        $this->select([$table . '.*','t.type_id','t.name as type_name']);
    }


    /**
     * @inheritdoc
     * @return Value[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Value|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * @param $itemId
     *
     * @return $this
     */
    public function andItemId($itemId)
    {
        return $this->andWhere([Value::tableName().'.item_id'=>$itemId]);
    }

    /**
     * @param $propertyId
     *
     * @return $this
     */
    public function andPropertyId($propertyId)
    {
        return $this->andWhere([Value::tableName().'.property_id'=>$propertyId]);
    }

    public function andValueItemId($value)
    {
        return $this->andWhere([Value::tableName().'.value_item_id'=>$value]);

    }
}
