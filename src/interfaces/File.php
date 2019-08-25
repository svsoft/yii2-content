<?php
/**
 * Created by PhpStorm.
 * User: Денис
 * Date: 19.12.2018
 * Time: 12:19
 * @author Denis Kolosov <kdnn@mail.ru>
 */

namespace svsoft\yii\content\interfaces;

interface File
{
    /**
     * @param string $file
     * @param bool $deleteTempFile
     *
     * @return bool
     */
    public function saveAs($file, $deleteTempFile = true);

    /**
     * @return string original file base name
     */
    public function getBaseName();

    /**
     * @return string file extension
     */
    public function getExtension();
}