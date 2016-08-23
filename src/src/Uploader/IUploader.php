<?php

namespace Vojtys\Utils;

use Nette;

/**
 * Interface IUploader
 * @package Vojtys\Utils
 */
interface IUploader
{
    /**
     * @param Nette\Http\FileUpload $file
     * @param $path
     * @return string
     */
    public function upload(Nette\Http\FileUpload $file, $path);
}