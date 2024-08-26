(function (window, undefined) {

    'use strict';
    /**
     * Notification Functions : 11 JAN 2020
     * LivelyWorks
     *
     *-------------------------------------------------------- */
    var notyDefaultOptions = {
        layout: 'topRight',
        theme: 'bootstrap-v4',
        progressBar: true,
        timeout: 3000,
        closeWith: ['click'],
        animation: {
            open: 'animated bounceInRight', // Animate.css class names
            close: 'animated bounceOutRight'
        }
    };

    /**
    * Show Message
    *************************************************/
    window.__showMessage = function (message, type, options) {
        if (!window['mdtoast']) {
            __Utils.warn('mdtoast not found for showMessage');
            return '';
        }
        if (!options) {
            var options = {};
        }
        var mdToastOptions = $.extend({}, {}, options);;
        switch (type) {
            case 'success':
                mdToastOptions.type = mdtoast.SUCCESS;
                break;
            case 'error':
                mdToastOptions.type = mdtoast.ERROR;
                break;
            case 'warning':
                mdToastOptions.type = mdtoast.WARNING;
                break;
            case 'info':
                mdToastOptions.type = mdtoast.INFO;
                break;
            default:
                mdtoast(message);
                break;
        }
        mdtoast(message, mdToastOptions);
    }

    /**
    * Show Success Message
    *************************************************/
    window.showSuccessMessage = function (message) {
        window.__showMessage(message, 'success');
    }

    /*
    * Show Error Message
    *************************************************/
    window.showErrorMessage = function (message) {
        window.__showMessage(message, 'error');
    };

    /*
    * Show Info Message
    *************************************************/
    window.showInfoMessage = function (message) {
        window.__showMessage(message, 'info');
    };

    /*
    * Show Warning Message
    *************************************************/
    window.showWarnMessage = function (message) {
        window.__showMessage(message, 'warning');
    };

    /*
    * Show confirmation dialog
    *************************************************/
    window.showConfirmation = function (containerId, yesCallback, options, confirmParams) {

        var $messageItem = (!_.includes(containerId, ' ')) ? $(containerId) : false,
            confirmationContainer = '';

        if ($messageItem && $messageItem.length) {
            confirmationContainer = _.template($messageItem.html());
        } else {
            confirmationContainer = containerId;
        }
        if (!options) {
            options = {};
        }
        options = _.assign({
            cancelButtonText: __Utils.getTranslation('confirmation_no', 'Cancel'),
            confirmButtonText: __Utils.getTranslation('confirmation_yes', 'Yes')
        }, options);

        if (!confirmParams) {
            confirmParams = {};
        }

        Swal.fire({
            html: _.isString(confirmationContainer) ? confirmationContainer : confirmationContainer(confirmParams),
            icon: options['type'] ? options['type'] : 'warning',
            showCancelButton: options['showCancelBtn'] ? options['showCancelBtn'] : true,
            confirmButtonColor: options['confirmBtnColor'] ? options['confirmBtnColor'] : '#d33d33', // 3085d6
            // cancelButtonColor: '#d33',
            cancelButtonText: options['cancelButtonText'] ? options['cancelButtonText'] : 'Cancel',
            confirmButtonText: options['confirmButtonText'] ? options['confirmButtonText'] : 'Yes'
        }).then(function (result) {
            if (result.isConfirmed) {
                yesCallback();
            }
        });
    };

    window.showAlert = function (message, type) {
        Swal.fire({
            icon: type ? type : 'info',
            text: message
        })
    };

})(window);