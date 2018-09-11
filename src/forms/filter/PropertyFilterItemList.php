<?php
namespace svsoft\yii\content\forms\filter;

class PropertyFilterItemList extends PropertyFilterItem
{
    public $values = [];

    function rules()
    {
        return [
            ['values', 'each','rule'=>['in', 'range'=>$this->allValues],],
        ];
    }

    public function isChecked($value)
    {
        return array_key_exists($value, $this->values);
    }

    function filter()
    {
        if ($this->values)
            $this->filter->query->andPropertyWhere([$this->property->property_id=>$this->values]);
    }
}