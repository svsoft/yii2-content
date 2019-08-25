<?php

namespace svsoft\yii\content\components\display;

use svsoft\yii\content\models\ItemProperty;
use svsoft\yii\content\traits\ModuleTrait;
use yii\base\BaseObject;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;

/**
 * Class Type
 * @package svsoft\yii\content\models
 *
 * @property mixed $value
 * @property boolean multiple
 *
 */
abstract class Property extends BaseObject
{
    use ModuleTrait;

    /**
     * Значения свойств экранированные подготовленные к выводу в шаблоне
     *
     * @var array
     */
    protected $displayValues = [];

    protected $values = [];

    protected $multiple;

    protected $name;

    protected $label;

    /**
     * ItemFrom constructor.
     *
     * @param ItemProperty $itemProperty
     * @param array $config
     *
     */
    public function __construct(ItemProperty $itemProperty,  $config = [])
    {
        parent::__construct($config);

        $this->load($itemProperty);
    }

    /**
     * @param ItemProperty $itemProperty
     */
    protected function load(ItemProperty $itemProperty)
    {
        $this->name          = $itemProperty->property->name;
        $this->label         = $itemProperty->property->label;
        $this->multiple      = $itemProperty->property->multiple;
        $this->values        = $itemProperty->getValues();
        $this->displayValues = $this->loadDisplayValues($itemProperty);
    }

    /**
     * @param ItemProperty $itemProperty
     *
     * @return array
     */
    abstract protected function loadDisplayValues(ItemProperty $itemProperty);

    final function getValue($encode = true)
    {
        if ($encode)
            $values = &$this->displayValues;
        else
            $values = &$this->values;

        if ($this->multiple)
            return $values;

        return ArrayHelper::getValue($values, 0);
    }

    public function getName($encode = true)
    {
        if ($encode)
            return Html::encode($this->name);

        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getMultiple()
    {
        return $this->multiple;
    }

    public function getLabel($encode = true)
    {
        if ($encode)
            return Html::encode($this->label);

        return $this->label;
    }
}
