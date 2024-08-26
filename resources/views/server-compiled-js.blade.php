(function ($) {
    'use strict';
    if ($.validator) {
        $.validator.messages = $.extend({}, $.validator.messages, {
            required: '{{ __tr("This field is required.") }}',
            remote: '{{ __tr("Please fix this field.") }}',
            email: '{{ __tr("Please enter a valid email address.") }}',
            url: '{{ __tr("Please enter a valid URL.") }}',
            date: '{{ __tr("Please enter a valid date.") }}',
            dateISO: '{{ __tr("Please enter a valid date (ISO).") }}',
            number: '{{ __tr("Please enter a valid number.") }}',
            digits: '{{ __tr("Please enter only digits.") }}',
            equalTo: '{{ __tr("Please enter the same value again.") }}',
            maxlength: $.validator.format('{{ __tr("Please enter no more than {0} characters.") }}'),
            minlength: $.validator.format('{{ __tr("Please enter at least {0} characters.") }}'),
            rangelength: $.validator.format('{{ __tr("Please enter a value between {0} and {1} characters long.") }}'),
            range: $.validator.format('{{ __tr("Please enter a value between {0} and {1}.") }}'),
            max: $.validator.format('{{ __tr("Please enter a value less than or equal to {0}.") }}'),
            min: $.validator.format('{{ __tr("Please enter a value greater than or equal to {0}.") }}'),
            step: $.validator.format('{{ __tr("Please enter a multiple of {0}.") }}')
        });
    }
    if ($.fn.dataTable) {
        $.fn.dataTable.defaults = $.extend({}, $.fn.dataTable.defaults, {
            "language": {
                /* url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/{{ app()->getLocale() }}.json', */
                "decimal": "",
                "emptyTable": '{{ __tr("No data available in table") }}',
                "info": '{{ __tr("Showing _START_ to _END_ of _TOTAL_ entries") }}',
                "infoEmpty": "{{ __tr('Showing 0 to 0 of 0 entries') }}",
                "infoFiltered": "{{ __tr('(filtered from _MAX_ total entries)') }}",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": "{{ __tr('Show _MENU_ entries') }}",
                "loadingRecords": "{{ __tr('Loading...') }}",
                "processing": '{{ __tr("Processing...") }}',
                "search": "{{ __tr('Search:') }}",
                "zeroRecords": "{{ __tr('No matching records found') }}",
                "paginate": {
                    "first": "{{ __tr('First') }}",
                    "last": "{{ __tr('Last') }}",
                    "next": "{{ __tr('Next') }}",
                    "previous": "{{ __tr('Previous') }}"
                },
                "aria": {
                    "sortAscending": "{{ __tr(': activate to sort column ascending') }}",
                    "sortDescending": "{{ __tr(': activate to sort column descending') }}"
                }
            }
            /* language: {
                url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/mr.json'
            } */
        });
    }

    if($('input.lw-date-range-picker').length) {
        $('input.lw-date-range-picker').each(function(index, element) {
            $(element).daterangepicker({
                "startDate": $(element).data('startDate'),
                "endDate": $(element).data('endDate'),
                locale : {
                    "format": "YYYY-MM-DD",
                    "separator": " - ",
                    "applyLabel": "{{ __tr('Apply') }}",
                    "cancelLabel": "{{ __tr('Cancel') }}",
                    "fromLabel": "{{ __tr('From') }}",
                    "toLabel": "{{ __tr('To') }}",
                    "customRangeLabel": "{{ __tr('Custom') }}",
                    "weekLabel": "{{ 'W' }}",
                    "daysOfWeek": [
                        "{{ __tr('Su') }}",
                        "{{ __tr('Mo') }}",
                        "{{ __tr('Tu') }}",
                        "{{ __tr('We') }}",
                        "{{ __tr('Th') }}",
                        "{{ __tr('Fr') }}",
                        "{{ __tr('Sa') }}"
                    ],
                    "monthNames": [
                        "{{ __tr('January') }}",
                        "{{ __tr('February') }}",
                        "{{ __tr('March') }}",
                        "{{ __tr('April') }}",
                        "{{ __tr('May') }}",
                        "{{ __tr('June') }}",
                        "{{ __tr('July') }}",
                        "{{ __tr('August') }}",
                        "{{ __tr('September') }}",
                        "{{ __tr('October') }}",
                        "{{ __tr('November') }}",
                        "{{ __tr('December') }}"
                    ],
                    // "firstDay": 1
                },
                ranges: {
                    '{{ __tr("Today") }}': [moment(),moment()],
                    '{{ __tr("Yesterday") }}': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    '{{ __tr("Last 7 Days") }}': [moment().subtract(6, 'days'), moment()],
                    '{{ __tr("Last 30 Days") }}': [moment().subtract(29, 'days'), moment()],
                    '{{ __tr("This Month") }}': [moment().startOf('month'), moment().endOf('month')],
                    '{{ __tr("Last Month") }}': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                },
                opens: 'right',
            }, function(start, end, label) {
               start.format('YYYY-MM-DD') + ',' + end.format('YYYY-MM-DD');
            });
        });
    }
})(jQuery);