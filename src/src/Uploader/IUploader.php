<?php

namespace Vojtys\Utils;

use Nette;

/**
 * Interface IUploader
 * @package Vojtys\Utils
 */
interface IUploader
{
    public function upload(Nette\Http\FileUpload $file, $path);
}