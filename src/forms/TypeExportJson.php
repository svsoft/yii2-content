<?php

namespace svsoft\yii\content\forms;

use svsoft\yii\content\models\Type;
use yii\base\Model;
use yii\helpers\Json;

/**
 * Class Type
 * @package svsoft\yii\content\forms\filter
 *
 */
class TypeExportJson extends Model
{
    /**
     * @var Type
     */
    protected $type;

    function __construct(Type $type, $config = [])
    {
        $this->type = $type;
        parent::__construct($config);
    }

    function toJson()
    {
        $typeArray = $this->type->toArrayWithProperties();
        return Json::encode($typeArray);
    }
}

