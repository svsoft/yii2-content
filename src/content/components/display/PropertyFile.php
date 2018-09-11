<?php

namespace svsoft\yii\content\components\display;

use svsoft\yii\content\models\ItemProperty;
use svsoft\yii\content\models\ValueFile;

/**
 * Class Type
 * @package svsoft\yii\content\models
 *
 */
class PropertyFile extends Property
{
    protected function loadDisplayValues(ItemProperty $itemProperty)
    {
        $values = [];
        /**
         * @var $valueModel ValueFile
         */
        foreach($itemProperty->valueModels as $valueModel)
        {
            $values[] = $valueModel->getFileWebPath();
        }

        return $values;
    }
}
