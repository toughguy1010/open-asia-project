/*jslint browser: true*/ /*global jQuery*/ /*jslint this:true */ /*global window*/ /*global document*/ /*global BYTAjax*/ /*global console*/
/*global bookYourTravelAdmin*/

(function ($) {

    "use strict";

    var bookYourTravelOptionsFramework;

    $(document).ready(function () {
        bookYourTravelOptionsFramework.init();
    });

    bookYourTravelOptionsFramework = {
        init: function () {
            bookYourTravelOptionsFramework.bindTypeSelectors();

            $(".input-label-for-dynamic-id").each(function () {
                bookYourTravelOptionsFramework.bindLabelForDynamicIdField($(this));
            });

            bookYourTravelOptionsFramework.bindDynamicIdField($(".input-dynamic-id"));
            bookYourTravelOptionsFramework.bindModifyDynamicIdCheckbox($(".modify-dynamic-element-id"));
            bookYourTravelOptionsFramework.bindRemoveIcons();

            bookYourTravelOptionsFramework.initializeOptionsTab("accommodations", "enable_accommodations");
            bookYourTravelOptionsFramework.initializeOptionsTab("tours", "enable_tours");
            bookYourTravelOptionsFramework.initializeOptionsTab("carrentals", "enable_car_rentals");
            bookYourTravelOptionsFramework.initializeOptionsTab("cruises", "enable_cruises");
            bookYourTravelOptionsFramework.initializeOptionsTab("reviews", "enable_reviews");

            if ($("#frontpage_show_slider").is(":checked")) 
                $("#section-homepage_slider").show();
            else
                $("#section-homepage_slider").hide();
            $("#frontpage_show_slider").change(function () {
                if (this.checked) 
                    $("#section-homepage_slider").show();
                else
                    $("#section-homepage_slider").hide();
            });            

            $("#section-show_prices_in_location_items input[type=checkbox]").off();
            $("#section-show_prices_in_location_items input[type=checkbox]").on("click", function (e) {

                var selectedCount = $(this).closest(".section").find("input:checked").length;

                if (selectedCount > 2) {
                    e.preventDefault();
                }
            });

            $(".synchronise_reviews").on("click", function (e) {
                var $parentDiv = $(this).parent();
                var $loadingDiv = $parentDiv.find(".loading");

                $loadingDiv.show();

                var dataObj = {
                    "action": "sync_reviews_ajax_request",
                    "nonce": $("#_wpnonce").val()
                };

                $.ajax({
                    url: BYTAdmin.ajaxurl,
                    data: dataObj,
                    success: function (response) {
                        $loadingDiv.hide();
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        console.log(xhr);
                        console.log(ajaxOptions);
                        console.log(thrownError);
                    }
                });

                e.preventDefault();
            });

			if ($("#use_woocommerce_for_checkout").is(":checked")) {
				$("#section-completed_order_woocommerce_statuses").show();
				$("#section-woocommerce_pages_sidebar_position").show();
			} else {
				$("#section-completed_order_woocommerce_statuses").hide();
				$("#section-woocommerce_pages_sidebar_position").hide();
			}

            $("#use_woocommerce_for_checkout").on("click", function (e) {
				if ($(this).is(":checked")) {
					$("#section-completed_order_woocommerce_statuses").show();
					$("#section-woocommerce_pages_sidebar_position").show();
				} else {
					$("#section-completed_order_woocommerce_statuses").hide();
					$("#section-woocommerce_pages_sidebar_position").hide();
				}
			});

            $(".upgrade_bookyourtravel_db").on("click", function (e) {

                var $parentDiv = $(this).parent();
                var $loadingDiv = $parentDiv.find(".loading");

                $loadingDiv.show();

                var dataObj = {
                    "action": "upgrade_bookyourtravel_db_ajax_request",
                    "nonce": $("#_wpnonce").val()
                };

                $.ajax({
                    url: BYTAdmin.ajaxurl,
                    data: dataObj,
                    success: function () {
                        // This outputs the result of the ajax request
                        $loadingDiv.hide();
                        window.location = window.adminSiteUrl;
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        console.log(xhr);
                        console.log(ajaxOptions);
                        console.log(thrownError);
                    }
                });

                e.preventDefault();
            });

            $(".of-repeat-review-fields").sortable({

                update: function () {

                    var $fieldLoop = $(this).closest(".section").find(".of-repeat-review-fields");

                    $fieldLoop.find(".of-repeat-group").each(function (index, ignore) {

                        var $inputFieldId = $(this).find("input.input-field-id");
                        var $inputFieldLabel = $(this).find("input.input-field-label");
                        var $inputFieldPostType = $(this).find("input.input-field-post-type");
                        var $labelFieldHide = $(this).find("label.label-field-hide");
                        var $labelFieldModify = $(this).find("label.label-field-modify");
                        var $checkboxFieldHide = $(this).find("input.checkbox-field-hide");
                        var $checkboxFieldModify = $(this).find("input.checkbox-field-modify");
                        var $inputFieldIndex = $(this).find("input.input-index");

                        $inputFieldId.attr("name", $inputFieldId.attr("data-rel") + "[" + index + "][id]");
                        $inputFieldLabel.attr("name", $inputFieldLabel.attr("data-rel") + "[" + index + "][label]");
                        $inputFieldPostType.attr("name", $inputFieldPostType.attr("data-rel") + "[" + index + "][post_type]");
                        $checkboxFieldHide.attr("name", $checkboxFieldHide.attr("data-rel") + "[" + index + "][hide]");
                        $labelFieldHide.attr("for", $checkboxFieldHide.attr("data-rel") + "[" + index + "][hide]");
                        $checkboxFieldModify.attr("name", $checkboxFieldModify.attr("data-rel") + "[" + index + "][modify]");
                        $labelFieldModify.attr("for", $checkboxFieldModify.attr("data-rel") + "[" + index + "][modify]");
                        $inputFieldIndex.attr("name", $inputFieldIndex.attr("data-rel") + "[" + index + "][index]");
                        $inputFieldIndex.val(index);
                    });
                }
            });

            $(".docopy_review_field").on("click", function (e) {

                var $section = $(this).closest(".section");
                var $loop = $section.find(".of-repeat-review-fields");
                var $toCopy = $loop.find(".of-repeat-group:last");
                var $newGroup = $toCopy.clone();
                var maxFieldIndex = parseInt($section.find(".max_field_index").val(), 10) + 1;

                $newGroup.insertAfter($toCopy);

                $section.find(".max_field_index").val(maxFieldIndex);
                $newGroup.find("input.input-index").val(maxFieldIndex);

                bookYourTravelOptionsFramework.initializeCustomField(".input-field-label", "label", $newGroup, maxFieldIndex, "label.label-field-label");
                bookYourTravelOptionsFramework.initializeCustomField(".input-field-id", "id", $newGroup, maxFieldIndex, "label.label-field-id");
                bookYourTravelOptionsFramework.initializeCustomField(".input-index", "index", $newGroup, maxFieldIndex, "");
                bookYourTravelOptionsFramework.initializeCustomField(".input-field-post-type", "post_type", $newGroup, maxFieldIndex, "label.label-field-post-type");
                bookYourTravelOptionsFramework.initializeCustomField(".checkbox-field-hide", "hide", $newGroup, maxFieldIndex, "label.label-field-hide");
                bookYourTravelOptionsFramework.initializeCustomField(".checkbox-field-modify", "modify", $newGroup, maxFieldIndex, "label.label-field-modify");

                $newGroup.append($("<span class=\"ui-icon ui-icon-close\"></span>"));
                bookYourTravelOptionsFramework.bindRemoveIcons();
                bookYourTravelOptionsFramework.bindLabelForDynamicIdField($newGroup.find("input.input-field-label"));
                bookYourTravelOptionsFramework.bindDynamicIdField($newGroup.find("input.input-field-id"));
                bookYourTravelOptionsFramework.bindModifyDynamicIdCheckbox($newGroup.find("input.modify-dynamic-element-id"));

                $newGroup.find(".input-field-id").val("review_");

                e.preventDefault();
            });

            $(".of-repeat-form-fields").sortable({
                update: function () {
                    var $fieldLoop = $(this).closest(".section").find(".of-repeat-form-fields");

                    $fieldLoop.find(".of-repeat-group").each(function (index, ignore) {

                        var $inputFieldId = $(this).find("input.input-field-id");
                        var $inputFieldLabel = $(this).find("input.input-field-label");
                        var $labelFieldType = $(this).find("label.label-field-type");
                        var $selectFieldType = $(this).find("select.select-field-type");
                        var $labelFieldHide = $(this).find("label.label-field-hide");
                        var $labelFieldModify = $(this).find("label.label-field-modify");
                        var $checkboxFieldHide = $(this).find("input.checkbox-field-hide");
                        var $checkboxFieldModify = $(this).find("input.checkbox-field-modify");
                        var $inputFieldIndex = $(this).find("input.input-index");

                        $inputFieldId.attr("name", $inputFieldId.attr("data-rel") + "[" + index + "][id]");
                        $inputFieldLabel.attr("name", $inputFieldLabel.attr("data-rel") + "[" + index + "][label]");
                        $selectFieldType.attr("name", $selectFieldType.attr("data-rel") + "[" + index + "][type]");
                        $labelFieldType.attr("for", $selectFieldType.attr("data-rel") + "[" + index + "][type]");
                        $checkboxFieldHide.attr("name", $checkboxFieldHide.attr("data-rel") + "[" + index + "][hide]");
                        $labelFieldHide.attr("for", $checkboxFieldHide.attr("data-rel") + "[" + index + "][hide]");
                        $checkboxFieldModify.attr("name", $checkboxFieldModify.attr("data-rel") + "[" + index + "][modify]");
                        $labelFieldModify.attr("for", $checkboxFieldModify.attr("data-rel") + "[" + index + "][modify]");
                        $inputFieldIndex.attr("name", $inputFieldIndex.attr("data-rel") + "[" + index + "][index]");
                        $inputFieldIndex.val(index);
                    });
                }
            });

            $(".of-repeat-extra-fields").sortable({

                update: function () {
                    var $fieldLoop = $(this).closest(".section").find(".of-repeat-extra-fields");

                    $fieldLoop.find(".of-repeat-group").each(function (index, ignore) {

                        var $inputFieldId = $(this).find("input.input-field-id");
                        var $labelFieldId = $(this).find("label.label-field-id");
                        var $inputFieldLabel = $(this).find("input.input-field-label");
                        var $labelFieldLabel = $(this).find("label.label-field-label");
                        var $inputFieldMin = $(this).find("input.input-field-min");
                        var $labelFieldMin = $(this).find("label.label-field-min");
                        var $inputFieldMax = $(this).find("input.input-field-max");
                        var $labelFieldMax = $(this).find("label.label-field-max");
                        var $inputFieldStep = $(this).find("input.input-field-step");
                        var $labelFieldStep = $(this).find("label.label-field-step");
                        var $inputFieldOptions = $(this).find("textarea.textarea-field-options");
                        var $labelFieldOptions = $(this).find("label.label-field-options");

                        var $labelFieldType = $(this).find("label.label-field-type");
                        var $labelFieldTab = $(this).find("label.label-field-tab");
                        var $selectFieldType = $(this).find("select.select-field-type");
                        var $selectFieldTab = $(this).find("select.select-field-tab");
                        var $labelFieldHide = $(this).find("label.label-field-hide");
                        var $labelFieldHideFront = $(this).find("label.label-field-hide-front");
                        var $labelFieldModify = $(this).find("label.label-field-modify");
                        var $checkboxFieldHide = $(this).find("input.checkbox-field-hide");
                        var $checkboxFieldHideFront = $(this).find("input.checkbox-field-hide-front");
                        var $checkboxFieldModify = $(this).find("input.checkbox-field-modify");
                        var $inputFieldIndex = $(this).find("input.input-index");

                        $inputFieldId.attr("name", $inputFieldId.attr("data-rel") + "[" + index + "][id]");
                        $labelFieldId.attr("for", $inputFieldId.attr("data-rel") + "[" + index + "][id]");
                        $inputFieldLabel.attr("name", $inputFieldLabel.attr("data-rel") + "[" + index + "][label]");
                        $labelFieldLabel.attr("for", $inputFieldLabel.attr("data-rel") + "[" + index + "][label]");
                        $inputFieldMin.attr("name", $inputFieldMin.attr("data-rel") + "[" + index + "][min]");
                        $labelFieldMin.attr("for", $inputFieldMin.attr("data-rel") + "[" + index + "][min]");
                        $inputFieldMax.attr("name", $inputFieldMax.attr("data-rel") + "[" + index + "][max]");
                        $labelFieldMax.attr("for", $inputFieldMax.attr("data-rel") + "[" + index + "][max]");
                        $inputFieldStep.attr("name", $inputFieldStep.attr("data-rel") + "[" + index + "][step]");
                        $labelFieldStep.attr("for", $inputFieldStep.attr("data-rel") + "[" + index + "][step]");
                        $inputFieldOptions.attr("name", $inputFieldOptions.attr("data-rel") + "[" + index + "][options]");
                        $labelFieldOptions.attr("for", $inputFieldOptions.attr("data-rel") + "[" + index + "][options]");
                        $selectFieldType.attr("name", $selectFieldType.attr("data-rel") + "[" + index + "][type]");
                        $labelFieldType.attr("for", $selectFieldType.attr("data-rel") + "[" + index + "][type]");
                        $selectFieldTab.attr("name", $selectFieldTab.attr("data-rel") + "[" + index + "][tab_id]");
                        $labelFieldTab.attr("for", $selectFieldTab.attr("data-rel") + "[" + index + "][tab_id]");
                        $checkboxFieldHide.attr("name", $checkboxFieldHide.attr("data-rel") + "[" + index + "][hide]");
                        $labelFieldHide.attr("for", $checkboxFieldHide.attr("data-rel") + "[" + index + "][hide]");
                        $checkboxFieldHideFront.attr("name", $checkboxFieldHideFront.attr("data-rel") + "[" + index + "][hide_front]");
                        $labelFieldHideFront.attr("for", $checkboxFieldHideFront.attr("data-rel") + "[" + index + "][hide_front]");
                        $checkboxFieldModify.attr("name", $checkboxFieldModify.attr("data-rel") + "[" + index + "][modify]");
                        $labelFieldModify.attr("for", $checkboxFieldModify.attr("data-rel") + "[" + index + "][modify]");
                        $inputFieldIndex.attr("name", $inputFieldIndex.attr("data-rel") + "[" + index + "][index]");
                        $inputFieldIndex.val(index);
                    });
                }
            });

            $(".docopy_field").on("click", function (e) {

                var $section = $(this).closest(".section");
                var $loop = $section.find(".of-repeat-extra-fields");
                var $toCopy = $loop.find(".of-repeat-group:last");
                var $newGroup = $toCopy.clone();
                var maxFieldIndex = parseInt($section.find(".max_field_index").val(), 10) + 1;

                $newGroup.insertAfter($toCopy);

                $section.find(".max_field_index").val(maxFieldIndex);
                $newGroup.find("input.input-index").val(maxFieldIndex);

                bookYourTravelOptionsFramework.initializeCustomField(".input-field-label", "label", $newGroup, maxFieldIndex, "label.label-field-label");
                bookYourTravelOptionsFramework.initializeCustomField(".input-field-id", "id", $newGroup, maxFieldIndex, "label.label-field-id");
                bookYourTravelOptionsFramework.initializeCustomField(".input-index", "index", $newGroup, maxFieldIndex, "");
                bookYourTravelOptionsFramework.initializeCustomField(".select-field-type", "type", $newGroup, maxFieldIndex, "label.label-field-type");

                bookYourTravelOptionsFramework.initializeCustomField(".input-field-min", "min", $newGroup, maxFieldIndex, "label.label-field-min");
                bookYourTravelOptionsFramework.initializeCustomField(".input-field-max", "max", $newGroup, maxFieldIndex, "label.label-field-max");
                bookYourTravelOptionsFramework.initializeCustomField(".input-field-step", "step", $newGroup, maxFieldIndex, "label.label-field-step");
                bookYourTravelOptionsFramework.initializeCustomField(".textarea-field-options", "options", $newGroup, maxFieldIndex, "label.label-field-options");

                bookYourTravelOptionsFramework.initializeCustomField(".select-field-tab", "tab_id", $newGroup, maxFieldIndex, "label.label-field-tab");
                bookYourTravelOptionsFramework.initializeCustomField(".checkbox-field-hide", "hide", $newGroup, maxFieldIndex, "label.label-field-hide");
                bookYourTravelOptionsFramework.initializeCustomField(".checkbox-field-hide-front", "hide_front", $newGroup, maxFieldIndex, "label.label-field-hide-front");
                bookYourTravelOptionsFramework.initializeCustomField(".checkbox-field-modify", "modify", $newGroup, maxFieldIndex, "label.label-field-modify");

                $newGroup.append($("<span class=\"ui-icon ui-icon-close\"></span>"));
                bookYourTravelOptionsFramework.bindRemoveIcons();
                bookYourTravelOptionsFramework.bindLabelForDynamicIdField($newGroup.find("input.input-field-label"));
                bookYourTravelOptionsFramework.bindDynamicIdField($newGroup.find("input.input-field-id"));
                bookYourTravelOptionsFramework.bindModifyDynamicIdCheckbox($newGroup.find("input.modify-dynamic-element-id"));
                bookYourTravelOptionsFramework.bindTypeSelectors();

                $newGroup.find("select.select-field-type").val("checkbox");     
                $newGroup.find("select.select-field-type").change();     

                e.preventDefault();
            });

            $(".docopy_form_field").on("click", function (e) {

                var $section = $(this).closest(".section");
                var $loop = $section.find(".of-repeat-form-fields");
                var $toCopy = $loop.find(".of-repeat-group:last");
                var $newGroup = $toCopy.clone();
                var maxFieldIndex = parseInt($section.find(".max_field_index").val(), 10) + 1;

                $newGroup.insertAfter($toCopy);

                $section.find(".max_field_index").val(maxFieldIndex);
                $newGroup.find("input.input-index").val(maxFieldIndex);

                bookYourTravelOptionsFramework.initializeCustomField(".input-field-label", "label", $newGroup, maxFieldIndex, "label.label-field-label");
                bookYourTravelOptionsFramework.initializeCustomField(".input-field-id", "id", $newGroup, maxFieldIndex, "label.label-field-id");
                bookYourTravelOptionsFramework.initializeCustomField(".input-index", "index", $newGroup, maxFieldIndex, "");
                bookYourTravelOptionsFramework.initializeCustomField(".select-field-type", "type", $newGroup, maxFieldIndex, "label.label-field-type");
                bookYourTravelOptionsFramework.initializeCustomField(".checkbox-field-hide", "hide", $newGroup, maxFieldIndex, "label.label-field-hide");
                bookYourTravelOptionsFramework.initializeCustomField(".checkbox-field-modify", "modify", $newGroup, maxFieldIndex, "label.label-field-modify");
                bookYourTravelOptionsFramework.initializeCustomField(".checkbox-field-required", "required", $newGroup, maxFieldIndex, "label.label-field-required");
                bookYourTravelOptionsFramework.initializeCustomField(".textarea-field-options", "options", $newGroup, maxFieldIndex, "label.label-field-options");

                $newGroup.append($("<span class=\"ui-icon ui-icon-close\"></span>"));
                bookYourTravelOptionsFramework.bindRemoveIcons();
                bookYourTravelOptionsFramework.bindLabelForDynamicIdField($newGroup.find("input.input-field-label"));
                bookYourTravelOptionsFramework.bindDynamicIdField($newGroup.find("input.input-field-id"));
                bookYourTravelOptionsFramework.bindModifyDynamicIdCheckbox($newGroup.find("input.modify-dynamic-element-id"));
                bookYourTravelOptionsFramework.bindTypeSelectors();

                $newGroup.find("select.select-field-type").val("checkbox");
                $newGroup.find("select.select-field-type").change();

                e.preventDefault();
            });

            $(".of-repeat-tabs").sortable({

                update: function () {
                    var $tabLoop = $(this).closest(".section").find(".of-repeat-tabs");

                    $tabLoop.find(".of-repeat-group").each(function (index, ignore) {

                        var $inputTabId = $(this).find("input.input-tab-id");
                        var $inputTabLabel = $(this).find("input.input-tab-label");
                        var $checkboxTabHide = $(this).find("input.checkbox-tab-hide");
                        var $labelTabHide = $(this).find("label.label-tab-hide");
                        var $labelTabLabel = $(this).find("label.label-tab-label");
                        var $labelTabId = $(this).find("label.label-tab-id");
                        var $labelTabIconClass = $(this).find("label.label-tab-icon_class");
                        var $checkboxTabModify = $(this).find("input.checkbox-tab-modify");
                        var $labelTabModify = $(this).find("label.label-tab-modify");
                        var $inputTabIndex = $(this).find("input.input-index");
                        var $inputTabIconClass = $(this).find("input.input-tab-icon-class");

                        $inputTabId.attr("name", $inputTabId.attr("data-rel") + "[" + (index) + "][id]");
                        $inputTabLabel.attr("name", $inputTabLabel.attr("data-rel") + "[" + index + "][label]");
                        $inputTabIconClass.attr("name", $inputTabIconClass.attr("data-rel") + "[" + index + "][icon_class]");
                        $checkboxTabHide.attr("name", $checkboxTabHide.attr("data-rel") + "[" + index + "][hide]");
                        $labelTabHide.attr("for", $checkboxTabHide.attr("data-rel") + "[" + index + "][hide]");

                        $labelTabLabel.attr("for", $inputTabLabel.attr("data-rel") + "[" + index + "][label]");
                        $labelTabId.attr("for", $inputTabId.attr("data-rel") + "[" + index + "][id]");
                        $labelTabIconClass.attr("for", $inputTabIconClass.attr("data-rel") + "[" + index + "][id]");

                        $checkboxTabModify.attr("name", $checkboxTabModify.attr("data-rel") + "[" + index + "][modify]");
                        $labelTabModify.attr("for", $checkboxTabModify.attr("data-rel") + "[" + index + "][modify]");
                        $inputTabIndex.attr("name", $inputTabIndex.attr("data-rel") + "[" + index + "][index]");
                        $inputTabIndex.val(index);
                    });
                }
            });

            $(".docopy_tab").on("click", function (e) {

                var $section = $(this).closest(".section");
                var $loop = $section.find(".of-repeat-tabs");
                var $toCopy = $loop.find(".of-repeat-group:last");
                var $newGroup = $toCopy.clone();

                $newGroup.insertAfter($toCopy);

                var maxTabIndex = parseInt($section.find(".max_tab_index").val(), 10) + 1;
                $newGroup.attr('class', 'ui-state-default of-repeat-group ui-sortable-handle of-repeat-tab' + maxTabIndex);
                $section.find(".max_tab_index").val(maxTabIndex);
                $newGroup.find("input.input-index").val(maxTabIndex);
                $newGroup.find("input.icon_class").val('');
                $newGroup.find(".lightbox-icon").html('');
                $newGroup.find(".thickbox_link").attr('class', 'thickbox thickbox_link thickbox' + maxTabIndex);

                bookYourTravelOptionsFramework.initializeCustomField(".input-tab-label", "label", $newGroup, maxTabIndex, "label.label-tab-label");
                bookYourTravelOptionsFramework.initializeCustomField(".input-tab-id", "id", $newGroup, maxTabIndex, "label.label-tab-id");
                bookYourTravelOptionsFramework.initializeCustomField(".input-index", "index", $newGroup, maxTabIndex, "");
                bookYourTravelOptionsFramework.initializeCustomField(".input-tab-icon-class", "icon_class", $newGroup, maxTabIndex, "label.label-tab-icon_class");
                bookYourTravelOptionsFramework.initializeCustomField(".checkbox-tab-hide", "hide", $newGroup, maxTabIndex, "label.label-tab-hide");
                bookYourTravelOptionsFramework.initializeCustomField(".checkbox-tab-hide-front", "hide_front", $newGroup, maxTabIndex, "label.label-tab-hide-front");

                $newGroup.append($("<span class=\"ui-icon ui-icon-close\"></span>"));
                bookYourTravelOptionsFramework.bindRemoveIcons();
                bookYourTravelOptionsFramework.bindLabelForDynamicIdField($newGroup.find("input.input-tab-label"));
                bookYourTravelOptionsFramework.bindDynamicIdField($newGroup.find("input.input-tab-id"));
                bookYourTravelOptionsFramework.bindModifyDynamicIdCheckbox($newGroup.find("input.modify-dynamic-element-id"));
                bookYourTravelAdmin.bindThickboxLinks();                

                e.preventDefault();
            });
        },
        bindTypeSelectors: function () {
            if ($(".select-field-type").length > 0) {

                $(".select-field-type").off("change");
                $(".select-field-type").on("change", function () {
                    var selectedType = $(this).val();
                    if (selectedType === "select") {
                        $(this).closest("div").next(".of-textarea-options").show();
                    } else {
                        $(this).closest("div").next(".of-textarea-options").hide();
                    }

                    if (selectedType === "slider") {
                        $(this).closest("div").nextAll(".of-input-range").show();
                    } else {
                        $(this).closest("div").nextAll(".of-input-range").hide();
                    }
                });
            }
        },
        bindModifyDynamicIdCheckbox: function ($checkboxInput) {

            $checkboxInput.on("click", function () {

                var $idInput = $(this).parent().parent().prevAll(".of-input-wrap.of-modify-id").find("input[type=text].input-dynamic-id");

                if ($idInput.is("[readonly]")) {
                    $idInput.prop("readonly", false);
                } else {
                    $idInput.prop("readonly", true);
                }
            });

        },
        bindDynamicIdField: function ($inputDynamicId) {

            $inputDynamicId.on("blur", function () {

                if (!$(this).is("[readonly]")) {

                    var $this = $(this);
                    var $parentDiv = $(this).parent().parent();
                    var $loadingDiv = $parentDiv.find(".loading");
                    var elementType = "";
                    var elementNewId = $(this).val();
                    var elementId = $(this).data("id");
                    var elementOriginalId = $(this).data("original-id");
                    var elementIsDefault = $(this).data("is-default");

                    if (elementNewId !== elementOriginalId && elementNewId !== elementId && !elementIsDefault) {
                        if ($this.hasClass("input-tab-id")) {
                            elementType = "tab";
                        } else if ($this.hasClass("input-review-field-id")) {
                            elementType = "review_field";
                        } else if ($this.hasClass("input-inquiry-form-field-id")) {
                            elementType = "inquiry_form_field";
                        } else if ($this.hasClass("input-booking-form-field-id")) {
                            elementType = "booking_form_field";
                        } else if ($this.hasClass("input-field-id")) {
                            elementType = "field";
                        }

                        $loadingDiv.show();

                        var newId = bookYourTravelOptionsFramework.getUniqueDynamicElementId(elementNewId, elementType, $this.data("parent"));

                        $this.val(newId);
                        $this.data("id", newId);

                        $loadingDiv.hide();
                    }
                }
            });
        },
        bindLabelForDynamicIdField: function ($inputElement) {

            var elementOriginalId = $inputElement.data("original-id");

            $inputElement.on("blur", function () {

                var val = $inputElement.val();
                var $parentDiv = $inputElement.parent().parent();
                var $loadingDiv = $parentDiv.find(".loading");
                var $idInput = $parentDiv.find(".input-dynamic-id");
                var currentInputId = $idInput.val();
                if (currentInputId.length === 0) {
                    var elementType = "";
                    var elementNewId = bookYourTravelOptionsFramework.cleanUpId(val);
                    var elementIsDefault = $(this).data("is-default");

                    if (!elementIsDefault && (elementOriginalId === null || elementOriginalId === "undefined" || elementOriginalId !== elementNewId)) {
                        $loadingDiv.show();

                        if ($idInput.hasClass("input-tab-id")) {
                            elementType = "tab";
                        } else if ($idInput.hasClass("input-review-field-id")) {
                            elementType = "review_field";
                        } else if ($idInput.hasClass("input-inquiry-form-field-id")) {
                            elementType = "inquiry_form_field";
                        } else if ($idInput.hasClass("input-booking-form-field-id")) {
                            elementType = "booking_form_field";
                        } else if ($idInput.hasClass("input-field-id")) {
                            elementType = "field";
                        }

                        var newId = bookYourTravelOptionsFramework.getUniqueDynamicElementId(elementNewId, elementType, $idInput.data("parent"));

                        $idInput.val(newId);
                        $idInput.data("id", newId);
                        $loadingDiv.hide();
                    }
                }
            });

            if (elementOriginalId === null || elementOriginalId === "undefined") {

                $inputElement.on("keyup", function (e) {

                    if (e.which === 13) {
                        // Enter key pressed
                        e.preventDefault();
                    } else {

                        var val = $inputElement.val();
                        var $parentDiv = $inputElement.parent().parent();
                        var $idInput = $parentDiv.find(".input-dynamic-id");
                        var slug = bookYourTravelOptionsFramework.cleanUpId(val);

                        $idInput.val(slug);
                    }
                });

            }
        },
        getUniqueDynamicElementId: function (elementNewId, elementType, parent) {

            var newId = "";

            var dataObj = {
                "action": "generate_unique_dynamic_element_id",
                "element_id": elementNewId,
                "nonce": $("#_wpnonce").val(),
                "element_type": elementType,
                "parent": parent
            };

            $.ajax({
                url: BYTAdmin.ajaxurl,
                data: dataObj,
                async: false,
                success: function (data) {
                    newId = JSON.parse(data);
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    console.log(xhr);
                    console.log(ajaxOptions);
                    console.log(thrownError);
                }
            });

            return newId;
        },
        cleanUpId: function (str) {
            return str.replace(/-/g, "_")
                .replace(/\s/g, "_")
                .replace(/:/g, "_")
                .replace(/\\/g, "_")
                .replace(/\//g, "_")
                .replace(/[^a-zA-Z0-9_]+/g, "")
                .replace(/-{2,}/g, "_")
                .toLowerCase();
        },
        bindRemoveIcons: function () {

            $(".ui-icon-close").unbind("click");
            $(".ui-icon-close").on("click", function () {
                $(this).parent().remove();
                return false;
            });
        },
        initializeOptionsTab: function (groupClass, checkboxId) {

            bookYourTravelOptionsFramework.toggleTabVisibility($("#" + checkboxId).is(":checked"), groupClass);

            $("#" + checkboxId).change(function () {
                bookYourTravelOptionsFramework.toggleTabVisibility(this.checked, groupClass);
            });
        },
        toggleTabVisibility: function (show, groupClass) {
            if (show) {
                $("." + groupClass + "-tab").show();
                $("." + groupClass + "_controls").show();
            } else {
                $("." + groupClass + "-tab").hide();
                $("." + groupClass + "_controls").hide();
            }
        },
        initializeCustomField: function (fieldSelector, fieldKey, $groupObj, fieldIndex, labelSelector) {

            var $fieldControl = $groupObj.find(fieldSelector);

            $fieldControl.attr("name", $fieldControl.attr("data-rel") + "[" + fieldIndex + "][" + fieldKey + "]");
            $fieldControl.attr("id", $fieldControl.attr("data-rel") + "[" + fieldIndex + "][" + fieldKey + "]");

            if ($fieldControl.attr("data-original-id")) {
                $fieldControl.removeAttr("data-original-id");
            }

            if ($fieldControl.attr("data-id")) {
                $fieldControl.removeAttr("data-original-id");
            }

            if ($fieldControl.attr("data-is-default")) {
                $fieldControl.removeAttr("data-is-default");
            }

            if ($fieldControl.attr("value")) {
                $fieldControl.removeAttr("value");
            }

            if (labelSelector.length > 0) {
                $groupObj.find(labelSelector).attr("for", $fieldControl.attr("data-rel") + "[" + fieldIndex + "][" + fieldKey + "]");
            }
        },
        parseCssRules: function (styleContent) {
            var doc = document.implementation.createHTMLDocument("");
            var styleElement = document.createElement("style");

            styleElement.textContent = styleContent;
            // the style will only be parsed once it is added to a document
            doc.body.appendChild(styleElement);

            return styleElement.sheet.cssRules;
        }
    };

}(jQuery));