<?php
/**
 * Created by PhpStorm.
 * User: Денис
 * Date: 19.12.2018
 * Time: 9:58
 * @author Denis Kolosov <kdnn@mail.ru>
 */

namespace svsoft\yii\content\components\files;

use svsoft\yii\content\interfaces\File;
use yii\base\BaseObject;
use yii\base\InvalidArgumentException;

/**
 *
 * @property string $extension
 * @property string $baseName
 */
class RemoteFile extends BaseObject implements File
{
    public static $tmpDir;

    public $urlFile;

    public $tmpFile;

    public $name;

    protected $loaded = false;

    public function __destruct()
    {
        if ($this->tmpFile && file_exists($this->tmpFile))
        {
            @unlink($this->tmpFile);
        }
    }

    public function init()
    {
        if (!$this->urlFile)
        {
            throw new InvalidArgumentException('Property "urlFile" is not set');
        }

        parent::init();

        if (!self::$tmpDir)
        {
            self::$tmpDir = ini_get('upload_tmp_dir');
        }

        $this->name = basename($this->urlFile);
    }

    /**
     * @return bool
     * @throws \HttpResponseException
     */
    public function download(): bool
    {
        if (!file_exists(self::$tmpDir))
        {
            return false;
        }

        $this->tmpFile = tempnam(self::$tmpDir, null);

        if (file_exists($this->tmpFile) && copy($this->urlFile, $this->tmpFile))
        {
            $this->loaded = true;

            return true;
        }

        throw new \HttpResponseException('Ошибка скачивания файла');
    }

    /**
     * @param string $filePath
     * @param bool $deleteTempFile
     *
     * @return bool
     */
    public function saveAs($filePath, $deleteTempFile = true): bool
    {
        if ($this->loaded)
        {
            return rename($this->tmpFile, $filePath);
        }

        return false;
    }

    /**
     * @return string имя файла
     */
    public function getBaseName(): string
    {
        $pathInfo = pathinfo('_' . $this->name, PATHINFO_FILENAME);

        return mb_substr($pathInfo, 1, mb_strlen($pathInfo, '8bit'), '8bit');
    }

    /**
     * @return string расширение файла
     */
    public function getExtension(): string
    {
        return strtolower(pathinfo($this->name, PATHINFO_EXTENSION));
    }
}