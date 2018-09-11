<?php

namespace svsoft\yii\content\forms\import;

use svsoft\yii\content\models\ItemObject;
use svsoft\yii\content\models\Type;
use svsoft\yii\content\models\Value;
use svsoft\yii\content\models\ValueFile;
use svsoft\yii\content\models\ValueItem;
use svsoft\yii\content\traits\ModuleTrait;
use svsoft\yii\content\traits\TransactionTrait;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;

/**
 * Class Type
 * @package svsoft\yii\content\forms\filter
 *
*/

/**
 * Class TypeImport
 * @package svsoft\yii\content\forms\import
 *
 * @property ItemObject[] $items
 * @property Type[] $types
 * @property Reader $reader
 * @property array $contentArray
 * @property string $content
 * @property UploadedFile[] $files
 *
 */
class ItemImport extends Model
{
    use TransactionTrait;

    use ModuleTrait;

    /**
     * @var Reader;
     */
    protected $reader;

    protected $files;

    /**
     * Индекс названий файлов
     *
     * @var array
     */
    protected $fileNameIndex = [];

    /**
     * @var array
     */
    protected $_contentArray = [];

    /**
     * @var ItemObject[]
     */
    protected $_items = [];

    /**
     * @var Type[]
     */
    protected $_types = [];

    protected $_typesNameIndex = [];

    /**
     * Парсит контент в массив
     */
    const SCENARIO_SEND_FORM = 'sendForm';

    /**
     * Загружает модели
     */
    const SCENARIO_FILL_MODELS = 'fillModels';

    /**
     * Сохраняет модели
     */
    const SCENARIO_SAVE = 'save';


    function __construct(Reader $reader, $config = [])
    {
        $this->reader = $reader;
        parent::__construct($config);
    }

    function rules()
    {
        return [
            ['files','each', 'rule'=>['file']],
            ['reader','readerValidator','on'=>[self::SCENARIO_SEND_FORM]],
            ['contentArray','contentArrayValidator','on'=>[self::SCENARIO_FILL_MODELS]],
            ['items','itemsValidator','on'=>[self::SCENARIO_SAVE],'skipOnError'=>true],
        ];
    }

    function readerValidator($attribute)
    {
        if (!$this->reader->validate())
            $this->addError($attribute, 'Ошибка чтения '.$this->reader->getAttributeLabel('content'));
    }


    /**
     * Валидатор типов и свойст в виде массива
     *
     * @param $attribute
     */
    function contentArrayValidator($attribute)
    {
        foreach($this->contentArray as $key=>$itemArray)
        {
            // Проверяем обязательность заполнеия элементов в массиве типов
            foreach(['type','properties'] as $name)
            {
                if (empty($itemArray[$name]))
                {
                    $this->addError($attribute, "У элемента {$key} не заполнено обязательное поле «{$name}»");
                }
            }

            $typeName = ArrayHelper::getValue($itemArray, 'type');

            if ($typeName)
                if (!Type::find()->andName($typeName)->one())
                    $this->addError($attribute, "У элемента {$key} тип «{$typeName}» не найден");

        }
    }

    function itemsValidator($attribute)
    {
        $valid = true;
        /**
         * @var $items ItemObject[]
         */
        foreach($this->items as $typeKey=>$items)
        {
            foreach($items as $key=>$item)
            {
                if (!$item->validate())
                {
                    $valid = false;
                }
            }
        }

        if (!$valid)
            $this->addError($attribute, "Ошибка валидации элементов");
    }

    function load($data, $formName = null)
    {
        $load = $this->reader->load($data);

        return $load | parent::load($data, $formName);
    }

    function beforeValidate()
    {
        if (!parent::beforeValidate())
            return false;

        $valid = $this->reader->validate();


        return $valid;
    }

    function afterValidate()
    {
        parent::afterValidate();

        // Добавляем ошибки в валидации contentArray в $this->reader
        if ($this->hasErrors('contentArray'))
            foreach($this->getErrors('contentArray') as $error)
                $this->reader->addError('content', $error);
    }

    public function getContentArray()
    {
        return $this->_contentArray;
    }

    public function getContent()
    {
        return $this->reader->content;
    }

    /**
     * @return Reader
     */
    public function getReader()
    {
        return $this->reader;
    }

    public function getTypes()
    {
        return $this->_types;
    }

