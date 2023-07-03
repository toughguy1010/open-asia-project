/*jslint browser: true*/ /*global jQuery*/ /*jslint this:true */ /*jslint long:true */
/*global window*/ /*global google*/ /*global InfoBox*/ /*global BYTContact*/

(function ($) {

    "use strict";

    var bookyourtravel_contact;

    $(window).on('load', function () {
        bookyourtravel_contact.load();
    });

    $(document).ready(function () {
        bookyourtravel_contact.init();
    });

    bookyourtravel_contact = {
        init: function () {
            if ($("#contact-form").length > 0) {
                $("#contact-form").validate({
                    onkeyup: false,
                    ignore: [],
                    rules: {
                        contact_name: "required",
                        contact_email: "required",
                        contact_message: "required"
                    },
                    messages: {
                        contact_name: window.contactNameError,
                        contact_email: window.contactEmailError,
                        contact_message: window.contactMessageError
                    },
                    errorPlacement: function (error, element) {
                        if ($(element).attr("type") === "checkbox") {
                            error.appendTo($(element).closest("div").parent());
                            $(element).closest("div").addClass("error");
                        } else if ($(element)[0].tagName === 'SELECT') {
                            $(element).closest(".selector").addClass("error");
                            error.appendTo($(element).closest(".selector").parent());
                        } else {
                            error.insertAfter(element);
                        }
                    },
                    success: function (error, element) {
                        if ($(element).attr("type") === "checkbox") {
                            $(element).closest("div").removeClass("error");
                        } else if ($(element)[0].tagName === 'SELECT') {
                            $(element).closest(".selector").removeClass("error");
                        }
                        error.remove();
                    }
                });

                if ($("#contact-form input[name='agree_gdpr']").length > 0) {
                    $("#contact-form input[name='agree_gdpr']").rules("add", {
                        required: true,
                        messages: {
                            required: window.gdprError
                        }
                    });
                }
            }
        },
        load: function () {
            if (google !== undefined && document.getElementById("map_canvas") !== undefined) {
                var latLong = new google.maps.LatLng(BYTContact.business_address_latitude, BYTContact.business_address_longitude);
                var myMapOptions = {
                    zoom: 15,
                    center: latLong,
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                };
                var theMap = new google.maps.Map(document.getElementById("map_canvas"), myMapOptions);
                var marker = new google.maps.Marker({
                    map: theMap,
                    draggable: true,
                    position: new google.maps.LatLng(BYTContact.business_address_latitude, BYTContact.business_address_longitude),
                    visible: true,
                });
                var boxText = document.createElement("div");
                boxText.innerHTML = BYTContact.company_address;
                var myOptions = {
                    content: boxText,
                    disableAutoPan: false,
                    maxWidth: 0,
                    // pixelOffset: new google.maps.Size(-163, -32),
                    pixelOffset: new google.maps.Size(BYTContact.business_address_latitude - 213, BYTContact.business_address_longitude -17),
                    zIndex: null,
                    closeBoxURL: "",
                    infoBoxClearance: new google.maps.Size(1, 1),
                    isHidden: false,
                    pane: "floatPane",
                    enableEventPropagation: false,
                    // anchor: new google.maps.LatLng(BYTContact.business_address_latitude - 200, BYTContact.business_address_longitude - 200),
                };

                var ib = new InfoBox(myOptions);
                google.maps.event.addListener(marker, "click", function () {
                    ib.open(theMap, this);
                });
                ib.open(theMap, marker);
            }
        }
    };

}(jQuery));
