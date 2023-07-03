/*jslint browser: true*/ /*jslint for:true*/ /*global bookyourtravel_scripts*/ /*global jQuery*/
/*jslint this:true */ /*global window*/ /*global BYTAjax*/ /*global console*/ /*global window.frontend_submit*/

(function ($) {

  $(document).ready(function () {
    frontend_submit_cruises.init();
  });

  var frontend_submit_cruises = {

    init: function () {
      if ($('.fes-upload-form.fes-form-cruise #fes_cruise_use_referral_url').length > 0) {
        if ($('.fes-upload-form.fes-form-cruise #fes_cruise_use_referral_url').is(":checked")) {
          $('.referral_url').show();
        } else {
          $('.referral_url').hide();
        }

        $('.fes-upload-form.fes-form-cruise #fes_cruise_use_referral_url').on('change', function (e) {
          if (this.checked) {
            $('.referral_url').show();
          } else {
            $('.referral_url').hide();
          }
        });
      }

      if ($('.fes-upload-form.fes-form-cruise').length > 0) {
        $('.fes-upload-form.fes-form-cruise').validate({
          submitHandler: function (form) {
            form.submit();
          },
          errorPlacement: window.frontend_submit.errorPlacement,
          highlight: window.frontend_submit.errorHighlight,
          unhighlight: window.frontend_submit.errorUnHighlight
        });
      }

      if ($('.fes-upload-form.fes-form-cabin_type').length > 0) {
        $('.fes-upload-form.fes-form-cabin_type').validate({
          submitHandler: function (form) {
            form.submit();
          },
          errorPlacement: window.frontend_submit.errorPlacement,
          highlight: window.frontend_submit.errorHighlight,
          unhighlight: window.frontend_submit.errorUnHighlight
        });
      }

      if ($('.fes-upload-form.fes-form-cruise_schedule').length > 0) {
        $('.fes-upload-form.fes-form-cruise_schedule').validate({
          submitHandler: function (form) {
            form.submit();
          },
          errorPlacement: window.frontend_submit.errorPlacement,
          highlight: window.frontend_submit.errorHighlight,
          unhighlight: window.frontend_submit.errorUnHighlight
        });
      }

      if ($('.fes-upload-form.fes-form-cruise_booking').length > 0) {
        $('.fes-upload-form.fes-form-cruise_booking').validate({
          submitHandler: function (form) {
            form.submit();
          },
          errorPlacement: window.frontend_submit.errorPlacement,
          highlight: window.frontend_submit.errorHighlight,
          unhighlight: window.frontend_submit.errorUnHighlight
        });
      }

      if ($('.fes-upload-form.fes-form-cruise #fes_cruise_is_price_per_person').is(":checked")) {
        $('.per_person').show();
      } else {
        $('.per_person').hide();
      }

      $('.fes-upload-form.fes-form-cruise #fes_cruise_is_price_per_person').on('change', function (e) {
        if (this.checked) {
          $('.per_person').show();
        } else {
          $('.per_person').hide();
        }
      });

      $('.button-delete-cruise-schedule').on('click', function (e) {

        var _wpnonce = ($(this).closest('div').find('#_wpnonce')).val();
        var scheduleId = ($(this).closest('div').find('.delete_cruise_schedule_id')).val();

        var dataObj = {
          'action': 'frontend_delete_cruise_schedule_ajax_request',
          'schedule_id': scheduleId,
          'nonce': _wpnonce
        }

        $.ajax({
          url: BYTAjax.ajaxurl,
          data: dataObj,
          async: false,
          success: function (data) {
            // This outputs the result of the ajax request
            $('.article_cruise_schedule_' + scheduleId).remove();
          },
          error: function (errorThrown) {
            console.log(errorThrown);
          }
        });

        e.preventDefault();
      });

      $('.fes-upload-form.fes-form-cruise_schedule select#fes_cruise_id').on('change', function (e) {
        var cruiseId = $(this).val()
        frontend_submit_cruises.initializeFields(cruiseId);

        $('.fes-upload-form.fes-form-cruise_schedule select#fes_cabin_type_id').find('option:gt(0)').remove();

        var cabinTypeOptions = "";

        $.each(window.cruiseCabinTypes, function (index) {
          cabinTypeOptions += '<option value="' + window.cruiseCabinTypes[index].id + '">' + window.cruiseCabinTypes[index].name + '</option>';
        });

        $('.fes-upload-form.fes-form-cruise_schedule select#fes_cabin_type_id').append(cabinTypeOptions);

        $('.cabin_types').addClass('required');
        $('.cabin_types').show();

        if (window.cruiseIsPricePerPerson) {
          $('.per_person').show();
          $('.per_person').addClass('required');
        } else {
          $('.per_person').hide();
          $('.per_person').removeClass('required');
        }

        if (!window.cruiseTypeIsRepeated) {
          $('.is_repeated').hide();
          $('.is_repeated').removeClass('required');
        } else {
          $('.is_repeated').addClass('required');
          $('.is_repeated').show();
        }
      });

      var cruiseId = $('.fes-upload-form.fes-form-cruise_schedule select#fes_cruise_id').val();
      if (cruiseId > 0) {
        frontend_submit_cruises.initializeFields(cruiseId);
        if (window.cruiseIsPricePerPerson) {
          $('.per_person').show();
          $('.per_person').addClass('required');
        } else {
          $('.per_person').hide();
          $('.per_person').removeClass('required');
        }

        if (!window.cruiseTypeIsRepeated) {
          $('.is_repeated').hide();
          $('.is_repeated').removeClass('required');
        } else {
          $('.is_repeated').addClass('required');
          $('.is_repeated').show();
        }
      }

    },
    initializeFields: function (cruiseId) {

      var dataObj = {
        'action': 'cruise_get_fields_ajax_request',
        'cruiseId': cruiseId,
        'nonce': $('#_wpnonce').val()
      }

      $.ajax({
        url: BYTAjax.ajaxurl,
        data: dataObj,
        async: false,
        success: function (data) {
          // This outputs the result of the ajax request
          var fields = JSON.parse(data);
          window.cruiseIsPricePerPerson = fields.is_price_per_person;
          window.cruiseTypeIsRepeated = fields.type_is_repeated;
          window.cruiseDurationDays = fields.duration_days;
          window.cruiseIsReservationOnly = fields.is_reservation_only;
          window.cruiseCabinTypes = fields.cruise_cabin_types;
        },
        error: function (errorThrown) {
          console.log(errorThrown);
        }
      });
    }
  };

})(jQuery);
