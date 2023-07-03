/*jslint browser: true*/ /*jslint for:true*/ /*global bookyourtravel_scripts*/ /*global jQuery*/
/*jslint this:true */ /*global window*/ /*global BYTAjax*/ /*global console*/ /*global window.frontend_submit*/

(function ($) {

  $(document).ready(function () {
    frontend_submit_accommodations.init();
  });

  var frontend_submit_accommodations = {

    init: function () {

      if ($('.fes-upload-form.fes-form-room_type').length > 0) {
        $('.fes-upload-form.fes-form-room_type').validate({
          submitHandler: function (form) {
            form.submit();
          },
          errorPlacement: window.frontend_submit.errorPlacement,
          highlight: window.frontend_submit.errorHighlight,
          unhighlight: window.frontend_submit.errorUnHighlight
        });
      }

      if ($('.fes-upload-form.fes-form-accommodation').length > 0) {
        $('.fes-upload-form.fes-form-accommodation').validate({
          submitHandler: function (form) {
            form.submit();
          },
          errorPlacement: window.frontend_submit.errorPlacement,
          highlight: window.frontend_submit.errorHighlight,
          unhighlight: window.frontend_submit.errorUnHighlight
        });
      }

      if ($('.fes-upload-form.fes-form-accommodation_vacancy').length > 0) {
        $('.fes-upload-form.fes-form-accommodation_vacancy').validate({
          submitHandler: function (form) {
            form.submit();
          },
          errorPlacement: window.frontend_submit.errorPlacement,
          highlight: window.frontend_submit.errorHighlight,
          unhighlight: window.frontend_submit.errorUnHighlight
        });
      }

      if ($('.fes-upload-form.fes-form-accommodation_booking').length > 0) {
        $('.fes-upload-form.fes-form-accommodation_booking').validate({
          submitHandler: function (form) {
            form.submit();
          },
          errorPlacement: window.frontend_submit.errorPlacement,
          highlight: window.frontend_submit.errorHighlight,
          unhighlight: window.frontend_submit.errorUnHighlight
        });
      }

      if ($('.fes-upload-form.fes-form-accommodation #fes_accommodation_is_price_per_person').is(":checked")) {
        $('.per_person').show();
      } else {
        $('.per_person').hide();
      }

      $('.fes-upload-form.fes-form-accommodation #fes_accommodation_is_price_per_person').on('change', function (e) {
        if (this.checked) {
          $('.per_person').show();
        } else {
          $('.per_person').hide();
        }
      });

      $('.button-delete-vacancy').on('click', function (e) {

        var vacancyId = ($(this).closest('div').find('.delete_vacancy_id')).val();

        var dataObj = {
          'action': 'frontend_delete_accommodation_vacancy_ajax_request',
          'vacancy_id': vacancyId,
          'nonce': $('#_wpnonce').val()
        }

        $.ajax({
          url: BYTAjax.ajaxurl,
          data: dataObj,
          async: false,
          success: function (data) {
            // This outputs the result of the ajax request
            $('.article_vacancy_' + vacancyId).remove();
          },
          error: function (errorThrown) {
            console.log(errorThrown);
          }
        });

        e.preventDefault();
      });

      $('.button-delete-booking').on('click', function (e) {

        var bookingId = ($(this).closest('div').find('.delete_booking_id')).val();

        var dataObj = {
          'action': 'frontend_delete_accommodation_booking_ajax_request',
          'booking_id': bookingId,
          'nonce': $('#_wpnonce').val()
        }

        $.ajax({
          url: BYTAjax.ajaxurl,
          data: dataObj,
          async: false,
          success: function (data) {
            // This outputs the result of the ajax request
            $('.article_booking_' + bookingId).remove();
          },
          error: function (errorThrown) {
            console.log(errorThrown);
          }
        });

        e.preventDefault();
      });

      if ($('.fes-upload-form.fes-form-accommodation #fes_accommodation_disabled_room_types').is(":checked")) {
        $('.room_types').hide();
        $('.not_room_types').show();
      } else {
        $('.room_types').show();
        $('.not_room_types').hide();
      }

      $('.fes-upload-form.fes-form-accommodation #fes_accommodation_disabled_room_types').on('change', function (e) {
        if (this.checked) {
          $('.room_types').hide();
          $('.not_room_types').show();
        } else {
          $('.room_types').show();
          $('.not_room_types').hide();
        }
      });

      if ($('.fes-upload-form.fes-form-accommodation #fes_accommodation_use_referral_url').length > 0) {
        if ($('.fes-upload-form.fes-form-accommodation #fes_accommodation_use_referral_url').is(":checked")) {
          $('.referral_url').show();
        } else {
          $('.referral_url').hide();
        }

        $('.fes-upload-form.fes-form-accommodation #fes_accommodation_use_referral_url').on('change', function (e) {
          if (this.checked) {
            $('.referral_url').show();
          } else {
            $('.referral_url').hide();
          }
        });
      }

      window.accommodationId = $('.fes-upload-form.fes-form-accommodation_vacancy select#fes_accommodation_id').val();
      if (window.accommodationId > 0) {

        frontend_submit_accommodations.initializeFields(window.accommodationId);

        if (window.accommodationDisabledRoomTypes) {
          $('.room_types').hide();
          $('.room_types').removeClass('required');
        } else {

          $('.fes-upload-form.fes-form-accommodation_vacancy select#fes_room_type_id').find('option:gt(0)').remove();

          var roomTypeOptions = "";
          $.each(window.accommodationRoomTypes, function (index) {
            roomTypeOptions += '<option value="' + window.accommodationRoomTypes[index].id + '">' + window.accommodationRoomTypes[index].name + '</option>';
          });

          $('.fes-upload-form.fes-form-accommodation_vacancy select#fes_room_type_id').append(roomTypeOptions);

          $('.room_types').addClass('required');
          $('.room_types').show();
        }

        if (window.accommodationRentType == 0) {
          $('.daily_rent').show();
          $('label[for="fes_price_per_day"]').contents()[0].nodeValue = window.pricePerDayLabel;
          $('label[for="fes_price_per_day_child"]').contents()[0].nodeValue = window.pricePerDayChildLabel;

          if (window.accommodationIsPricePerPerson) {
            $('.per_person').show();
            $('.per_person').addClass('required');
          } else {
            $('.per_person').hide();
            $('.per_person').removeClass('required');
          }
        } else {
          $('.daily_rent').hide();

          if (window.accommodationRentType == 1) {
            $('label[for="fes_price_per_day"]').contents()[0].nodeValue = window.pricePerWeekLabel;
            $('label[for="fes_price_per_day_child"]').contents()[0].nodeValue = window.pricePerWeekChildLabel;
          } else {
            $('label[for="fes_price_per_day"]').contents()[0].nodeValue = window.pricePerMonthLabel;
            $('label[for="fes_price_per_day_child"]').contents()[0].nodeValue = window.pricePerMonthChildLabel;
          }

          if (window.accommodationIsPricePerPerson) {
            $('.per_person').show();
            $('.per_person').addClass('required');
          } else {
            $('.per_person').hide();
            $('.per_person').removeClass('required');
          }
        }
      }

      $('.fes-upload-form.fes-form-accommodation_vacancy select#fes_accommodation_id').on('change', function (e) {
        window.accommodationId = $(this).val()
        frontend_submit_accommodations.initializeFields(window.accommodationId);

        if (window.accommodationDisabledRoomTypes) {
          $('.room_types').hide();
          $('.room_types').removeClass('required');
        } else {

          $('.fes-upload-form.fes-form-accommodation_vacancy select#fes_room_type_id').find('option:gt(0)').remove();

          var roomTypeOptions = "";
          $.each(window.accommodationRoomTypes, function (index) {
            roomTypeOptions += '<option value="' + window.accommodationRoomTypes[index].id + '">' + window.accommodationRoomTypes[index].name + '</option>';
          });

          $('.fes-upload-form.fes-form-accommodation_vacancy select#fes_room_type_id').append(roomTypeOptions);

          $('.room_types').addClass('required');
          $('.room_types').show();
        }

        if (window.accommodationRentType == 0) {
          $('.daily_rent').show();
          $('label[for="fes_price_per_day"]').contents()[0].nodeValue = window.pricePerDayLabel;
          $('label[for="fes_price_per_day_child"]').contents()[0].nodeValue = window.pricePerDayChildLabel;

          if (window.accommodationIsPricePerPerson) {
            $('.per_person').show();
            $('.per_person').addClass('required');
          } else {
            $('.per_person').hide();
            $('.per_person').removeClass('required');
          }
        } else {
          $('.daily_rent').hide();

          if (window.accommodationRentType == 1) {
            $('label[for="fes_price_per_day"]').contents()[0].nodeValue = window.pricePerWeekLabel;
            $('label[for="fes_price_per_day_child"]').contents()[0].nodeValue = window.pricePerWeekChildLabel;
          } else {
            $('label[for="fes_price_per_day"]').contents()[0].nodeValue = window.pricePerMonthLabel;
            $('label[for="fes_price_per_day_child"]').contents()[0].nodeValue = window.pricePerMonthChildLabel;
          }

          if (window.accommodationIsPricePerPerson) {
            $('.per_person').show();
            $('.per_person').addClass('required');
          } else {
            $('.per_person').hide();
            $('.per_person').removeClass('required');
          }
        }
      });

      window.accommodationId = $('.fes-upload-form.fes-form-accommodation_booking select#fes_accommodation_id').val();
      if (window.accommodationId > 0) {

        frontend_submit_accommodations.initializeFields(window.accommodationId);

        if (window.accommodationDisabledRoomTypes) {
          $('.room_types').hide();
          $('.room_types').removeClass('required');
        } else {
          $('.fes-upload-form.fes-form-accommodation_vacancy select#fes_room_type_id').find('option:gt(0)').remove();

          var roomTypeOptions = "";
          $.each(window.accommodationRoomTypes, function (index) {
            roomTypeOptions += '<option value="' + window.accommodationRoomTypes[index].id + '">' + window.accommodationRoomTypes[index].name + '</option>';
          });

          $('.fes-upload-form.fes-form-accommodation_vacancy select#fes_room_type_id').append(roomTypeOptions);

          $('.room_types').addClass('required');
          $('.room_types').show();
        }
      }

      $('.fes-upload-form.fes-form-accommodation_booking select#fes_accommodation_id').on('change', function (e) {
        window.accommodationId = $(this).val()
        frontend_submit_accommodations.initializeFields(window.accommodationId);

        if (window.accommodationDisabledRoomTypes) {
          $('.room_types').hide();
          $('.room_types').removeClass('required');
        } else {
          $('.fes-upload-form.fes-form-accommodation_vacancy select#fes_room_type_id').find('option:gt(0)').remove();

          var roomTypeOptions = "";
          $.each(window.accommodationRoomTypes, function (index) {
            roomTypeOptions += '<option value="' + window.accommodationRoomTypes[index].id + '">' + window.accommodationRoomTypes[index].name + '</option>';
          });

          $('.fes-upload-form.fes-form-accommodation_vacancy select#fes_room_type_id').append(roomTypeOptions);

          $('.room_types').addClass('required');
          $('.room_types').show();
        }
      });
    },
    initializeFields: function (accommodationId) {

      var dataObj = {
        'action': 'accommodation_get_fields_ajax_request',
        'accommodationId': accommodationId,
        'nonce': $('#_wpnonce').val()
      }

      $.ajax({
        url: BYTAjax.ajaxurl,
        data: dataObj,
        async: false,
        success: function (data) {
          // This outputs the result of the ajax request
          var fields = JSON.parse(data);

          window.accommodationRentType = fields.rent_type;
          window.accommodationIsReservationOnly = fields.is_reservation_only;
          window.accommodationCheckinWeekday = fields.checkin_week_day;
          window.accommodationCheckoutWeekday = fields.checkout_week_day;
          window.accommodationDisabledRoomTypes = fields.disabled_room_types;
          window.accommodationIsPricePerPerson = fields.is_price_per_person;
          window.accommodationMinDaysStay = fields.min_days_stay;
          window.accommodationMaxDaysStay = fields.max_days_stay;
          window.accommodationChildrenStayFree = fields.children_stay_free;
          window.accommodationMinAdultCount = fields.min_adult_count;
          window.accommodationMaxAdultCount = fields.max_adult_count;
          window.accommodationMinChildCount = fields.min_child_count;
          window.accommodationMaxChildCount = fields.max_child_count;
          window.accommodationRoomTypes = fields.room_types;
        },
        error: function (errorThrown) {
          console.log(errorThrown);
        }
      });
    }
  };

})(jQuery);
