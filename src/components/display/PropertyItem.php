<?php

namespace svsoft\yii\content\components\display;

use svsoft\yii\content\models\ItemProperty;
use svsoft\yii\content\models\ValueItem;
use svsoft\yii\content\traits\ModuleTrait;

/**
 * Class Type
 * @package svsoft\yii\content\models
 *
 */
class PropertyItem extends Property
{
    use ModuleTrait;
    protected function loadDisplayValues(ItemProperty $itemProperty)
    {
        $values = [];

        /**
         * @var $valueModel ValueItem
         */
        foreach($itemProperty->valueModels as $valueModel)
        {
            $values[] = self::getModule()->getter->getItemById($valueModel->value, $itemProperty->property->type_id);
            //$values[] = Item::instance($valueModel->valueItem);
        }

        return $values;
    }
}
