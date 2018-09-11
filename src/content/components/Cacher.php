<?php
namespace svsoft\yii\content\components;

use svsoft\yii\content\models\Item;
use Yii;
use svsoft\yii\content\models\Property;
use svsoft\yii\content\models\Type;
use svsoft\yii\content\traits\ModuleTrait;
use yii\base\Component;
use yii\caching\TagDependency;

/**
 * Осуществляет чистку кеша
 * Первая версия чистки кеша сделана только по типа
 * т.е. Если изменится тип, удаляются весть кеш по типу
 * Если изменится элемент, удаляются весть кеш по типу этого элемента
 * Если изменится свойство, также удаляются весть кеш по типу свойства
 * Очистка кеша происходит рекурсивно
 *
 * Class Cacher
 * @package svsoft\yii\content\components
 *
 */
class Cacher extends Component
{
    use ModuleTrait;

    const TAG_TYPE_ID = 'content-type-id';
    const TAG_TYPE_NAME = 'content-type-name';

    /**
     * @var \yii\caching\CacheInterface
     */
    protected $cache;

    function __construct($config = [])
    {
        $this->cache = Yii::$app->cache;

        parent::__construct($config);
    }

    /**
     * Получает название тега по названию типа
     *
     * @param $id
     *
     * @return string
     */
    function tagTypeId($id)
    {
        return self::TAG_TYPE_ID . '-' . $id;
    }

    /**
     * Получает название тега по названию типа
     *
     * @param $name
     *
     * @return string
     */
    function tagTypeName($name)
    {
        return self::TAG_TYPE_NAME . '-' . $name;
    }

    /**
     * Чистит кеш по типу рекурсивно, поднимаясь к родительскуму
     *
     * @param Type $type
     */
    function cleanByType(Type $type)
    {
        $alreadyCleaned = [];
        return $this->cleanByTypeInner($type, $alreadyCleaned);
    }

    /**
     * Чистит кеш по типу рекурсивно, поднимаясь к родительскуму.
     * Сохраняя очишенные типы в массив, чтоб не произошло зацикливание
     *
     * @param Type $type
     * @param $alreadyCleaned
     */
    protected function cleanByTypeInner(Type $type, &$alreadyCleaned)
    {
        $this->clean([
            $this->tagTypeId($type->type_id),
            $this->tagTypeName($type->name)
        ]);

        if (isset($alreadyCleaned[$type->type_id]))
            return;

        $alreadyCleaned[$type->type_id] = true;

        foreach($type->typeProperties as $property)
        {
            $this->cleanByTypeInner($property->parentType, $alreadyCleaned);
        }
    }

    /**
     * Читстит кеш по свойству
     *
     * @param Property $property
     */
    function cleanByProperty(Property $property)
    {
        // Пока заглушка, чистит сразу по типу свойства
        $this->cleanByType($property->parentType);
    }

    /**
     * Чистит кеш по элементу
     *
     * @param Item $item
     */
    function cleanByItem(Item $item)
    {
        // Пока заглушка, чистит сразу по типу элемента
        $this->cleanByType($item->type);
    }

    /**
     * Чистит кеш по списку тегов
     *
     * @param $tags
     */
    protected function clean($tags)
    {
        foreach($tags as $tag)
            TagDependency::invalidate(\Yii::$app->cache, $tag);
    }

    /**
     * Получает из кеша данные, и записывет полученные по ченные результатрм функции $callback
     * Тегирут кеш
     *
     * @param $cacheKey
     * @param \Closure $callback
     * @param array $tags
     *
     * @return mixed
     */
    public function getOrSet($cacheKey, \Closure $callback, $tags = [])
    {
        $dependency = null;
        if ($tags)
            $dependency = new TagDependency(['tags'=>$tags]);

        return $this->cache->getOrSet($cacheKey, $callback, 0, $dependency);
    }
}
