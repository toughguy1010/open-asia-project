/*jslint browser: true*/ /*global jQuery*/ /*jslint long:true */ /*jslint this:true */ /*global window*/ /*global google*/ /*global InfoBox*/

(function ($) {

    "use strict";

    var bookyourtravel_maps;

    $(document).ready(function () {
        $(document).on("map_tab_click", function (ignore) {
            bookyourtravel_maps.init();
        });
        bookyourtravel_maps.init();
    });

    bookyourtravel_maps = {

        init: function () {
            if (google !== undefined && document.getElementById("map_canvas") !== null && window.entityLatitude && window.entityLatitude !== undefined && window.entityLongitude && window.entityLongitude !== undefined && window.entityLatitude.length > 0 && window.entityLongitude.length > 0) {
                var latLong = new google.maps.LatLng(window.entityLatitude, window.entityLongitude);
                var myMapOptions = {
                    zoom: 17,
                    center: latLong,
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                };
                var theMap = new google.maps.Map(document.getElementById("map_canvas"), myMapOptions);
                google.maps.event.trigger(theMap, "resize");

                var marker = new google.maps.Marker({
                    map: theMap,
                    draggable: true,
                    position: new google.maps.LatLng(window.entityLatitude, window.entityLongitude),
                    visible: true
                });
                var boxText = document.createElement("div");
                boxText.innerHTML = window.entityInfoboxText;
                var myOptions = {
                    content: boxText,
                    disableAutoPan: false,
                    maxWidth: 0,
                    pixelOffset: new google.maps.Size(-163, -32),
                    zIndex: null,
                    closeBoxURL: "",
                    infoBoxClearance: new google.maps.Size(1, 1),
                    isHidden: false,
                    pane: "floatPane",
                    enableEventPropagation: false
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