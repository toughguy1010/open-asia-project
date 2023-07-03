/*jslint browser: true*/ /*jshint sub:true*/ /*global jQuery*/ /*jslint this:true */ /*global window*/ /*global document*/ /*global tb_remove*/ /*global BYTAjax*/ /*global console*/ /*global wp*/ /*global BYT*/

/**
 * File customize_controls.js.
 */

(function ($) {
    "use strict";

    function applyColorScheme(scheme_control_id, scheme_id) {

        var dataObj = {
            "action": "bookyourtravel_customizer_color_scheme_request",
            "nonce": BYT.nonce,
            "scheme_id": scheme_id
        };

        $.ajax({
            url: BYT.ajaxUrl,
            data: dataObj,
            success: function (data) {
                var scheme = $.parseJSON(data);
                if (scheme !== undefined) {
                    if (wp.customize.has(scheme_control_id)) {
                        var section_id = wp.customize.control(scheme_control_id).section();
                        if (scheme.hasOwnProperty("sections") && scheme.sections.hasOwnProperty(section_id) && scheme.sections[section_id].hasOwnProperty("settings")) {
                            $.each(scheme.sections[section_id].settings, function (setting_id, setting) {
                                if (setting.type === "color") {
                                    if (wp.customize.has(setting_id)) {
                                        wp.customize.instance(setting_id).set(setting.color);
                                    }
                                }
                            });
                        }
                    }
                }
            },
            error: function (jqxhr, textStatus, error) {
                console.log(jqxhr);
                console.log(textStatus);
                console.log(error);
            }
        });
    }

    wp.customize.bind("ready", function () {
        var customize = this;

        if (window.bytCustomizeControls !== undefined) {
            if (window.bytCustomizeControls.sliders !== undefined) {
                window.bytCustomizeControls.sliders.forEach(function (slider) {
                    var tooltip = $("#" + slider.setting_id + "_tooltip");
                    tooltip.text(slider.value);
                    $("#" + slider.setting_id + "_slider").slider({
                        range: "min",
                        value: slider.value,
                        min: slider.min,
                        max: slider.max,
                        step: slider.step,
                        slide: function (ignore, ui) {
                            tooltip.text(ui.value);
                        }
                    }).find(".ui-slider-handle");

                    $("#" + slider.setting_id + "_slider").on("slidechange", function (ignore, ui) {
                        $("input#" + slider.setting_id).val(ui.value);
                        customize(slider.setting_id, function (obj) {
                            obj.set(ui.value);
                        });
                    });
                });
            }
        }
    });
}(jQuery));
