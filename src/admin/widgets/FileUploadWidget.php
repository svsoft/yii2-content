<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace svsoft\yii\content\admin\widgets;

use Yii;

/**
 * Class UploadFormWidget
 * @package svsoft\yii\content\admin\widgets
 */
class FileUploadWidget extends \yii\bootstrap\Widget
{
    public $model;

    public $form;

    public $multiple;

    public $attribute;

    public $files;

    public $webDirPath;
    
    public function run()
    {
        $model = $this->model;

        if (!isset($this->form->options['enctype'])) {
            $this->form->options['enctype'] = 'multipart/form-data';
        }

        $view = Yii::$app->getView();

        FileUploadAsset::register($view);

        return $this->render('file-upload-widget', ['form' => $this->form, 'model' => $model, 'attribute'=>$this->attribute, 'files'=>$this->files, 'widget'=>$this]);
    }
}
