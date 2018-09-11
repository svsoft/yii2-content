<?php

namespace svsoft\yii\content\components\display;

use svsoft\yii\content\models\ItemProperty;
use yii\helpers\HtmlPurifier;

/**
 * Class Type
 * @package svsoft\yii\content\models
 *
 */
class PropertyHtml extends Property
{
    protected function loadDisplayValues(ItemProperty $itemProperty)
    {
        $values = [];
        foreach($itemProperty->getValues() as $value)
        {
            $values[] = HtmlPurifier::process($value);
        }

        return $values;
    }
}
