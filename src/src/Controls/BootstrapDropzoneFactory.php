<?php

namespace Vojtys\Controls;

use Nette;
use Nette\Http\Request;
use Nette\Http\Response;

/**
 * Class BootstrapDropzoneFactory
 * @package Vojtys\Controls
 */
class BootstrapDropzoneFactory extends Nette\Object implements IBootstrapDropzone
{
    /** @var  Request */
    protected $request;

    /** @var  Response */
    protected $response;

    /** @var  string */
    protected $wwwDir;

    /**
     * BootstrapDropzone constructor.
     * @param Request $request
     * @param Response $response
     * @param $wwwDir
     */
    public function __construct(Request $request, Response $response, $wwwDir)
    {
        $this->request = $request;
        $this->response = $response;
        $this->wwwDir = $wwwDir;
    }

    /**
     * @param $path
     * @return BootstrapDropzone
     */
    public function create($path)
    {
        return new BootstrapDropzone($this->request, $this->response, $this->wwwDir, $path);
    }
}