<?php

namespace svsoft\yii\content\forms\filter;

use svsoft\yii\content\models\Property;
use yii\base\Exception;
use yii\base\Model;

/**
 * Class Type
 * @package svsoft\yii\content\forms\filter
 *
 *
 * @property Property[] $properties
 * @property string $label
 *
*/
class PropertyFilterItem extends Model
{
    /**
     * @var Property
     */
    public $property;

    /**
     * @var array
     */
    public $allValues = [];

    /**
     * @var PropertyFilter
     */
    protected $filter;

    public $formatter;

    /**
     * Сортировка элементов
     *
     * @var string
     */
    public $sort = self::SORT_ASC;

    const SORT_DESC = 'desc';
    const SORT_ASC = 'asc';
    const SORT_NAT_DESC = 'nat_desc';
    const SORT_NAT_ASC = 'nat_asc';

    function __construct(PropertyFilter $filter, $config = [])
    {
        $this->filter = $filter;

        parent::__construct($config);
    }

    function displayValue($value)
    {
        if ($this->formatter)
            return call_user_func($this->formatter, $value);

        return $value;
    }
    function init()
    {
        parent::init();

        switch($this->sort)
        {
            case self::SORT_ASC:
                sort($this->allValues);
                break;
            case self::SORT_DESC:
                rsort($this->allValues);
                break;
            case self::SORT_NAT_ASC:
                natsort($this->allValues);
                break;
            // TOTO: добавть натуральную по убыванию
        }
    }

    function getLabel()
    {
        return $this->property->label;
    }

    function formName()
    {
        $formName = 'filter' . ucfirst($this->property->property_id);

        return $formName;
    }

    function filter()
    {
        throw new Exception('This object does not have method filter');
    }
}

