<?php

namespace Vojtys\Controls;

use Nette;
use Nette\Http\Request;
use Nette\Http\Response;
use Vojtys\Utils\IUploader;
use Vojtys\Utils\Uploader;

/**
 * Class BootstrapDropzone
 *
 * @package Vojtys\Controls
 * @author Vojtech sedlacek (sedlacekvojtech@gmail.com)
 */
class BootstrapDropzone extends Nette\Application\UI\Control
{
    const DEFAULT_TEMPLATE = 'templates/bootstrap-dropzone.latte';
    const DEFAULT_PREVIEW_TEMPLATE = 'templates/bootstrap-dropzone-preview.latte';
    const DEFAULT_PANEL_TEMPLATE = 'templates/bootstrap-dropzone-panel.latte';
    const DEFAULT_PREVIEW_CONTAINER = 'vojtys-dropzone-previews';
    const DEFAULT_CLICKABLE = 'vojtys-dropzone-clickable';
    const DEFAULT_AUTO_PROCESS_QUEUE = TRUE;
    const DEFAULT_THUMBNAIL_WIDTH = 50;
    const DEFAULT_THUMBNAIL_HEIGHT = 50;
    const DEFAULT_PARALLEL_UPLOADS = 20;
    const DEFAULT_AUTO_QUEUE = FALSE;
    const DEFAULT_PREVIEW_DISABLED = FALSE;


    /** @var string  */
    protected $bdTemplate = self::DEFAULT_TEMPLATE;

    /** @var  string */
    protected $previewTemplate;

    /** @var bool  */
    protected $previewDisabled = self::DEFAULT_PREVIEW_DISABLED;

    /** @var  string */
    protected $panelTemplate;

    /** @var int  */
    protected $thumbnailWidth = self::DEFAULT_THUMBNAIL_WIDTH;

    /** @var int  */
    protected $thumbnailHeight = self::DEFAULT_THUMBNAIL_HEIGHT;

    /** @var int  */
    protected $parallelUploads = self::DEFAULT_PARALLEL_UPLOADS;

    /** @var bool  */
    protected $autoProcessQueue = self::DEFAULT_AUTO_PROCESS_QUEUE;

    /**
     * Define the container to display the previews
     * @var string
     */
    protected $previewsContainer = self::DEFAULT_PREVIEW_CONTAINER;

    /**
     * Define the element that should be used as click trigger to select files.
     * @var string
     */
    protected $clickable = self::DEFAULT_CLICKABLE;

    /**
     * Make sure the files aren't queued until manually added
     * @var bool
     */
    protected $autoQueue = self::DEFAULT_AUTO_QUEUE;


    /** @var  IUploader */
    protected $uploader;

    /** @var  Request */
    protected $request;

    /** @var  Response */
    protected $response;

    /** @var  string */
    protected $wwwDir;

    /** @var  string */
    protected $path;

    /** @var  int */
    protected $id;

    /** @var  Nette\Localization\ITranslator */
    protected $translator = NULL;

    /** @var callable  */
    public $onFileUpload = [];

    /** @var callable */
    public $onUploadComplete = [];

    /** @var array  */
    protected $uploaded = [];


    /**
     * BootstrapDropzone constructor.
     * @param Request $request
     * @param Response $response
     * @param $wwwDir
     * @param $path
     */
    public function __construct(Request $request, Response $response, $wwwDir, $path)
    {
        $this->request = $request;
        $this->response = $response;
        $this->wwwDir = $wwwDir;
        $this->path = $path;
        $this->id = $this->generateId();
    }

    public function render()
    {
        $template = $this->createTemplate();
        $template->setTranslator($this->getTranslator());
        $template->setFile(dirname(__FILE__) . '/' . $this->bdTemplate);
        $template->settings = json_encode([
            'thumbnailWidth' => $this->getThumbnailWidth(),
            'thumbnailHeight' => $this->getThumbnailHeight(),
            'parallelUploads' => $this->getParallelUploads(),
            'previewsContainer' => $this->getPreviewsContainer(),
            'clickable' => $this->getClickable(),
            'autoQueue' => $this->getAutoQueue(),
            'url' => $this->getUrl(),
            'refreshUrl' => $this->getRefreshUrl(),
            'autoProcessQueue' => $this->isAutoProcessQueue(),
        ]);
        $template->id = $this->id;
        $template->render();
    }

    /**
     * @return mixed
     */
    protected function generateId()
    {
        $length = 8;
        $characters = 'abcdefghijklmnopqrstuvwxyz';
        $characters_length = strlen($characters) - 1;
        $string = '';

        for ($i = 0; $i < $length; $i++) {
            $string .= $characters[mt_rand(0, $characters_length)];
        }

        return $string;
    }

    public function handleUpload()
    {
        if ($this->request->files) {
            $uploader = $this->getUploader();

            /** @var Nette\Http\FileUpload $fileUpload */
            foreach ($this->request->files as $fileUpload) {
                $this->uploaded[] = $file = $uploader->upload($fileUpload, $this->path);

                // on upload file callback
                foreach ($this->onFileUpload as $callback) {
                    if (is_callable($callback)) {
                        call_user_func($callback, $fileUpload, $file);
                    }
                }
            }
        }
    }

