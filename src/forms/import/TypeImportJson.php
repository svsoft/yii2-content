<?php

namespace svsoft\yii\content\forms\import;

use yii\base\InvalidParamException;
use yii\helpers\Json;

/**
 * Class TypeImport
 * @package svsoft\yii\content\forms
 */
class TypeImportJson extends TypeImport
{
    protected $parseJson;

    protected $parseJsonError = false;


    public function attributeLabels()
    {
        return [
            'content'=>'Json',
        ];
    }

    function rules()
    {
        $rules = parent::rules();

        $rules[] = ['content','jsonValidator'];

        return $rules;
    }

    function jsonValidator($attribute)
    {
        if ($this->parseJsonError)
            $this->addError($attribute, $this->getAttributeLabel('content') . ' должен быть в формате json');
    }

    /**
     * Парсит сожержимое иморта возвращает данные в виде массива
     *
     * @return mixed
     */
    protected function parseContent($content)
    {
        try
        {
            $typesData = Json::decode($this->content);
            if ($typesData && empty($typesData[0]))
                $typesData = [$typesData];

        }
        catch(InvalidParamException $e)
        {
            $this->parseJsonError = true;
            $typesData = [];
        }


        return $typesData;
    }
}
