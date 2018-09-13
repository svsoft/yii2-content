<?php
namespace svsoft\yii\content\components\filter;

use yii\base\DynamicModel;

class FilterForm extends DynamicModel
{
    /**
     * @var Filter
     */
    private $filter;

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

    /**
     * @return Filter
     */
    function getFilter()
    {
        return $this->filter;
    }
}