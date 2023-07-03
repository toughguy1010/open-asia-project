/*jslint browser: true*/ /*jslint long:true */
/*global jQuery*/ /*jslint this:true */ /*global window*/

(function ($) {

    "use strict";

    window.bookyourtravel_gallery = null;

    $(document).ready(function () {
        window.bookyourtravel_gallery.init();
    });

    window.bookyourtravel_gallery = {
        init: function () {
            window.bookyourtravel_gallery.bindGallery();
        },
        bindGallery: function () {
            if ($(".post-gallery.cS-hidden").length > 0) {
                $(".post-gallery.cS-hidden").lightSlider({
                    item: 1,
                    rtl: Boolean(window.enableRtl),
                    slideMargin: 0,
                    auto: true,
                    loop: true,
                    speed: 900,
                    pause: window.pauseBetweenSlides,
                    keyPress: true,
                    gallery: true,
                    thumbItem: 8,
                    galleryMargin: 3,
                    onSliderLoad: function (ignore) {
                        $(".post-gallery").removeClass("cS-hidden");
                    }
                });
            }
        }
    };

}(jQuery));