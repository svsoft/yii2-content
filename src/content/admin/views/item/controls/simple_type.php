<?php

use svsoft\yii\content\models\Type;
use svsoft\yii\content\models\ItemProperty;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $itemProperty ItemProperty */

switch($itemProperty->property->type->name)
{
    case Type::TYPE_FILE:
        echo $this->render('file', ['form' => $form, 'itemProperty' => $itemProperty]);
        break;
    case Type::TYPE_TEXT:
        echo $this->render('text', ['form' => $form, 'itemProperty' => $itemProperty]);
        break;
    case Type::TYPE_HTML:
        echo $this->render('html', ['form' => $form, 'itemProperty' => $itemProperty]);
        break;
    case Type::TYPE_DATE:
        echo $this->render('date', ['form' => $form, 'itemProperty' => $itemProperty]);
        break;
    case Type::TYPE_DATETIME:
        echo $this->render('datetime', ['form' => $form, 'itemProperty' => $itemProperty]);
        break;
    case Type::TYPE_BOOLEAN:
        echo $this->render('boolean', ['form' => $form, 'itemProperty' => $itemProperty]);
        break;
    default :
        echo $this->render('string', ['form' => $form, 'itemProperty' => $itemProperty]);
}

