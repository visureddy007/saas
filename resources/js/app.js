"use strict";
window.__globals.default_show_message = true;
window.appFuncs = {
    modelSuccessCallback: function (data, callbackParams) {
        if ((data.reaction === 1) && callbackParams) {
            if (callbackParams.datatableId) {
                if (_.isArray(callbackParams.datatableId)) {
                    _.each(callbackParams.datatableId, function (index) {
                        $(index).dataTable().api().ajax.reload();
                    });
                } else {
                    $(callbackParams.datatableId).dataTable().api().ajax.reload();
                }
            }
            if (callbackParams.modalId) {
                $(callbackParams.modalId).modal('hide');
            }

            if (callbackParams.pageReload) {
                _.delay(function () {
                    __Utils.viewReload();
                }, 300);
            }
        }
    },
    clearContainer: function (data, $element) {
        var $responseHolder = $($element.data('response-template')),
            $responseTemplate = $($element.data('response-template') + '-template');
        $responseHolder.html(
            '<div class="lw-spinner-box"><div class="text-center align-middle"><div class="lds-ring"><div></div><div></div><div></div><div></div></div><div></div>'
        );
    },
    resetForm: function (data, $element) {
        var $targetForm = $('#whatsAppMessengerForm');
        $targetForm[0].reset();
        var validator = $targetForm.validate();
        validator.resetForm();
        if (jQuery().emojioneArea) {
            window.lwMessengerEmojiArea[0].emojioneArea.setText('');
        }
    },
    prepareUpload: function () {
    },
    formatWhatsAppText: function (text) {
        // Bold: Wrap text marked with * in <strong> tags
        text = text.replace(/\*(.*?)\*/g, '<strong>$1</strong>');

        // Italics: Wrap text marked with _ in <em> tags
        text = text.replace(/_(.*?)_/g, '<em>$1</em>');

        // Strikethrough: Wrap text marked with ~ in <del> tags
        text = text.replace(/~(.*?)~/g, '<del>$1</del>');

        // Monospace: Wrap text marked with ``` in <code> tags
        text = text.replace(/```(.*?)```/gs, '<code>$1</code>');

        // Single backtick: Replace with <span> tags
        text = text.replace(/`(.*?)`/g, '<span class="badge badge-light">$1</span>');

        // Convert URLs to clickable links, YouTube
        text = text.replace(/(https?:\/\/[^\s]+)/g, function (match) {
            var url = match;
            // YouTube URL
            var youtubeMatch = url.match(/(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/watch\?v=|youtu\.be\/)([\w\-]+)/);
            if (youtubeMatch) {
                return '<iframe width="100%" height="300" src="https://www.youtube.com/embed/' + youtubeMatch[1] + '" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></br></br>' + '<a href="' + url + '" target="_blank">' + url + '</a>';
            } else {
                return '<a href="' + url + '" target="_blank">' + url + '</a>';
            }
        });

        // Convert email addresses to mailto links
        text = text.replace(/([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6})/g, '<a href="mailto:$1">$1</a>');

        return text;
    }

};

$('.modal').on('shown.bs.modal', function (shownEvent) {
    var $targetModal = $(shownEvent.target);
    if ($targetModal.data('init-uploader') && $targetModal.find('.lw-file-uploader').length) {
        window.initUploader();
    }
    var $targetForm = $targetModal;
    if ($targetForm.length) {
        lwPluginsInit();
    }
});

// Reset forms in the modal after close
$('.modal').on('hidden.bs.modal', function (hiddenEvent) {
    // Get the modal
    var $targetForm = $(hiddenEvent.target).find('form');
    if ($targetForm.length) {
        $targetForm[0].reset();
        var validator = $targetForm.validate();
        validator.resetForm();
        $targetForm.find('div.lw-validation-error').remove();
        $targetForm.find('.lw-validation-error').removeClass('.lw-validation-error');
        if ($targetForm.data('on-close-update-models')) {
            __DataRequest.updateModels(($targetForm.data('on-close-update-models')))
        }
        // reset Selectize
        $targetForm.find('[data-lw-plugin="lwSelectize"]').each(function (index, selectizeFieldElement) {
            if (selectizeFieldElement.selectize) {
                selectizeFieldElement.selectize.destroy();
                $(selectizeFieldElement).val('').change();
            }
        });
    }
});

//Outer-home
window.addEventListener('DOMContentLoaded', event => {

    // Activate Bootstrap scrollspy on the main nav element
    const mainNav = document.body.querySelector('#mainNav');
    if (mainNav) {
        new bootstrap.ScrollSpy(document.body, {
            target: '#mainNav',
            offset: 74,
        });
    };

    // Collapse responsive navbar when toggler is visible
    const navbarToggler = document.body.querySelector('.navbar-toggler');
    const responsiveNavItems = [].slice.call(
        document.querySelectorAll('#navbarResponsive .nav-link')
    );
    responsiveNavItems.map(function (responsiveNavItem) {
        responsiveNavItem.addEventListener('click', () => {
            if (window.getComputedStyle(navbarToggler).display !== 'none') {
                navbarToggler.click();
            }
        });
    });
    // scroll to bottom on chatbox message submit
    $(document).on('onChatBoxMessageSubmit', function (event, dataResponse) {
        window.lwScrollTo('#lwEndOfChats', true);
    });

});