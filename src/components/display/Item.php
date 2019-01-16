<?php

namespace svsoft\yii\content\components\display;

use svsoft\yii\content\models\ItemObject;
use svsoft\yii\content\traits\ModuleTrait;
use yii\base\BaseObject;
use yii\helpers\ArrayHelper;

/**
 * Class Type
 * @package svsoft\yii\content\models
 *
 * @property int $item_id
 * @property string $name
 * @property string $label
 */
class Item extends BaseObject
{
    use ModuleTrait;

    /**
     * @var Property[]
     */
    protected $properties = [];

    protected $indexByName = [];

    public $item_id;

    public $name;

    public $slug;

    public $sort;

    public $active;

    /**
     * ItemFrom constructor.
     *
     * @param ItemObject|int $contentItem
     * @param array $config
     *
     */
    public function __construct(ItemObject $contentItem,  $config = [])
    {
        parent::__construct($config);

        $this->load($contentItem);
    }

    protected function load(ItemObject $contentItem)
    {
        $this->item_id = $contentItem->item_id;
        $this->name = $contentItem->name;
        $this->slug = $contentItem->slug;

        foreach($contentItem->getItemProperties() as $itemProperty)
        {
            $propertyId = $itemProperty->property->property_id;
            $this->indexByName[$itemProperty->property->name] = $propertyId;

            $class = self::getModule()->getDisplayPropertyClass($itemProperty->property->getTypeName());

            $this->properties[$propertyId] = \Yii::createObject(['class'=>$class], [$itemProperty]);
        }
    }

    /**
     * Получает сначение свойства по названию
     *
     * @param $name
     * @param bool $encode
     *
     * @return array|mixed|null|Item[]|Item
     */
    public function byName($name, $encode = true)
    {
        if (!$propertyId = ArrayHelper::getValue($this->indexByName, $name))
            return null;

        return $this->byId($propertyId, $encode);
    }

    /**
     * Получает сначение свойства по Ид
     *
     * @param $id
     * @param bool $encode
     *
     * @return Property
     */
    public function byId($id, $encode = true)
    {
        if (!$property = $this->getPropertyById($id))
            return null;

        return $property->getValue($encode);
    }

    /**
     * Получает список свойств по названиям
     *
     * @param $names
     *
     * @return Property[]
     */
    public function getPropertiesByNames($names)
    {
        $properties = [];
        foreach($names as $name)
        {
            $propertyId = ArrayHelper::getValue($this->indexByName, $name);

            if (!$propertyId)
                continue;

            if (!$property = ArrayHelper::getValue($this->properties, $propertyId))
                continue;

            $properties[$propertyId] = $property;
        }

        return $properties;
    }

    /**
     * @param $propertyId
     *
     * @return Property
     */
    public function getPropertyById($propertyId)
    {
        return ArrayHelper::getValue($this->properties, $propertyId);
    }

    /**
     * Возвращает модель Property по названию
     *
     * @param $name
     *
     * @return Property
     */
    public function getPropertyByName($name)
    {
        $properties = $this->getPropertiesByNames([$name]);
        if (!$properties)
            return null;

        reset($properties);

        return current($properties);

    }

    public function  getProperties()
    {
        return $this->properties;
    }

    /**
     * Возвращает экземпляр класса по модели ItemObject
     *
     * @param ItemObject $itemObject
     *
     * @return static
     */
    static public function instance(ItemObject $itemObject)
    {
        return new static($itemObject);
    }

    /**
     * Возвращает экземпляры класса по моделям ItemObject
     *
     * @param $itemObjects ItemObject[]
     *
     * @return static
     */
    static public function instances($itemObjects)
    {
        $items = [];
        foreach($itemObjects as $itemObject)
            $items = new static($itemObject);

        return $items;
    }
}
