<?php

namespace svsoft\yii\content\components\display;

use svsoft\yii\content\models\ItemProperty;
use svsoft\yii\content\models\ValueItem;

/**
 * Class Type
 * @package svsoft\yii\content\models
 *
 */
class PropertyItem extends Property
{
    protected function loadDisplayValues(ItemProperty $itemProperty)
    {
        $values = [];

        /**
         * @var $valueModel ValueItem
         */
        foreach($itemProperty->valueModels as $valueModel)
        {
            $values[] = Item::instance($valueModel->valueItem);
        }

        return $values;
    }
}
