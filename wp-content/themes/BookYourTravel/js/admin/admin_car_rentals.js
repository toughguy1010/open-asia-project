/*jslint browser: true*/ /*jslint long: true*/ /*global jQuery*/ /*jslint this:true */ /*global window*/ /*global document*/ /*global tb_remove*/ /*global BYTAjax*/ /*global console*/

(function ($) {

    "use strict";

    var bookYourTravelAdminCarRentals;

    $(document).ready(function () {
        bookYourTravelAdminCarRentals.init();
    });

    bookYourTravelAdminCarRentals = {
        init: function () {
            $("#car_rentals_filter").on("change", function (ignore) {
                var crId = $(this).val();
                document.location = "edit.php?post_type=car_rental&page=theme_car_rental_availability_admin.php&car_rental_id=" + crId;
            });

            if (BYTAdminCarRentals.carRentalId > 0) {
                bookYourTravelAdminCarRentals.loadCarRentalValues();
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
                        },
                        onSelect: function (dateText, ignore) {
                            $("#datepicker_to").datepicker("setDate", null);
                            $("#date_to").val(null);
                        }
                    });

                    if (BYTAdminCarRentals.carRentalDateFromValue !== undefined && BYTAdminCarRentals.carRentalDateFromValue !== null && BYTAdminCarRentals.carRentalDateFromValue.length > 0) {
                        $("#datepicker_from").datepicker("setDate", BYTAdminCarRentals.carRentalDateFromValue);
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

                    if (BYTAdminCarRentals.carRentalDateToValue !== undefined && BYTAdminCarRentals.carRentalDateToValue !== null && BYTAdminCarRentals.carRentalDateToValue.length > 0) {
                        $("#datepicker_to").datepicker("setDate", BYTAdminCarRentals.carRentalDateToValue);
                    }
                }
            }

            if ($(".car_rentals_select") !== undefined && $(".car_rentals_select").length > 0) {
                $(".car_rentals_select").on("change", function () {
                    BYTAdminCarRentals.carRentalId = $(this).val();
                    bookYourTravelAdminCarRentals.loadCarRentalValues();
                });
            }
        },
        loadCarRentalValues: function (callDelegate) {

            $(".tr-car-rental .loading").show();

            if (BYTAdminCarRentals.carRentalId > 0) {

                var dataObj = {
                    "action": "car_rental_get_fields_ajax_request",
                    "carRentalId": BYTAdminCarRentals.carRentalId,
                    "nonce": $("#_wpnonce").val()
                };

                $.ajax({
                    url: BYTAdmin.ajaxurl,
                    data: dataObj,
                    success: function (json) {
                        // This outputs the result of the ajax request
                        if (json !== "") {
                            $(".step_1").show();
                            $(".tr-car-rental .loading").hide();

                            var fields = JSON.parse(json);

                            BYTAdminCarRentals.carRentalLocations = fields.locations;

                            var locationPickupOptions = "";
                            $.each(BYTAdminCarRentals.carRentalLocations, function (index) {
                                var locationId = parseInt(BYTAdminCarRentals.carRentalLocations[index].id);
                                locationPickupOptions += "<option value='" + BYTAdminCarRentals.carRentalLocations[index].id + "' " + (
                                    locationId === parseInt(BYTAdminCarRentals.carRentalPickUpLocationId)
                                    ? "selected"
                                    : ""
                                ) + ">" + BYTAdminCarRentals.carRentalLocations[index].name + "</option>";
                            });                            

                            $("select#car_rental_pick_up_id").find('option').not(':first').remove();
                            $("select#car_rental_pick_up_id").append(locationPickupOptions);

                            var locationDropOffOptions = "";
                            $.each(BYTAdminCarRentals.carRentalLocations, function (index) {
                                var locationId = parseInt(BYTAdminCarRentals.carRentalLocations[index].id);
                                locationDropOffOptions += "<option value='" + BYTAdminCarRentals.carRentalLocations[index].id + "' " + (
                                    locationId === parseInt(BYTAdminCarRentals.carRentalDropOffLocationId)
                                    ? "selected"
                                    : ""
                                ) + ">" + BYTAdminCarRentals.carRentalLocations[index].name + "</option>";
                            });                            

                            $("select#car_rental_drop_off_id").find('option').not(':first').remove();
                            $("select#car_rental_drop_off_id").append(locationDropOffOptions);

                            if (callDelegate && callDelegate !== undefined) {
                                callDelegate();
                            }
                        }
                    },
                    error: function (errorThrown) {
                        console.log(errorThrown);
                    }
                });
            }
        }
    };

}(jQuery));