(function (window, undefined) {
    'use strict';
    if ($.fn.dataTable) {
        $.extend($.fn.dataTable.defaults, {
            serverSide: true,
            iCookieDuration: 60,
            paging: true,
            processing: true,
            responsive: true,
            destroy: true,
            retrieve: true,
            lengthChange: true,
            language: {
                emptyTable: "There are no records to display."
            },
            searching: false,
            ajax: {
                // any additional data to send
                data: function (additionalData) {
                    additionalData.page =
                        additionalData.start / additionalData.length + 1;
                }
            }
        });
    }

    /**
     * Initialize DataTable
     *
     * @param tableID {string} - table id
     * @param dtOptions {object} - datatable options
     * @param dsOptions {object} - datastore options
     *
     * @return datatable instance
     *-------------------------------------------------------- */

    window.dataTable = function (tableID, dtOptions, dsOptions, callbackFunction) {
        if (callbackFunction) {
            dtOptions.callbackFunction = callbackFunction;
        }

        return $(tableID).DataTable(dtConfig(dtOptions, dsOptions));
    }

    /**
     * DataTable Custom Configuration generation based on provided data
     *
     * @param object {options} - Object
     *                         url          (required)
     *                         scope        (required)
     *                         columnsData  (required)
     *                         dtOptions    (optional)
     *
     * @return array
     *-------------------------------------------------------- */

    function dtConfig(options, dsOptions) {
        var dataStoreInstance = this;

        if (!dsOptions || !_.isObject(dsOptions)) {
            dsOptions = {};
        }
        var dtOptionsCollection = {
            ajax: function (data, callback, settings) {
                // for laravel 5 paginate
                data.page = data.start / data.length + 1;

                var drawID = data.draw ? data.draw : false,
                    optionsSendToFetch = {
                        params: data,
                        fresh: dsOptions.fresh ? dsOptions.fresh : false
                    };

                if (_.has(dsOptions, "persist")) {
                    optionsSendToFetch["persist"] = dsOptions.persist;
                }

                var urlID = options.url,
                    requestURL = "";
                if (optionsSendToFetch.params) {
                    requestURL = urlID + "?" + $.param(optionsSendToFetch.params);

                    if (_.has(optionsSendToFetch.params, "draw")) {
                        delete optionsSendToFetch.params["draw"];
                    }

                    urlID = urlID + "?" + $.param(optionsSendToFetch.params);
                }
                // Send Ajax request for get datatable data
                __DataRequest.get(requestURL, {}, function (response) {
                    response.draw = drawID;

                    // callback for datatable after data fetched
                    if (
                        options.callbackFunction &&
                        _.isFunction(options.callbackFunction)
                    ) {
                        options.callbackFunction.call(this, response);
                    }

                    callback(response);
                }, {
                    thisScope: options.scope
                });
            },
            drawCallback: function (settings) {
                var api = this.api(),
                    $thisDataTable = api.table();
                if (
                    _.has($thisDataTable.columns, "adjust") &&
                    _.has($thisDataTable.responsive, "recalc")
                ) {
                    // responsive fix for datatable for cached datastore item
                    _.delay(function () {
                        $thisDataTable.columns.adjust().responsive.recalc();
                    }, 180);
                }
                // added on 27 APR 2022 and updated on 06 MAY 2022
                $('.paginate_button').not('.previous, .next').find('.page-link').each(function (i, element) {
                    var paginateNumberValue = $(element).text();
                    $(element).text(__Utils.formatAsLocaleNumber(paginateNumberValue));
                });
            },
            columns: [],
            /* "createdRow": function (nRow, data, dataIndex, cells) {
                $('td', nRow).eq(5).append('highlight');
            }, */
            // added on 27 APR 2022
            formatNumber: function (numberValue) {
                return __Utils.formatAsLocaleNumber(numberValue);
            },
            // added on 19 MAY 2016 - 0.4.0
            responsive: {
                details: {
                    renderer: function (api, rowIdx, columns) {
                        var data = _.map(columns, function (col, i) {
                            return col.hidden ?
                                '<li data-dtr-index="' +
                                col.columnIndex +
                                '" data-dt-row="' +
                                col.rowIndex +
                                '" data-dt-column="' +
                                col.columnIndex +
                                '">' +
                                '<span class="dtr-title">' +
                                col.title +
                                "</span> " +
                                '<span class="dtr-data">' +
                                col.data +
                                "</span>" +
                                "</li>" :
                                "";
                        }).join("");
                        _.defer(function () {
                            // this method also called on drawCallback
                            if (options['elementData'] && options['elementData']['callback']) {
                                window[options.elementData.callback](api, rowIdx, columns);
                            }
                        });
                        return data;
                    }
                }
            }
        };

        if (options.dtOptions) {
            _.assign(dtOptionsCollection, options.dtOptions);
        }

        dtOptionsCollection.columns = _.map(options.columnsData, function (
            dtColumnData
        ) {
            return {
                defaultContent: "",
                data: dtColumnData.name ? dtColumnData.name : null,
                orderable: dtColumnData.orderable ? true : false,
                className: dtColumnData.className ? dtColumnData.className : null,
                render: function (subject, data, obj, settings) {
                    if (!dtColumnData.name) {
                        obj = subject;
                    } else {
                        obj.dtSubject = dtColumnData.name;
                    }
                    // if template given
                    if (dtColumnData.template) {
                        // compile data using underscore template
                        return __Utils.template(dtColumnData.template, obj);
                    } else {
                        return obj[dtColumnData.name];
                    }
                }
            };
        });

        return dtOptionsCollection;
    }

    /**
     * Ajax Reload DataTable
     *
     * @param dataTableInstance {object} - datatable instance
     *
     * @return datatable instance
     *-------------------------------------------------------- */

    window.reloadDT = function (dataTableInstance, callback) {
        if (!callback) {
            callback = null;
        }
        if (_.isArray(dataTableInstance)) {
            _.each(dataTableInstance, function (index) {
                $(index).dataTable().api().ajax.reload(callback, false);
            });
        } else if (_.isString(dataTableInstance)) {
            $(dataTableInstance).dataTable().api().ajax.reload(callback, false);
        } else {
            dataTableInstance.ajax.reload(callback, false);
        }
        __Utils.log("__DataStore:: DataTable reloaded");
    }

    /**
     * identify dataTables and initialize
     *
     * @param dataTableInstance {object} - datatable instance
     * added to function so can be called from template replace - 12 OCT 2023
     *
     * @return void
     *-------------------------------------------------------- */
    window.initializeDatatable = function () {
        // check if
        var $lwDataTable = $('[lwDataTable]');
        if ($lwDataTable.length) {
            (function initDatatable() {
                $lwDataTable.each(function (index) {
                    var $element = $(this),
                        columnsData = [],
                        elementData = ($element.data()),
                        $tableHeaders = $element.find('thead th'),
                        dtOptions = {
                            "searching": true,
                            'order': []
                        };
                    // set the item on datatable element
                    if (!_.isUndefined(elementData['order'])) {
                        dtOptions['order'] = [
                            elementData['order'], (!_.isUndefined(elementData['orderType']) ? elementData['orderType'] : 'asc')
                        ];
                    }

                    if (!_.isUndefined(elementData['orderBy'])) {
                        dtOptions['order'] = [
                            elementData['orderBy'],
                            elementData['orderType']
                        ];
                    }
                    $tableHeaders.each(function (index) {
                        var $thElement = $(this),
                            thData = $thElement.data();
                        // Order by column
                        if (!_.isUndefined(thData['orderBy'])) {
                            // Order type
                            if (thData['orderType']) {
                                dtOptions['order'] = [
                                    index, thData['orderType']
                                ];
                            } else {
                                dtOptions['order'] = [
                                    index, 'asc'
                                ];
                            }
                        }

                        // insert data
                        columnsData.push(_.assign({
                            name: _.snakeCase($thElement.text())
                        }, $thElement.data()));

                    });
                    // to avoid following error
                    // Uncaught TypeError: Cannot use 'in' operator to search for 'length' in g
                    $element.removeData();
                    if (elementData['pageLength'] && _.isInteger(elementData['pageLength'])) {
                        dtOptions['pageLength'] = elementData['pageLength'];
                        if (!_.includes($.fn.dataTable.defaults.aLengthMenu, dtOptions['pageLength'])) {
                            $.extend($.fn.dataTable.defaults, {
                                lengthMenu: _.assign($.fn.dataTable.defaults.aLengthMenu, [dtOptions['pageLength']])
                            });
                        }
                    }

                    if (elementData['rowcallback']) {
                        /* instead using eval we have used this
                        ref: https://datatables.net/reference/option/rowCallback
                        */
                        dtOptions['rowCallback'] = window[elementData['rowcallback']];
                    }

                    if (elementData['callback']) {
                        /* instead using eval we have used this
                        ref: https://datatables.net/reference/option/
                        This method also called on responsive > details > renderer above in the code
                        */
                        dtOptions['drawCallback'] = window[elementData['callback']];
                    }

                    if (_.isEmpty(columnsData)) {
                        throw Error('Columns not defined');
                    }

                    if (!elementData['url']) {
                        throw Error('DataTable url is missing, it should be on table as data-url');
                    }
                    // function present in this same file as window.dataTable
                    dataTable($element, {
                        url: elementData['url'],
                        dtOptions: dtOptions,
                        columnsData: columnsData,
                        scope: $element,
                        elementData: elementData
                    });
                });
            })();
        }
    };
    // initialize datatables
    document.addEventListener('DOMContentLoaded', window.initializeDatatable);
})(window);