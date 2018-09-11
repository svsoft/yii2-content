<?php

namespace svsoft\yii\content\models;

use svsoft\yii\content\traits\ModuleTrait;
use yii\helpers\ArrayHelper;

/**
 * Class Type
 * @package svsoft\yii\content\models
 *
 * @property int $item_id
 * @property Type $type
 * @property string $name
 * @property string $slug
 * @property Item $parentItem
 *
 * @property ItemProperty[] $itemProperties
 */
class ItemObject extends Item
{
    use ModuleTrait;

    /**
     * @var ItemProperty[]
     */
    private $_itemProperties;

    public $parentItemId;

    public $parentPropertyId;

    protected $indexByName = [];

    function rules()
    {
        $rules = parent::rules();
        $rules[] = ['itemProperties','propertiesValidator'];

        return $rules;
    }

    protected function getProperties()
    {
        return $this->type->getProperties()->with('type')->all();
    }

    /**
     * @return ItemProperty[]
     */
    public function getItemProperties()
    {
        if ($this->_itemProperties === null)
        {
            $properties = $this->getProperties();
            $values = $this->values;

            // Создаем ItemProperty и заполняем свойства
            foreach($properties as $property)
            {
                // Заполняем индекс по названию
                $this->indexByName[$property->name] = $property->property_id;

                /**
                 * @var $itemProperty ItemProperty
                 */
                $itemProperty = \Yii::createObject([
                    'class' => ItemProperty::className(),
                    'property'=>$property,
                    'item' => $this
                ]);

                $this->_itemProperties[$property->property_id] = $itemProperty;
            }

            // Заполняем значениями
            foreach($values as $value)
            {
                $propertyId = $value->property_id;

                $itemProperty = $this->getItemProperty($propertyId);

                $itemProperty->valueModels[] = $value;
            }
        }

        return $this->_itemProperties;
    }

    /**
     * Получить свойство элемента по Ид
     *
     * @param $id
     *
     * @return ItemProperty
     */
    public function getItemProperty($id)
    {
        return ArrayHelper::getValue($this->getItemProperties(), $id);
    }

    /**
     * Получает itemProperty по Ид
     *
     * @param $name
     *
     * @return ItemProperty
     */
    public function getItemPropertyByName($name)
    {
        $itemProperties = $this->getItemProperties();

        if (!$propertyId = ArrayHelper::getValue($this->indexByName, $name))
            return null;

        return ArrayHelper::getValue($itemProperties, $propertyId);
    }

    /**
     * @inheritdoc
     * @return ItemObjectQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ItemObjectQuery(get_called_class());
    }

    /**
     * Переопределяем для загрузки во воложенные объекты itemProperties
     * @param array $data
     * @param null $formName
     *
     * @return bool|int
     */
    public function load($data, $formName = null)
    {
        $load = parent::load($data, $formName);

        foreach($this->getItemProperties() as $item)
        {
            $load = $load | $item->load($data);
        }

        return $load;
    }

//    /**
//     * @return bool
//     */
//    public function validateItemProperties()
//    {
//        $valid = true;
//        foreach($this->getItemProperties() as $item)
//        {
//            $valid = $valid && $item->validate();
//        }
//
//        return $valid;
//    }

    public function propertiesValidator($attribute)
    {
        foreach($this->getItemProperties() as $item)
        {
            if (!$item->validate())
            {
                $this->addError($attribute, $item->getFirstError('value'));
            }

        }
    }

    /**
     * @param bool $runValidation
     * @param null $attributeNames
     *
     * @return bool
     */
    public function save($runValidation = true, $attributeNames = null)
    {
        $this->beginTransaction();

        if (!parent::save())
            return $this->rollBackTransaction();

//        // Валидируем значения свойств
//        if (!$this->validateItemProperties())
//            return $this->rollBackTransaction();

        // Сохраняем все свойства
        foreach($this->getItemProperties() as $itemProperty)
        {
            if (!$itemProperty->save(false))
                return $this->rollBackTransaction();
        }

        // Сохраняем ИД элемента в значение родительского свойства
        if ($this->parentItemId && $this->parentPropertyId)
        {
            $parentModel = self::findOne($this->parentItemId);

            $itemProperty = $parentModel->getItemProperty($this->parentPropertyId);

            $itemProperty->loadValues([$this->item_id]);

            if (!$parentModel->save())
            {
                return $this->rollBackTransaction();
            }
        }

        $this->commitTransaction();
        return true;
    }

    /**
     * Получет элементы к которым привязан текущий элемент
     * Todo: Пока не используется, возможно удалить 03.03.2018
     *
     * @return ItemObject[]|array
     *
     */
    function getParentItems()
    {
        $valueModels = Value::find()->andValueItemId($this->item_id)->all();

        $itemId = [];
        foreach($valueModels as $valueModel)
        {
            $itemId[] = $valueModel->item_id;
        }

        if (!$itemId)
            return [];

        return ItemObject::find()->andId($itemId)->all();
    }
}
