<?php

namespace Vojtys\Controls;

use Nette;

/**
 * Class BootstrapDropzoneExtension
 * @package Vojtys\Controls
 */
class BootstrapDropzoneExtension extends Nette\DI\CompilerExtension
{
    /** @var array  */
    public $defaults = [
        'wwwDir' => '%wwwDir%',
        'thumbnailWidth' => NULL,
        'thumbnailHeight' => NULL,
        'parallelUploads'=> NULL,
        'autoQueue' => NULL
    ];

    public function loadConfiguration()
    {
        // validate config
        $config = $this->validateConfig($this->defaults);

        // add bootstrap dropzone
        $builder = $this->getContainerBuilder();
        $builder->addDefinition($this->prefix('dropzone'))
            ->setFactory('Vojtys\Controls\BootstrapDropzoneFactory')
            ->setArguments(['wwwDir' => $config['wwwDir']]);
    }
}