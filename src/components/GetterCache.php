<?php

namespace svsoft\yii\content\components;

use svsoft\yii\content\components\display\Item;
use svsoft\yii\content\models\ItemObjectQuery;
use svsoft\yii\content\models\TypeQuery;
use svsoft\yii\content\traits\ModuleTrait;

/**
 * Class Content
 * @package svsoft\yii\content\components
 *
 * @property TypeQuery $typeQuery
 * @property ItemObjectQuery $itemObjectQuery
 *
 */
class GetterCache extends Getter
{
    use ModuleTrait;

    /**
     * @var Cacher
     */
    protected $cacher;

    function __construct($config = [])
    {
        $this->cacher = self::getModule()->cacher;

        parent::__construct($config);
    }

    /**
     * @param $name
     *
     * @return Item
     */
    public function getItemByTypeName($name)
    {
        $cacheKey = [__FUNCTION__, $name];

        return $this->cacher->getOrSet($cacheKey, function () use ($name) {
            return parent::getItemByTypeName($name);
        }, $this->cacher->tagTypeName($name));
    }

    public function getItemById($id, $typeId = null)
    {
        if (!$typeId)
        {
            return parent::getItemById($id, $typeId);
        }

        $cacheKey = [__FUNCTION__, $id];

        return $this->cacher->getOrSet($cacheKey, function () use ($id, $typeId) {
            return parent::getItemById($id, $typeId);
        }, $this->cacher->tagTypeId($typeId));
    }
}
