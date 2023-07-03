/*jslint browser: true*/ /*jslint long: true*/ /*global jQuery*/ /*jslint this:true */ /*global window*/ /*global document*/ /*global tb_remove*/ /*global BYTAjax*/ /*global console*/

(function ($) {

    "use strict";

    var bookYourTravelAdminCruises;

    $(document).ready(function () {
        bookYourTravelAdminCruises.init();
    });

    bookYourTravelAdminCruises = {
        init: function () {

            $("#cruises_filter").on("change", function (ignore) {
                var id = $(this).val();
                document.location = "edit.php?post_type=cruise&page=theme_cruise_schedule_admin.php&cruise_id=" + id;
            });

            if ($.fn.datepicker) {
                if ($("#datepicker_cruise_date") !== undefined) {
                    $("#datepicker_cruise_date").datepicker({
                        dateFormat: BYTAdmin.datepickerDateFormat,
                        altFormat: BYTAdmin.datepickerAltFormat,
                        altField: "#cruise_date",
                        minDate: 0
                    });
                    if (BYTAdminCruises.cruiseDateValue !== undefined && BYTAdminCruises.cruiseDateValue !== null && BYTAdminCruises.cruiseDateValue.length > 0) {
                        $("#datepicker_cruise_date").datepicker("setDate", BYTAdminCruises.cruiseDateValue);
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
                    if (BYTAdminCruises.cruiseStartDateValue !== undefined && BYTAdminCruises.cruiseStartDateValue !== null && BYTAdminCruises.cruiseStartDateValue.length > 0) {
                        $("#datepicker_start_date").datepicker("setDate", BYTAdminCruises.cruiseStartDateValue);
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
                    if (BYTAdminCruises.cruiseEndDateValue !== undefined && BYTAdminCruises.cruiseEndDateValue !== null && BYTAdminCruises.cruiseEndDateValue.length > 0) {
                        $("#datepicker_end_date").datepicker("setDate", BYTAdminCruises.cruiseEndDateValue);
                    }
                }
            }

            if (BYTAdminCruises.cruiseId > 0) {
                bookYourTravelAdminCruises.loadCruiseCabins();
            }

            if ($(".cruises_select") !== undefined && $(".cruises_select").length > 0) {
                $(".cruises_select").on("change", function () {
                    BYTAdminCruises.cruiseId = $(this).val();
                    bookYourTravelAdminCruises.loadCruiseCabins();
                });
            }
        },
        loadCruiseCabins: function () {
            $(".step_1").hide();
            $(".step_1_error").hide();
            $(".step_2").hide();
            $(".step_2_error").hide();
            $("#cabin_types_select").val("");

            if (BYTAdminCruises.cruiseId > 0) {
                $(".tr-cruise .loading").show();

                var dataObj = {
                    "action": "cruise_get_fields_ajax_request",
                    "cruiseId": BYTAdminCruises.cruiseId,
                    "nonce": $("#_wpnonce").val()
                };

                $.ajax({
                    url: BYTAdmin.ajaxurl,
                    data: dataObj,
                    success: function (json) {
                        // This outputs the result of the ajax request
                        if (json !== "") {

                            var fields = JSON.parse(json);
                            var cabinTypeOptions = "";

                            BYTAdminCruises.cruiseTypeIsRepeated = parseInt(fields.type_is_repeated) > 0;
                            BYTAdminCruises.cruiseCabinTypes = fields.cruise_cabin_types;

                            $(".tr-cruise .loading").hide();

                            if (!BYTAdminCruises.cruiseTypeIsRepeated) {
                                $("#datepicker_end_date").datepicker("setDate", null);
                            }

                            $("select#cabin_types_select").find("option:gt(0)").remove();

                            $.each(BYTAdminCruises.cruiseCabinTypes, function (index) {
                                var cabinTypeId = parseInt(BYTAdminCruises.cruiseCabinTypes[index].id);
                                var cabinTypeName = BYTAdminCruises.cruiseCabinTypes[index].name;
                                cabinTypeOptions += "<option value='" + cabinTypeId + "' " + (
                                    cabinTypeId === parseInt(BYTAdminCruises.cabinTypeId)
                                    ? "selected"
                                    : ""
                                ) + ">" + cabinTypeName + "</option>";
                            });

                            $("select#cabin_types_select").append(cabinTypeOptions);

                            $("#cabin_types_select").on("change", function () {
                                BYTAdminCruises.cabinTypeId = $(this).val();
                                bookYourTravelAdminCruises.loadCruiseValues();
                            });

                            $(".step_1").show();

                            bookYourTravelAdminCruises.loadCruiseValues();

                            if (BYTAdminCruises.cruiseTypeIsRepeated) {
                                $(".step_1 .is_repeated").show();
                                $(".is_not_repeated").hide();
                            } else {
                                $(".step_1 .is_repeated").hide();
                                $(".is_not_repeated").show();
                            }
                        }
                    }
                });
            }
        },
        loadCruiseValues: function () {
            $(".step_2").hide();
            $(".step_2_error").hide();

            if (BYTAdminCruises.cabinTypeId > 0 && BYTAdminCruises.cruiseId > 0) {
                $(".tr-cabin .loading").show();

                var dataObj = {
                    "action": "cruise_get_fields_ajax_request",
                    "cruiseId": BYTAdminCruises.cruiseId,
                    "nonce": $("#_wpnonce").val()
                };

                $.ajax({
                    url: BYTAdmin.ajaxurl,
                    data: dataObj,
                    success: function (json) {
                        // This outputs the result of the ajax request
                        if (json !== "") {

                            var fields = JSON.parse(json);

                            BYTAdminCruises.cruiseIsPricePerPerson = parseInt(fields.is_price_per_person) === 1;
                            BYTAdminCruises.cruiseTypeIsRepeated = parseInt(fields.type_is_repeated) > 0;

                            $(".tr-cabin .loading").hide();

                            $(".step_2").show();

                            if (BYTAdminCruises.cruiseIsPricePerPerson) {
                                $(".per_person").show();
                            } else {
                                $(".per_person").hide();
                                $("#price_child").val(0);
                            }

                            if (BYTAdminCruises.cruiseTypeIsRepeated > 0) {
                                $(".step_2.is_repeated").show();
                                $(".is_not_repeated").hide();
                            } else {
                                $(".step_2.is_repeated").hide();
                                $(".is_not_repeated").show();
                            }
                        }
                    }
                });
            }
        }
    };

}(jQuery));
