/*jslint long:true */ /*jslint browser: true*/ /*jslint for:true */ /*global bookyourtravel_scripts*/
/*global jQuery*/ /*jslint this:true */ /*global window*/ /*global BYTAjax*/ /*global console*/


var bookyourtravel_extra_items;

(function ($) {

    "use strict";

    $(document).ready(function () {
        bookyourtravel_extra_items.init();
    });

    bookyourtravel_extra_items = {
        init: function () {
            if (!window.bookingRequest || window.bookingRequest === undefined || window.bookingRequest.length === 0) {
                window.bookingRequest = {};
            }
            window.bookingRequest.extraItems = {};
        },
        bindRequiredExtraItems: function () {
            if (window.requiredExtraItems !== undefined && window.requiredExtraItems.length > 0) {
                $.each(window.requiredExtraItems, function (ignore, extraItemId) {
                    bookyourtravel_extra_items.updateExtraItemSelection(extraItemId, 1);
                    $("#extra_item_quantity_" + extraItemId).val("1");
                });
            }
        },
        recalculateExtraItemTotals: function (totalPrice) {

            if (Object.size(window.bookingRequest.extraItems) > 0) {

                if (window.bookingRequest.extraItemsTotalPrice > 0) {
                    window.bookingRequest.totalPrice = totalPrice;
                    window.bookingRequest.extraItemsTotalPrice = 0;
                }

                $.each(window.bookingRequest.extraItems, function (ignore, extraItem) {

                    var extraItemPrice = extraItem.price;

                    if (extraItem.pricePerPerson) {
                        extraItemPrice = (window.bookingRequest.people * extraItemPrice);
                    }

                    if (extraItem.pricePerDay) {
                        extraItemPrice = extraItemPrice * window.bookingRequest.totalDays;
                    }

                    extraItem.summedPrice = extraItem.quantity * extraItemPrice;

                    window.bookingRequest.totalPrice += extraItem.summedPrice;
                    window.bookingRequest.extraItemsTotalPrice += extraItem.summedPrice;
                });

                bookyourtravel_extra_items.rebuildExtraItemSummary();
            }

            $(".extra_items_total").html(bookyourtravel_scripts.formatPrice(window.bookingRequest.extraItemsTotalPrice));
            $(".total_price").html(bookyourtravel_scripts.formatPrice(window.bookingRequest.totalPrice));
        },
        buildExtraItemsTable: function () {

            $("table.extra_items_price_breakdown thead").html("");
            $("table.extra_items_price_breakdown tfoot").html("");
            $("table.extra_items_price_breakdown tbody").html("");

            var headerRow = "";
            headerRow += "<tr class='rates_head_row'>";
            headerRow += "<th>" + window.itemLabel + "</th>";
            headerRow += "<th>" + window.priceLabel + "</th>";
            headerRow += "</tr>";

            $("table.extra_items_price_breakdown thead").append(headerRow);

            var footerRow = "";
            footerRow += "<tr>";
            footerRow += "<th>" + window.priceTotalLabel + "</th>";
            footerRow += "<td class='extra_items_total'>" + bookyourtravel_scripts.formatPrice(0) + "</td>";
            footerRow += "</tr>";

            $("table.extra_items_price_breakdown tfoot").append(footerRow);
        },
        bindExtraItemsQuantitySelect: function () {

            $("select.extra_item_quantity").unbind("change");
            $("select.extra_item_quantity").on("change", function (ignore) {

                var quantity = parseInt($(this).val());
                var extraItemId = $(this).attr("id").replace("extra_item_quantity_", "");

                bookyourtravel_extra_items.updateExtraItemSelection(extraItemId, quantity);
            });
        },
        rebuildExtraItemSummary: function () {
            var extraItemRows = "";
            var pricingMethod = "";

            if (Object.size(window.bookingRequest.extraItems) > 0) {
                $.each(window.bookingRequest.extraItems, function (ignore, value) {
                    pricingMethod = "";
                    if (value.pricePerDay && value.pricePerPerson) {
                        pricingMethod = window.pricedPerDayPerPersonLabel.format(window.bookingRequest.totalDays, window.bookingRequest.people);
                    } else if (value.pricePerDay) {
                        pricingMethod = window.pricedPerDayLabel.format(window.bookingRequest.totalDays);
                    } else if (value.pricePerPerson) {
                        pricingMethod = window.pricedPerPersonLabel.format(window.bookingRequest.people);
                    }

                    if (window.bookingRequest.units > 1) {
                        pricingMethod += window.perExtraItemUnitLabel.format(window.bookingRequest.units);
                    }

                    extraItemRows += "<tr class='extra_item_row_" + value.Id + "'>";
                    extraItemRows += "<td>" + value.quantity + " x " + value.title + " (" + (pricingMethod) + ")</td>";
                    extraItemRows += "<td>" + bookyourtravel_scripts.formatPrice(value.summedPrice) + "</td>";
                    extraItemRows += "</tr>";
                });
            }

            $("table.extra_items_price_breakdown tbody").html(extraItemRows);
        },
        updateExtraItemSelection: function (extraItemId, quantity) {

            if (extraItemId > 0) {
                var extraItemPrice = parseFloat($("#extra_item_price_" + extraItemId).val());
                var extraItemTitle = $("#extra_item_title_" + extraItemId).html();
                var extraItemPricePerPerson = parseInt($("#extra_item_price_per_person_" + extraItemId).val());
                var extraItemPricePerDay = parseInt($("#extra_item_price_per_day_" + extraItemId).val());
                var oldExtraItem = null;
                var extraItem = {};

                // reduce total by old item summed price.
                if (window.bookingRequest.extraItems[extraItemId] !== undefined) {
                    oldExtraItem = window.bookingRequest.extraItems[extraItemId];
                    window.bookingRequest.totalPrice -= parseFloat(oldExtraItem.summedPrice);
                    window.bookingRequest.extraItemsTotalPrice -= parseFloat(oldExtraItem.summedPrice);
                    bookyourtravel_extra_items.adjustDepositAmounts(-parseFloat(oldExtraItem.summedPrice));
                    delete window.bookingRequest.extraItems[extraItemId];
                }

                $("table.extra_items_price_breakdown tbody").html("");

                if (quantity > 0) {
                    extraItem.quantity = quantity;
                    extraItem.id = extraItemId;
                    extraItem.price = extraItemPrice;
                    extraItem.pricePerPerson = extraItemPricePerPerson;
                    extraItem.pricePerDay = extraItemPricePerDay;

                    if (extraItem.pricePerPerson) {
                        extraItemPrice = window.bookingRequest.people * extraItemPrice;
                    }

                    extraItemPrice = window.bookingRequest.units * extraItemPrice;

                    if (extraItem.pricePerDay) {
                        extraItemPrice = extraItemPrice * window.bookingRequest.totalDays;
                    }

                    extraItem.summedPrice = extraItem.quantity * extraItemPrice;
                    extraItem.title = extraItemTitle;

                    bookyourtravel_extra_items.adjustDepositAmounts(extraItem.summedPrice);

                    window.bookingRequest.totalPrice += extraItem.summedPrice;
                    window.bookingRequest.extraItemsTotalPrice += extraItem.summedPrice;
                    window.bookingRequest.extraItems[extraItemId] = extraItem;
                }

                bookyourtravel_extra_items.adjustDepositTotals();
                bookyourtravel_extra_items.rebuildExtraItemSummary();

                $(".extra_items_total").html(bookyourtravel_scripts.formatPrice(window.bookingRequest.extraItemsTotalPrice));
                $(".total_price").html(bookyourtravel_scripts.formatPrice(window.bookingRequest.totalPrice));

                $.uniform.update(".extra_item_quantity");
            }
        }, 
        adjustDepositAmounts: function(summedPrice) {
            if (window.enableDeposits && window.depositPercentage < 100) {
                var depositAmount = summedPrice * (window.depositPercentage / 100);
                var depositDifference = summedPrice - depositAmount;

                window.bookingRequest.depositAmount += depositAmount;
                window.bookingRequest.depositDifference += depositDifference;
            }
        },
        adjustDepositTotals: function() {
            if (window.enableDeposits && window.depositPercentage < 100) {
                if ($(".deposits_row").length > 0) {
                    $(".deposits_row").show();
                    $(".deposit_amount").html(bookyourtravel_scripts.formatPrice(window.bookingRequest.depositAmount));
                    if (window.depositPercentage < 100) {
                        $(".deposit-info").html(window.depositInfo.format(window.depositPercentage, bookyourtravel_scripts.formatPrice(window.bookingRequest.depositDifference)));
                    } else {
                        $(".deposits_row").hide();
                    }
                }
            }
        },        
    };
}(jQuery));
