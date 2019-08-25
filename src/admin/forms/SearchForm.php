<?php

namespace svsoft\yii\content\admin\forms;

use svsoft\yii\content\models\Type;

class SearchForm extends \yii\base\DynamicModel
{
    function __construct(Type $type)
    {

        $attributes = [
            'item_id','type_id','name','slug'
        ];
        foreach($type->properties as $property)
        {
            $attributes[] = $property->name;
            $this->addRule($property->name, 'string');
        }


        parent::__construct($attributes, []);
    }
}