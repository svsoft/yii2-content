<?php

namespace svsoft\yii\content\models;

use svsoft\yii\content\traits\ModuleTrait;
use svsoft\yii\content\traits\TransactionTrait;
use yii\db\Query;

/**
 * This is the model class for table "{{%svs_content_value}}".
 *
 * @property integer $value_id
 * @property integer $item_id
 * @property integer $property_id
 * @property integer $value_item_id
 * @property string $value_string
 * @property string $value_text
 * @property integer $value_int
 * @property double $value_float
 * @property mixed $value
 *
 * @property Property $property
 * @property Item $item
 * @property Item $valueItem - Модель элемента для комплексного типа свойств
 */
abstract class Value extends \yii\db\ActiveRecord
{
    use ModuleTrait;
    use TransactionTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%svs_content_value}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            //[['item_id', 'property_id'], 'required'],
            [['item_id', 'property_id', 'value_item_id', 'value_int'], 'integer'],
            [['property_id'], 'exist', 'skipOnError' => true, 'targetClass' => Property::className(), 'targetAttribute' => ['property_id' => 'property_id']],
            //[['item_id'], 'exist', 'skipOnError' => true, 'targetClass' => Item::className(), 'targetAttribute' => ['item_id' => 'item_id']],
            ['property_id', 'unique', 'targetAttribute' => ['property_id', 'item_id'],
             'filter'=>function (Query $query){

                 $pt = Property::tableName();
                 $vt = self::tableName();

                 return $query->innerJoin($pt, "$pt.property_id = $vt.property_id AND $pt.multiple = 0");
             }
            ],
            ['item_id','valueValidator'],
        ];
    }

    /**
     * проверяет заполненость только одного поля значения
     *
     * @param $attribute
     */
    public function valueValidator($attribute)
    {
        $attributes = [];
        foreach($this->getAttributes() as $name=>$value)
        {
            if (strpos($name, 'value_') === 0)
                if ($name !== 'value_id')
                    $attributes[] = $name;

        }

        $count = 0;
        foreach($this->getAttributes($attributes) as $value)
        {
            if ($value !== null)
                $count ++;
        }

        if ($count > 1)
            $this->addError($attribute, 'Должно быть заполнено только одно поле значений у модели значение свойства');
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'value_id' => 'Value ID',
            'item_id' => 'Item ID',
            'property_id' => 'Property ID',
            'value_item_id' => 'Value Item ID',
            'value_string' => 'Value String',
            'value_text' => 'Value Text',
            'value_int' => 'Value Int',
            'value_float' => 'Value Float',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProperty()
    {
        return $this->hasOne(Property::className(), ['property_id' => 'property_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItem()
    {
        return $this->hasOne(Item::className(), ['item_id' => 'item_id']);
    }

    /**
     * @inheritdoc
     * @return ValueQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ValueQuery(get_called_class());
    }

    public function clearValueFields()
    {
        $this->value_item_id = null;
        $this->value_float = null;
        $this->value_int = null;
        $this->value_string = null;
        $this->value_text = null;
    }

    /**
     * Устанавливает значение в соответстующее поле
     *
     * @param $value
     */
    public function setValue($value)
    {
        if ($value!==null)
            $value = $this->prepareSetValue($value);

        $this->clearValueFields();
        $this->setAttribute($this->getValueField(), $value);
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->prepareGetValue($this->getAttribute($this->getValueField()));
    }

    /**
     * Получает перезаписанное значение
     *
     * @return mixed
     */
    public function getOldValue()
    {
        $field = $this->getValueField();
        return $this->getOldAttribute($field);
    }

    /**
     * Возращает поле хранения значения
     *
     * @return string
     */
    abstract public function getValueField();

    /**
     * Функция обработеи значения. Вызывается при установеи значения
     * @param $value
     *
     * @return mixed
     */
    abstract public function prepareSetValue($value);

    /**
     *
     * Функция обработеи значения. при получении из БД
     *
     * @param $value
     *
     * @return mixed
     */
    public function prepareGetValue($value)
    {
        return $value;
    }

    /**
     * Функция смены типа хранимого значения
     *
     * @param Type $newType
     *
     * @return boolean
     */
    abstract public function changeType(Type $newType);

    /**
     * Стандартное смена типа. Копированием значение из одного поле в другое
     *
     * @param Type $newType
     *
     * @return bool
     */
    protected function changeTypeBase(Type $newType)
    {
        // создаем пустой объек значения соответствующего класса
        $newValueModel = Value::createModel($newType->name);

        if ($this->getValueField() == $newValueModel->getValueField())
            return true;

        $newValueModel->setOldAttributes($this->getAttributes());
        $newValueModel->setAttributes($this->getAttributes());
        $newValueModel->value = $this->value;

        if (!$newValueModel->save())
            return false;

        return true;
    }

    /**
     * Переопределяем для создания моделей классов соответствующих типу
     *
     * @param array $row
     *
     * @return mixed
     */
    static function instantiate($row)
    {
        return self::createModel($row['type_name']);
    }

    /**
     * Создает модель значения по типу свойства
     *
     * @param $typeName
     *
     * @return Value
     */
    static function createModel($typeName)
    {
        $class = self::getModule()->getValueClass($typeName);

        return new $class();
    }
}
