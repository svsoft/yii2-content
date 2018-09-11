<?php

namespace svsoft\yii\content\models;

use svsoft\yii\content\components\validators\NameValidator;
use svsoft\yii\content\traits\ModuleTrait;
use svsoft\yii\content\traits\TransactionTrait;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%svs_content_type}}".
 *
 * @property integer $type_id
 * @property string $name
 * @property integer $simple
 * @property string $classname
 * @property string $value_field
 * @property string $label
 *
 * @property Property[] $properties - дочернии свойства типа
 * @property Property[] $typeProperties - все свойства этого типа
 * @property ItemObject[] $items - элементы этого типа
 */
class Type extends \yii\db\ActiveRecord
{
    use ModuleTrait;
    use TransactionTrait;

    const TYPE_STRING   = 'string';
    const TYPE_FLOAT    = 'float';
    const TYPE_FILE     = 'file';
    const TYPE_TEXT     = 'text';
    const TYPE_HTML     = 'html';
    const TYPE_INT      = 'int';
    const TYPE_ITEM     = 'item';
    const TYPE_DATE     = 'date';
    const TYPE_DATETIME = 'datetime';
    const TYPE_BOOLEAN = 'boolean';

    const FIELD_ITEM_ID = 'value_item_id';
    const FIELD_STRING  = 'value_string';
    const FIELD_TEXT    = 'value_text';
    const FIELD_INT     = 'value_int';
    const FIELD_FLOAT   = 'value_float';
    const FIELD_DATE    = 'value_date';
    const FIELD_DATETIME  = 'value_date';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%svs_content_type}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name','label'], 'required'],
            [['simple'], 'boolean'],
            [['name', 'classname','label'], 'string', 'max' => 255],
            ['simple', 'default', 'value'=>false],
            ['value_field', 'default', 'value'=>'value_item_id'],
            [['name', 'label'],'unique'],
            ['name', NameValidator::className()]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'type_id'       => 'Type ID',
            'name'          => 'Название',
            'label'          => 'Подпись',
            'simple'        => 'Простой',
            'classname'     => 'Имя класса',
            'value_field'   => 'Поле для хранения значения'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTypeProperties()
    {
        return $this->hasMany(Property::className(), ['type_id' => 'type_id'])->indexBy('property_id');
    }

    /**
     * @return PropertyQuery
     */
    public function getProperties()
    {
        return $this->hasMany(Property::className(), ['parent_type_id' => 'type_id'])->indexBy('property_id');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItems()
    {
        return $this->hasMany(ItemObject::className(), ['type_id' => 'type_id']);
    }

    /**
     * @inheritdoc
     * @return TypeQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TypeQuery(get_called_class());
    }

    public function getPropertyByName($name)
    {
        foreach($this->properties as $property)
        {
            if ($property->name == $name)
                return $property;
        }

        return null;
    }

    public function getPropertyIdByName($name)
    {
        if ($property = $this->getPropertyByName($name))
            return $property->property_id;

        return null;
    }

    /**
     * Получает свойство по ID
     *
     * @param $propertyId
     *
     * @return null|Property
     */
    public function getPropertyById($propertyId)
    {
        return ArrayHelper::getValue($this->properties, $propertyId);
    }

    static public function getTypeList($simple = false)
    {
        $types = Type::find()->active()->andSimple($simple)->select(['type_id', 'name'])->asArray()->all();

        return ArrayHelper::map($types, 'type_id', 'name');
    }

    public function afterSave($insert, $changedAttributes)
    {
        self::getModule()->cacher->cleanByType($this);

        parent::afterSave($insert, $changedAttributes);
    }

    public function beforeDelete()
    {
        if (!parent::beforeDelete())
            return false;

        if($this->simple)
            return true;

        $this->beginTransaction();

        // Удаляем все свойства привязанные к типу
        foreach($this->properties as $property)
        {
            if (!$property->delete())
            {
                $this->rollBackTransaction();
                return false;
            }
        }

        // Удаляем все свойства этого типа в других типах
        foreach($this->typeProperties as $property)
        {
            if (!$property->delete())
            {
                $this->rollBackTransaction();
                return false;
            }
        }

        // Удаляем все элементы этого типа
        foreach($this->items as $item)
        {
            if (!$item->delete())
            {
                $this->rollBackTransaction();
                return false;
            }
        }

        return true;
    }

    public function afterDelete()
    {
        $this->commitTransaction();
        parent::afterDelete();
    }

    /**
     * Возвращает массив объекта со свойствами
     *
     * @return array
     */
    public function toArrayWithProperties()
    {
        $typeArray = $this->toArray();

        foreach($this->properties as $property)
        {
            $typeArray['properties'][] = $property->toArray();
        }

        return $typeArray;
    }
}
