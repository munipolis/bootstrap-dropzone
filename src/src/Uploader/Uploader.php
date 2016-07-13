<?php

namespace Vojtys\Utils;

use Nette;

/**
 * Class Uploader
 * @package Vojtys\Utils
 */
class Uploader extends Nette\Object implements IUploader
{
    protected $wwwDir;

    /**
     * Uploader constructor.
     * @param $wwwDir
     */
    public function __construct($wwwDir)
    {
        $this->wwwDir = $wwwDir;
    }

    /**
     * @param Nette\Http\FileUpload $file
     * @param $path
     */
    public function upload(Nette\Http\FileUpload $file, $path)
    {
        try {
            $tempFile = $file->getTemporaryFile();
            $uploadPath = $this->wwwDir . $path;
            $mainFile = $uploadPath . time() . '-' . $file->getName();
            move_uploaded_file($tempFile, $mainFile);
        } catch (\Exception $e) {
            header('HTTP/1.1 500 Internal Server Error');
            header('Content-type: text/plain');
            exit($e->getMessage());
        }
    }
}