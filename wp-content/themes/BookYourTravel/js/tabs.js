/*jslint browser: true*/ /*jslint long:true */ /*global jQuery*/ /*jslint this:true */ /*global window*/

(function ($) {

    "use strict";

    window.bookyourtravel_tabs = null;

    $(document).ready(function () {
        window.bookyourtravel_tabs.init();
    });

    window.bookyourtravel_tabs = {
        init: function () {
            window.bookyourtravel_tabs.bindTabs();
        },
        bindTabs: function () {
            if ($(".tab-content").length > 0 && $(".inner-nav").length > 0) {
                $(".tab-content").hide();
                $(".tab-content.initial").show();
                var activeIndex = $(".inner-nav li.active").index();
                var currentMenuItemIndex = $(".inner-nav li.current-menu-item").index();
                if (activeIndex === -1) {
                    $(".inner-nav li:first").addClass("active");
                }
                if (currentMenuItemIndex > -1) {
                    $(".inner-nav li").removeClass("active");
                }
                $(".custom-inner-nav-link").on("click", function (e) {
                    $(".inner-nav li").removeClass("active");
                    var currentTab = $(this).attr("href");
                    var hashbang = currentTab.replace("#", "");
                    var newAnchor = $(".inner-nav li a[href='#" + hashbang + "']");
                    $(newAnchor).parent().addClass("active");
                    $(".tab-content").hide();
                    $(currentTab).show();
                    if (currentTab === "#availability") {					
                        $("html, body").animate({
                            scrollTop: $("#booking-form-calendar").offset().top - 100
                        }, 500);
                    }
					e.preventDefault();
				});
                $(".inner-nav a").on("click", function (e) {
                    $(".inner-nav li").removeClass("active");
                    var currentTab = $(this).attr("href");
                    var hashbang = currentTab.replace("#", "");
                    var newAnchor = $(".inner-nav li a[href='#" + hashbang + "']");
                    $(newAnchor).parent().addClass("active");
                    $(".tab-content").hide();
                    $(currentTab).show();
                    if (currentTab === "#location" || currentTab === "#map") {
                        $(document).trigger("map_tab_click");
                        var $mapIframe = $(".gmap iframe");
                        if ($mapIframe !== undefined && $mapIframe.length > 0) {
                            $mapIframe.attr("src", $mapIframe.attr("src"));
                        }
                    }
                    e.preventDefault();
                });
                if (window.location.hash.length > 0) {
                    var hashbang = window.location.hash.replace("#", "");
                    if (hashbang.length > 0) {
                        var anchor = $(".inner-nav li a[href='#" + hashbang + "']");
                        if (anchor.length > 0) {
                            if (anchor.parent().length > 0) {
                                $(".inner-nav li").removeClass("active");
                                anchor.parent().addClass("active");
                                $(".tab-content").hide();
                                $(".tab-content#" + hashbang).show();
                            }
                        }
                    }
                }
            }
        }
    };

}(jQuery));