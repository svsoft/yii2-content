<?php

namespace svsoft\yii\content\components\filter;

use yii\base\Component;
use yii\helpers\ArrayHelper;

/**
 * Фильтр содержит данные для создания филтра. набор свойств, состоящие из допустимых значений
 *
 * Class Filter
 * @package svsoft\yii\content\components\filter
 */
class Filter extends Component
{
    /**
     * @var FilterProperty[]
     */
    protected $properties;

    /**
     * Filter constructor.
     *
     * @param FilterProperty[] $properties
     * @param array $config
     */
    function __construct($properties, array $config = [])
    {
        $this->properties = [];
        foreach($properties as $property)
            $this->properties[$property->name] = $property;

        parent::__construct($config);
    }

    /**
     * @param $field
     *
     * @return FilterProperty|null
     */
    public function getProperty($field)
    {
        return ArrayHelper::getValue($this->properties, $field);
    }

    public function getProperties()
    {
        return $this->properties;
    }
}
