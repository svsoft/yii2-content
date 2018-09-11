<?php

namespace svsoft\yii\content\components\display;

use svsoft\yii\content\models\ItemProperty;

/**
 * Class Type
 * @package svsoft\yii\content\models
 *
 */
class PropertyInt extends PropertyFloat
{
    protected function loadDisplayValues(ItemProperty $itemProperty)
    {
        return $this->values;
    }
}
