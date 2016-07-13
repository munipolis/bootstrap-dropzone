<?php

namespace Vojtys\Controls;

use Nette;

/**
 * Class BootstrapDropzonePreview
 * @package Vojtys\Controls
 * @author Vojtech sedlacek (sedlacekvojtech@gmail.com)
 */
class BootstrapDropzonePreview extends Nette\Application\UI\Control
{
    /** @var  string */
    protected $previewTemplate;

    /** @var  Nette\Localization\ITranslator */
    protected $translator;

    /**
     * BootstrapDropzonePreview constructor.
     * @param Nette\Localization\ITranslator|NULL $translator
     */
    public function __construct(Nette\Localization\ITranslator $translator = NULL)
    {
        $this->translator = $translator;
    }

    public function render()
    {
        if (!empty($this->previewTemplate)) {
            $template = $this->createTemplate();
            $template->setFile($this->previewTemplate);
            if (!is_null($this->translator)) {
                $template->setTranslator($this->getTranslator());
            }
            $template->render();
        }
    }

    /**
     * @param mixed $previewTemplate
     * @return BootstrapDropzonePreview
     */
    public function setPreviewTemplate($previewTemplate)
    {
        $this->previewTemplate = $previewTemplate;
        return $this;
    }

    /**
     * @return Nette\Localization\ITranslator
     */
    public function getTranslator()
    {
        return $this->translator;
    }

    /**
     * @param Nette\Localization\ITranslator $translator
     * @return BootstrapDropzonePreview
     */
    public function setTranslator($translator)
    {
        $this->translator = $translator;
        return $this;
    }
}