    /**
     * Files upload success callback
     */
    public function handleUploadSuccess()
    {
        foreach ($this->onUploadComplete as $callback) {
            if (is_callable($callback)) {
                call_user_func($callback, $this->uploaded);
            }
        }
    }

    public function handleRefresh()
    {
        $this->redrawControl();
    }

    /**
     * @return BootstrapDropzonePreview
     */
    public function createComponentPreview()
    {
        $preview = new BootstrapDropzonePreview($this->getTranslator());
        if (!$this->isPreviewDisabled()) {
            $preview->setPreviewTemplate($this->getPreviewTemplate());
        }
        return $preview;
    }

    /**
     * @return BootstrapDropzonePanel
     */
    public function createComponentPanel()
    {
        $panel = new BootstrapDropzonePanel($this->id, $this->getTranslator());
        return $panel->setPanelTemplate($this->getPanelTemplate());
    }

    /**
     * @return string
     */
    public function getRefreshUrl()
    {
        return $this->link('refresh!');
    }

    /**
     * @return string
     */
    public function getBdTemplate()
    {
        return $this->bdTemplate;
    }

    /**
     * @param string $bdTemplate
     * @return BootstrapDropzone
     */
    public function setBdTemplate($bdTemplate)
    {
        $this->bdTemplate = $bdTemplate;
        return $this;
    }

    /**
     * @return int
     */
    public function getThumbnailWidth()
    {
        return $this->thumbnailWidth;
    }

    /**
     * @param int $thumbnailWidth
     * @return BootstrapDropzone
     */
    public function setThumbnailWidth($thumbnailWidth)
    {
        $this->thumbnailWidth = $thumbnailWidth;
        return $this;
    }

    /**
     * @return int
     */
    public function getThumbnailHeight()
    {
        return $this->thumbnailHeight;
    }

    /**
     * @param int $thumbnailHeight
     * @return BootstrapDropzone
     */
    public function setThumbnailHeight($thumbnailHeight)
    {
        $this->thumbnailHeight = $thumbnailHeight;
        return $this;
    }

    /**
     * @return int
     */
    public function getParallelUploads()
    {
        return $this->parallelUploads;
    }

    /**
     * @param int $parallelUploads
     * @return BootstrapDropzone
     */
    public function setParallelUploads($parallelUploads)
    {
        $this->parallelUploads = $parallelUploads;
        return $this;
    }

    /**
     * Returns selector for previews
     *
     * @return string
     */
    public function getPreviewsContainer()
    {
        return '#' . $this->id . '-' . $this->previewsContainer;
    }

    /**
     * Define the container to display the previews
     *
     * @param string $previewsContainer
     * @return BootstrapDropzone
     */
    public function setPreviewsContainer($previewsContainer)
    {
        $this->previewsContainer = $previewsContainer;
        return $this;
    }

    /**
     * Returns selector for clickable action
     *
     * @return string
     */
    public function getClickable()
    {
        return '.' . $this->id . '-' . $this->clickable;
    }

    /**
     * Define the element that should be used as click trigger to select files.
     *
     * @param string $clickable
     * @return BootstrapDropzone
     */
    public function setClickable($clickable)
    {
        $this->clickable = $clickable;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isAutoQueue()
    {
        return $this->autoQueue;
    }

    /**
     * @return bool
     */
    public function getAutoQueue()
    {
        return $this->autoQueue;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->link('upload!');
    }

    /**
     * @return IUploader|Uploader
     */
    public function getUploader()
    {
        if (!$this->uploader instanceof IUploader) {
            return new Uploader($this->wwwDir);
        } else {
            return $this->uploader;
        }
    }

    /**
     * @param IUploader $uploader
     * @return BootstrapDropzone
     */
    public function setUploader($uploader)
    {
        $this->uploader = $uploader;
        return $this;
    }

    /**
     * @return string
     */
    public function getPreviewTemplate()
    {
        return empty($this->previewTemplate) ?
            dirname(__FILE__) . '/' . self::DEFAULT_PREVIEW_TEMPLATE : $this->previewTemplate;
    }

    /**
     * @param string $previewTemplate
     * @return BootstrapDropzone
     */
    public function setPreviewTemplate($previewTemplate)
    {
        $this->previewTemplate = $previewTemplate;
        return $this;
    }

    /**
     * @return string
     */
    public function getPanelTemplate()
    {
        return empty($this->panelTemplate) ?
            dirname(__FILE__) . '/' . self::DEFAULT_PANEL_TEMPLATE : $this->panelTemplate;
    }

    /**
     * @param string $panelTemplate
     * @return BootstrapDropzone
     */
    public function setPanelTemplate($panelTemplate)
    {
        $this->panelTemplate = $panelTemplate;
        return $this;
    }

    /**
     * @return $this
     */
    public function disablePreviewTemplate()
    {
        $this->previewDisabled = TRUE;
        return $this;
    }

    /**
     * @return bool
     */
    public function isPreviewDisabled()
    {
        return ($this->previewDisabled == TRUE) ? TRUE : FALSE;
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
     * @return BootstrapDropzone
     */
    public function setTranslator($translator)
    {
        $this->translator = $translator;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isAutoProcessQueue()
    {
        return $this->autoProcessQueue;
    }

    /**
     * @return $this
     */
    public function setAutoUpload()
    {
        $this->autoProcessQueue = TRUE;
        $this->autoQueue = TRUE;
        return $this;
    }
}