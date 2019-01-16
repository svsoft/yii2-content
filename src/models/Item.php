<?php

namespace svsoft\yii\content\models;

use svsoft\yii\content\traits\ModuleTrait;
use svsoft\yii\content\traits\TransactionTrait;

/**
 * This is the model class for table "{{%svs_content_item}}".
 *
 * @property integer $item_id
 * @property integer $type_id
 * @property string $name
 * @property string $slug
 * @property integer $sort
 * @property bool $active
 *
 * @property Type $type
 * @property Value[] $values
 * @property Value[] $itemValues - модели значений которые ссылаются на $this
 */
class Item extends \yii\db\ActiveRecord
{
    use TransactionTrait;
    use ModuleTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%svs_content_item}}';
    }

    public function init()
    {
        $this->sort = 1000;
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type_id', 'sort'], 'required'],
            [['type_id', 'sort'], 'integer'],
            ['active', 'boolean'],
            [['name', 'slug'], 'string', 'max' => 255],
            //['name', NameValidator::className()],
            [['name'], 'unique', 'filter'=>function(ItemQuery $query){ return $query->andTypeId($this->type_id); }],
            ['slug', 'unique'],
            [['type_id'], 'exist', 'skipOnError' => true, 'targetClass' => Type::className(), 'targetAttribute' => ['type_id' => 'type_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'item_id' => 'Ид',
            'type_id' => 'Тип',
            'name' => 'Наименование',
            'slug' => 'Код',
            'sort' => 'Сортировка',
            'active' => 'Активность',
            'type.name' =>  'Тип'
        ];
    }

    public function beforeDelete()
    {
        if (!parent::beforeDelete())
            return false;

        $this->beginTransaction();

        // Удаляем все привязанные значения
        foreach($this->values as $value)
        {
            if (!$value->delete())
            {
                $this->rollBackTransaction();
                return false;
            }
        }

        // Удаляем все значения у которых $this является значением
        foreach($this->itemValues as $value)
        {
            if (!$value->delete())
            {
                $this->rollBackTransaction();
                return false;
            }
        }

        return true;
    }

    public function afterDelete()
    {
        self::getModule()->cacher->cleanByItem($this);
        $this->commitTransaction();
        parent::afterDelete();
    }

    public function afterSave($insert, $changedAttributes)
    {
        self::getModule()->cacher->cleanByItem($this);
        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getType()
    {
        return $this->hasOne(Type::className(), ['type_id' => 'type_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getValues()
    {
        return $this->hasMany(Value::className(), ['item_id' => 'item_id'])->inverseOf('item');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItemValues()
    {
        return $this->hasMany(Value::className(), ['value_item_id' => 'item_id'])->inverseOf('valueItem');
    }

    /**
     * @inheritdoc
     * @return ItemQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ItemQuery(get_called_class());
    }

    /**
     * Возвращает название типа
     *
     * @return null|string
     */
    public function getTypeName()
    {
        if (!$this->type)
            return null;

        return $this->type->name;
    }
}
