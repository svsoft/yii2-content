<?php

namespace svsoft\yii\content\models;

use svsoft\yii\content\interfaces\File;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%svs_content_value}}".
 *
 * @property integer $value_id
 * @property integer $item_id
 * @property integer $property_id
 * @property integer $value_item_id
 * @property string $value_string
 * @property string $value_text
 * @property integer $value_int
 * @property double $value_float
 * @property mixed $value
 *
 * @property Property $property
 * @property Item $item
 * @property string $valueField
 * @property Item $valueItem - Модель элемента для комплексного типа свойств
 */
class ValueFile extends Value
{
    /**
     * @return array
     */
    public function rules()
    {
        $rules = parent::rules();

        $rules['value'] = [['value'], 'file', 'maxFiles' => 1, 'checkExtensionByMimeType' => false];

        return $rules;
    }

    /**
     * @return string
     */
    public function getValueField(): string
    {
        return 'value_string';
    }

    /**
     * @param $value
     *
     * @return string
     */
    public function prepareSetValue($value)
    {
        if ($value instanceof File)
        {
            return $value;
        }

        if (!$value)
        {
            $value = null;
        }

        return $value;
    }

    /**
     * @param Type $newType
     *
     * @return false|int
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function changeType(Type $newType)
    {
        return $this->delete();
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value_string;
    }

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert))
        {
            return false;
        }

        if ($this->value instanceof File)
        {
            $uploadedFile = $this->value;
            $filename = $this->generateFileName();
            $this->value = $filename;
            $uploadedFile->saveAs($this->getFileDirPath());
        }

        return true;
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if (!$insert)
        {
            // Удаляем файл если загрузили новый
            $oldFilename = ArrayHelper::getValue($changedAttributes, $this->getValueField());
            if ($oldFilename && $this->value != $oldFilename)
                $this->deleteFile($this->getFileDirPath($oldFilename));
        }
    }

    public function beforeDelete()
    {
        if (!parent::beforeDelete())
            return false;

        $oldFilename = $this->getOldValue();

        if ($oldFilename)
            $this->deleteFile($this->getFileDirPath($oldFilename));

        return true;
    }

    protected function deleteFile($filePath)
    {
        if (file_exists($filePath) && !is_dir($filePath))
            return unlink($filePath);

        return true;
    }

    /**
     * Получает src до файла
     *
     * @param null $filename
     *
     * @return null|string
     */
    public function getFileWebPath($filename = null)
    {
        if ($filename === null)
        {
            $filename = $this->value;
        }

        if ($filename instanceof File)
        {
            return null;
        }

        return self::getModule()->webDirPath . DIRECTORY_SEPARATOR . $filename;
    }

    /**
     * Получает путь на диске до файла
     *
     * @param null|string $filename
     *
     * @return null|string
     */
    public function getFileDirPath($filename = null)
    {
        if ($filename === null)
        {
            $filename = $this->value;
        }

        if ($filename instanceof File)
        {
            return null;
        }

        return self::getModule()->fileDirPath . DIRECTORY_SEPARATOR . $filename;
    }

    /**
     * Генерирует имя файла для загруженного файла
     *
     * @return null|string
     */
    public function generateFileName()
    {
        if (!$this->value instanceof File)
        {
            return null;
        }

        return md5($this->value->baseName . time()) . '.' . $this->value->extension;
    }
}
