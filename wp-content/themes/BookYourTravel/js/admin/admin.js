/*jslint browser: true*/ /*jslint long: true*/ /*global jQuery*/ /*global wp*/ /*jslint this:true */ /*global window*/ /*global confirm*/ /*global document*/ /*global tb_remove*/ /*global BYTAjax*/ /*global console*/

var bookYourTravelAdmin;

(function ($) {

    "use strict";

    var bindPublishUpdateButton;

    $(document).ready(function () {
        bookYourTravelAdmin.init();

        $(document).on("metaTabShow", function (ignore) {
            bookYourTravelAdmin.init();
        });

        $(document).ready(function ($) {
            var publish_button_bound = false;
            var toggle_button_bound = false;
            var publish_button_check_interval = null;
            // on publish
            if (window.location.href.indexOf("post-new.php") > -1 || window.location.href.indexOf("post.php") > -1) {
                if (window.location.href.indexOf("post-new.php") > -1) {
                    var toggle_button_check_interval = setInterval(function () {
                        var $publish_toggle_button = $(".edit-post-header__settings .editor-post-publish-panel__toggle");
                        if ($publish_toggle_button.length > 0 && !toggle_button_bound) {
                            $publish_toggle_button.on("click", function () {
                                publish_button_check_interval = setInterval(function () {
                                    publish_button_bound = bindPublishUpdateButton(publish_button_bound, ".editor-post-publish-panel__header .editor-post-publish-button");
                                    if (publish_button_bound) {
                                        clearInterval(publish_button_check_interval);
                                    }
                                }, 100);
                            });
                            toggle_button_bound = true;
                        }

                        if (toggle_button_bound) {
                            clearInterval(toggle_button_check_interval);
                        }
                    }, 200);
                } else {
                    publish_button_check_interval = setInterval(function () {
                        publish_button_bound = bindPublishUpdateButton(publish_button_bound, ".edit-post-header__settings .editor-post-publish-button");
                        if (publish_button_bound) {
                            clearInterval(publish_button_check_interval);
                        }
                    }, 100);
                }
            }
        });
    });

    bindPublishUpdateButton = function (publish_button_bound, publish_button_selector) {
        var $publish_button = $(publish_button_selector);
        if ($publish_button.length > 0 && !publish_button_bound) {
            publish_button_bound = true;
            $publish_button.on("click", function () {
                var publish_button_click_interval = setInterval(function () {
                    var postsaving = wp.data.select("core/editor").isSavingPost();
                    var autosaving = wp.data.select("core/editor").isAutosavingPost();
                    var success = wp.data.select("core/editor").didPostSaveRequestSucceed();
                    if (postsaving || autosaving || !success) {
                        return;
                    }
                    clearInterval(publish_button_click_interval);

                    if ($(".post-type-page").length > 0) {
                        var redirectUrl = window.location.href.replace(/refreshed=\d+/, "");
                        window.location.href = redirectUrl + "&refreshed=1";
                    }
                }, 300);
            });
        }

        return publish_button_bound;
    };

    bookYourTravelAdmin = {
        processIconArray: function (array) {
            // set this to whatever number of items you can process at once
            var chunk = 100;
            var index = 0;
            function doChunk() {
                var cnt = chunk;
                var iconClass = "";
                var $iconSpan = null;
                var $iconAnchor = null;
                var $TB_ajaxContent = $("#TB_ajaxContent .icons");
                if ($TB_ajaxContent.length > 0) {
                    while (cnt && index < array.length) {
                        // process array[index] here
                        index += 1;
                        if (array[index] !== undefined && array[index].length > 0) {
                            iconClass = array[index].trim();
                            if (iconClass.length > 0) {
                                $iconSpan = $("<span/>");
                                $iconAnchor = $("<a/>");
                                $iconAnchor.attr("class", "widgets_select_icon");
                                $iconAnchor.attr("href", "#");
                                $iconSpan.attr("class", "icon material-icons");
                                $iconSpan.html(iconClass);
                                $iconAnchor.append($iconSpan);
                                $TB_ajaxContent.append($iconAnchor);
                            }
                        }

                        // rebind every 100 to conserve resources but still allow selection while loading
                        if (index % 100 === 0) {
                            bookYourTravelAdmin.bindSelectIcons();
                        }

                        cnt -= 1;
                    }

                    // rebind one last time when all done
                    bookYourTravelAdmin.bindSelectIcons();

                    if (index < array.length) {
                        // set Timeout for async iteration
                        setTimeout(doChunk, 10);
                    }
                }
            }
            doChunk();
        },
        bindSelectIcons: function () {

            $(".widgets_select_icon").off("click");
            $(".widgets_select_icon").on("click", function (e) {

                var selectedIcon = $(this).find(".icon").html();

                var idPrefix = "";
                if (window.loadIconsSectionId && window.loadIconsSectionId.length > 0) {
                    idPrefix = "#" + window.loadIconsSectionId + " ";
                }

                $(idPrefix + "." + window.themeenergyIconsContainerClass + window.loadedIconsThickboxIndex + " .icon_class").val(selectedIcon);
                $(idPrefix + "." + window.themeenergyIconsContainerClass + window.loadedIconsThickboxIndex + " .lightbox-icon").attr("class", "lightbox-icon icon material-icons");
                $(idPrefix + "." + window.themeenergyIconsContainerClass + window.loadedIconsThickboxIndex + " .lightbox-icon").html(selectedIcon);

                tb_remove();

                e.preventDefault();
            });
        },
        bindThickboxLinks: function () {
            $(".thickbox_link").off("click");
            $(".thickbox_link").on("click", function () {
                var linkClass = $(this).attr("class");
                linkClass = linkClass.replace(/thickbox_link/g, "");
                linkClass = linkClass.replace(/thickbox/g, "");

                var parentSection = $(this).closest(".widget");
                window.loadIconsSectionId = "";
                if (parentSection.length > 0) {
                    window.loadIconsSectionId = parentSection.attr("id");
                } else {
                    parentSection = $(this).closest(".section-repeat_tab");
                    window.loadIconsSectionId = parentSection.attr("id");
                }

                window.loadedIconsThickboxIndex = linkClass.trim();
            });
        },
        init: function () {

            $(".tour_type_repeat_type").off("change");
            $(".tour_type_repeat_type").on("change", function (ignore) {

                var val = parseInt($(this).val(), 10);

                if (val === 3 && $(this).hasClass("display_block")) {
                    document.getElementById("tr_tour_type_day_of_week").style.display = "block";
                } else if (val === 3 && $(this).hasClass("display_table_row")) {
                    document.getElementById("tr_tour_type_day_of_week").style.display = "table-row";
                } else {
                    document.getElementById("tr_tour_type_day_of_week").style.display = "none";
                }

                if (val === 4 && $(this).hasClass("display_block")) {
                    document.getElementById("tr_tour_type_days_of_week").style.display = "block";
                } else if (val === 4 && $(this).hasClass("display_table_row")) {
                    document.getElementById("tr_tour_type_days_of_week").style.display = "table-row";
                } else {
                    document.getElementById("tr_tour_type_days_of_week").style.display = "none";
                }
            });

            $(".cruise_type_repeat_type").off("change");
            $(".cruise_type_repeat_type").on("change", function (ignore) {

                var val = parseInt($(this).val(), 10);

                if (val === 3 && $(this).hasClass("display_block")) {
                    document.getElementById("tr_cruise_type_day_of_week").style.display = "block";
                } else if (val === 3 && $(this).hasClass("display_table_row")) {
                    document.getElementById("tr_cruise_type_day_of_week").style.display = "table-row";
                } else {
                    document.getElementById("tr_cruise_type_day_of_week").style.display = "none";
                }

                if (val === 4 && $(this).hasClass("display_block")) {
                    document.getElementById("tr_cruise_type_days_of_week").style.display = "block";
                } else if (val === 4 && $(this).hasClass("display_table_row")) {
                    document.getElementById("tr_cruise_type_days_of_week").style.display = "table-row";
                } else {
                    document.getElementById("tr_cruise_type_days_of_week").style.display = "none";
                }
            });

            if ($("#accommodation_use_referral_url") !== undefined && $("#accommodation_use_referral_url").length > 0) {
                bookYourTravelAdmin.showHideReferralUrlFields("accommodation");
                $("#accommodation_use_referral_url").change(function () {
                    bookYourTravelAdmin.showHideReferralUrlFields("accommodation");
                });
            }

            if ($("#car_rental_use_referral_url") !== undefined && $("#car_rental_use_referral_url").length > 0) {
                bookYourTravelAdmin.showHideReferralUrlFields("car_rental");
                $("#car_rental_use_referral_url").change(function () {
                    bookYourTravelAdmin.showHideReferralUrlFields("car_rental");
                });
            }

            if ($("#cruise_use_referral_url") !== undefined && $("#cruise_use_referral_url").length > 0) {
                bookYourTravelAdmin.showHideReferralUrlFields("cruise");
                $("#cruise_use_referral_url").change(function () {
                    bookYourTravelAdmin.showHideReferralUrlFields("cruise");
                });
            }

            if ($("#tour_use_referral_url") !== undefined && $("#tour_use_referral_url").length > 0) {
                bookYourTravelAdmin.showHideReferralUrlFields("tour");
                $("#tour_use_referral_url").change(function () {
                    bookYourTravelAdmin.showHideReferralUrlFields("tour");
                });
            }

            if ($("#accommodation_disabled_room_types") !== undefined && $("#accommodation_disabled_room_types").length > 0) {
                bookYourTravelAdmin.showHideRoomTypes($("#accommodation_disabled_room_types").is(":checked"));
                $("#accommodation_disabled_room_types").change(function () {
                    bookYourTravelAdmin.showHideRoomTypes($(this).is(":checked"));
                });
            }

            if ($("#accommodation_is_price_per_person") !== undefined && $("#accommodation_is_price_per_person").length > 0) {
                bookYourTravelAdmin.showHideCountChildrenStayFree($("#accommodation_is_price_per_person").is(":checked"));
                $("#accommodation_is_price_per_person").change(function () {
                    bookYourTravelAdmin.showHideCountChildrenStayFree($(this).is(":checked"));
                });
            }

            if ($("#location_display_as_directory") !== undefined && $("#location_display_as_directory").length > 0) {
                bookYourTravelAdmin.showHideDisplayLocationAsDirectory($("#location_display_as_directory").is(":checked"));
                $("#location_display_as_directory").change(function () {
                    bookYourTravelAdmin.showHideDisplayLocationAsDirectory($(this).is(":checked"));
                });
            }

            $(document).off("DOMNodeInserted");
            $(document).on("DOMNodeInserted", "#TB_ajaxContent", function (event) {
                var element = event.target;
                if ($(element).is("div.icons")) {
                    var $TB_ajaxContent = $("#TB_ajaxContent .icons");
                    $TB_ajaxContent.empty();
                    let icons;
                    if (window.themeenergyIconsString) {
                        icons = window.themeenergyIconsString.split(/\r?\n/);
                    }
                    bookYourTravelAdmin.processIconArray(icons);
                }
            });

            jQuery(document).on("widget-updated", function () {
                bookYourTravelAdmin.bindThickboxLinks();
            });

            bookYourTravelAdmin.bindThickboxLinks();

            if ($("#iconic_features_widget_classes").length > 0) {

                var previewDiv = $("<div/>");
                previewDiv.attr("class", "icon-preview");

                var previewLink = $("<a/>");
                previewLink.attr("href", "#");
                previewLink.attr("class", "icons-toggle-preview icons-show-preview");
                previewLink.text("Show icon previews");
                previewDiv.append(previewLink);
                $("#iconic_features_widget_classes").after(previewDiv);

                $(".icons-toggle-preview").off("click");
                $(".icons-toggle-preview").on("click", function (e) {

                    if ($(this).hasClass("icons-show-preview")) {

                        $(".icons-toggle-preview").text("Hide icon previews");
                        $(".icons-toggle-preview").removeClass("icons-show-preview");

                        var $iconsDiv = $("<div/>");
                        $iconsDiv.attr("class", "icons");

                        var classesArray = $("#iconic_features_widget_classes").val().split(/\r?\n/);

                        var $iconSpan = null;

                        classesArray.forEach(function (iconClass) {
                            $iconSpan = $("<span/>");
                            $iconSpan.attr("class", "icon material-icons");
                            $iconSpan.html(iconClass);
                            $iconSpan.attr("title", iconClass);
                            $iconsDiv.append($iconSpan);
                        });

                        $(".icon-preview").append($iconsDiv);
                    } else {
                        $(".icons").remove();
                        $(".icons-toggle-preview").addClass("icons-show-preview");
                        $(".icons-toggle-preview").text("Show icon previews");
                    }

                    e.preventDefault();
                });
            }
        },
        showHideReferralUrlFields: function (post_type) {
            if ($("#" + post_type + "_use_referral_url").is(":checked")) {
                $("[name='" + post_type + "_referral_url']").closest(".meta-holder").show();
                $("[name='" + post_type + "_referral_price']").closest(".meta-holder").show();
            } else {
                $("[name='" + post_type + "_referral_url']").closest(".meta-holder").hide();
                $("[name='" + post_type + "_referral_price']").closest(".meta-holder").hide();
            }
        },
        showHideRoomTypes: function (checked) {
            if (checked) {
                $(".meta_box_items.room_types").closest(".meta-holder").hide();
                $("[name='accommodation_max_count']").closest(".meta-holder").show();
                $("[name='accommodation_max_child_count']").closest(".meta-holder").show();
                $("[name='accommodation_min_count']").closest(".meta-holder").show();
                $("[name='accommodation_min_child_count']").closest(".meta-holder").show();
            } else {
                $(".meta_box_items.room_types").closest(".meta-holder").show();
                $("[name='accommodation_max_count']").closest(".meta-holder").hide();
                $("[name='accommodation_max_child_count']").closest(".meta-holder").hide();
                $("[name='accommodation_min_count']").closest(".meta-holder").hide();
                $("[name='accommodation_min_child_count']").closest(".meta-holder").hide();
            }
        },
        showHideCountChildrenStayFree: function (checked) {
            if (checked) {
                $("[name='accommodation_count_children_stay_free']").closest(".meta-holder").show();
            } else {
                $("[name='accommodation_count_children_stay_free']").closest(".meta-holder").hide();
            }
        },
        showHideDisplayLocationAsDirectory: function (checked) {
            if (checked) {
                $("[name='location_directory_exclude_descendant_locations']").closest(".meta-holder").show();
                $("[name='location_directory_posts_per_row']").closest(".meta-holder").show();
                $("[name='location_directory_hide_item_titles']").closest(".meta-holder").show();
                $("[name='location_directory_hide_item_images']").closest(".meta-holder").show();
                $("[name='location_directory_hide_item_descriptions']").closest(".meta-holder").show();
                $("[name='location_directory_hide_item_actions']").closest(".meta-holder").show();
                $("[name='location_directory_hide_item_counts']").closest(".meta-holder").show();
                $("[name='location_directory_hide_item_ribbons']").closest(".meta-holder").show();
            } else {
                $("[name='location_directory_exclude_descendant_locations']").closest(".meta-holder").hide();
                $("[name='location_directory_posts_per_row']").closest(".meta-holder").hide();
                $("[name='location_directory_hide_item_titles']").closest(".meta-holder").hide();
                $("[name='location_directory_hide_item_images']").closest(".meta-holder").hide();
                $("[name='location_directory_hide_item_descriptions']").closest(".meta-holder").hide();
                $("[name='location_directory_hide_item_actions']").closest(".meta-holder").hide();
                $("[name='location_directory_hide_item_counts']").closest(".meta-holder").hide();
                $("[name='location_directory_hide_item_ribbons']").closest(".meta-holder").hide();
            }
        }
    };

}(jQuery));

function confirmDelete(form_id, message) {
    "use strict";

    var answer = confirm(message);
    if (answer) {
        document.getElementById(form_id.replace("#", "")).submit();
        return true;
    }
    return false;
}