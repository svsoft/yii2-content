<?php

namespace svsoft\yii\content\forms\filter;

use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * Class Type
 * @package svsoft\yii\content\forms\filter
 *
*/
class PropertyFilterItemRange extends PropertyFilterItem
{
    /**
     * @var string|float|int
     */
    public $valueFrom;

    /**
     * @var string|float|int
     */
    public $valueTo;

    public $max;

    public $min;

    public $type = self::TYPE_FLOAT;

    const TYPE_FLOAT = 'float';

    const TYPE_INT = 'int';


    function init()
    {
        parent::init();

        if ($this->allValues)
        {
            if ($this->min === null)
                $this->min = min($this->allValues);

            if ($this->max === null)
                $this->max = max($this->allValues);

            if ($this->valueFrom === null)
                $this->valueFrom = $this->min;

            if ($this->valueTo === null)
                $this->valueTo = $this->max;
        }
    }

    function rules()
    {
        return [
//            ['valueTo', 'number','max'=>$this->max],
//            ['valueFrom', 'number','min'=>$this->min],
            ['valueTo', 'compare', 'compareAttribute' => 'valueFrom', 'operator' => '>='],
            ['value','safe']
        ];
    }

    function getValue()
    {
        return Json::encode([$this->valueFrom, $this->valueTo]);
    }

    function setValue($value)
    {
        $value = explode(',', $value);

        $this->valueFrom = (float)ArrayHelper::getValue($value, 0);
        $this->valueTo = (float)ArrayHelper::getValue($value, 1);
    }

    function filter()
    {
        if ($this->valueFrom != $this->min)
            $this->filter->query->andPropertyWhere(['>=', $this->property->property_id, $this->valueFrom]);

        if ($this->valueTo != $this->max)
            $this->filter->query->andPropertyWhere(['<=', $this->property->property_id, $this->valueTo]);
    }
}