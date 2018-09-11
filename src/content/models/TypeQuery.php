<?php

namespace svsoft\yii\content\models;

/**
 * This is the ActiveQuery class for [[Type]].
 *
 * @see Type
 */
class TypeQuery extends \yii\db\ActiveQuery
{
    public function active()
    {
        return $this;
    }

    public function andSimple($simple)
    {
        if ($simple === null)
            return $this;

        return $this->andWhere(['simple'=>(int)boolval($simple)]);
    }

    public function andName($value)
    {
        return $this->andWhere(['name'=>$value]);
    }

    public function andSlug($value)
    {
        return $this->andWhere(['slug'=>$value]);
    }

    public function andId($value)
    {
        return $this->andWhere(['type_id'=>$value]);
    }

    public function simple()
    {
        return $this->andSimple(true);
    }

    public function complex()
    {
        return $this->andSimple(false);
    }

    /**
     * @inheritdoc
     * @return Type[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Type|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
