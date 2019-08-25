<?php

namespace svsoft\yii\content\models;

use svsoft\yii\content\traits\ModuleTrait;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * Class ItemProperty
 * @package svsoft\yii\content\models
 * @property $value
 */
class ItemProperty extends Model
{
    use ModuleTrait;

    /**
     * @var Value[]
     */
    public $valueModels = [];

    /**
     * @var Property
     */
    public $property;

    /**
     * @var ItemObject
     */
    public $item;

    private $_value;

    function rules()
    {
        return [
            ['value', 'valueValidator']
        ];
    }

    //abstract function valueRule();

    public function beforeValidate()
    {
        if (!parent::beforeValidate())
            return false;

        $this->loadValueModels($this->getValues());

        return true;
    }

    public function valueValidator($attribute)
    {
        foreach ($this->valueModels as $key => $valueModel)
        {
            if (!$valueModel->validate())
            {
                $this->addError($attribute, $valueModel->getFirstError('value'));
            }
        }
    }

    public function attributeLabels()
    {
        return [
            'value' => $this->property->label,
        ];
    }

    /**
     * @return Value
     */
    public function getValueModel($key = 0)
    {
        return ArrayHelper::getValue($this->valueModels, $key);
    }

    /**
     * Получает массив значений
     */
    public function getValue()
    {
        if ($this->_value !== null)
            return $this->_value;

        $values = [];
        foreach ($this->valueModels as $key => $valueModel)
        {
            $values[$key] = $valueModel->value;
        }

        if (!$this->property->multiple)
        {
            return ArrayHelper::getValue($values, 0);
        }

        return $values;
    }

    public function createValueModel()
    {
        $model = Value::createModel($this->property->getTypeName());

        $model->item_id = $this->item->item_id;
        $model->property_id = $this->property->property_id;

        return $model;
    }

    /**
     * Загружает переданное значение в модель значение свойства
     *
     * @param \svsoft\yii\content\models\Value $valueModel
     * @param $value
     */
    protected function loadValueModel(Value $valueModel, $value)
    {
        $valueModel->value = $value;
    }

    /**
     * Загружает переданные значения в массив valueModels
     *
     * @param $values
     */
    protected function loadValueModels($values)
    {
        if (!\is_array($values))
        {
            return;
        }

        // Устанавливаем $valueModel->value null для всех не переданых, для дальнейшего удаления
        foreach ($this->valueModels as $key => $valueModel)
        {
            if (!array_key_exists($key, $values))
            {
                $values[$key] = null;
            }
        }

        foreach ($values as $key => $value)
        {
            if (\is_array($value))
            {
                continue;
            }

            if (!$model = $this->getValueModel($key))
            {
                $model = $this->createValueModel();
                $this->valueModels[] = $model;
            }

            $this->loadValueModel($model, $value);
        }
    }

    /**
     * @param $value
     */
    public function setValue($value)
    {
        $this->_value = $value;
    }

    /**
     * Получает значения в виде массива в том числе для не множественного типа
     *
     * @return array
     */
    public function getValues()
    {
        if ($this->property->multiple)
        {
            return $this->value;
        }

        if ($this->value !== null)
        {
            return [$this->value];
        }

        return [];
    }

    /**
     * Добавляет значения в массив values, для не множественного переписывает values
     *
     * @param $loadValues
     */
    public function loadValues($loadValues)
    {
        if (!$loadValues)
        {
            return;
        }

        $values = $this->getValues();

        if ($this->property->multiple)
        {
            $values = array_merge($values, $loadValues);
        }
        else
        {
            $values = $loadValues;
        }

        $this->setValues($values);
    }

    /**
     * @param $values
     */
    public function setValues($values)
    {
        if ($this->property->multiple)
        {
            $this->setValue($values);

            return;
        }

        $this->setValue(ArrayHelper::getValue($values, 0));
    }

    public function formName()
    {
        return 'ItemProperty' . $this->property->property_id;
    }

    public function save($runValidation = true)
    {
        if ($runValidation && !$this->validate())
        {
            return false;
        }

        $valueModels = $this->valueModels;

        // передираем все можели значаний и мохраняем которые были установлены
        foreach ($valueModels as $key => $valueModel)
        {

            $valueModel->item_id = $this->item->item_id;

            if ($valueModel->value === null)
            {
                if (!$valueModel->isNewRecord && !$valueModel->delete())
                {
                    return false;
                }
                continue;
            }

            if (!$valueModel->save())
            {
                return false;
            }
        }

        return true;
    }
}
