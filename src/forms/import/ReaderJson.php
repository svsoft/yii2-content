<?php

namespace svsoft\yii\content\forms\import;

use yii\base\InvalidParamException;
use yii\helpers\Json;

/**
 * Class TypeImport
 * @package svsoft\yii\content\forms
 */
class ReaderJson extends Reader
{
    protected $parseJsonError = false;

    public function attributeLabels()
    {
        return [
            'content'=>'Json',
        ];
    }

    function contentValidator($attribute)
    {
        if ($this->parseJsonError)
            $this->addError($attribute, $this->getAttributeLabel('content') . ' должен быть в формате json');
    }

    /**
     * Парсит сожержимое иморта возвращает данные в виде массива
     * @param $content
     *
     * @return array|mixed
     */
    public function parseContent($content)
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
