<?php

namespace svsoft\yii\content\models;


use  yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[Property]].
 *
 * @see Property
 */
class PropertyQuery extends ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return Property[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Property|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function andPropertyId($propertyId)
    {
        return $this->andWhere(['property_id'=>$propertyId]);
    }

    public function andParentTypeId($typeId)
    {
        return $this->andWhere(['parent_type_id'=>$typeId]);
    }

    public function andTypeId($typeId)
    {
        return $this->andWhere(['type_id'=>$typeId]);
    }

    public function andName($name)
    {
        return $this->andWhere(['name'=>$name]);
    }

}
