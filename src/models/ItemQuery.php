<?php

namespace svsoft\yii\content\models;

/**
 * This is the ActiveQuery class for [[Item]].
 *
 * @see Item
 */
class ItemQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return Item[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Item|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * @param $id
     *
     * @return $this
     */
    public function andId($id)
    {
        return $this->andWhere(['item_id'=>$id]);
    }

    /**
     * @param $typeId
     *
     * @return $this
     */
    public function andTypeId($typeId)
    {
        return $this->andWhere(['type_id'=>$typeId]);
    }

    /**
     * @param $name
     *
     * @return $this
     */
    public function andName($name)
    {
        return $this->andWhere(['name'=>$name]);
    }

    /**
     * @param $slug
     *
     * @return $this
     */
    public function andSlug($slug)
    {
        return $this->andWhere(['slug'=>$slug]);
    }

    /**
     * @param $value
     *
     * @return $this
     */
    public function andSlugOrName($value)
    {
        return $this->andWhere(['OR',['slug'=>$value],['name'=>$value]]);

    }

}
