<?php

namespace svsoft\yii\content;

use svsoft\yii\content\components\Cacher;
use svsoft\yii\content\components\display\PropertyBoolean;
use svsoft\yii\content\components\display\PropertyDate;
use svsoft\yii\content\components\display\PropertyDatetime;
use svsoft\yii\content\components\display\PropertyFile;
use svsoft\yii\content\components\display\PropertyFloat;
use svsoft\yii\content\components\display\PropertyHtml;
use svsoft\yii\content\components\display\PropertyInt;
use svsoft\yii\content\components\display\PropertyItem;
use svsoft\yii\content\components\display\PropertyString;
use svsoft\yii\content\components\display\PropertyText;
use svsoft\yii\content\components\Getter;
use svsoft\yii\content\components\GetterCache;
use svsoft\yii\content\models\Type;
use svsoft\yii\content\models\ValueBoolean;
use svsoft\yii\content\models\ValueDate;
use svsoft\yii\content\models\ValueDatetime;
use svsoft\yii\content\models\ValueFile;
use svsoft\yii\content\models\ValueFloat;
use svsoft\yii\content\models\ValueHtml;
use svsoft\yii\content\models\ValueInt;
use svsoft\yii\content\models\ValueItem;
use svsoft\yii\content\models\ValueString;
use svsoft\yii\content\models\ValueText;
use svsoft\yii\content\services\ImageThumb;
use Yii;

/**
 * Class ContentModule
 * @package svsoft\yii\content
 *
 * @property Getter $getter
 * @property Cacher $cacher
 * @property ImageThumb $imageThumb

 */
class ContentModule extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'svsoft\yii\content\controllers';

    public $fileDirPath;

    public $webDirPath;

    /**
     * Массив классов значений. ключ название типа
     *
     * @var
     */
    public $valueClasses = [];

    public $displayPropertyClasses = [];

    protected $getter;

    /**
     * @var Cacher
     */
    protected $cacher;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if ($this->fileDirPath === null)
            $this->fileDirPath = '@app/web/upload';
        if ($this->webDirPath === null)
            $this->webDirPath = '/upload';

        $this->fileDirPath = \Yii::getAlias($this->fileDirPath);
        $this->webDirPath = \Yii::getAlias($this->webDirPath);

        $this->valueClasses = [
            Type::TYPE_FLOAT => ValueFloat::class,
            Type::TYPE_FILE => ValueFile::class,
            Type::TYPE_STRING => ValueString::class,
            Type::TYPE_TEXT => ValueText::class,
            Type::TYPE_HTML => ValueHtml::class,
            Type::TYPE_ITEM => ValueItem::class,
            Type::TYPE_DATE => ValueDate::class,
            Type::TYPE_DATETIME => ValueDatetime::class,
            Type::TYPE_BOOLEAN => ValueBoolean::class,
            Type::TYPE_INT => ValueInt::class
        ];

        $this->displayPropertyClasses = [
            Type::TYPE_STRING   => PropertyString::class,
            Type::TYPE_FLOAT    => PropertyFloat::class,
            Type::TYPE_FILE     => PropertyFile::class,
            Type::TYPE_TEXT     => PropertyText::class,
            Type::TYPE_HTML     => PropertyHtml::class,
            Type::TYPE_INT      => PropertyInt::class,
            Type::TYPE_ITEM     => PropertyItem::class,
            Type::TYPE_DATE     => PropertyDate::class,
            Type::TYPE_DATETIME => PropertyDatetime::class,
            Type::TYPE_BOOLEAN  => PropertyBoolean::class,
        ];

        $this->set('getter',GetterCache::class);
        $this->set('cacher',Cacher::class);

        if (empty($this->components['imageThumb']))
        {
            $this->set('imageThumb', ImageThumb::class);
        }
    }

    public function getFilePath($filename)
    {
        return $this->fileDirPath . DIRECTORY_SEPARATOR . $filename;
    }

    public function getWebPath($filename)
    {
        return $this->webDirPath . DIRECTORY_SEPARATOR . $filename;
    }

    public function getValueClass($type)
    {
        if (empty($this->valueClasses[$type]))
            $type = Type::TYPE_ITEM;

        return $this->valueClasses[$type];
    }

    public function getDisplayPropertyClass($type)
    {
        if (empty($this->displayPropertyClasses[$type]))
            $type = Type::TYPE_ITEM;

        return $this->displayPropertyClasses[$type];
    }

    /**
     * @return Getter
     */
    public function getter()
    {
        if ($this->getter === null)
            $this->getter = Yii::$app->getModule('content')->get('getter');

        return $this->getter;
    }

    /**
     * @return Cacher
     */
    public function getCacher()
    {
        if ($this->cacher === null)
            $this->cacher = Yii::$app->getModule('content')->get('cacher');

        return $this->cacher;
    }
}
