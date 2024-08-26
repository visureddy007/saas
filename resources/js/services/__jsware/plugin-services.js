(function (window, $) {
    "use strict"; // Start of use strict
    /**
         * lw plugin functions container in window scope
         *
         * @var {object}
         */
    window.lwPluginFuncs = {
        /**
         * Initialize Date Picker
         *
         * @param  string  elementFinder  element identifier
         * @see https://amsul.ca/pickadate.js/date/
         * @return  void
         */
        lwDatePicker: function (elementFinder) {
            $(elementFinder).each(function (index, datePickerFieldElement) {
                var $datePickerFieldElement = $(datePickerFieldElement),
                    datePickerData = $($datePickerFieldElement).data();
                if (datePickerData) {
                    datePickerData = _.assign({
                        format: 'd mmmm yyyy',
                        formatSubmit: 'yyyy-mm-dd',
                        hiddenName: true
                    }, datePickerData);
                }
                $($datePickerFieldElement).pickadate(datePickerData);
                $datePickerFieldElement = datePickerFieldElement = datePickerData = null;
            });
            elementFinder = null;
        },
        /**
         * Initialize Time Picker
         *
         * @param  string  elementFinder  element identifier
         * @see https://amsul.ca/pickadate.js/date/
         * @return  void
         */
        lwTimePicker: function (elementFinder) {
            $(elementFinder).each(function (index, datePickerFieldElement) {
                var $datePickerFieldElement = $(datePickerFieldElement),
                    timePickerData = $($datePickerFieldElement).data();
                if (timePickerData) {
                    timePickerData = _.assign({
                        format: 'h:i A',
                        formatSubmit: 'HH:i',
                        hiddenName: true
                    }, timePickerData);
                }
                $($datePickerFieldElement).pickatime(timePickerData);
                $datePickerFieldElement = datePickerFieldElement = timePickerData = null;
            });
            elementFinder = null;
        },
        /**
         * Initialize Switchery
         *
         * @param  string  elementFinder  element identifier
         *
         * @return  void
         */
        lwSwitchery: function (elementFinder) {
            $(elementFinder).each(function (index, switcheryFieldElement) {
                var $switcheryFieldElement = $(switcheryFieldElement),
                    isAlreadyInitialized = $switcheryFieldElement.siblings('.switchery').length,
                    switcheryData = $($switcheryFieldElement).data();
                if (!isAlreadyInitialized) {
                    if (switcheryData) {
                        switcheryData = _.assign({
                            // color             : '#64bd63',
                            // secondaryColor    : '#dfdfdf',
                            // jackColor         : '#fff',
                            // jackSecondaryColor: null,
                            // className         : 'switchery',
                            // disabled          : false,
                            // disabledOpacity   : 0.5,
                            // speed             : '0.4s',
                            // size              : 'default',
                        }, switcheryData);
                    }
                    new Switchery($switcheryFieldElement[0], switcheryData);
                }
                $switcheryFieldElement = switcheryFieldElement = switcheryData = null;
            });
            elementFinder = null;
        },
        /**
     * Initialize Selectize
     *
     * @param   {string}  elementFinder  element identifier
     *
     * @notes use select for single selection and use input text for multiple selection
     * @return  {void}
     */
        lwSelectize: function (elementFinder) {
            $(elementFinder).each(function (index, selectizeFieldElement) {
                var $selectizeFieldElement = $(selectizeFieldElement);
                var options = {
                        onChange: function (value) {
                            var itemToUpdate = {},
                                inputModelName = $(this.$input[0]).attr('x-model');
                            // Update x-model value if present
                            if (inputModelName) {
                                itemToUpdate[inputModelName] = value;
                                __DataRequest.updateModels(itemToUpdate);
                            }
                            // dispatch event for change to grab
                            $(this.$input[0]).trigger('lwSelectizeOnChange', value);
                        }
                    },
                    selectizeFieldElementData = $selectizeFieldElement.data();
                if (selectizeFieldElementData) {
                    if (selectizeFieldElementData['selected']) {
                        options.items = _.isArray(selectizeFieldElementData['selected']) ? selectizeFieldElementData['selected'] : [selectizeFieldElementData['selected']];
                    }
                    options = _.assign(options, selectizeFieldElementData);
                }
                // use options eg. data-create="true" on the particular element
                try {
                    options.options = $.parseJSON($selectizeFieldElement.attr('data-options'));
                } catch (error) {
                    options.options = $selectizeFieldElement.attr('data-options');
                }
                $($selectizeFieldElement).selectize(options);
                // clear the memory
                options = $selectizeFieldElement = selectizeFieldElementData = selectizeFieldElement = null;
            });
            elementFinder = null;
        },

        /**
         * Initialize Signature Pad
         *
         * @param  string  elementFinder  element identifier
         *
         * @return  void
         */
        lwSignaturePad: function (elementFinder) {
            $(elementFinder).each(function (index, fieldElement) {
                var $fieldElement = $(fieldElement),
                    optionsData = $fieldElement.data(),
                    $inputField = $fieldElement.siblings('.lw-signature-input-field');
                if (!$inputField.val()) {
                    if (optionsData) {
                        optionsData = _.assign({
                            onEnd: function () {
                                var data = signaturePad.toDataURL('image/png');
                                $inputField.val(data);
                                // Send data to server instead...
                            }
                        }, optionsData);
                    }
                    var signaturePad = new SignaturePad(fieldElement, optionsData);
                    $fieldElement.parent().on('click', '.lw-clear-signature-btn', function (event) {
                        event.preventDefault();
                        $inputField.val('');
                        signaturePad.clear();
                    });
                    optionsData = null;

                    function resizeCanvas() {
                        var ratio = Math.max(window.devicePixelRatio || 1, 1);
                        fieldElement.width = fieldElement.offsetWidth * ratio;
                        fieldElement.height = fieldElement.offsetHeight * ratio;
                        fieldElement.getContext("2d").scale(ratio, ratio);
                        signaturePad.clear(); // otherwise isEmpty() might return incorrect value
                    }
                    window.addEventListener("resize", resizeCanvas);
                    resizeCanvas();
                }
            });
            elementFinder = null;
        },
        /**
         * Initialize text editor
         *
         * @param  string  elementFinder  element identifier
         * @see https://alex-d.github.io/Trumbowyg/documentation
         * @return  void
         */
        lwTextEditor: function (elementFinder) {
            $(elementFinder).each(function (index, textEditorFieldElement) {
                var $textEditorFieldElement = $(textEditorFieldElement),
                    textEditorData = $($textEditorFieldElement).data();
                if ($textEditorFieldElement.trumbowyg) {
                    $textEditorFieldElement.trumbowyg('destroy');
                }
                // icons path
                $textEditorFieldElement.trumbowyg({
                    btns: [
                        ['indent', 'outdent',],
                        ['unorderedList', 'orderedList'],
                    ],
                    autogrow: true
                });

            });
            elementFinder = null;
        },
        /**
         * Initialize Uploader
         *
         * @param  string  elementFinder  element identifier
         * @see Filepond
         * @return  void
         */
        lwUploader: function (elementFinder) {
            if (window['FilePond']) {
                FilePond.registerPlugin(
                    FilePondPluginImagePreview,
                    FilePondPluginFilePoster,
                    FilePondPluginFileValidateType
                );
                $(elementFinder).each(function (index, uploader) {
                    var $this = $(this);
                    var actionUrl = $this.data('action'),
                        responseCallback = $this.data('callback'),
                        defaultImage = $this.data('default-image-url'),
                        removeMediaAfterUpload = $this.data('remove-media'),
                        removeAllMediaAfterUpload = $this.data('remove-all-media'),
                        allowedMediaExtension = $this.data('allowed-media'),
                        filePondAdditionalOptions = _.assign({
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
                            allowRevert: false,
                            allowReplace: false,
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
                                                $this.find('.lw-uploaded-file').val(requestData.data.fileName);
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
                                        var responseCallbackFn = window[responseCallback];
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
                            }
                        }, $this.data ? $this.data : {});

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

                elementFinder = null;
            }
        },

        /**
         * Copy to clipboard functionality for text
         *
         * @param  string  elementFinder  element identifier
         * @since 19 APR 2022
         * @return  void
         */
        lwCopyToClipboard: function (elementFinder) {
            $(elementFinder).on('click', function () {
                var copyTextElement = document.getElementById($(this).data('target-id'));
                /* Select the text field */
                copyTextElement.select();
                copyTextElement.setSelectionRange(0, 99999); /* For mobile devices */
                /* Copy the text inside the text field */
                window.navigator.clipboard.writeText(copyTextElement.value);
            });
        },
    };

    window.lwPluginsInit = function (requestedElement) {
        if (!requestedElement) {
            requestedElement = '';
        }
        // default Initialization of plugins
        var $lwPluginFuncsElements = $(requestedElement + '[data-lw-plugin]');
        if ($lwPluginFuncsElements.length) {
            $lwPluginFuncsElements.each(function (index, element) {
                var funcToCall = $(element).data('lw-plugin');
                window.lwPluginFuncs[funcToCall]('[data-lw-plugin=' + funcToCall + ']');
                funcToCall = null;
            });
        }
        $lwPluginFuncsElements = null;
    };
    lwPluginsInit();
})(window, jQuery); // End of use strict