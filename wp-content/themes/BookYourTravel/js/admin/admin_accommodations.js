/*jslint browser: true*/ /*jslint long:true*/ /*jslint for: true*/ /*global jQuery*/ /*jslint this:true */ /*global window*/ /*global document*/ /*global tb_remove*/ /*global BYTAjax*/ /*global console*/

(function ($) {

    "use strict";

    var bookYourTravelAdminAccommodations;

    $(document).ready(function () {
        bookYourTravelAdminAccommodations.init();
    });

    bookYourTravelAdminAccommodations = {
        init: function () {
            $("#accommodations_filter").on("change", function (ignore) {
                var aId = $(this).val();
                document.location = "edit.php?post_type=accommodation&page=theme_accommodation_vacancy_admin.php&accommodation_id=" + aId;
            });

            if (BYTAdminAccommodations.accommodationId > 0) {
                bookYourTravelAdminAccommodations.hideControls();
                bookYourTravelAdminAccommodations.loadAccommodationValues(null);
            }

            if ($(".accommodations_select") !== undefined && $(".accommodations_select").length > 0) {
                $(".accommodations_select").on("change", function () {
                    bookYourTravelAdminAccommodations.hideControls();
                    BYTAdminAccommodations.accommodationId = (
                        ($(this).val() !== null && $(this).val() !== "")
                        ? parseInt($(this).val())
                        : 0
                    );
                    bookYourTravelAdminAccommodations.loadAccommodationValues(null);
                });
            }

            if ($.fn.datepicker) {

                if ($("#datepicker_from") !== undefined) {

                    $("#datepicker_from").datepicker({
                        dateFormat: BYTAdmin.datepickerDateFormat,
                        altFormat: BYTAdmin.datepickerAltFormat,
                        altField: "#date_from",
                        hourMin: 6,
                        hourMax: 18,
                        minDate: 0,
                        onClose: function (selectedDate) {
                            var d = $.datepicker.parseDate(BYTAdmin.datepickerDateFormat, selectedDate);
                            if (d && d !== undefined) {
                                d = new Date(d.getFullYear(), d.getMonth(), d.getDate() + 1);
                                $("#datepicker_to").datepicker("option", "minDate", d);
                            }
                        }
                    });

                    if (BYTAdminAccommodations.accommodationDateFromValue !== undefined && BYTAdminAccommodations.accommodationDateFromValue !== null && BYTAdminAccommodations.accommodationDateFromValue.length > 0) {
                        $("#datepicker_from").datepicker("setDate", BYTAdminAccommodations.accommodationDateFromValue);
                    }
                }

                if ($("#datepicker_to") !== undefined) {

                    $("#datepicker_to").datepicker({
                        dateFormat: BYTAdmin.datepickerDateFormat,
                        altFormat: BYTAdmin.datepickerAltFormat,
                        altField: "#date_to",
                        hourMin: 6,
                        hourMax: 18,
                        minDate: 0,
                        onClose: function (selectedDate) {
                            var d = $.datepicker.parseDate(BYTAdmin.datepickerDateFormat, selectedDate);
                            if (d && d !== undefined) {
                                d = new Date(d.getFullYear(), d.getMonth(), d.getDate() - 1);
                                $("#datepicker_from").datepicker("option", "maxDate", d);
                            }
                        }
                    });

                    if (BYTAdminAccommodations.accommodationDateToValue !== undefined && BYTAdminAccommodations.accommodationDateToValue !== null && BYTAdminAccommodations.accommodationDateToValue.length > 0) {
                        $("#datepicker_to").datepicker("setDate", BYTAdminAccommodations.accommodationDateToValue);
                    }
                }

            }
        },
        loadAccommodationValues: function (callDelegate = null) {

            $(".tr-accommodation .loading").show();

            if (BYTAdminAccommodations.accommodationId > 0) {

                var dataObj = {
                    "action": "accommodation_get_fields_ajax_request",
                    "accommodationId": BYTAdminAccommodations.accommodationId,
                    "nonce": $("#_wpnonce").val()
                };

                $.ajax({
                    url: BYTAdmin.ajaxurl,
                    data: dataObj,
                    success: function (json) {
                        // This outputs the result of the ajax request
                        if (json !== "") {

                            var fields = JSON.parse(json);

                            BYTAdminAccommodations.accommodationRentType = fields.rent_type > 0 ? parseInt(fields.rent_type) : 0;
                            BYTAdminAccommodations.accommodationDisabledRoomTypes = fields.disabled_room_types === "1" || fields.disabled_room_types === 1;
                            BYTAdminAccommodations.accommodationIsPricePerPerson = fields.is_price_per_person === "1" || fields.is_price_per_person === 1;
                            BYTAdminAccommodations.roomTypes = fields.room_types;

                            var roomTypeOptions = "";

                            $("select#room_type_id").find("option:gt(0)").remove();

                            if (!BYTAdminAccommodations.accommodationDisabledRoomTypes && BYTAdminAccommodations.roomTypes !== undefined && BYTAdminAccommodations.roomTypes !== null && BYTAdminAccommodations.roomTypes.length > 0) {

                                $.each(BYTAdminAccommodations.roomTypes, function (index) {
                                    var roomTypeId = parseInt(BYTAdminAccommodations.roomTypes[index].id);
                                    roomTypeOptions += "<option value='" + BYTAdminAccommodations.roomTypes[index].id + "' " + (
                                        roomTypeId === parseInt(BYTAdminAccommodations.roomTypeId)
                                        ? "selected"
                                        : ""
                                    ) + ">" + BYTAdminAccommodations.roomTypes[index].name + "</option>";
                                });

                                $("select#room_type_id").append(roomTypeOptions);

                                $("select#room_type_id").unbind("change");
                                $("select#room_type_id").on("change", function () {
                                    BYTAdminAccommodations.roomTypeId = parseInt($(this).val());
                                    bookYourTravelAdminAccommodations.displayControls();
                                });

                                $("#room_types_row").show();
                                $("#room_count_row").show();

                            } else {
                                BYTAdminAccommodations.roomTypeId = 0;
                                $("#room_types_row").hide();
                                $("#room_count_row").hide();
                            }

                            bookYourTravelAdminAccommodations.displayControls();
                            $(".tr-accommodation .loading").hide();

                            if (callDelegate && callDelegate !== undefined) {
                                callDelegate();
                            }
                        }
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        console.log(xhr);
                        console.log(ajaxOptions);
                        console.log(thrownError);
                    }
                });
            }
        },
        hideControls: function () {
            $(".accommodation_selected.step_0").hide();
            $(".accommodation_selected.step_1").hide();
        },
        displayControls: function () {

            if (BYTAdminAccommodations.accommodationId > 0) {

                if (BYTAdminAccommodations.accommodationDisabledRoomTypes) {
                    $(".accommodation_selected.step_1").show();
                } else {
                    $(".accommodation_selected.step_0").show();
                }

                if (BYTAdminAccommodations.roomTypeId > 0) {
                    $(".accommodation_selected.step_1").show();
                }

                if (BYTAdminAccommodations.accommodationDisabledRoomTypes || BYTAdminAccommodations.roomTypeId > 0) {

                    if (BYTAdminAccommodations.accommodationRentType === 0) {
                        $(".daily_rent").show();
                        $(".th_price .first").html(BYTAdminAccommodations.accommodationPricePerDayLabel);

                        if (BYTAdminAccommodations.accommodationIsPricePerPerson) {
                            $(".per_person").show();
                        } else {
                            $(".per_person").hide();
                        }
                    } else {
                        $(".daily_rent").hide();
                        if (BYTAdminAccommodations.accommodationRentType === 1) {
                            $(".th_price .first").html(BYTAdminAccommodations.accommodationPricePerWeekLabel);
                            $(".th_price_per_child .first").html(BYTAdminAccommodations.accommodationPricePerWeekLabel);
                        } else {
                            $(".th_price .first").html(BYTAdminAccommodations.accommodationPricePerMonthLabel);
                            $(".th_price_per_child .first").html(BYTAdminAccommodations.accommodationPricePerMonthLabel);
                        }

                        if (BYTAdminAccommodations.accommodationIsPricePerPerson) {
                            $(".per_person:not(.daily_rent)").show();
                        } else {
                            $(".per_person").hide();
                        }
                    }
                }

            } else {
                $(".accommodation_selected").hide();
            }
        }
    };

}(jQuery));
