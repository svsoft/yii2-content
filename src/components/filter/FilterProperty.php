<?php

namespace svsoft\yii\content\components\filter;

use yii\base\Component;

/**
 * Class ApartmentFilterItem
 * @package frontend\services
 */
class FilterProperty extends Component
{
    const FILTER_TYPE_VALUE = 1;

    const FILTER_TYPE_RANGE = 2;

    public $name;

    public $label;

    public $values = [];

    public $type = self::FILTER_TYPE_VALUE;

    /**
     * @return mixed|null
     */
    public function getMinValue()
    {
        if (!$this->values)
            return null;

        return min($this->values);
    }

    public function getMaxValue()
    {
        if (!$this->values)
            return null;

        return max($this->values);
    }

    /**
     * @return array
     */
    public function getValueList()
    {
        $valueList = [];
        foreach($this->values as $value)
        {
            $valueList[$value] = $value;
        }

        return $valueList;
    }
}
