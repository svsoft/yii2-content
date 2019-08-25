<?php

namespace svsoft\yii\content\forms\filter;

use svsoft\yii\content\models\ItemObjectQuery;
use svsoft\yii\content\models\Property;
use svsoft\yii\content\models\Type;
use svsoft\yii\content\models\Value;
use yii\base\Exception;
use yii\base\Model;

/**
 * Class Type
 * @package svsoft\yii\content\forms\filter
 *
 */
class PropertyFilter extends Model
{

    protected $itemsConfig = [];

    /**
     * Запрос на получение элементов
     *
     * @var ItemObjectQuery
     */
    public $query;

    /**
     * @var PropertyFilterItem[]
     */
    private $_items;

    /**
     * @var Type
     */
    private $type;

    function __construct(ItemObjectQuery $query, Type $type, $itemsConfig = [], $config = [])
    {
        $this->query = $query;

        $this->type = $type;

        // Нормализуем формат массива
        $this->itemsConfig = $this->normalizeItemsConfig($itemsConfig);

        $itemsConfig = $this->itemsConfig;

        // получаем ид элементов
        $itemIds = $this->getItemIds();

        // Получаем значения свойств элементов сгруппированные по совойствам
        $valuesGroup = $this->getValuesGroupByPropertyId($itemIds, array_keys($this->getProperties()));

        // Заполняем значения
        foreach($itemsConfig as $itemConfig)
        {
            /**
             * @var $property Property
             */
            $property = $itemConfig['property'];

            $propertyId = $property->property_id;

            $values = $valuesGroup[$propertyId];
            $itemConfig['allValues'] = $values;

            $this->_items[] = \Yii::createObject($itemConfig, [$this]);
        }

        parent::__construct($config);
    }

    /**
     * Приводит itemsConfig к единому формату. Получает модели Property, там где указа Ид или Название
     *
     * @param $itemsConfig
     *
     * @return mixed
     */
    protected function normalizeItemsConfig($itemsConfig)
    {
        foreach($itemsConfig as $key=>&$item)
        {
            if (!is_array($item))
                $item = ['property'=>$item];

            // Если не задон ключ property удаляем этот элемент из массива
            if (!array_key_exists('property', $item))
            {
                unset($itemsConfig[$key]);
                continue;
            }

            // Если не задан ключ class Устанавливаем класс PropertyFilterItemList
            if (empty($item['class']))
                $item['class'] = PropertyFilterItemList::className();
        }
        unset($item);

        // Получаем свойства по названиям и ид
        $names = [];
        $ids = [];
        foreach($itemsConfig as $key=>$item)
        {
            $property = $item['property'];

            if (is_string($property))
                $names[$property] = $key;
            elseif (is_numeric($property))
                $ids[$property] = $key;
        }

        $propertyQuery = Property::find()
            ->andParentTypeId($this->type->type_id)
            ->indexBy('property_id');

        if ($names)
        {
            $q = clone $propertyQuery;
            $properties = $q
                ->andName(array_keys($names))
                ->all();

            foreach($properties as $property)
            {
                $itemIndex = $names[$property->name];
                $itemsConfig[$itemIndex]['property'] = $property;
            }
        }

        if ($ids)
        {
            $q = clone $propertyQuery;
            $properties = $q
                ->andPropertyId($ids)
                ->all();

            foreach($properties as $property)
            {
                $itemIndex = $names[$property->property_id];
                $itemsConfig[$itemIndex]['property'] = $property;
            }
        }

        // Удаляем все элементы из конфига для которых не получены свойтсва
        foreach($itemsConfig as $key=>$item)
        {
            if (!is_object($item['property']))
                unset($itemsConfig[$key]);
        }

        return $itemsConfig;
    }

    /**
     * @return array
     */
    protected function  getItemIds()
    {
        $query = clone $this->query;

        $query->select(['item_id','type_id'])
            ->indexBy('item_id')
            ->asArray();

        return array_keys($query->all());
    }

    /**
     * @return \svsoft\yii\content\models\Property[]|array
     * @throws Exception
     */
    protected function getProperties()
    {
        $properties = [];
        foreach($this->itemsConfig as $item)
        {
            /**
             * @var $property Property
             */
            $property = $item['property'];

            $properties[$property->property_id] = $property;
        }


        return $properties;
    }

    /**
     * @return array
     */
    protected function getPropertyIds()
    {
        return array_keys($this->getProperties());
    }

    protected function getValuesGroupByPropertyId($itemIds, $propertyIds)
    {
        $queryValue = Value::find()->andItemId($itemIds)->andPropertyId($propertyIds);
        $valueModels = $queryValue->all();

        $valuesGroupByPropertyId = [];
        foreach($valueModels as $valueModel)
        {
            $propertyId = $valueModel->property_id;
            $valuesGroupByPropertyId[$propertyId][$valueModel->value] = $valueModel->value;
        }

        foreach($propertyIds as $id)
        {
            if (empty($valuesGroupByPropertyId[$id]))
                $valuesGroupByPropertyId[$id] = [];
        }

        return $valuesGroupByPropertyId;
    }

    /**
     * @return PropertyFilterItem[]
     */
    public function getItems()
    {
        return $this->_items;
    }

    public function load($data, $formName = null)
    {
        $load = false;
        foreach($this->_items as $item)
            $load = $load | $item->load($data);

        return $load | parent::load($data, $formName);
    }

    public function validate($attributeNames = null, $clearErrors = true)
    {
        $valid = true;
        foreach($this->_items as $item)
            $valid = $valid && $item->validate();

        $valid = $valid & parent::validate($attributeNames, $clearErrors);

        return $valid;
    }

    public function filter()
    {
        if (!$this->validate())
            return false;

        foreach($this->_items as $item)
            $item->filter();

        return true;
    }
}

