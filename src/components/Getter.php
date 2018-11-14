<?php
namespace svsoft\yii\content\components;

use svsoft\yii\content\components\display\Item;
use svsoft\yii\content\models\ItemObject;
use svsoft\yii\content\models\ItemObjectQuery;
use svsoft\yii\content\models\Type;
use svsoft\yii\content\models\TypeQuery;
use yii\base\Component;
use yii\base\Exception;
use yii\helpers\ArrayHelper;

/**
 * Class Content
 * @package svsoft\yii\content\components
 *
 * @property TypeQuery $typeQuery
 * @property ItemObjectQuery $itemObjectQuery
 *
 */
class Getter extends Component
{
    /**
     * @var TypeQuery
     */
    protected $_typeQuery;

    /**
     * @var ItemObjectQuery
     */
    protected $_itemObjectQuery;

    /**
     * Получает модель ItemObject по ИД
     *
     * @param $id
     *
     * @return $this
     */
    function getItemObjectById($id)
    {
        return $this->itemObjectQuery->andId($id)->one();
    }

    /**
     * @param $name
     *
     * @return ItemObject|array|null
     */
    function getItemObjectBySlug($name)
    {
        return $this->itemObjectQuery->andSlug($name)->one();
    }

    /**
     * @param array $id
     *
     * @return \svsoft\yii\content\models\ItemObject[]|array
     */
    function getItemsObjectById($id)
    {
        return $this->itemObjectQuery->andId($id)->all();
    }

    /**
     * Получает модели ItemObject по названию типа
     *
     * @param $name
     *
     * @return \svsoft\yii\content\models\ItemObject[]|array
     */
    function getItemObjectsByTypeName($name)
    {
        $type = $this->getTypeByName($name);

        if (!$type)
            return [];

        $query = $this->itemObjectQuery;

        return $query->andTypeId($type->type_id)->all();
    }

    /**
     * TODO: не оптимально выбирается элемент, ограничить выборку один элементом
     * Получает объект ItemObject по названия типа
     *
     * @param $name
     *
     * @return \svsoft\yii\content\models\ItemObject
     */
    function getItemObjectByTypeName($name)
    {
        return ArrayHelper::getValue($this->getItemObjectsByTypeName($name), 0);
    }

    /**
     * получает объект Item по Ид
     *
     * @param $id
     *
     * @return Item
     */
    function getItemById($id)
    {
        return $this->getResultItemModel($this->getItemObjectById($id));
    }

    function getItemBySlug($name)
    {
        return $this->getResultItemModel($this->getItemObjectBySlug($name));
    }

    /**
     * Получает объекты Item по названия типа
     *
     * @param $name
     *
     * @return Item[]|array
     */
    function getItemsByTypeName($name)
    {
        return $this->getResultItemModels($this->getItemObjectsByTypeName($name));
    }


    /**
     * Получает объект Item по названия типа
     *
     * @param $name
     *
     * @return Item
     */
    function getItemByTypeName($name)
    {
        return $this->getResultItemModel($this->getItemObjectByTypeName($name));
    }

    /**
     * Преобразует модели ItemObject в объекты Item
     *
     * @param ItemObject[] $items
     *
     * @return Item[]
     */
    protected function getResultItemModels($items)
    {
        $resultItems = [];
        foreach($items as $key=>$item)
        {
            $resultItems[$key] = $this->getResultItemModel($item);
        }

        return $resultItems;
    }

    /**
     * Преобразует модель ItemObject в объект Item
     *
     * @param ItemObject $itemObject
     *
     * @return Item|null
     */
    protected function getResultItemModel($itemObject)
    {
        if (!$itemObject)
            return null;

        return Item::instance($itemObject);
    }

    /**
     * Получает модель Type по названию
     *
     * @param $name
     *
     * @return Type|array|null
     */
    function getTypeByName($name)
    {
        $query = $this->typeQuery;
        return $query->andName($name)->one();
    }

    /**
     * Возвращает клон объекта запроса TypeQuery
     *
     * @return TypeQuery
     */
    function getTypeQuery()
    {
        if ($this->_typeQuery === null)
        {
            $this->_typeQuery = Type::find();
        }

        return clone $this->_typeQuery;
    }

    /**
     * Возвращает клон объекта запроса ItemObjectQuery
     *
     * @return ItemObjectQuery
     */
    function getItemObjectQuery()
    {
        if ($this->_itemObjectQuery === null)
        {
            $this->_itemObjectQuery = ItemObject::find();
        }

        return clone $this->_itemObjectQuery;
    }

    /**
     * Возвращает клон объекта запроса ItemObjectQuery c фильтром по названию типа
     *
     * @param $typeName
     *
     * @return ItemObjectQuery
     * @throws Exception
     */
    function getItemObjectQueryFilterTypeName($typeName)
    {
        $type = $this->getTypeByName($typeName);

        if (!$type)
            throw new Exception('Type '.$typeName.' is not found');

        return $this->itemObjectQuery->andTypeId($type->type_id);
    }


    /**
     * Получает объекты Item по обхекту запроса  ItemObjectQuery
     *
     * @param ItemObjectQuery $query
     *
     * @return display\Item[]
     */
    function getItemsByQuery(ItemObjectQuery $query)
    {
        return $this->getResultItemModels($query->all());
    }
}
