<?php


namespace svsoft\yii\content\traits;

use svsoft\yii\content\ContentModule;

/**
 * Class ModuleTrait
 *
 * @property-read ContentModule $module
 *
 * @package svsoft\yii\content\traits
 */
trait ModuleTrait
{

    /**
     * @return ContentModule|\yii\base\Module
     */
    static public function getModule()
    {
        return \Yii::$app->getModule('content');
    }

}
