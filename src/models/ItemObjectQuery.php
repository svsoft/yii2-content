<?php

namespace svsoft\yii\content\models;

use yii\base\Exception;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * This is the ActiveQuery class for [[Item]].
 *
 * @see Item
 *
 * @property array $properties
 */
class ItemObjectQuery extends ItemQuery
{
    protected $valueQuery;

    /**
     * Условия на филтр по свойствам [<ИД совйства>=><значение>,[<оператор>,<ид свойства>,<значение>], ... ]
     * 
     * @var array
     */
    public $propertyWhere = [];

    /**
     * Сортировка по свойствам [<ИД совйства>=><направление>, ... ]
     *
     * @var
     */
    protected $propertyOrderBy = [];

    /**
     * Ключ джоина на фильтрацию по значениям в массиве join
     * @var int
     *
     */
    protected $filterJoinIndex;

    /**
     * Ключи в масстве orderBy для сортировки по свойствам
     *
     * @var array[]
     */
    protected $orderByJoinIndexes = [];

    /**
     * Свойства требуемые для запроса
     *
     * @var array
     */
    protected $_properties;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->addOrderBy(['sort' => SORT_ASC]);
        return parent::init();
    }

    /**
     * @inheritdoc
     * @return ItemObject[]|array
     */
    public function all($db = null)
    {
        return  parent::all($db);
    }

    /**
     * @inheritdoc
     * @return ItemObject|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * Получает свойста для фильтрации и сортировки
     *
     * @return Property[]|array
     */
    protected function getProperties()
    {
        if ($this->_properties === null)
        {
            $propertyId = array_merge(array_keys($this->propertyWhere), array_keys($this->propertyOrderBy));
            $propertyId = array_unique($propertyId);

            $this->_properties = Property::find()
                ->alias('p')
                ->leftJoin(['t'=>Type::tableName()],'t.type_id = p.type_id')
                ->select(['p.name', 'p.label', 'p.property_id','value_field'])
                ->where(['p.property_id'=>$propertyId])
                ->asArray()
                ->indexBy('property_id')
                ->all();
        }

        return $this->_properties;
    }

    /**
     * Сбрасывает полученные свойста для фильтрации и сортировки
     */
    protected function resetProperties()
    {
        $this->_properties = null;
    }

    /**
     * @param $propertyId
     *
     * @return mixed
     * @throws Exception
     */
    protected function getPropertyById($propertyId)
    {
        if (!$property = ArrayHelper::getValue($this->properties, $propertyId))
            throw new Exception('Property '.$propertyId.' not found');

        return ArrayHelper::getValue($this->properties, $propertyId);
    }

    /**
     * Добаялет условия в запрос на полчучение элементов, условия и сортироки для мвойств
     * @param \yii\db\QueryBuilder $builder
     *
     * @return $this|Query
     * @throws Exception
     */
    public function prepare($builder)
    {
        // добавляем в запрос условия на фильтрация
        if ($this->propertyWhere)
        {
            // Формируем условия для таблицы значений свойств content_value
            $conditionCount = 0;
            $where = ['or'];
            foreach($this->propertyWhere as $propertyId=>$conditions)
            {
                $property = $this->getPropertyById($propertyId);

                $conditionValues = [];
                foreach($conditions as $condition)
                {
                    $conditionValue = $condition;

                    $operator = $condition[0];
                    $valueField = $property['value_field'];

                    $conditionValue[0] = $operator;
                    $conditionValue[1] = $valueField;

                    if ($operator === '!=' || $operator === '<>')
                    {
                        $conditionValue = ['or', $conditionValue, ['IS', $valueField, null]];
                    }

                    $conditionValues[] = $conditionValue;
                }

                $conditionCount ++;
                $where[] = array_merge(['and', ['=', 'v.property_id', $propertyId]], $conditionValues);
            }

            // Добавляем синоним для таблицы элементов
            $this->alias('i');

            // собираем запрос по фильтрации item_id из content_value
            $query = Value::find()
                ->select('item_id as property_filter_item_id')
                ->alias('v')
                ->where($where)
                ->groupBy('item_id');
            // Сбрасываем джойны, т.к. стандартно для получение названия типа делаются два джойна
            $query->join = [];

            if ($conditionCount)
                $query->having(["count(item_id)"=>$conditionCount]);

            // Если join на фельтрацию еще не добавлен
            if ($this->filterJoinIndex === null)
                $this->filterJoinIndex = count($this->join);

            // Добавляем филтрации по item_id для таблицы элементов
            $this->join[$this->filterJoinIndex] = ['INNER JOIN', ['v'=>$query], 'property_filter_item_id = item_id'];
        }


        // Добавлеем в запрос условия на сортировку
        // Алгоритм: Для каждого свойства по которому предполагается фильтрация
        // добавляем left join со значением этого свойства и в основном запросе делаем по этим значением сортировку
        if ($this->propertyOrderBy)
        {
            foreach($this->propertyOrderBy as $propertyId => $direction)
            {
                $property = $this->getPropertyById($propertyId);

                $orderValueField = 'property_'.$propertyId.'_value';
                $orderItemIdField = 'property_'.$propertyId.'_item_id';
                $alias = 'order_'.$propertyId;
                $query = Value::find()
                    ->where(['property_id'=>$propertyId])
                    ->select([$orderValueField=>$property['value_field'],$orderItemIdField=>'item_id']);
                // Сбрасываем джойны, т.к. стандартно для получение названия типа делаются два джойна
                $query->join = [];

                // Что бы при повторном вызове prepare не дублировлист джоины на сортировки, сохраняем их индексы из массива this->join
                // Если для текущего свойства еще не добавлен join, то добавляем иначе пропускаем
                $joinIndex = ArrayHelper::getValue($this->orderByJoinIndexes, $propertyId);
                if ($joinIndex === null)
                {
                    $joinIndex = count($this->join);
                    $this->orderByJoinIndexes[$propertyId] = $joinIndex;
                }

                $this->join[$joinIndex] = ['LEFT JOIN', [$alias=>$query], $orderItemIdField . ' = item_id'];

                $this->addOrderBy([$orderValueField=>$direction]);
            }
        }

        return parent::prepare($builder);
    }

    /**
     * Добавляет условие фильтрации по свойству
     *
     * @param $condition
     *
     * @return $this
     */
    public function andPropertyWhere($condition)
    {
        if (!$condition && !is_array($condition))
            return $this;

        if (isset($condition[0]) && is_array($condition[0]))
        {
            foreach($condition as $conditionItem)
            {
                $this->andPropertyWhere($conditionItem);
            }

            return $this;
        }

        // Если условие заданно в формате ключ-значение, то преобразуем к виду [<оператор>,<ид свойства>,<значение>]
        if (count($condition) == 1)
        {
            $propertyId = key($condition);

            $value = $condition[$propertyId];
            $operator = is_array($value) ? 'IN' : '=';
            $condition = [$operator, $propertyId, $value];
        }

        $this->propertyWhere[$condition[1]][] = $condition;

        // сбрасываем полученые свойста
        $this->resetProperties();

        return $this;
    }

    /**
     * Добавляет сортировку по свойству
     *
     * @param $columns
     *
     * @return $this
     */
    public function addPropertyOrderBy($columns)
    {
        $columns = $this->normalizeOrderBy($columns);

        if ($this->propertyOrderBy === null) {
            $this->propertyOrderBy = $columns;
        } else {
            $this->propertyOrderBy = ArrayHelper::merge($this->propertyOrderBy, $columns);
        }

        // сбрасываем полученые свойста
        $this->resetProperties();

        return $this;
    }


}
