<?php

namespace svsoft\yii\content\components\display;

use svsoft\yii\content\models\ItemProperty;

/**
 * Class Type
 * @package svsoft\yii\content\models
 *
 */
class PropertyText extends PropertyString
{
    protected function loadDisplayValues(ItemProperty $itemProperty)
    {
        $values = [];
        foreach($itemProperty->getValues() as $value)
        {
            $values[] = nl2br($value);
        }

        return $values;
    }
}
