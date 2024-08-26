(function ($) {
    "use strict"; // Start of use strict

    __globals.translate_strings['uploader_default_text'] = "<span class='filepond--label-action'><?= __tr('Drag & Drop Files or Browse') ?></span>";
    // Toggle the side navigation
    $("#sidebarToggle, #sidebarToggleTop").on('click tap', function (e) {
        $("body").toggleClass("sidebar-toggled");
        $(".sidebar").toggleClass("toggled");
        if ($(".sidebar").hasClass("toggled")) {
            $('.sidebar .collapse').collapse('hide');
        };
    });

    // Close any open menu accordions when window is resized below 768px
    $(window).resize(function () {
        if ($(window).width() < 768) {
            $('.sidebar .collapse').collapse('hide');
        };
    });

    if ($(window).width() < 768) {
        $("body").toggleClass("sidebar-toggled");
        $(".sidebar").toggleClass("toggled");
    };

    // Prevent the content wrapper from scrolling when the fixed side navigation hovered over
    $('body.fixed-nav .sidebar').on('mousewheel DOMMouseScroll wheel', function (e) {
        if ($(window).width() > 768) {
            var e0 = e.originalEvent,
                delta = e0.wheelDelta || -e0.detail;
            this.scrollTop += (delta < 0 ? 1 : -1) * 30;
            e.preventDefault();
        }
    });

    // Scroll to top button appear
    $(document).on('scroll', function () {
        var scrollDistance = $(this).scrollTop();
        if (scrollDistance > 100) {
            $('.scroll-to-top').fadeIn();
        } else {
            $('.scroll-to-top').fadeOut();
        }
    });

    // Smooth scrolling using jQuery easing
    $(document).on('click', 'a.scroll-to-top', function (e) {
        var $anchor = $(this);
        $('html, body').stop().animate({
            scrollTop: ($($anchor.attr('href')).offset().top)
        }, 1000, 'easeInOutExpo');
        e.preventDefault();
    });
    // Use for filepond file uploader
    window.initUploader = function () {
        if (window['FilePond']) {
            FilePond.registerPlugin(
                FilePondPluginImagePreview,
                FilePondPluginFilePoster,
                FilePondPluginFileValidateType,
                FilePondPluginMediaPreview
            );
            $('.lw-file-uploader').each(function (index, uploader) {
                var $this = $(this);
                var actionUrl = $this.data('action'),
                    responseCallback = $this.data('callback'),
                    defaultImage = $this.data('default-image-url'),
                    removeMediaAfterUpload = $this.data('remove-media'),
                    allowReplace = $this.data('allow-replace'),
                    allowRevert = $this.data('allow-revert'),
                    removeAllMediaAfterUpload = $this.data('remove-all-media'),
                    allowedMediaExtension = $this.data('allowed-media'),
                    filePondAdditionalOptions = {
                        allowVideoPreview: false,
                        allowImagePreview: false,
                        maxParallelUploads: 10,
                        imagePreviewMaxHeight: 175,
                        labelIdle: $this.data('label-idle') ? $this.data('label-idle') : __Utils.getTranslation('uploader_default_text'),
                        acceptedFileTypes: allowedMediaExtension,
                        fileValidateTypeDetectType: function (source, type) {
                            return new Promise(function (resolve, reject) {
                                if (allowedMediaExtension) {
                                    if (allowedMediaExtension.indexOf(type) < 0) {
                                        reject();
                                    }
                                }
                                resolve(type);
                            })
                        },
                        allowRevert: allowRevert ? allowRevert : false,
                        allowReplace: allowReplace ? allowReplace : false,
                        credits: false,
                        server: {
                            process: {
                                url: actionUrl,
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': appConfig.csrf_token
                                },
                                withCredentials: false,
                                onload: function (response) {
                                    var requestData = JSON.parse(response);
                                    // Show message when upload complete
                                    switch (requestData.reaction) {
                                        case 1:
                                            $('.lw-uploaded-file').val(requestData.data.fileName);
                                            showSuccessMessage(requestData.data.message);
                                            break;
                                        case 14:
                                            showWarnMessage(requestData.data.message);
                                            break;
                                        default:
                                            showErrorMessage(requestData.data.message);
                                            break;
                                    }

                                    if ($this.data('file-input-element')) {
                                        var $fileInputElement = $($this.data('file-input-element'));
                                        if ($fileInputElement.length) {
                                            $fileInputElement.val(requestData.data.fileName);
                                        }
                                    }
                                    // use this if you require raw uploaded like original name etc
                                    if ($this.data('raw-upload-data-element')) {
                                        var $rawUploadElement = $($this.data('raw-upload-data-element'));
                                        if ($rawUploadElement.length) {
                                            $rawUploadElement.val(JSON.stringify(requestData.data));
                                        }
                                    }
                                    var responseCallbackFn = window[responseCallback]
                                    if (typeof responseCallbackFn === 'function') {
                                        responseCallbackFn(requestData, $this);
                                    }
                                },
                                onerror: function (response) {
                                    var requestData = JSON.parse(response);
                                    // Show message when upload complete
                                    if (requestData.reaction) {
                                        showErrorMessage(requestData.data.message);
                                    }
                                }
                            }
                        },
                        onprocessfile: function (error, file) {
                            if (removeMediaAfterUpload) {
                                pond.removeFile(file.id);
                            }
                            if (removeAllMediaAfterUpload) {
                                pond.removeFiles();
                            }
                        },
                        onremovefile: function (error, file) {
                            $('.lw-uploaded-file').val('');
                            if ($this.data('file-input-element')) {
                                var $fileInputElement = $($this.data('file-input-element'));
                                if ($fileInputElement.length) {
                                    $fileInputElement.val('');
                                }
                            }
                            // use this if you require raw uploaded like original name etc
                            if ($this.data('raw-upload-data-element')) {
                                var $rawUploadElement = $($this.data('raw-upload-data-element'));
                                if ($rawUploadElement.length) {
                                    $rawUploadElement.val('');
                                }
                            }
                        }
                    };
                if (defaultImage && typeof defaultImage != 'undefined' && !_.isEmpty(defaultImage)) {
                    filePondAdditionalOptions = _.assign(filePondAdditionalOptions, {
                        files: [
                            {
                                // set type to local to indicate an already uploaded file
                                options: {
                                    type: 'local',
                                    file: {
                                        name: '',
                                        size: uploader.size,
                                        type: 'image/jpg'
                                    },
                                    // Pass Default Image Url
                                    metadata: {
                                        poster: defaultImage
                                    }
                                }
                            }
                        ]
                    });
                }

                var pond = FilePond.create(this, filePondAdditionalOptions);
            });
        }
    };
    initUploader();
    window.lwCopyToClipboard = function (elementIdToSelect) {
        /* Get the text field */
        var copyTextElement = document.getElementById(elementIdToSelect);
        /* Select the text field */
        copyTextElement.select();
        copyTextElement.setSelectionRange(0, 99999); /* For mobile devices */
        /* Copy the text inside the text field */
        window.navigator.clipboard.writeText(copyTextElement.value);
    };

    window.lwScrollTo = function (elem, doNotHighlightElement) {
        var $messageItem = $(elem);
        if (!doNotHighlightElement) {
            $(elem).addClass('lw-highlight-replied-message');
            setTimeout(function () {
                $(elem).removeClass('lw-highlight-replied-message');
            }, 2000);
        }
        _.defer(function () {
            document.getElementById('lwConversionChatContainer').scrollBy({
                top: ($(elem).offset().top - ($('#lwConversionChatContainer').outerHeight() * 0.5)),
                behavior: "smooth",
            });
        });
    };

    var photoSwipeGallery = function (items, index) {

        //default index
        var index = parseInt(index);

        // default options
        var options = {
            index: index,
            history: false,
            focus: false,
            closeEl: true,
            captionEl: true,
            fullscreenEl: true,
            zoomEl: true,
            shareEl: false,
            counterEl: true,
            arrowEl: true,
            preloaderEl: true,
            tapToToggleControls: false,
            showAnimationDuration: 0,
            hideAnimationDuration: 0
        };

        var gallery = new PhotoSwipe(document.querySelectorAll('.pswp')[0], PhotoSwipeUI_Default, items, options);
        gallery.init();
    }

    //for handling photoswipe gallery
    $('.lw-datatable-photoswipe-gallery').on('click', function (event) {

        var items;
        var index = 0;

        if ($(event.target).hasClass('lw-photoswipe-gallery-img')) {
            // for fetching  all imgs url
            items = [{
                'src': $(event.target).attr('src'),
                'w': 900,
                'h': 900
            }];

            photoSwipeGallery(items, index);
        }
    });

    $('.lw-photoswipe-gallery-img').on('click', function (event) {

        var siblings = $(this).siblings('.lw-photoswipe-gallery-img').addBack();
        var items;
        var index = 0;

        if (siblings.length > 0) {
            items = siblings.map(function (index, elem) {
                return {
                    'src': $(elem).attr('src'),
                    'w': 900,
                    'h': 900
                }
            });

            //if index is set
            if ($(event.target).data('img-index')) {
                index = $(event.target).data('img-index');
            }

            // if items not empty
            if (items.length > 0) {
                photoSwipeGallery(items, index);
            }
        } else {
            items = [{
                'src': $(event.target).attr('src'),
                'w': 900,
                'h': 900
            }];
            // if items not empty
            if (items.length > 0) {
                photoSwipeGallery(items, index);
            }
        }
    });

    var applyLazyImages = function () {
        if (window['Lazy']) {
            $(".lw-lazy-img").Lazy({
                beforeLoad: function ($element) {
                    // called before an elements gets handled
                },
                afterLoad: function ($element) {
                    // called after an element was successfully handled
                    $element.addClass('lw-lazy-img-loaded');
                },
                onError: function ($element) {
                    $element.addClass('lw-lazy-img-error');
                    console.log('error loading ' + $element.data('src'));
                }
            });
        }

    }

    $(document).on('lwPrepareUploadPlugIn', function (e, options) {
        window.initUploader();
    });

    // initialize
    applyLazyImages();

})(jQuery); // End of use strict