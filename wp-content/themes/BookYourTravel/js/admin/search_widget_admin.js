/*jslint browser: true*/ /*jslint for:true */ /*global jQuery*/ /*jslint this:true */ /*global window*/

(function ($) {

    "use strict";

    var bytSearchWidgets;

    $(document).ready(function () {
        $(document).on('widget-updated widget-added', function (e, widget) {
            bytSearchWidgets.init();
        })
        bytSearchWidgets.init();
    });

    bytSearchWidgets = {
        init: function () {
            bytSearchWidgets.$searchWidgets = $('div[id*="_bookyourtravel_search_widget-"]');
            bytSearchWidgets.initWidgets();
        },
        initWidgets: function () {
            if (bytSearchWidgets.$searchWidgets.length > 0) {
                bytSearchWidgets.$searchWidgets.each(function (ignore, widget) {

                    var widgetId = $(widget).find('input.widget-id').val();

                    bytSearchWidgets.bindClearButtonCheckbox(widget);
                    bytSearchWidgets.bindTabs(widget);
                    bytSearchWidgets.loadFirstTab(widget);
                    bytSearchWidgets.bindBlocksCount(widget);
                    bytSearchWidgets.bindFiltersRemoveButton(widget);

                    var genericFilterControlTypes = {
                        'calendar-date-from': window.genericFilterCalendarDateFrom,
                        'calendar-date-to': window.genericFilterCalendarDateTo,
                        'star-rating-slider': window.genericFilterStarRatingSlider,
                        'user-rating-slider': window.genericFilterUserRatingSlider,
                        'price-range-checkboxes': window.genericFilterPriceRangeCheckboxes,
                        'facility-checkboxes': window.genericFilterFacilityCheckboxes,
                    }

                    bytSearchWidgets.bindFiltersCount(widget, 'generic', genericFilterControlTypes, true);

                    if (window.enableAccommodations) {
                        var accommodationFilterControlTypes = {
                            'accommodation-tag-radios': window.accommodationFilterTagRadios,
                            'accommodation-tag-checkboxes': window.accommodationFilterTagCheckboxes,
                            'accommodation-tag-select': window.accommodationFilterTagSelect,
                            'accommodation-type-radios': window.accommodationFilterTypeRadios,
                            'accommodation-type-checkboxes': window.accommodationFilterTypeCheckboxes,
                            'accommodation-type-select': window.accommodationFilterTypeSelect,                            
                            'accommodation-name': window.accommodationFilterName,
                            'accommodation-name-select': window.accommodationFilterNameSelect,
                            'accommodation-room-count': window.accommodationRoomCount
                        };
                        bytSearchWidgets.bindFiltersCount(widget, 'accommodation', accommodationFilterControlTypes, false);
                    }

                    if (window.enableCarRentals) {
                        var carRentalFilterControlTypes = {
                            'carrental-tag-radios': window.carRentalFilterTagRadios,
                            'carrental-tag-checkboxes': window.carRentalFilterTagCheckboxes,
                            'carrental-tag-select': window.carRentalFilterTagSelect,
                            'carrental-type-radios': window.carRentalFilterTypeRadios,
                            'carrental-type-checkboxes': window.carRentalFilterTypeCheckboxes,
                            'carrental-type-select': window.carRentalFilterTypeSelect,
                            'carrental-name': window.carRentalFilterName,
                            'carrental-name-select': window.carRentalFilterNameSelect
                        };
                        bytSearchWidgets.bindFiltersCount(widget, 'carrental', carRentalFilterControlTypes, false);
                    }

                    if (window.enableCruises) {
                        var cruiseFilterControlTypes = {
                            'cruise-tag-radios': window.cruiseFilterTagRadios,
                            'cruise-tag-checkboxes': window.cruiseFilterTagCheckboxes,
                            'cruise-tag-select': window.cruiseFilterTagSelect,
                            'cruise-type-radios': window.cruiseFilterTypeRadios,
                            'cruise-type-checkboxes': window.cruiseFilterTypeCheckboxes,
                            'cruise-type-select': window.cruiseFilterTypeSelect,
                            'cruise-duration-radios': window.cruiseFilterDurationRadios,
                            'cruise-duration-checkboxes': window.cruiseFilterDurationCheckboxes,
                            'cruise-duration-select': window.cruiseFilterDurationSelect,                            
                            'cruise-name': window.cruiseFilterName,
                            'cruise-name-select': window.cruiseFilterNameSelect,
                            'cruise-cabin-count': window.cruiseCabinCount
                        };
                        bytSearchWidgets.bindFiltersCount(widget, 'cruise', cruiseFilterControlTypes, false);
                    }

                    var locationFilterControlTypes = {
                        'location-by-type': window.locationFilterByTypeSelect,
                        'location-select': window.locationFilterSelect
                    };
                    bytSearchWidgets.bindFiltersCount(widget, 'location', locationFilterControlTypes, true);

                    if (window.enableTours) {
                        var tourFilterControlTypes = {
                            'tour-tag-radios': window.tourFilterTagRadios,
                            'tour-tag-checkboxes': window.tourFilterTagCheckboxes,
                            'tour-tag-select': window.tourFilterTagSelect,
                            'tour-type-radios': window.tourFilterTypeRadios,
                            'tour-type-checkboxes': window.tourFilterTypeCheckboxes,
                            'tour-type-select': window.tourFilterTypeSelect,
                            'tour-duration-radios': window.tourFilterDurationRadios,
                            'tour-duration-checkboxes': window.tourFilterDurationCheckboxes,
                            'tour-duration-select': window.tourFilterDurationSelect,                            
                            'tour-name': window.tourFilterName,
                            'tour-name-select': window.tourFilterNameSelect
                        };
                        bytSearchWidgets.bindFiltersCount(widget, 'tour', tourFilterControlTypes, false);
                    }

                    $(widget).find('.widget-background-color-field').wpColorPicker({
                        change: function(e, ui) {
                            $( e.target ).val( ui.color.toString() );
                            $( e.target ).trigger('change'); // enable widget "Save" button
                        }
                    });

                    $(widget).find('.widget-text-color-field').wpColorPicker({
                        change: function(e, ui) {
                            $( e.target ).val( ui.color.toString() );
                            $( e.target ).trigger('change'); // enable widget "Save" button
                        }
                    });

                    if (widgetId.indexOf('__i__') === -1) {
                        var dataObj = {
                            "action": "admin_get_sidebar_id_ajax_request",
                            "widget_id": widgetId,
                            "nonce": $("#_wpnonce").val()
                        };

                        $.ajax({
                            url: ajaxurl,
                            data: dataObj,
                            success: function (s) {
                                var sidebar = JSON.parse(s);
                                if (sidebar === '') {
                                    if ($(widget).closest('#hero')) {
                                        $(widget).find('.sidebar-hero').show();
                                    }
                                } else if (sidebar === 'hero') {
                                    $(widget).find('.sidebar-hero').show();
                                }
                            },
                            error: function (errorThrown) {
                                console.log(errorThrown);
                            }
                        });
                    }
                });
            }
        },
        addBlock: function (widget, index) {
            var widgetId = $(widget).find('input[name=widget-id]').val();
            var widgetName = bytSearchWidgets.getWidgetNameAttribute(widget);

            var $dtBlock = $("<dt>")
                .attr("data-block", index)
                .addClass("block-name")
                .appendTo($(widget).find("dl.blocks"));

            $("<span />")
                .text("Block " + index)
                .appendTo($dtBlock);

            $("<input />")
                .addClass("block-index")
                .attr("id", "widget-" + widgetId + "-widget-block-index_" + index)
                .attr("name", "widget-" + widgetName + "[widget_block_index_" + index + "]")
                .attr("value", index)
                .attr("type", "hidden")
                .appendTo($dtBlock);

            var $ddBlock1 = $("<dd>")
                .attr("data-block", index)
                .appendTo($(widget).find("dl.blocks"));

            $("<label />")
                .attr("for", "widget-" + widgetId + "-widget-block-width_" + index)
                .text("Width")
                .appendTo($ddBlock1);

            var $select = $("<select />");

            $select.attr("id", "widget-" + widgetId + "-widget-block-width_" + index)
                .attr("name", "widget-" + widgetName + "[widget_block_width_" + index + "]");

            $("<option value='one-sixth'>1/6</option>").appendTo($select);
            $("<option value='one-fifth'>1/5</option>").appendTo($select);
            $("<option value='one-fourth'>1/4</option>").appendTo($select);
            $("<option value='one-third'>1/3</option>").appendTo($select);
            $("<option value='two-fifth'>2/5</option>").appendTo($select);
            $("<option value='one-half'>1/2</option>").appendTo($select);
            $("<option value='three-fifth'>3/5</option>").appendTo($select);
            $("<option value='two-third'>2/3</option>").appendTo($select);
            $("<option value='three-fourth'>3/4</option>").appendTo($select);
            $("<option value='four-fifth'>4/5</option>").appendTo($select);
            $("<option value='full-width' selected>1/1</option>").appendTo($select);

            $select.appendTo($ddBlock1);

            var $ddBlock2 = $("<dd>")
                .attr("data-block", index)
                .appendTo($(widget).find("dl.blocks"));

            $("<label />")
                .attr("for", "widget-" + widgetId + "-widget-block-order_" + index)
                .text("Order")
                .appendTo($ddBlock2);

            $("<input />")
                .attr("id", "widget-" + widgetId + "-widget-block-order_" + index)
                .attr("name", "widget-" + widgetName + "[widget_block_order_" + index + "]")
                .attr("type", "number")
                .attr("value", "1")
                .attr("min", "1")
                .attr("max", "20")
                .appendTo($ddBlock2);
        },
        addFilter(widget, index, filterEntityType, filterControlTypes, filterIncludeShowFor) {
            var widgetId = $(widget).find('input[name=widget-id]').val();
            var widgetName = bytSearchWidgets.getWidgetNameAttribute(widget);

            var $dtFilterName = $("<dt>")
                .attr("data-" + filterEntityType + "-filter", index)
                .addClass("filter-name")
                .appendTo($(widget).find("dl.filters." + filterEntityType + "-filters"));

            $("<span />")
                .text("Filter " + index)
                .appendTo($dtFilterName);

            $("<input />")
                .addClass(filterEntityType + "-filter-index")
                .attr("id", "widget-" + widgetId + "-widget-" + filterEntityType + "-filter-index_" + index)
                .attr("name", "widget-" + widgetName + "[widget_" + filterEntityType + "_filter_index_" + index + "]")
                .attr("value", index)
                .attr("type", "hidden")
                .appendTo($dtFilterName);

            $("<a />")
                .addClass("remove-filter")
                .attr("data-filter-type", filterEntityType)
                .attr("data-filter-index", index)
                .attr("href", "#")
                .text(window.removeFilterText)
                .appendTo($dtFilterName);

            if (filterIncludeShowFor && (window.enableAccommodations || window.enableCarRentals || window.enableCruises || window.enableTours)) {
                var $ddShowFor = $("<dd class='show-for'>")
                .attr("data-" + filterEntityType + "-filter", index)
                .appendTo($(widget).find("dl.filters." + filterEntityType + "-filters"));

                $("<span />")
                    .text(window.showForLabel)
                    .appendTo($ddShowFor);

                var $ulShowFor = $("<ul>").appendTo($ddShowFor);
                var $liAccommodationsShowFor = $("<li>").appendTo($ulShowFor);
                var $liCarRentalsShowFor = $("<li>").appendTo($ulShowFor);
                var $liCruisesShowFor = $("<li>").appendTo($ulShowFor);
                var $liToursShowFor = $("<li>").appendTo($ulShowFor);

                $("<input />")
                    .attr("id", "widget-" + widgetId + "-widget-" + filterEntityType + "-filter-show-for-accommodations_" + index)
                    .attr("name", "widget-" + widgetName + "[widget_" + filterEntityType + "_filter_show_for_accommodations_" + index + "]")
                    .attr("type", "checkbox")
                    .attr("value", "1")
                    .appendTo($liAccommodationsShowFor);

                $("<label />")
                    .attr("for", "widget-" + widgetId + "-widget-" + filterEntityType + "-filter-show-for-accommodations_" + index)
                    .text(window.showForAccommodationsLabel)
                    .appendTo($liAccommodationsShowFor);

                $("<input />")
                    .attr("id", "widget-" + widgetId + "-widget-" + filterEntityType + "-filter-show-for-carrentals_" + index)
                    .attr("name", "widget-" + widgetName + "[widget_" + filterEntityType + "_filter_show_for_carrentals_" + index + "]")
                    .attr("type", "checkbox")
                    .attr("value", "1")
                    .appendTo($liCarRentalsShowFor);

                $("<label />")
                    .attr("for", "widget-" + widgetId + "-widget-" + filterEntityType + "-filter-show-for-carrentals_" + index)
                    .text(window.showForCarRentalsLabel)
                    .appendTo($liCarRentalsShowFor);

                $("<input />")
                    .attr("id", "widget-" + widgetId + "-widget-" + filterEntityType + "-filter-show-for-cruises_" + index)
                    .attr("name", "widget-" + widgetName + "[widget_" + filterEntityType + "_filter_show_for_cruises_" + index + "]")
                    .attr("type", "checkbox")
                    .attr("value", "1")
                    .appendTo($liCruisesShowFor);

                $("<label />")
                    .attr("for", "widget-" + widgetId + "-widget-" + filterEntityType + "-filter-show-for-cruises_" + index)
                    .text(window.showForCruisesLabel)
                    .appendTo($liCruisesShowFor);

                $("<input />")
                    .attr("id", "widget-" + widgetId + "-widget-" + filterEntityType + "-filter-show-for-tours_" + index)
                    .attr("name", "widget-" + widgetName + "[widget_" + filterEntityType + "_filter_show_for_tours_" + index + "]")
                    .attr("type", "checkbox")
                    .attr("value", "1")
                    .appendTo($liToursShowFor);

                $("<label />")
                    .attr("for", "widget-" + widgetId + "-widget-" + filterEntityType + "-filter-show-for-tours_" + index)
                    .text(window.showForToursLabel)
                    .appendTo($liToursShowFor);
            }

            var $ddFilterBlock = $("<dd class='filter-show-in-block'>")
                .attr("data-" + filterEntityType + "-filter", index)
                .appendTo($(widget).find("dl.filters." + filterEntityType + "-filters"));

            $("<label />")
                .attr("for", "widget-" + widgetId + "-widget-" + filterEntityType + "-filter-block_" + index)
                .text(window.showInBlockLabel)
                .appendTo($ddFilterBlock);

            var $filterBlockSelect = $("<select />");

            $filterBlockSelect.attr("id", "widget-" + widgetId + "-widget-" + filterEntityType + "-filter-block_" + index)
                .attr("name", "widget-" + widgetName + "[widget_" + filterEntityType + "_filter_block_" + index + "]");

            var currentBlockCount = $(widget).find("dl.blocks dt").length;
            for (var i = 1; i <= currentBlockCount; i++) {
                $("<option value='" + i + "'>" + i + "</option>").appendTo($filterBlockSelect);
            }

            $filterBlockSelect.appendTo($ddFilterBlock);

            var $ddFilterWidth = $("<dd class='filter-width'>")
                .attr("data-" + filterEntityType + "-filter", index)
                .appendTo($(widget).find("dl.filters." + filterEntityType + "-filters"));

            $("<label />")
                .attr("for", "widget-" + widgetId + "-widget-" + filterEntityType + "-filter-width_" + index)
                .text("Width")
                .appendTo($ddFilterWidth);

            var $filterWidthSelect = $("<select />");

            $filterWidthSelect.attr("id", "widget-" + widgetId + "-widget-" + filterEntityType + "-filter-width_" + index)
                .attr("name", "widget-" + widgetName + "[widget_" + filterEntityType + "_filter_width_" + index + "]");

            $("<option value='full-width' selected>1/1</option>").appendTo($filterWidthSelect);
            $("<option value='four-fifth'>4/5</option>").appendTo($filterWidthSelect);
            $("<option value='three-fourth'>3/4</option>").appendTo($filterWidthSelect);
            $("<option value='two-third'>2/3</option>").appendTo($filterWidthSelect);
            $("<option value='three-fifth'>3/5</option>").appendTo($filterWidthSelect);
            $("<option value='one-half'>1/2</option>").appendTo($filterWidthSelect);
            $("<option value='two-fifth'>2/5</option>").appendTo($filterWidthSelect);
            $("<option value='one-third'>1/3</option>").appendTo($filterWidthSelect);
            $("<option value='one-fourth'>1/4</option>").appendTo($filterWidthSelect);
            $("<option value='one-fifth'>1/5</option>").appendTo($filterWidthSelect);
            $("<option value='one-sixth'>1/6</option>").appendTo($filterWidthSelect);

            $filterWidthSelect.appendTo($ddFilterWidth);

            var $ddFilterOrder = $("<dd class='filter-order'>")
                .attr("data-" + filterEntityType + "-filter", index)
                .appendTo($(widget).find("dl.filters." + filterEntityType + "-filters"));

            $("<label />")
                .attr("for", "widget-" + widgetId + "-widget-" + filterEntityType + "-filter-order_" + index)
                .text("Filter order")
                .appendTo($ddFilterOrder);

            $("<input />")
                .attr("id", "widget-" + widgetId + "-widget-" + filterEntityType + "-filter-order_" + index)
                .attr("name", "widget-" + widgetName + "[widget_" + filterEntityType + "_filter_order_" + index + "]")
                .attr("type", "number")
                .attr("value", "1")
                .attr("min", "1")
                .attr("max", "20")
                .appendTo($ddFilterOrder);

            var $ddFilterLabel = $("<dd class='filter-label'>")
                .attr("data-" + filterEntityType + "-filter", index)
                .appendTo($(widget).find("dl.filters." + filterEntityType + "-filters"));

            $("<label />")
                .attr("for", "widget-" + widgetId + "-widget-" + filterEntityType + "-filter-label_" + index)
                .text("Filter label")
                .appendTo($ddFilterLabel);

            $("<input />")
                .attr("id", "widget-" + widgetId + "-widget-" + filterEntityType + "-filter-label_" + index)
                .attr("name", "widget-" + widgetName + "[widget_" + filterEntityType + "_filter_label_" + index + "]")
                .attr("type", "text")
                .attr("value", "")
                .appendTo($ddFilterLabel);

            var $ddFilterType = $("<dd class='filter-type'>")
                .attr("data-" + filterEntityType + "-filter", index)
                .appendTo($(widget).find("dl.filters." + filterEntityType + "-filters"));

            $("<label />")
                .attr("for", "widget-" + widgetId + "-widget-" + filterEntityType + "-filter-type_" + index)
                .text("Filter type")
                .appendTo($ddFilterType);

            var $filterTypeSelect = $("<select />");

            $filterTypeSelect.attr("id", "widget-" + widgetId + "-widget-" + filterEntityType + "-filter-type_" + index)
                .attr("name", "widget-" + widgetName + "[widget_" + filterEntityType + "_filter_type_" + index + "]");

            for (var typeKey in filterControlTypes) {
                var typeLabel = filterControlTypes[typeKey];
                $("<option value='" + typeKey + "'>" + typeLabel + "</option>").appendTo($filterTypeSelect);
            }

            $filterTypeSelect.appendTo($ddFilterType);

            bytSearchWidgets.bindFiltersRemoveButton(widget);
        },
        bindFiltersRemoveButton: function(widget) {
            if ($(widget).find('.remove-filter').length > 0) {
                $(widget).find('.remove-filter').off('click');
                $(widget).find('.remove-filter').on('click', function(e) {
                    var filterType = $(this).data('filter-type');
                    var filterIndex = $(this).data('filter-index');

                    $(widget).find("[data-" + filterType + "-filter='" + filterIndex + "']").remove();

                    if ($(widget).find(".widget_" + filterType + "_filter_count").length > 0) {
                        var currentSelectedIndex = $(widget).find(".widget_" + filterType + "_filter_count option:selected").index();
                        if (currentSelectedIndex > 0) {
                            $(widget).find(".widget_" + filterType + "_filter_count").prop('selectedIndex', currentSelectedIndex - 1);
                            $(widget).find(".widget_" + filterType + "_filter_count").trigger("change");
                        }
                    }

                    e.preventDefault();
                });
            }
        },
        bindFiltersCount: function(widget, filterEntityType, filterControlTypes, filterIncludeShowFor) {
            if ($(widget).find('.widget_' + filterEntityType + '_filter_count').length > 0) {
                $(widget).find('.widget_' + filterEntityType + '_filter_count').off();
                $(widget).find('.widget_' + filterEntityType + '_filter_count').on('change', function (e) {
                    let filterCount = $(this).val();
                    if (filterCount < 0) {
                        $(this).val(0);
                        filterCount = 0;
                    }
                    bytSearchWidgets.reloadFilters(widget, filterCount, filterEntityType, filterControlTypes, filterIncludeShowFor);
                });
            }
        },
        reloadFilters: function (widget, filterCount, filterEntityType, filterControlTypes, filterIncludeShowFor) {
            let filterClass = filterEntityType + '-filters';
            let filterDataAttribute = filterEntityType + '-filter';

            if ($(widget).find("dl.filters." + filterClass + " dd, dl.filters." + filterClass + " dt").length > 0) {
                var $obsoleteElements = $(widget).find("dl.filters." + filterClass + " dd:not(.placeholder), dl.filters." + filterClass + " dt:not(.placeholder)").filter(function () {
                    return parseInt($(this).data(filterDataAttribute)) > filterCount;
                });

                if ($obsoleteElements.length > 0) {
                    $obsoleteElements.remove();
                } else {
                    var currentFilterCount = $(widget).find("dl.filters." + filterClass + " dt:not(.placeholder)").length;
                    if (currentFilterCount < filterCount) {
                        var newFilterIndex = 1;
                        var $lastDt = $(widget).find("dl.filters." + filterClass + " dt:not(.placeholder):last-of-type");
                        if ($lastDt.length > 0) {
                            if ($lastDt.length > 0 && $lastDt.data(filterDataAttribute)) {
                                newFilterIndex = parseInt($lastDt.data(filterDataAttribute)) + 1;
                            }
                        }
                        for (var i = newFilterIndex; i <= filterCount; i++) {
                            bytSearchWidgets.addFilter(widget, newFilterIndex, filterEntityType, filterControlTypes, filterIncludeShowFor);
                            newFilterIndex++;
                        }
                    }
                }
            }
        },
        bindBlocksCount: function (widget) {
            if ($(widget).find('.widget_block_count').length > 0) {
                $(widget).find('.widget_block_count').off();
                $(widget).find('.widget_block_count').on('change', function (e) {
                    let blockCount = $(this).val();
                    if (blockCount <= 0) {
                        $(this).val(1);
                        blockCount = 1;
                    }
                    bytSearchWidgets.reloadBlocks(widget, blockCount);
                });
            }
        },
        reloadBlocks: function (widget, blockCount) {
            if ($(widget).find("dl.blocks dd, dl.blocks dt").length > 0) {
                var $obsoleteElements = $(widget).find("dl.blocks dd:not(.placeholder), dl.blocks dt:not(.placeholder)").filter(function () {
                    return parseInt($(this).data("block")) > blockCount;
                });

                if ($obsoleteElements.length > 0) {
                    $obsoleteElements.remove();
                } else {
                    var currentBlockCount = $(widget).find("dl.blocks dt:not(.placeholder)").length;
                    if (currentBlockCount < blockCount) {
                        var newBlockIndex = 1;
                        var $lastDt = $(widget).find("dl.blocks dt:not(.placeholder):last-of-type");
                        if ($lastDt.length > 0) {
                            newBlockIndex = parseInt($lastDt.data('block')) + 1;
                        }
                        for (var i = newBlockIndex; i <= blockCount; i++) {
                            bytSearchWidgets.addBlock(widget, newBlockIndex);
                            newBlockIndex++;
                        }
                    }
                }
            }
        },
        bindClearButtonCheckbox: function (widget) {
            if ($(widget).find('.show_clear_button').length > 0) {
                $(widget).find('.show_clear_button').off();
                $(widget).find('.show_clear_button').on('change', function (e) {
                    console.log('inside handler');
                    console.log($(this).is(':checked'));
                    if ($(this).is(':checked')) {
                        $(widget).find('.clear-button-controls').show();
                    } else {
                        $(widget).find('.clear-button-controls').hide();
                    }
                });
            }
        },
        bindTabs: function (widget) {
            if ($(widget).find('.byt-widget-tabs li a').length > 0) {
                $(widget).find('.byt-widget-tabs li a').off();
                $(widget).find('.byt-widget-tabs li a').on('click', function (e) {
                    e.preventDefault();
                    bytSearchWidgets.selectTab(widget, $(this).attr('href').replace('#', ''));
                });
            }
        },
        loadFirstTab: function (widget) {
            if ($(widget).find('.byt-widget-tabs-content .tab-content').length > 0) {
                var $tabContent = $(widget).find('.byt-widget-tabs-content .tab-content');
                bytSearchWidgets.selectTab(widget, $($tabContent[0]).attr("id"));
            }
        },
        selectTab: function (widget, tabId) {
            if ($(widget).find('.byt-widget-tabs-content .tab-content').length > 0) {
                $(widget).find('.byt-widget-tabs-content .tab-content').hide();
                $(widget).find('.byt-widget-tabs li').removeClass("active");
                var $activeAnchor = $(widget).find('.byt-widget-tabs li a[href="#' + tabId + '"]');
                if ($activeAnchor.length > 0) {
                    $activeAnchor.parent().addClass("active");
                    $(widget).find('.byt-widget-tabs-content .tab-content#' + tabId).show();
                }
            }
        },
        getWidgetNameAttribute: function (widget) {
            var widgetIdBase = $(widget).find('input[name=id_base]').val();
            var multiNumber = $(widget).find('input[name=multi_number]').val();
            var widgetNumber = $(widget).find('input[name=widget_number]').val();

            var widgetName = "";
            if (multiNumber && multiNumber.length > 0) {
                widgetName = widgetIdBase + "[" + multiNumber + "]";
            } else {
                widgetName = widgetIdBase + "[" + widgetNumber + "]";
            }
            return widgetName;
        },
    };

}(jQuery));
