/*jslint browser: true*/ /*jslint long: true*/ /*global jQuery*/ /*jslint this:true */ /*global window*/ /*global document*/ /*global tb_remove*/ /*global BYTAjax*/ /*global console*/

(function ($) {

    "use strict";

    var bookYourTravelAdminTours;

    $(document).ready(function () {
        bookYourTravelAdminTours.init();
    });

    bookYourTravelAdminTours = {
        init: function () {

            $("#tours_filter").on("change", function (ignore) {
                var id = $(this).val();
                document.location = "edit.php?post_type=tour&page=theme_tour_schedule_admin.php&tour_id=" + id;
            });

            if ($.fn.datepicker) {
                if ($("#datepicker_tour_date") !== undefined && $("#datepicker_tour_date") !== null) {
                    $("#datepicker_tour_date").datepicker({
                        dateFormat: BYTAdmin.datepickerDateFormat,
                        altFormat: BYTAdmin.datepickerAltFormat,
                        altField: "#tour_date",
                        minDate: 0
                    });
                    if (BYTAdminTours.tourDateValue !== undefined && BYTAdminTours.tourDateValue !== null && BYTAdminTours.tourDateValue.length > 0) {
                        $("#datepicker_tour_date").datepicker("setDate", BYTAdminTours.tourDateValue);
                    }
                }

                if ($("#datepicker_start_date") !== undefined) {
                    $("#datepicker_start_date").datepicker({
                        dateFormat: BYTAdmin.datepickerDateFormat,
                        altFormat: BYTAdmin.datepickerAltFormat,
                        altField: "#start_date",
                        minDate: 0,
                        onClose: function (selectedDate) {
                            var d = $.datepicker.parseDate(BYTAdmin.datepickerDateFormat, selectedDate);
                            if (d && d !== undefined) {
                                d = new Date(d.getFullYear(), d.getMonth(), d.getDate() + 1);
                                $("#datepicker_end_date").datepicker("option", "minDate", d);
                            }
                        }
                    });
                    if (BYTAdminTours.tourStartDateValue !== undefined && BYTAdminTours.tourStartDateValue !== null && BYTAdminTours.tourStartDateValue.length > 0) {
                        $("#datepicker_start_date").datepicker("setDate", BYTAdminTours.tourStartDateValue);
                    }
                }

                if ($("#datepicker_end_date") !== undefined) {
                    $("#datepicker_end_date").datepicker({
                        dateFormat: BYTAdmin.datepickerDateFormat,
                        altFormat: BYTAdmin.datepickerAltFormat,
                        altField: "#end_date",
                        minDate: 0,
                        onClose: function (selectedDate) {
                            var d = $.datepicker.parseDate(BYTAdmin.datepickerDateFormat, selectedDate);
                            if (d && d !== undefined) {
                                d = new Date(d.getFullYear(), d.getMonth(), d.getDate() - 1);
                                $("#datepicker_start_date").datepicker("option", "maxDate", d);
                            }
                        }
                    });
                    if (BYTAdminTours.tourEndDateValue !== undefined && BYTAdminTours.tourEndDateValue !== null && BYTAdminTours.tourEndDateValue.length > 0) {
                        $("#datepicker_end_date").datepicker("setDate", BYTAdminTours.tourEndDateValue);
                    }
                }

                if (BYTAdminTours.tourId > 0) {
                    bookYourTravelAdminTours.loadTourValues();
                }

                if ($(".tours_select") !== undefined && $(".tours_select").length > 0) {
                    $(".tours_select").on("change", function () {
                        BYTAdminTours.tourId = $(this).val();
                        bookYourTravelAdminTours.loadTourValues();
                    });
                }
            }
        },
        loadTourValues: function () {
            $(".step_1").hide();
            $(".step_1_error").hide();

            if (BYTAdminTours.tourId > 0) {
                $(".tr-tour .loading").show();

                var dataObj = {
                    "action": "tour_get_fields_ajax_request",
                    "tourId": BYTAdminTours.tourId,
                    "nonce": $("#_wpnonce").val()
                };

                $.ajax({
                    url: BYTAdmin.ajaxurl,
                    data: dataObj,
                    success: function (json) {
                        // This outputs the result of the ajax request
                        if (json !== "") {

                            var fields = JSON.parse(json);

                            BYTAdminTours.tourIsPricePerGroup = fields.is_price_per_group === 1;
                            BYTAdminTours.tourTypeIsRepeated = fields.type_is_repeated > 0;

                            $(".tr-tour .loading").hide();

                            $(".step_1").show();

                            if (BYTAdminTours.tourIsPricePerGroup) {
                                $(".per_person").hide();
                                $(".per_group").show();
                                $("#price_child").val(0);
                            } else {
                                $(".per_person").show();
                                $(".per_group").hide();
                            }

                            if (BYTAdminTours.tourTypeIsRepeated > 0) {
                                $(".is_repeated").show();
                                $(".is_not_repeated").hide();
                            } else {
                                $(".is_repeated").hide();
                                $(".is_not_repeated").show();
                            }
                        }
                    }
                });
            }
        }
    };

}(jQuery));
