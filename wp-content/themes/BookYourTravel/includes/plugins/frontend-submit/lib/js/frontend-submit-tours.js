/*jslint browser: true*/ /*jslint for:true*/ /*global bookyourtravel_scripts*/ /*global jQuery*/
/*jslint this:true */ /*global window*/ /*global BYTAjax*/ /*global console*/ /*global window.frontend_submit*/

(function ($) {

  $(document).ready(function () {
    frontend_submit_tours.init();
  });

  var frontend_submit_tours = {

    init: function () {
      if ($('.fes-upload-form.fes-form-tour #fes_tour_use_referral_url').length > 0) {
        if ($('.fes-upload-form.fes-form-tour #fes_tour_use_referral_url').is(":checked")) {
          $('.referral_url').show();
        } else {
          $('.referral_url').hide();
        }

        $('.fes-upload-form.fes-form-tour #fes_tour_use_referral_url').on('change', function (e) {
          if (this.checked) {
            $('.referral_url').show();
          } else {
            $('.referral_url').hide();
          }
        });
      }

      if ($('.fes-upload-form.fes-form-tour').length > 0) {
        $('.fes-upload-form.fes-form-tour').validate({
          submitHandler: function (form) {
            form.submit();
          },
          errorPlacement: window.frontend_submit.errorPlacement,
          highlight: window.frontend_submit.errorHighlight,
          unhighlight: window.frontend_submit.errorUnHighlight
        });
      }

      if ($('.fes-upload-form.fes-form-tour_schedule').length > 0) {
        $('.fes-upload-form.fes-form-tour_schedule').validate({
          submitHandler: function (form) {
            form.submit();
          },
          errorPlacement: window.frontend_submit.errorPlacement,
          highlight: window.frontend_submit.errorHighlight,
          unhighlight: window.frontend_submit.errorUnHighlight
        });
      }

      if ($('.fes-upload-form.fes-form-tour_booking').length > 0) {
        $('.fes-upload-form.fes-form-tour_booking').validate({
          submitHandler: function (form) {
            form.submit();
          },
          errorPlacement: window.frontend_submit.errorPlacement,
          highlight: window.frontend_submit.errorHighlight,
          unhighlight: window.frontend_submit.errorUnHighlight
        });
      }

      $('.button-delete-tour-schedule').on('click', function (e) {

        var _wpnonce = ($(this).closest('div').find('#_wpnonce')).val();
        var scheduleId = ($(this).closest('div').find('.delete_tour_schedule_id')).val();

        var dataObj = {
          'action': 'frontend_delete_tour_schedule_ajax_request',
          'schedule_id': scheduleId,
          'nonce': _wpnonce
        }

        $.ajax({
          url: BYTAjax.ajaxurl,
          data: dataObj,
          async: false,
          success: function (data) {
            // This outputs the result of the ajax request
            $('.article_tour_schedule_' + scheduleId).remove();
          },
          error: function (errorThrown) {
            console.log(errorThrown);
          }
        });

        e.preventDefault();
      });

      $('.fes-upload-form.fes-form-tour_schedule select#fes_tour_id').on('change', function (e) {
        var tourId = $(this).val();
        frontend_submit_tours.initializeFields(tourId);

        if (window.tourIsPricePerGroup) {
          $('.per_person').hide();
          $('.per_person').removeClass('required');
        } else {
          $('.per_person').show();
          $('.per_person').addClass('required');
        }

        if (!window.tourTypeIsRepeated) {
          $('.is_repeated').hide();
          $('.is_repeated').removeClass('required');
        } else {
          $('.is_repeated').addClass('required');
          $('.is_repeated').show();
        }
      });

      var tourId = $('.fes-upload-form.fes-form-tour_schedule select#fes_tour_id').val();
      if (tourId > 0) {
        frontend_submit_tours.initializeFields(tourId);

        if (window.tourIsPricePerGroup) {
          $('.per_person').hide();
          $('.per_person').removeClass('required');
        } else {
          $('.per_person').show();
          $('.per_person').addClass('required');
        }

        if (!window.tourTypeIsRepeated) {
          $('.is_repeated').hide();
          $('.is_repeated').removeClass('required');
        } else {
          $('.is_repeated').addClass('required');
          $('.is_repeated').show();
        }
      }
    },
    initializeFields: function (tourId) {

      var dataObj = {
        'action': 'tour_get_fields_ajax_request',
        'tourId': tourId,
        'nonce': $('#_wpnonce').val()
      }

      $.ajax({
        url: BYTAjax.ajaxurl,
        data: dataObj,
        async: false,
        success: function (data) {
          // This outputs the result of the ajax request
          var fields = JSON.parse(data);
          window.tourIsPricePerGroup = fields.is_price_per_group;
          window.tourTypeIsRepeated = fields.type_is_repeated;
          window.tourDurationDays = fields.duration_days;
          window.tourIsReservationOnly = fields.is_reservation_only;
        },
        error: function (errorThrown) {
          console.log(errorThrown);
        }
      });
    }
  };

})(jQuery);
