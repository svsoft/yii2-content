<?php

namespace svsoft\yii\content\services;

use svsoft\yii\content\components\display\Item;
use svsoft\yii\content\components\filter\Filter;
use svsoft\yii\content\components\filter\FilterForm;
use svsoft\yii\content\components\filter\FilterProperty;
use svsoft\yii\content\components\Getter;
use svsoft\yii\content\models\ItemObjectQuery;
use svsoft\yii\content\models\Type;
use yii\base\Component;
use yii\data\ArrayDataProvider;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;
use Yii;

/**
 * Обеспечивают фильтрацию элементов определенного типа.
 *
 * Class FilterService
 * @package svsoft\yii\content\services
 */
class FilterService extends Component
{
    /**
     * Текущий фильтр
     *
     * @var Filter
     */
    private $_filter;


    /**
     * Форма фильтра
     *
     * @var FilterForm
     */
    private $_filterForm;

    /**
     * Запрос для построения фильтра, оъекта $this->_filter
     *
     * @var ItemObjectQuery
     */
    private $_query;

    /**
     * Запрос для выборки элементов после фильтрации
     *
     * @var ItemObjectQuery
     */
    private $_filteredQuery;

    /**
     * @var Getter
     */
    private $getter;

    /**
     * Тип на основе которого стрится фильтр
     * @var Type
     */
    private $type;

    /**
     * Массив конфигурация свойств фильтра
     *
     * @var array
     */
    private $filterPropertyConfigs;

    /**
     * FilterService constructor.
     *
     * @param Type $type
     * @param Getter $getter
     * @param array $filterPropertyConfigs
     */
    function __construct(Type $type, Getter $getter, $filterPropertyConfigs = [])
    {
        $this->type = $type;
        $this->getter = $getter;
        $this->filterPropertyConfigs = $filterPropertyConfigs;

        parent::__construct([]);
    }

    function init()
    {
        $filterPropertyConfigs = [];
        foreach($this->filterPropertyConfigs as $key=>$filterPropertyConfig)
        {
            if (!is_array($filterPropertyConfig))
            {
                $name = $filterPropertyConfig;
                $filterPropertyConfigs[$name] = [
                    'name'=>$name
                ];
            }
            else
            {
                if (is_int($key))
                {
                    $filterPropertyConfigs[$filterPropertyConfig['name']] = $filterPropertyConfig;
                }
            }
        }

        $this->filterPropertyConfigs = $filterPropertyConfigs;

        parent::init();
    }

    /**
     * Возвращает запрос для построения фильтра
     *
     * @return ItemObjectQuery
     * @throws \yii\base\Exception
     */
    function getQuery()
    {
        if ( $this->_query === null )
            $this->_query = $this->getter->getItemObjectQueryFilterTypeName($this->type->name);

        return $this->_query;
    }

    /**
     * Возвращает запрос для получение отфильтрованных элементов
     *
     * @return ItemObjectQuery
     * @throws \yii\base\Exception
     */
    function getFilteredQuery()
    {
        if ($this->_filteredQuery)
            return clone $this->_filteredQuery;

        return $this->getQuery();
    }

    /**
     * Создает форму филтра на основе текущего фильтра
     *
     * @return FilterForm
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    function getFilterForm()
    {
        if ($this->_filterForm === null)
            $this->_filterForm = new FilterForm($this->getFilter());

        return $this->_filterForm;
    }

    /**
     * Загружает форму фильтрации
     *
     * @return void
     * @throws \yii\base\Exception
     */
    private function loadFilterForm()
    {
        $query = clone $this->getQuery();

        $filterForm = $this->getFilterForm();

        $filter = $filterForm->getFilter();

        if (!$filterForm->validate())
            return;

        foreach($filter->getProperties() as $filterProperty)
        {
            $attribute = $filterProperty->name;
            $property = $this->type->getPropertyByName($attribute);
            $attributeValue  = $filterForm->$attribute;
            if ($attributeValue === '' || $attributeValue === null)
                continue;

            switch($filterProperty->type)
            {
                case FilterProperty::FILTER_TYPE_RANGE:
                    $query->andPropertyWhere(['BETWEEN', $property->property_id, $attributeValue[0], $attributeValue[1]]);
                    break;
                default:
                    $query->andPropertyWhere([$property->property_id => $attributeValue]);
            }
        }

        $this->_filteredQuery = $query;
    }


    /**
     * Возвращет массив отфильтрованных элементов
     *
     * @return Item[]
     * @throws \yii\base\Exception
     */
    function getItems()
    {
        $this->loadFilterForm();

        return $this->getter->getItemsByQuery($this->getFilteredQuery());
    }
    
    /**
     * Возвращает объект класса ArrayDataProvider отфильтрованных элементов
     *
     * @param Pagination|null $pagination
     *
     * @return ArrayDataProvider
     * @throws \yii\base\Exception
     */
    function getDataProvider(Pagination $pagination = null)
    {
        $this->loadFilterForm();

        $query = clone $this->getFilteredQuery();

        $dataProvider = new ArrayDataProvider();

        if ($pagination)
            $dataProvider->pagination = $pagination;
        else
            $pagination = $dataProvider->pagination;


        $pagination->totalCount = $query->count();

        $query->offset = $pagination->offset;
        $query->limit = $pagination->limit;

        $dataProvider->models = $this->getter->getItemsByQuery($query);

        return $dataProvider;

    }

    /**
     * Возвращает объект класса Filter
     *
     * @return Filter
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    function getFilter()
    {
        if ($this->_filter === null)
        {
            $filterPropertyConfigs = $this->filterPropertyConfigs;

            $query = clone $this->getQuery();

            $items = $this->getter->getItemsByQuery($query);

            $valuesGroupByProperties = [];

            foreach($items as $item)
            {
                foreach($item->getProperties() as $propertyId=>$property)
                {
                    $propertyName = $property->getName();

                    if (empty($filterPropertyConfigs[$propertyName]))
                        continue;

                    $propertyValue = $property->getValue();

                    if ($propertyValue === '' && $propertyValue === null)
                        continue;

                    // Если значение свойства объек, то в качестве текста выводим его название
                    if ($propertyValue instanceof Item)
                    {
                        $value = $propertyValue->item_id;
                        $text = $propertyValue->name;
                    }
                    else
                    {
                        $value = $propertyValue;
                        $text = $value;
                    }
                    $valuesGroupByProperties[$propertyId][$value] = $text;
                }
            }

            $filterProperties = [];
            foreach($valuesGroupByProperties as $propertyId=>$values)
            {
                $property = $this->type->getPropertyById($propertyId);

                $filterProperties[] = Yii::createObject(ArrayHelper::merge($filterPropertyConfigs[$property->name], [
                    'class'=>FilterProperty::class,
                    'label' => $property->label,
                    'values' => ArrayHelper::getValue($valuesGroupByProperties, $propertyId, []),
                ]));
            }

            $this->_filter = new Filter(
                $filterProperties
            );
        }

        return $this->_filter;
    }


}
