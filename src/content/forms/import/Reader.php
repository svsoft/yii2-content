<?php

namespace svsoft\yii\content\forms\import;

use yii\base\Model;

/**
 * Class TypeImport
 * @package svsoft\yii\content\forms
 */
abstract class Reader extends Model
{
    public $content;

    function rules()
    {
        return [
            ['content','required'],
            ['content','contentValidator']
        ];
    }

    abstract function contentValidator($attribute);

    /**
     * @param $content
     *
     * @return array|mixed
     */
    abstract public function parseContent($content);
}
