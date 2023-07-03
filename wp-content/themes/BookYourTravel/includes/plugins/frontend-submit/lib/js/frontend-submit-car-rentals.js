/*jslint browser: true*/ /*jslint for:true*/ /*global bookyourtravel_scripts*/ /*global jQuery*/
/*jslint this:true */ /*global window*/ /*global BYTAjax*/ /*global console*/ /*global window.frontend_submit*/

(function ($) {

  $(document).ready(function () {
    frontend_submit_car_rentals.init();
  });

  var frontend_submit_car_rentals = {

    init: function () {
      if ($('.fes-upload-form.fes-form-car_rental #fes_car_rental_use_referral_url').length > 0) {
        if ($('.fes-upload-form.fes-form-car_rental #fes_car_rental_use_referral_url').is(":checked")) {
          $('.referral_url').show();
        } else {
          $('.referral_url').hide();
        }

        $('.fes-upload-form.fes-form-car_rental #fes_car_rental_use_referral_url').on('change', function (e) {
          if (this.checked) {
            $('.referral_url').show();
          } else {
            $('.referral_url').hide();
          }
        });
      }

      if ($('.fes-upload-form.fes-form-car_rental').length > 0) {
        $('.fes-upload-form.fes-form-car_rental').validate({
          submitHandler: function (form) {
            form.submit();
          },
          errorPlacement: window.frontend_submit.errorPlacement,
          highlight: window.frontend_submit.errorHighlight,
          unhighlight: window.frontend_submit.errorUnHighlight
        });
      }

      if ($('.fes-upload-form.fes-form-car_rental_availability').length > 0) {
        $('.fes-upload-form.fes-form-car_rental_availability').validate({
          submitHandler: function (form) {
            form.submit();
          },
          errorPlacement: window.frontend_submit.errorPlacement,
          highlight: window.frontend_submit.errorHighlight,
          unhighlight: window.frontend_submit.errorUnHighlight
        });
      }

      if ($('.fes-upload-form.fes-form-car_rental_booking').length > 0) {
        $('.fes-upload-form.fes-form-car_rental_booking').validate({
          submitHandler: function (form) {
            form.submit();
          },
          errorPlacement: window.frontend_submit.errorPlacement,
          highlight: window.frontend_submit.errorHighlight,
          unhighlight: window.frontend_submit.errorUnHighlight
        });
      }
    },
    initializeFields: function (carRentalId) {

      var dataObj = {
        'action': 'car_rental_get_fields_ajax_request',
        'carRentalId': carRentalId,
        'nonce': $('#_wpnonce').val()
      }

      $.ajax({
        url: BYTAjax.ajaxurl,
        data: dataObj,
        async: false,
        success: function (data) {
          // This outputs the result of the ajax request
          var fields = JSON.parse(data);

          window.carRentalMinBookingDays = fields.min_booking_days;
          window.carRentalMaxBookingDays = fields.max_booking_days;

          window.carRentalIsReservationOnly = fields.is_reservation_only;
        },
        error: function (errorThrown) {
          console.log(errorThrown);
        }
      });
    }
  };

})(jQuery);
