<?php

namespace svsoft\yii\content\forms\import;

use svsoft\yii\content\models\Property;
use svsoft\yii\content\models\Type;
use svsoft\yii\content\traits\TransactionTrait;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * Class Type
 * @package svsoft\yii\content\forms\filter
 *
*/

/**
 * Class TypeImport
 * @package svsoft\yii\content\forms\import
 *
 * @property string $content
 * @property array $typesArray
 * @property Type[] $types
 * @property Property[] $properties
 */
abstract class TypeImport extends Model
{
    use TransactionTrait;

    /**
     * @var string;
     */
    public $content;

    public $contentFile;

    public $deleteProperties = true;

    /**
     * @var array
     */
    protected $_typesArray = [];

    /**
     * @var Type[]
     */
    protected $_types = [];

    protected $typeNameIndex = [];

    /**
     * @var Property[]
     */
    protected $_properties = [];

    function rules()
    {
        return [
            ['content','required'],
            ['content','typesArrayValidator'],
        ];
    }

    /**
     * Валидатор типов и свойст в виде массива
     *
     * @param $attribute
     */
    function typesArrayValidator($attribute)
    {
        $typeNames = [];
        foreach($this->typesArray as $typeArray)
        {

            $typeNames[] = ArrayHelper::getValue($typeArray, 'name');


            // Проверяем обязательность заполнеия элементов в массиве типов
            foreach(['name','properties'] as $typeKey=>$name)
            {
                if (empty($typeArray[$name]))
                {
                    $this->addError($attribute,"У типа {$typeKey} не заполнено обязательное поле «{$name}»");
                }

                // Проверяем обязательность заполнеия элементов в массиве свойств
                foreach($typeArray['properties'] as $propertyKey=>$propertyArray)
                {
                    foreach(['name','type'] as $name)
                    {
                        if (empty($propertyArray[$name]))
                        {

                            $this->addError($attribute, "У свойства {$propertyKey} типа {$typeKey} не заполнено обязательное поле «{$name}»");
                        }
                    }

                    $typeName = ArrayHelper::getValue($propertyArray, 'type');

                    if ($typeName)
                    {
                        if (!Type::find()->andName($typeName)->one() && !in_array($typeName, $typeNames))
                            $this->addError($attribute, "У свойства {$propertyKey} типа {$typeKey} тип «{$typeName}» не найден");
                    }

                }
            }
        }
    }

    /**
     * Валидирует модели типов и свойств
     */
    function validationTypes()
    {
        $valid = true;
        foreach($this->types as $typeKey=>$type)
        {
            $valid = $valid & $type->validate();

            /**
             * @var $property Property
             */
            foreach($this->getPropertiesByTypeKey($typeKey) as $propertyKey=>$property)
            {
                // Устанавливаем сценария для исключения проверки parent_type_id, т.к. у новых свойств он еще не заполнен
                $property->scenario = Property::SCENARIO_VALIDATION_WITHOUT_TYPE;
                $valid = $valid & $property->validate();
                $property->scenario = Property::SCENARIO_DEFAULT;
            }
        }

        return $valid;
    }

    function beforeValidate()
    {
        if (!parent::beforeValidate())
            return false;

        return $this->validationTypes();
    }

    /**
     * Заполняет типы и свойства. $this->_properties, $this->_types
     */
    protected function fillTypes()
    {
        foreach($this->typesArray as $typeKey=>$typeArray)
        {
            if (empty($typeArray['name']))
                continue;

            // получаем тип по названию, иначе создаем новую модель
            if (!$type = Type::find()->andName($typeArray['name'])->one())
                $type = new Type();

            $type->setAttributes($typeArray);
            $type->simple = 0;

            $this->_types[$typeKey] = $type;

            $this->typeNameIndex[$type->name] = $typeKey;

            foreach($typeArray['properties'] as $propertyKey=>$propertyArray)
            {
                if (empty($propertyArray['name']))
                    continue;

                $this->preparePropertyArray($propertyArray);

                // получаем свойство по названию, иначе создаем новую модель
                if (!$property = $type->getProperties()->andName($propertyArray['name'])->one())
                    $property = new Property();

                $property->setAttributes($propertyArray);

                $this->_properties[$typeKey][$propertyKey] = $property;
            }
        }
    }

    /**
     * Преобразует массив свойства
     *
     * @param $propertyArray
     */
    protected function preparePropertyArray(&$propertyArray)
    {
        // Получаем type_id по названию типа
        if (isset($propertyArray['type']))
        {
            $propertyArray['type_id'] = null;
            if ($type = Type::find()->andName($propertyArray['type'])->one())
                $propertyArray['type_id'] = $type->type_id;
        }
    }

    public function getTypesArray()
    {
        return $this->_typesArray;
    }

    public function getTypes()
    {
        return $this->_types;
    }

    /**
     * @return Type
     */
    public function getTypeByKey($key)
    {
        return ArrayHelper::getValue($this->types, $key);
    }

    public function getProperties()
    {
        return $this->_properties;
    }

    /**
     * Возвращает массив свойсвт которые будут удалены
     *
     * @param $typeKey
     *
     * @return \svsoft\yii\content\models\Property[]|array
     */
    public function getPropertiesForDeleteByKey($typeKey)
    {
        $properties = $this->getPropertiesByTypeKey($typeKey);
        $type = $this->getTypeByKey($typeKey);
        if (!$type->type_id)
            return [];

        $propertiesForDelete = $type->properties;
        foreach($properties as $propertyKey=>$property)
        {
            $propertyId = $property->property_id;

            if (isset($propertiesForDelete[$propertyId]))
                unset($propertiesForDelete[$propertyId]);
        }

        return $propertiesForDelete;
    }

    /**
     * @return mixed
     */
    abstract protected function parseContent($content);

    /**
     * Заполняет модели типов и свойств
     */
    public function read()
    {
        if ($this->content)
        {
            $this->_typesArray = $this->parseContent($this->content);
            $this->fillTypes();
        }

        // Todo: Убрать валидацию
        $this->validate();

    }

    /**
     * @param $key - ключ в массиве $this->properties
     *
     * @return Property[]
     */
    public function getPropertiesByTypeKey($key)
    {
        return ArrayHelper::getValue($this->properties, $key, []);
    }

    /**
     * Сохраняет типы и свойства
     *
     * @return bool
     */
    public function save()
    {
        if (!$this->validate())
            return false;

        $this->beginTransaction();

        foreach($this->types as $typeKey=>$type)
        {
            // Сохраняем тип
            if (!$type->save())
                return $this->rollBackTransaction();

            // Сохраняем свойства
            foreach($this->getPropertiesByTypeKey($typeKey) as $propertyKey=>$property)
            {
                // Если не заполнен тип свойства, а такое может быть если тип свойства передан вы этом же массиве
                // Заполняем type_id
                if (!$property->type_id)
                {
                    $propertyArray = $this->typesArray[$typeKey]['properties'][$propertyKey];

                    $index = $this->typeNameIndex[$propertyArray['type']];

                    $propertyType = $this->types[$index];

                    $property->type_id = $propertyType->type_id;
                }

                $property->parent_type_id = $type->type_id;

                $property->scenario = Property::SCENARIO_DEFAULT;
                if (!$property->save())
                    return $this->rollBackTransaction();
            }

            // Удаялем свойства которые не переданы
            foreach($this->getPropertiesForDeleteByKey($typeKey) as $property)
                if (!$property->delete())
                    return $this->rollBackTransaction();
        }

        $this->commitTransaction();

        return true;
    }
}