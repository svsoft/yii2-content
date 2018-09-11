<?php

namespace svsoft\yii\content\components\validators;

use yii\validators\RegularExpressionValidator;

class NameValidator extends RegularExpressionValidator
{
    public $pattern = '/^[a-z]\w*$/i';

    public function validateAttribute($model, $attribute)
    {
        $result = $this->validateValue($model->$attribute);
        if (!empty($result)) {
            $this->addError($model, $attribute, '"{attribute}" должно начинается с буквы и содержит только латинские буквы, числа и знак подчеркивания');
        }
    }
}