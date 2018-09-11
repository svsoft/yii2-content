<?php

namespace svsoft\yii\content\models;

use svsoft\yii\content\components\validators\NameValidator;
use svsoft\yii\content\traits\ModuleTrait;
use svsoft\yii\content\traits\TransactionTrait;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%svs_content_property}}".
 *
 * @property integer $property_id
 * @property string $name
 * @property string $label
 * @property integer $parent_type_id
 * @property integer $multiple
 * @property integer $type_id
 *
 * @property Type $type
 * @property Type $parentType
 * @property Value[] $values
 */
class Property extends \yii\db\ActiveRecord
{
    use TransactionTrait;
    use ModuleTrait;

    const EVENT_BEFORE_CHANGE_TYPE = 'beforeChangeType';

    const EVENT_AFTER_CHANGE_TYPE = 'afterChangeType';

    /**
     * Не валидирует родительский тип(parent_type_id) и тип свойства(type_id)
     */
    const SCENARIO_VALIDATION_WITHOUT_TYPE = 'validationWithoutType';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%svs_content_property}}';
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();

        // Добавляем сценарий для валидации без parent_type_id
        $scenarios[self::SCENARIO_VALIDATION_WITHOUT_TYPE] = $scenarios[self::SCENARIO_DEFAULT];

        ArrayHelper::removeValue($scenarios[self::SCENARIO_VALIDATION_WITHOUT_TYPE], 'parent_type_id');
        ArrayHelper::removeValue($scenarios[self::SCENARIO_VALIDATION_WITHOUT_TYPE], 'type_id');

        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'label', 'parent_type_id', 'type_id'], 'required'],
            [['parent_type_id', 'type_id'], 'integer'],
            ['multiple', 'boolean'],
            [['name','label'], 'string', 'max' => 255],
            [
                ['type_id'],
                'exist',
                'skipOnError'     => true,
                'targetClass'     => Type::className(),
                'targetAttribute' => ['type_id' => 'type_id']
            ],
            [
                ['parent_type_id'],
                'exist',
                'skipOnError'     => true,
                'targetClass'     => Type::className(),
                'targetAttribute' => ['parent_type_id' => 'type_id'],
            ],
            ['multiple', 'default', 'value' => false],
            [['name','label'],'unique', 'filter'=>function(PropertyQuery $query){ return $query->andParentTypeId($this->parent_type_id); }],
            ['name', NameValidator::className()],
            ['name', 'in', 'not'=>true, 'range'=>['name','slug','item_id','active','sort'], 'message'=>'Не может иметь значения: name, slug, item_id, active, sort']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'property_id'      => 'Property ID',
            'name'             => 'Название',
            'label'             => 'Подпись',
            'parent_type_id' => 'Родительский тип',
            'type_id'        => 'Тип',
            'multiple'         => 'Множественное',
            'type.name'             => 'Тип',
        ];
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
    public function getParentType()
    {
        return $this->hasOne(Type::className(), ['type_id' => 'parent_type_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getValues()
    {
        return $this->hasMany(Value::className(), ['property_id' => 'property_id']);
    }

    /**
     * @inheritdoc
     * @return PropertyQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new PropertyQuery(get_called_class());
    }

    /**
     * @param $type string - проверка по названию
     *
     * @return bool
     */
    public function checkType($type)
    {
        return $this->type->name == $type;
    }

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert))
            return false;

        $this->beginTransaction();

        if (!$insert)
        {
            if ($this->type_id != $this->getOldAttribute('type_id'))
            {
                if (!$this->beforeChangeType())
                    return false;
            }

            if (!$this->multiple && $this->getOldAttribute('multiple'))
            {
                if (!$this->beforeChangeMultiple())
                    return false;
            }
        }

        return true;
    }

    public function afterSave($insert, $changedAttributes)
    {
        self::getModule()->cacher->cleanByProperty($this);

        parent::afterSave($insert, $changedAttributes);

        $this->commitTransaction();
    }

    protected function beforeChangeMultiple()
    {
        if ($this->multiple)
            return true;

        $i=0;
        // Удаляем все элементы кроме первого
        foreach($this->values as $valueModel)
        {
            $i++;
            if ($i === 1)
                continue;

            if (!$valueModel->delete())
            {
                $this->rollBackTransaction();
                return false;
            }
        }

        return true;
    }

    protected function beforeChangeType()
    {
        $newType = Type::findOne($this->type_id);

        // Пересохраняем значения в новые поле у моделей значений
        foreach($this->values as $valueModel)
        {
            if (!$valueModel->changeType($newType))
                return $this->rollBackTransaction();
        }

        return true;
    }

    public function beforeDelete()
    {
        if (!parent::beforeDelete())
            return false;

        $this->beginTransaction();

        // Удаляем все привязанные к свойству значения
        foreach($this->values as $value)
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
        self::getModule()->cacher->cleanByProperty($this);

        parent::afterDelete();

        $this->commitTransaction();
    }

    public function getTypeName()
    {
        if (!$this->type)
            return null;

        return $this->type->name;
    }

}
