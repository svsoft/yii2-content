<?php

namespace svsoft\yii\content\services;

use svsoft\yii\content\components\Cacher;
use svsoft\yii\content\components\display\Item;
use svsoft\yii\content\components\filter\Filter;
use svsoft\yii\content\components\filter\FilterForm;
use svsoft\yii\content\components\filter\FilterProperty;
use svsoft\yii\content\components\Getter;
use svsoft\yii\content\models\ItemObject;
use svsoft\yii\content\models\ItemObjectQuery;
use svsoft\yii\content\models\Type;
use svsoft\yii\content\traits\ModuleTrait;
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
 *
 * @property \svsoft\yii\content\components\filter\Filter $filter
 * @property \svsoft\yii\content\models\ItemObjectQuery $filteredQuery
 * @property \svsoft\yii\content\models\ItemObjectQuery $query
 * @property \svsoft\yii\content\components\filter\FilterForm $filterForm
 * @property \svsoft\yii\content\components\display\Item[] $items
 */
class FilterService extends Component
{
    use ModuleTrait;
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
    private $propertiesConfig;

    /**
     * @var Cacher
     */
    protected $cacher;

    /**
     * FilterService constructor.
     *
     * @param Type $type
     * @param Getter $getter
     * @param array $propertiesConfig
     */
    public function __construct(Type $type, Getter $getter, $propertiesConfig = [])
    {
        $this->type = $type;
        $this->getter = $getter;
        $this->propertiesConfig = $propertiesConfig;
        $this->cacher = self::getModule()->cacher;

        parent::__construct([]);
    }

    public function init()
    {
        $propertiesConfig = [];
        foreach ($this->propertiesConfig as $key => $propertyConfig)
        {
            if (!\is_array($propertyConfig))
            {
                $name = $propertyConfig;
                $propertyConfig = [
                    'name' => $propertyConfig
                ];
            }
            else
            {
                $name = $propertyConfig['name'] ?? $key;
            }

            if (!$property = $this->type->getPropertyByName($name))
            {
                continue;
            }

            $propertyConfig = ArrayHelper::merge([
                'class' => FilterProperty::class,
                'label' => $property->label,
            ], $propertyConfig);

            $propertiesConfig[$name] = $propertyConfig;
        }

        $this->propertiesConfig = $propertiesConfig;

        parent::init();
    }

    /**
     * Возвращает запрос для построения фильтра
     *
     * @return ItemObjectQuery
     * @throws \yii\base\Exception
     */
    public function getQuery(): ItemObjectQuery
    {
        if ($this->_query === null)
        {
            $this->_query = $this->getter->getItemObjectQueryFilterTypeName($this->type->name);
        }

        return $this->_query;
    }

    /**
     * Возвращает запрос для получение отфильтрованных элементов
     *
     * @return ItemObjectQuery
     * @throws \yii\base\Exception
     */
    public function getFilteredQuery(): ItemObjectQuery
    {
        if ($this->_filteredQuery)
        {
            return clone $this->_filteredQuery;
        }

        return $this->getQuery();
    }

    /**
     * Создает форму филтра на основе текущего фильтра
     *
     * @return FilterForm
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function getFilterForm(): FilterForm
    {
        if ($this->_filterForm === null)
        {
            $this->_filterForm = new FilterForm($this->getFilter());
        }

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
        {
            return;
        }

        foreach ($filter->getProperties() as $filterProperty)
        {
            $propertyName = $filterProperty->name;
            if (!$property = $this->type->getPropertyByName($propertyName))
            {
                continue;
            }
            $propertyValue = $filterForm->$propertyName;
            if ($propertyValue === '' || $propertyValue === null)
            {
                continue;
            }

            switch ($filterProperty->type)
            {
                case FilterProperty::FILTER_TYPE_RANGE:
                    if ($propertyValue[0] && $propertyValue[1])
                    {
                        $query->andPropertyWhere([
                            'BETWEEN',
                            $property->property_id,
                            $propertyValue[0],
                            $propertyValue[1]
                        ]);
                    }
                    break;
                default:
                    $query->andPropertyWhere([$property->property_id => $propertyValue]);
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
    public function getItems(): array
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
    public function getDataProvider(Pagination $pagination = null): ArrayDataProvider
    {
        $this->loadFilterForm();
        $query = clone $this->getFilteredQuery();
        $dataProvider = new ArrayDataProvider();

        if (!$pagination)
        {
            $pagination = $dataProvider->pagination;
        }

        $dataProvider->pagination = $pagination;
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
     */
    public function getFilter(): Filter
    {

        if ($this->_filter === null)
        {
            $query = clone $this->getQuery();
            $cacheKey = [__FUNCTION__, md5(serialize($query))];
            $this->_filter = $this->cacher->getOrSet($cacheKey, function () use ($query) {
                $propertiesConfig = $this->propertiesConfig;
                $items = $query->all();
                $valuesGroupByProperties = [];

                foreach ($items as $item)
                {
                    foreach ($item->getItemProperties() as $propertyId => $property)
                    {
                        $propertyName = $property->property->name;

                        if (empty($propertiesConfig[$propertyName]))
                        {
                            continue;
                        }

                        $propertyValues = $property->getValue();

                        if (!$property->property->multiple)
                        {
                            $propertyValues = [$propertyValues];
                        }

                        foreach ($propertyValues as $propertyValue)
                        {
                            if ($propertyValue === '' && $propertyValue === null)
                            {
                                continue;
                            }

                            if ($property->property->type->simple)
                            {
                                $value = $text = $propertyValue;
                            }
                            else
                            {
                                // Если значение свойство является привязкой, то в качестве текста выводим его название
                                // TODO: попробовать ускорить эту часть
                                /** @var ItemObject $propertyItem */
                                $propertyItem = $this->getter->getItemObjectById($propertyValue);
                                $value = $propertyItem->item_id;
                                $text = $propertyItem->name;
                            }

                            $valuesGroupByProperties[$propertyName][$value] = $text;
                        }
                    }
                }

                $filterProperties = [];

                foreach ($this->propertiesConfig as $propertyName => $propertyConfig)
                {
                    $filterProperties[] = Yii::createObject(ArrayHelper::merge($propertyConfig, [
                        'values' => ArrayHelper::getValue($valuesGroupByProperties, $propertyName, []),
                    ]));
                }

                return new Filter($filterProperties);
            }, $this->cacher->tagTypeId($this->type->type_id));
        }

        return $this->_filter;
    }


}
