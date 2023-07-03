/*jslint browser: true*/ /*jslint bitwise: true*/ /*jshint sub:true*/ /*global jQuery*/ /*jslint this:true */ /*global window*/ /*global document*/ /*global tb_remove*/ /*global BYTAjax*/ /*global console*/ /*global wp*/ /*global BYT*/

/**
 * File customize_preview.js.
 *
 * Instantly live-update customizer settings in the preview for improved user experience.
 */

(function ($) {
    "use strict";

    var api = wp.customize;
    var byt_customizer;

    var dataObj = {
        "action": "bookyourtravel_customizer_color_scheme_request",
        "nonce": BYT.nonce,
        "scheme_id": "default"
    };

    $.ajax({
        url: BYT.ajaxUrl,
        data: dataObj,
        success: function (data) {
            var scheme = $.parseJSON(data);
            if (scheme !== undefined) {
                if (scheme.hasOwnProperty("sections")) {
                    $.each(scheme.sections, function (ignore, section) {
                        $.each(section.settings, function (setting_id, setting) {
                            byt_customizer(setting_id, setting);
                        });
                    });
                }
            }
        },
        error: function (jqxhr, textStatus, error) {
            console.log(jqxhr);
            console.log(textStatus);
            console.log(error);
        }
    });

    function shadeColor(color, percent) {
        var f = parseInt(color.slice(1), 16);
        var t = percent < 0
            ? 0
            : 255;
        var p = percent < 0
            ? percent * -1
            : percent;
        var R = f >> 16;
        var G = f >> 8 & 0x00FF;
        var B = f & 0x0000FF;
        var res = (0x1000000 + (Math.round((t - R) * p) + R) * 0x10000 + (Math.round((t - G) * p) + G) * 0x100 + (Math.round((t - B) * p) + B));
        return "#" + res.toString(16).slice(1);
    }

    byt_customizer = function (setting_id, properties) {
        api(setting_id, function (value) {
            value.bind(function (to) {
                var property = (properties.property !== undefined && properties.property.length > 0)
                    ? properties.property
                    : "color";
                var selector = properties.selector;
                var color = properties.color;
                var force = (properties.force !== undefined && properties.force.length > 0)
                    ? "!important"
                    : "";
                var direction = (properties.direction && properties.direction.length > 0)
                    ? properties.direction
                    : "";
                if (direction.length > 0) {
                    direction += ", ";
                }
                var color_stop_1 = (properties["color-stop-1"] && properties["color-stop-1"].length > 0)
                    ? properties["color-stop-1"]
                    : "";
                var color_stop_2 = (properties["color-stop-1"] && properties["color-stop-1"].length > 0)
                    ? properties["color-stop-1"]
                    : "";
                var color_stop_1_opacity = (properties["color-stop-1-opacity"] && properties["color-stop-1-opacity"].length > 0)
                    ? properties["color-stop-1-opacity"]
                    : "0%";
                var color_stop_2_opacity = (properties["color-stop-2-opacity"] && properties["color-stop-2-opacity"].length > 0)
                    ? properties["color-stop-2-opacity"]
                    : "100%";

                var vs = "";

                if (property === "button-background-color") {
                    vs = "background: " + to + " ";
                    $("<style>" + selector + " { " + vs + " " + force + "; }</style>").appendTo("head");
                } else if (property === "placeholder-color") {
                    vs = "color: " + to + " ";
                    var styles = "";
                    selector.split(",").forEach(function (sel) {
                        styles += sel + " { " + vs + " " + force + "; }\n";
                    });
                    $("<style>" + styles + "</style>").appendTo("head");
                } else if (property.indexOf("padding") != -1 ||
					property.indexOf("margin") != -1 ||
					property.indexOf("width") != -1 ||
					property.indexOf("height") != -1) {

                    var styles = "";
                    selector.split(",").forEach(function (sel) {
						property.split(",").forEach(function (pr) {
							vs = pr + ": " + to + "px ";
							styles += sel + " { " + vs + " " + force + "; }\n";
						});
                    });
                    $("<style>" + styles + "</style>").appendTo("head");					
                } else if (color_stop_1.length > 0 && color_stop_2.length > 0) {
                    if (to !== color) {
                        color_stop_1 = shadeColor(to, -0.05);
                        color_stop_2 = shadeColor(to, 0.05);
                    }

                    vs = "background: " + to + "; ";

                    $("<style>" + selector + " { " + vs + " " + force + "; }</style>").appendTo("head");
                } else {
                    $("<style>" + selector + " { " + property + ": " + to + " " + force + "; }</style>").appendTo("head");
                }

                if (properties.dependents !== undefined) {
                    $.each(properties.dependents, function (ignore, dependent) {
                        var p = (dependent.property !== undefined && dependent.property.length > 0)
                            ? dependent.property
                            : "color";
                        var s = dependent.selector;
                        var f = (dependent.force !== undefined && dependent.force.length > 0)
                            ? "!important"
                            : "";
                        $("<style>" + s + " { " + p + ": " + to + f + "; }</style>").appendTo("head");
                    });
                }
            });
        });
    };
}(jQuery));