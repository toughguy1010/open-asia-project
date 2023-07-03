/*jslint browser: true*/ /*global jQuery*/ /*jslint this:true */
/*global window*/ /*global BYTAjax*/ /*global console*/

(function ($) {

    "use strict";

    var bookyourtravel_account;

    $(document).ready(function () {
        bookyourtravel_account.init();
    });

    bookyourtravel_account = {

        init: function () {

            $("#settings-first-name-form").validate({
                onkeyup: false,
                rules: {
                    first_name: "required"
                },
                messages: {
                    first_name: window.settingsFirstNameError
                },
                submitHandler: function () {
                    bookyourtravel_account.processFirstNameSubmit();
                },
                debug: true
            });

            $("#settings-last-name-form").validate({
                onkeyup: false,
                rules: {
                    last_name: "required"
                },
                messages: {
                    last_name: window.settingsLastNameError
                },
                submitHandler: function () {
                    bookyourtravel_account.processLastNameSubmit();
                },
                debug: true
            });

            $("#settings-email-form").validate({
                onkeyup: false,
                rules: {
                    email: {
                        required: true,
                        email: true
                    }
                },
                messages: {
                    email: window.settingsEmailError
                },
                submitHandler: function () {
                    bookyourtravel_account.processEmailSubmit();
                },
                debug: true
            });

            $("#settings-password-form").validate({
                onkeyup: false,
                rules: {
                    new_password: "required",
                    old_password: "required"
                },
                messages: {
                    new_password: window.settingsPasswordError
                },
                submitHandler: function () {
                    bookyourtravel_account.processPasswordSubmit();
                },
                debug: true
            });

            $(".edit_button").on("click", function (event) {
                $("div.edit_field").hide();
                $(this).parent().parent().find("td div.edit_field").show();
                event.preventDefault();
            });

            $(".hide_edit_field").on("click", function (event) {
                $("div.edit_field").hide();
                event.preventDefault();
            });

            $(".export_account_button").on("click", function (event) {
                event.preventDefault();
                $(".request_success").hide();

                var dataObj = {
                    "action": "settings_ajax_request_account_export",
                    "userId": BYTAjax.current_user_id,
                    "nonce": BYTAjax.nonce
                };

                $.ajax({
                    url: BYTAjax.ajaxurl,
                    data: dataObj,
                    success: function () {
                        $(".export_account_button").hide();
                        $(".request_success").show();
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        console.log(xhr);
                        console.log(ajaxOptions);
                        console.log(thrownError);
                    }
                });
            });

            $(".delete_account_button").on("click", function (event) {
                event.preventDefault();

                $(".request_success").hide();

                var dataObj = {
                    "action": "settings_ajax_request_account_delete",
                    "userId": BYTAjax.current_user_id,
                    "nonce": BYTAjax.nonce
                };

                $.ajax({
                    url: BYTAjax.ajaxurl,
                    data: dataObj,
                    success: function () {
                        $(".delete_account_button").hide();
                        $(".request_success").show();
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        console.log(xhr);
                        console.log(ajaxOptions);
                        console.log(thrownError);
                    }
                });
            });
        },
        processFirstNameSubmit: function () {
            var first_name = $("#first_name").val();

            var dataObj = {
                "action": "settings_ajax_save_first_name",
                "firstName": first_name,
                "userId": BYTAjax.current_user_id,
                "nonce": BYTAjax.nonce
            };

            $.ajax({
                url: BYTAjax.ajaxurl,
                data: dataObj,
                success: function () {
                    $("#span_first_name").html(first_name);
                    $("div.edit_field").hide();
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    console.log(xhr);
                    console.log(ajaxOptions);
                    console.log(thrownError);
                }
            });
        },
        processLastNameSubmit: function () {
            var last_name = $("#last_name").val();

            var dataObj = {
                "action": "settings_ajax_save_last_name",
                "lastName": last_name,
                "userId": BYTAjax.current_user_id,
                "nonce": BYTAjax.nonce
            };

            $.ajax({
                url: BYTAjax.ajaxurl,
                data: dataObj,
                success: function () {
                    $("#span_last_name").html(last_name);
                    $("div.edit_field").hide();
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    console.log(xhr);
                    console.log(ajaxOptions);
                    console.log(thrownError);
                }
            });
        },
        processEmailSubmit: function () {
            var email = $("#email").val();

            var dataObj = {
                "action": "settings_ajax_save_email",
                "email": email,
                "userId": BYTAjax.current_user_id,
                "nonce": BYTAjax.nonce
            };

            $.ajax({
                url: BYTAjax.ajaxurl,
                data: dataObj,
                success: function () {
                    $("#span_email").html(email);
                    $("div.edit_field").hide();
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    console.log(xhr);
                    console.log(ajaxOptions);
                    console.log(thrownError);
                }
            });
        },
        processPasswordSubmit: function () {
            var new_password = $("#new_password").val();
            var old_password = $("#old_password").val();
            $("div.edit_field").hide();

            var dataObj = {
                "action": "settings_ajax_save_password",
                "password": new_password,
                "oldPassword": old_password,
                "userId": BYTAjax.current_user_id,
                "nonce": BYTAjax.nonce
            };

            $.ajax({
                url: BYTAjax.ajaxurl,
                data: dataObj,
                success: function () {
                    $("div.edit_field").hide();
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    console.log(xhr);
                    console.log(ajaxOptions);
                    console.log(thrownError);
                }
            });
        }
    };

}(jQuery));