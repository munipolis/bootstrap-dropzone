<?php

namespace Vojtys\Controls;

/**
 * Interface IBootstrapDropzone
 * @package Vojtys\Controls
 */
interface IBootstrapDropzone
{
    /**
     * @param $path
     * @return BootstrapDropzone
     */
    public function create($path);
}