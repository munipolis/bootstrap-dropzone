(function($, window, document, location, navigator) {

    /* jshint laxbreak: true, expr: true */
    "use strict";

    // Init default objects
    var Vojtys = window.Vojtys || {};

    // Check dependences
    if (Dropzone === undefined) {
        console.error('Plugin "dropzone.js" is missing!');
        return;
    } else if ($.nette === undefined) {
        console.error('Plugin "nette.ajax.js" is missing!.');
        return;
    }

    /**
     * Init VojtysDropzone
     *
     * @returns {*}
     */
    $.fn.initVojtysDropzone = function() {
        return this.each(function() {
            var $this = $(this);

            if (!$this.data('vojtys-dropzone')) {
                $this.data('vojtys-dropzone', (new Vojtys.Dropzone($this, $this.data('settings'))));
            }
        });
    };

    /**
     * Setup and create Dropzone object
     *
     * @param $element
     * @param options
     * @constructor
     */
    Vojtys.Dropzone = function($element, options) {

        var id = $element.attr('id');
        var settings = $element.data('vojtys-dropzone-settings');
        var previewNode = document.querySelector('#' + id + '-vojtys-dropzone-template');
        var previewTemplate = previewNode.parentNode.innerHTML,
            labelFiles = $element.data('vojtys-dropzone-files'),
            labelChosen = $element.data('vojtys-dropzone-chosen'),
            labelUploaded = $element.data('vojtys-dropzone-uploaded'),
            labelProcess = $element.data('vojtys-dropzone-process'),
            uploadBtnLabel = $element.find(settings.clickable + ' span'),
            uploaded = 0,
            count = 0;
        var labelDefault = uploadBtnLabel.html();

        previewNode.id = "";
        previewNode.parentNode.removeChild(previewNode);
        settings = $.extend({}, settings, {'previewTemplate' : previewTemplate});

        var myDropzone = new Dropzone($element.get(0), settings);

        /* DROPZONE EVENTS -------------------------------------------------------------------------------------------*/

        myDropzone.on("addedfile", function(file) {
            count = myDropzone.getFilesWithStatus(Dropzone.ADDED).length;
            uploadBtnLabel.text(labelChosen + ' ' + count + ' ' + labelFiles);
        });
        myDropzone.on("removedfile", function() {
            count = myDropzone.getFilesWithStatus(Dropzone.ADDED).length;
            uploadBtnLabel.text(labelChosen + ' ' + count + ' ' + labelFiles);
        });
        myDropzone.on("success", function(file, responseText) {
            //this.removeFile(file);
        });
        myDropzone.on("totaluploadprogress", function(progress) {
            liveLabel(labelProcess);
        });
        myDropzone.on("sending", function(file) {
            // @TODO show preloader
        });
        myDropzone.on("queuecomplete", function(progress) {
            liveLabel(labelUploaded);
            // @TODO hide preloader
        });
        document.querySelector('#' + id + ' .start').onclick = function() {
            myDropzone.enqueueFiles(myDropzone.getFilesWithStatus(Dropzone.ADDED));
        };
        document.querySelector('#' + id + ' .cancel').onclick = function() {
            myDropzone.removeAllFiles(true);
            uploadBtnLabel.text(labelDefault);
        };

        function liveLabel(label) {
            if (!settings.autoQueue) {
                uploaded = count - myDropzone.getFilesWithStatus(Dropzone.UPLOADING).length;
                uploadBtnLabel.text(label + ' ' + uploaded + '/' + count);
            } else {
                uploadBtnLabel.text(label)
            }
        }
    };

    // Autoload dropzone
    $('[data-vojtys-dropzone]').initVojtysDropzone();

    // Assign to DOM
    window.Vojtys = Vojtys;

    // Return Objects
    return Vojtys;

    // Immediately invoke function with default parameters
})(jQuery, window, document, location, navigator);

