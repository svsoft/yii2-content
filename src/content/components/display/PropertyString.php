<?php

namespace svsoft\yii\content\components\display;

use svsoft\yii\content\models\ItemProperty;
use yii\helpers\Html;

/**
 * Class Type
 * @package svsoft\yii\content\models
 *
 */
class PropertyString extends Property
{
    protected function loadDisplayValues(ItemProperty $itemProperty)
    {
        $values = [];
        foreach($itemProperty->getValues() as $value)
        {
            $values[] = Html::encode($value);
        }

        return $values;
    }
}
