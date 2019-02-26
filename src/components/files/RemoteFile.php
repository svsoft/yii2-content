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
            self::$tmpDir = sys_get_temp_dir();
        }

        $this->name = basename($this->urlFile);
    }

    /**
     * @param string $filePath
     * @param bool $deleteTempFile
     *
     * @return bool
     */
    public function saveAs($filePath, $deleteTempFile = true): bool
    {
        if (!$this->download())
        {
            return false;
        }

        if (!rename($this->tmpFile, $filePath))
        {
            return false;
        }

        if (!chmod($filePath, 0644))
        {
            return false;
        }

        return true;
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
        $url = parse_url($this->name, PHP_URL_PATH);
        return strtolower(pathinfo($url, PATHINFO_EXTENSION));
    }

    /**
     * @return bool
     */
    protected function download(): bool
    {
        $this->tmpFile = tempnam(self::$tmpDir, null);

        return file_exists($this->tmpFile) && copy($this->urlFile, $this->tmpFile);
    }
}