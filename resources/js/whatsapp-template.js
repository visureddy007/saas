(function($) {
    "use strict";
    var fields = [];
    var patternToSearchInputField = /\{\{(\d+)\}\}/g;
    $("#lwTemplateBody").on("input", function() {
        // Get the input value
        var inputValue = $(this).val();
        updatePlaceholders(inputValue);
    });

    $('#lwAddPlaceHolder').click(function() {
        addNewPlaceholder();
    });
    $("#lwHeaderTextBody").on("input", function() {
        // Get the input value
        var inputValue = $(this).val();
        var matches = inputValue.match(patternToSearchInputField);
        if(matches) {
            __DataRequest.updateModels({enableHeaderVariableExample : true});
        } else {
            __DataRequest.updateModels({enableHeaderVariableExample : false});
        }
    });
    $('#lwAddSinglePlaceHolder').click(function() {
        let headerTextBody= $('#lwHeaderTextBody');
        let currentText = headerTextBody.val();
        let cursorPos = headerTextBody.prop('selectionStart');
        let beforeText = currentText.substring(0, cursorPos);
        let afterText = currentText.substring(cursorPos, currentText.length);
        let newText = beforeText + ` {{1}}` + afterText;
        headerTextBody.val(newText);
        $('#lwHeaderTextBody').trigger('input');
        __DataRequest.updateModels({header_text_body:newText});
    });

    $('#lwBoldBtn').click(function() {
        wrapWithItem('*');
    });
    $('#lwItalicBtn').click(function() {
        wrapWithItem('_');
    });
    $('#lwStrikeThroughBtn').click(function() {
        wrapWithItem('~');
    });
    $('#lwCodeBtn').click(function() {
        wrapWithItem('```');
    });

    function updatePlaceholders(text) {
        const placeholderRegex = /\{\{\d+\}\}/g;
        let newText = updateSequence(text, placeholderRegex);
        $('#lwTemplateBody').val(newText);
        var res = {};
        var matches = newText.match(patternToSearchInputField);
        if (matches) {
            for (let i = 0; i < matches.length; i++) {
                var newArr = {
                    'text_variable': matches[i],
                    'text_variable_value': matches[i],
                };
                res[matches[i].replace(/\{\{(\d+)\}\}/g, '$1')] = newArr;
                // Your code to handle each matched pattern goes here
                __DataRequest.updateModels({newBodyTextInputFields : res});
            }
        } else{
            __DataRequest.updateModels({newBodyTextInputFields : res});
        }
    }

function addNewPlaceholder() {
    let textarea = $('#lwTemplateBody');
    let currentText = textarea.val();
    let cursorPos = textarea.prop('selectionStart');
    const placeholderRegex = /\{\{\d+\}\}/g;
    let matches = currentText.match(placeholderRegex) || [];
    let maxNumber = 0;
    matches.forEach(function(item) {
        const currentNumber = parseInt(item.match(/\d+/)[0], 10);
        if (currentNumber > maxNumber) {
            maxNumber = currentNumber;
        }
    });
    // Insert the new placeholder at the current cursor position
    let beforeText = currentText.substring(0, cursorPos);
    let afterText = currentText.substring(cursorPos, currentText.length);
    let newText = beforeText + ` {{${maxNumber + 1}}} ` + afterText;
    textarea.val(newText);
    // Place cursor right after the newly added placeholder
    let newPos = cursorPos + ` {{${maxNumber + 1}}} `.length;
    textarea[0].selectionStart = textarea[0].selectionEnd = newPos;
    textarea.focus(); // refocus the textarea after manipulation
    $('#lwTemplateBody').trigger('input');
    __DataRequest.updateModels({text_body:newText});
}

    function wrapWithItem(wrapWith) {
        let $textarea = $('#lwTemplateBody');
        let start = $textarea[0].selectionStart;
        let end = $textarea[0].selectionEnd;
        let selectedText = $textarea.val().substring(start, end);
        let beforeText = $textarea.val().substring(0, start);
        let afterText = $textarea.val().substring(end);
        let newText = beforeText + wrapWith + selectedText + wrapWith + afterText;
        $textarea.val(newText);
        // Update the cursor to be at the end of the newly wrapped text
        $textarea[0].selectionStart = $textarea[0].selectionEnd = start + selectedText.length + 2;
        $textarea.focus(); // Refocus the textarea after manipulation
        $('#lwTemplateBody').trigger('input');
        __DataRequest.updateModels({text_body:newText});
    }

    function updateSequence(text, regex) {
        let matches = text.match(regex);
        let unique = [];
        if (matches) {
            $.each(matches, function(i, el) {
                if ($.inArray(el, unique) === -1) unique.push(el);
            });

            unique.sort((a, b) => Number(a.match(/\d+/)[0]) - Number(b.match(/\d+/)[0]));
            const newNumbers = unique.reduce((acc, cur, index) => {
                const num = cur.match(/\d+/)[0];
                acc[num] = index + 1;
                return acc;
            }, {});
            return text.replace(regex, function(match) {
                const num = match.match(/\d+/)[0];
                return `{{${newNumbers[num]}}}`;
            });
        }
        return text;
    };
})(jQuery);