    public function getItems()
    {
        return $this->_items;
    }

    public function getFiles()
    {
        return $this->files;
    }

    /**
     * Получает файл из массива files по имени
     * @param $name
     *
     * @return mixed|null
     */
    protected function getFileByName($name)
    {
        $key = ArrayHelper::getValue($this->fileNameIndex, $name);
        if ($key === null)
            return null;

        return ArrayHelper::getValue($this->files, $key);
    }

    public function setFiles($files)
    {
        /**
         * @var $files UploadedFile[]
         */
        $this->files = $files;

        foreach($this->files as $key=>$file)
        {
            if ($file instanceof  UploadedFile)
                $this->fileNameIndex[$file->name] = $key;
        }
    }

    /**
     * Заполняет модели типов и свойств
     */
    public function read()
    {
        $this->scenario = self::SCENARIO_SEND_FORM;
        if (!$this->validate())
            return false;

        $this->_contentArray = $this->reader->parseContent($this->content);

        $this->scenario = self::SCENARIO_FILL_MODELS;
        if (!$this->validate())
            return false;

        $this->fillModels();

        return true;
    }

    public function fillModels()
    {
        // массив индексов названий типов, нужен для получения индекса по порядку для массива $this->_$types
        // При чтении массива элементов $this->contentArray
        $typesNameIndex = [];

        foreach($this->contentArray as $key=>$itemArray)
        {
            $typeName = $itemArray['type'];

            if (!array_key_exists($typeName, $typesNameIndex))
                $typesNameIndex[$typeName] = count($typesNameIndex);

            $typeKey = $typesNameIndex[$typeName];

            // получаем тип по названию, иначе пропускаем итерацию
            if (!$type = Type::find()->andName($itemArray['type'])->one())
                continue;

            $this->_types[$typeKey] = $type;

            $item = null;
            // Елси задано название элемента, пробуем его найти
            if (isset($itemArray['name']))
                $item = ItemObject::find()->andTypeId($type->type_id)->andName($itemArray['name'])->one();
            elseif (isset($itemArray['slug']))
                $item = ItemObject::find()->andTypeId($type->type_id)->andSlug($itemArray['slug'])->one();


            if (!$item)
                $item = new ItemObject();

            $item->setAttributes($itemArray);
            $item->type_id = $type->type_id;

            // Устанавливаем значения свойств
            foreach($itemArray['properties'] as $propertyName=>$value)
            {
                if ($value!==null && !is_array($value))
                    $value = [$value];

                if ($propertyItem = $item->getItemPropertyByName($propertyName))
                {
                    $property = $propertyItem->property;

                    // Создаем заглущку модели значения для определения типа
                    $dummyValueModel = Value::createModel($property->getTypeName());

                    // Добвляем файлы
                    if ($dummyValueModel instanceof ValueFile)
                    {
                        $files = [];
                        if ($value)
                        {
                            foreach($value as $filename)
                            {
                                if ($file = $this->getFileByName($filename))
                                    $files[] = $file;
                            }
                        }

                        if ($files)
                            $propertyItem->setValues($files);
                    }
                    elseif ($dummyValueModel instanceof ValueItem)
                    {
                        if ($value)
                        {
                            $itemModels = ItemObject::find()->andTypeId($property->type_id)->andSlugOrName($value)->indexBy('item_id')->all();

                            $propertyItem->setValues(array_keys($itemModels));
                        }
                    }
                    else
                    {
                        $propertyItem->setValues($value);
                    }
                }

            }

            $item->setAttributes($itemArray);

            $this->_items[$typeKey][] = $item;
        }
    }

    /**
     * @param $key - ключ в массиве $this->properties
     *
     * @return ItemObject[]
     */
    public function getItemsByTypeKey($key)
    {
        return ArrayHelper::getValue($this->items, $key, []);
    }

    /**
     *
     * Сохраняет элементы
     *
     * @param bool $runValidation
     *
     * @return bool
     */
    public function save($runValidation = true)
    {
        if ($runValidation && !$this->validate())
            return false;

        $this->beginTransaction();

        foreach($this->types as $typeKey=>$type)
        {

            // Сохраняем свойства
            foreach($this->getItemsByTypeKey($typeKey) as $item)
            {
                if (!$item->save())
                    return $this->rollBackTransaction();
            }
        }

        $this->commitTransaction();

        return true;
    }
}