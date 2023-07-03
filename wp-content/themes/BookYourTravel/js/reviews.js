/*jslint browser: true*/ /*global jQuery*/ /*jslint long:true */ /*jslint this:true */ /*global window*/ /*global BYTAjax*/

(function ($) {

    "use strict";

    var bookyourtravel_reviews;

    $(document).ready(function () {
        if ($('.review-form-section').length > 0) {
            bookyourtravel_reviews.preInit();
        }
    });

    bookyourtravel_reviews = {

        preInit: function () {
            if ($('.booked_item .review-booking').length > 0) {
                $('.booked_item .review-booking').on('click', function (event) {
                    event.preventDefault();

                    var postType = $(this).closest('.booked_item').data('post-type');
                    window.postType = postType;

                    var postId = $(this).closest('.booked_item').data('post-id');
                    window.postId = postId;

                    $(".review-form-thank-you").hide();

                    bookyourtravel_reviews.loadData(function() {
                        bookyourtravel_reviews.init();
                        bookyourtravel_reviews.showReviewForm();                        
                    });
                });
            } else {
                bookyourtravel_reviews.loadData(bookyourtravel_reviews.init);
            }
        },

        loadData: function(callback) {
            var dataObj = {
                "action": "list_review_fields_request",
                "postType": window.postType,
                "postId": window.postId,
                "nonce": BYTAjax.nonce
            };

            $.ajax({
                url: BYTAjax.ajaxurl,
                data: dataObj,
                success: function (data) {
                    // This outputs the result of the ajax request
                    var fields = JSON.parse(data);
                    window.reviewFields = fields.review_fields;
                    
                    var postTypeLabel = fields.post_type_label;
                    $('.review-form-section .post-type-likes').html($('.review-form-section .post-type-likes').html().replace('%s', postTypeLabel));
                    $('.review-form-section .post-type-dislikes').html($('.review-form-section .post-type-dislikes').html().replace('%s', postTypeLabel));

                    var postTitle = fields.post_title;
                    $('.review-form-section .post-title').html($('.review-form-section .post-title').html().replace('%s', postTitle));

                    $("table.review-fields tbody").find("tr").remove();

                    $.each(window.reviewFields, function (ignore, field) {
                        var tr = $('<tr>');
                        var th = $('<th>');
                       
                        th.html(field.label);
                        tr.append(th);

                        for (var i = 1; i <= 10; i++) {
                            var td = $('<td>');
                            var input = $('<input>');
                            input.attr('type', 'radio')
                            .attr('id', 'reviewField_' + field.id + '_' + i)
                            .attr('name', 'reviewField_' + field.id)
                            .attr('value', i);

                            td.append(input);
                            tr.append(td);
                        }

                        $('table.review-fields tbody').append(tr);
                    });                    

                    callback();     
                }
            });
        },

        init: function () {
            window.reviewsValidationAttempted = false;

            $(".review-" + window.postType).on("click", function (event) {
                $(".inquiry-form-thank-you").hide();
                $(".review-form-thank-you").hide();
                $(".inquiry-form-section").hide();
                bookyourtravel_reviews.showReviewForm();
                event.preventDefault();
            });

            $(".cancel-review").on("click", function (event) {
                bookyourtravel_reviews.hideReviewForm();
                event.preventDefault();
            });

            $(".review-form").validate({
                onkeyup: false,
                ignore: [],
                invalidHandler: function (ignore, validator) {
                    window.reviewsValidationAttempted = true;
                    var errors = validator.numberOfInvalids();
                    var message = "";
                    var multipleErrorMessages = window.formMultipleError.format(errors);
                    if (errors.length > 0) {
                        message = (
                            errors === 1
                            ? window.formSingleError
                            : multipleErrorMessages
                        );
                        $(".review-form-section div.error-summary div p").html(message);
                        $(".review-form-section div.error-summary").show();
                    } else {
                        $(".review-form-section div.error-summary").hide();
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
                        $(element).closest("tr").addClass("error");
                    }
                },
                success: function (error, element) {
                    if ($(element).attr("type") === "checkbox") {
                        $(element).closest("div").removeClass("error");
                    } else if ($(element)[0].tagName === 'SELECT') {
                        $(element).closest(".selector").removeClass("error");
                    } else {
                        $(element).closest("tr").removeClass("error");
                    }
                    error.remove();
                },
                messages: {
                    likes: window.reviewFormLikesError,
                    dislikes: window.reviewFormDislikesError
                },
                submitHandler: function () {
                    bookyourtravel_reviews.processReview();
                }
            });

            $.each(window.reviewFields, function (ignore, field) {
                var $input = $(".review-form-section input[type='radio'][name='reviewField_" + field.id + "']");
                if ($input !== null && $input !== undefined && $input.length > 0) {
                    $input.rules("add", {
                        required: true,
                        messages: {
                            required: "field is required"
                        }
                    });
                }
            });

            if ($(".review-form-section input[name='agree_gdpr']").length > 0) {
                $(".review-form-section input[name='agree_gdpr']").rules("add", {
                    required: true,
                    messages: {
                        required: window.gdprError
                    }
                });
            }

            var $inputs = $(".review-form-section input[type='radio'][name^='reviewField_']");
            $inputs.off("change");
            $inputs.on("change", function (e) {
                if (window.reviewsValidationAttempted) {
                    $(".review-form-form").valid();
                }
                e.preventDefault();
            });
        },
        showReviewForm: function () {
            $("body").addClass("modal-open");
            $(".section-" + window.postType + "-content").hide();
            $(".review-form-section").show();
        },
        hideReviewForm: function () {
            $("body").removeClass("modal-open");
            $(".section-" + window.postType + "-content").show();
            $(".review-form-section").hide();
        },
        processReview: function () {
            var likes = $("#likes").val();
            var dislikes = $("#dislikes").val();

            var dataObj = {
                "action": "review_ajax_request",
                "likes": likes,
                "dislikes": dislikes,
                "userId": window.currentUserId,
                "postId": window.postId,
                "nonce": BYTAjax.nonce
            };

            window.reviewFields.forEach(function (field) {
                dataObj["reviewField_" + field.id] = $("input[type='radio'][name='reviewField_" + field.id + "']:checked").val();
            });

            $.ajax({
                url: BYTAjax.ajaxurl,
                data: dataObj,
                success: function (ignore) {
                    // This outputs the result of the ajax request
                    if ($(".review-" + window.postType).length > 0) {
                        $(".review-" + window.postType).hide(); // hide the button
                    }

                    $(".review-form-thank-you").show(); // show thank you message

                    if ($('.booked_item .review-booking').length > 0) {
                        $('.booked_item .review-booking').hide();
                    }

                    bookyourtravel_reviews.hideReviewForm();
                }
            });
        }
    };

}(jQuery));