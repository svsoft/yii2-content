<?php
namespace svsoft\yii\content\components\filter;

use yii\base\DynamicModel;
use yii\helpers\ArrayHelper;

class FilterForm extends DynamicModel
{
    /**
     * @var Filter
     */
    private $filter;

    public $formName;

    function __construct(Filter $filter)
    {
        $this->filter = $filter;
        $attributes = [];
        foreach($filter->getProperties() as $property)
        {
            $attributes[] = $property->name;
        }

        parent::__construct($attributes, []);
    }

    function init()
    {
        foreach($this->filter->getProperties() as $property)
        {
            $attribute = $property->name;
            if ($property->type == FilterProperty::FILTER_TYPE_RANGE)
            {
                $this->$attribute = [$property->getMinValue(), $property->getMaxValue()];
            }
            $this->addRule($attribute, 'safe');

        }

        parent::init();
    }

    function setAttributes($values, $safeOnly = true)
    {
        foreach($this->filter->getProperties() as $attribute=>$propertyFilter)
        {
            $value = ArrayHelper::getValue($values, $attribute);
            if ($value === null)
                continue;

            if ($propertyFilter->type == FilterProperty::FILTER_TYPE_RANGE)
            {
                $value = $this->$attribute;
                if ($value[0]< $propertyFilter->getMinValue())
                    $value[0] = $propertyFilter->getMinValue();
                if ($value[1] > $propertyFilter->getMaxValue())
                    $value[1] = $propertyFilter->getMaxValue();

                $values[$attribute] = $value;
            }
            elseif (($propertyFilter->type == FilterProperty::FILTER_TYPE_VALUE))
            {
                if (!isset($propertyFilter->values[$value]))
                    $values[$attribute] = null;
            }

        }

        parent::setAttributes($values, $safeOnly);
    }

    /**
     * @return Filter
     */
    function getFilter()
    {
        return $this->filter;
    }

    function formName()
    {
        if ($this->formName !== null)
            return $this->formName;

        return 'f';
    }

    function getAttribute($name)
    {
        $values = parent::getAttributes([$name]);
        return ArrayHelper::getValue($values, $name);
    }
}