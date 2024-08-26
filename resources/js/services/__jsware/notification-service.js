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
        timeout: 3000
    };


    /**
    * Show Success Message
    *************************************************/
    window.showSuccessMessage = function (message) {
        new Noty($.extend({}, notyDefaultOptions, {
            type: 'success',
            text: message
        })).show();
    }

    /**
    * Show Error Message
    *************************************************/
    window.showErrorMessage = function (message) {
        new Noty($.extend({}, notyDefaultOptions, {
            type: 'error',
            text: message
        })).show();
    };

    /**
    * Show Info Message
    *************************************************/
    window.showInfoMessage = function (message) {
        new Noty($.extend({}, notyDefaultOptions, {
            type: 'info',
            text: message
        })).show();
    };

    /**
    * Show Warning Message
    *************************************************/
    window.showWarnMessage = function (message) {
        new Noty($.extend({}, notyDefaultOptions, {
            type: 'warning',
            text: message
        })).show();
    };
    /**
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
            showCancelBtn: true,
            type: 'warning',
            confirmBtnColor: '#d33d33',
            cancelButtonText: __Utils.getTranslation('confirmation_no', 'Cancel'),
            confirmButtonText: __Utils.getTranslation('confirmation_yes', 'Yes')
        }, options);

        if (!confirmParams) {
            confirmParams = {};
        }

        Swal.fire({
            html: _.isString(confirmationContainer) ? confirmationContainer : confirmationContainer(confirmParams),
            icon: options['type'],
            showCancelButton: options['showCancelBtn'],
            confirmButtonColor: options['confirmBtnColor'], // 3085d6
            cancelButtonText: options['cancelButtonText'],
            confirmButtonText: options['confirmButtonText']
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