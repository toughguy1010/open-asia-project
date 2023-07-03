/*jslint long:true */ /*jslint browser: true*/ /*global jQuery*/ /*jslint this:true */ /*global window*/ /*global BYTAjax*/

(function ($) {

    "use strict";

    var bookyourtravel_inquiry;

    $(document).ready(function () {
        bookyourtravel_inquiry.init();
    });

    bookyourtravel_inquiry = {

        init: function () {

            $(".contact-" + window.postType).on("click", function (event) {
                $(".inquiry-form-thank-you").hide();
                $(".review-form-thank-you").hide();
                $(".review-form-section").hide();
                bookyourtravel_inquiry.showInquiryForm();
                event.preventDefault();
            });

            $(".cancel-inquiry").on("click", function (event) {
                bookyourtravel_inquiry.hideInquiryForm();
                event.preventDefault();
            });

            if ($(".inquiry-form").length > 0) {
                $(".inquiry-form").validate({
                    onkeyup: false,
                    ignore: [],
                    invalidHandler: function (ignore, validator) {
                        var errors = validator.numberOfInvalids();
                        if (errors) {
                            var message = (
                                errors === 1
                                ? window.formSingleError
                                : window.formMultipleError.format(errors)
                            );
                            $(".inquiry-form div.error-summary div p").html(message);
                            $(".inquiry-form div.error-summary").show();
                        } else {
                            $(".inquiry-form div.error-summary").hide();
                        }
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
                    },
                    submitHandler: function () {
                        bookyourtravel_inquiry.processInquiry();
                    }
                });

                $.each(window.inquiryFormFields, function (ignore, field) {
                    if (field.hide !== "1" && field.id !== null && field.id.length > 0) {
                        var $input = null;
                        if (field.type === "text" || field.type === "email") {
                            $input = $(".inquiry-form").find("input[name=" + field.id + "]");
                        } else if (field.type === "textarea") {
                            $input = $(".inquiry-form").find("textarea[name=" + field.id + "]");
                        } else if (field.type === "checkbox") {
                            $input = $(".inquiry-form").find("input[name=" + field.id + "]");
                        } else if (field.type === "select") {
                            $input = $(".inquiry-form").find("select[name=" + field.id + "]");
                        }

                        if ($input !== null && $input !== undefined) {
                            if (field.required === "1" || field.required === 1) {
                                $input.rules("add", {
                                    required: true,
                                    messages: {
                                        required: window.inquiryFormRequiredError
                                    }
                                });
                            }
                            if (field.type === "email") {
                                $input.rules("add", {
                                    email: true,
                                    messages: {
                                        required: window.inquiryFormEmailError
                                    }
                                });
                            }
                        }
                    }
                });

                if ($(".inquiry-form input[name='agree_gdpr']").length > 0) {
                    $(".inquiry-form input[name='agree_gdpr']").rules("add", {
                        required: true,
                        messages: {
                            required: window.gdprError
                        }
                    });
                }
            }
        },
        showInquiryForm: function () {
            $("body").addClass("modal-open");
            $(".section-" + window.postType + "-content").hide();
            $(".inquiry-form-section").show();
        },
        hideInquiryForm: function () {
            $("body").removeClass("modal-open");
            $(".section-" + window.postType + "-content").show();
            $(".inquiry-form-section").hide();
        },
        processInquiry: function () {

            var dataObj = {
                "action": "inquiry_ajax_request",
                "userId": window.currentUserId,
                "postId": window.postId,
                "nonce": BYTAjax.nonce,
                "g-recaptcha-response": $("#g-recaptcha-response").val()
            };

            $.each(window.inquiryFormFields, function (ignore, field) {
                if (field.hide !== "1") {
                    dataObj[field.id] = $("#" + field.id).val();
                }
            });

            $.ajax({
                url: BYTAjax.ajaxurl,
                data: dataObj,
                success: function (data) {
                    if (data === "captcha_error") {
                        $("div.error div p").html(window.InvalidCaptchaMessage);
                        $("div.error").show();
                    } else {
                        $("div.error div p").html("");
                        $("div.error").hide();
                        $(".contact-" + window.postType).hide(); // hide the button
                        bookyourtravel_inquiry.hideInquiryForm();
                        $(".inquiry-form-thank-you").show();
                    }
                }
            });
        }
    };

}(jQuery));