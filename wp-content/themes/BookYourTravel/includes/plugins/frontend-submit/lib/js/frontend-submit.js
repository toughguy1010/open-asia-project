window.frontend_submit = null;

(function ($) {

  // Dropzone Configuration
  Dropzone.autoDiscover = false;

  $(document).ready(function () {
    window.frontend_submit.init();
  });

  String.prototype.filename = function (extension) {
    var s = this.replace(/\\/g, '/');
    s = s.substring(s.lastIndexOf('/') + 1);
    return extension ? s.replace(/[?#].+$/, '') : s.split('.')[0];
  };

  String.prototype.rtrim = function (chr) {
    var rgxtrim = (!chr) ? new RegExp('\\s+$') : new RegExp(chr + '+$');
    return this.replace(rgxtrim, '');
  };

  String.prototype.ltrim = function (chr) {
    var rgxtrim = (!chr) ? new RegExp('^\\s+') : new RegExp('^' + chr + '+');
    return this.replace(rgxtrim, '');
  };

  window.frontend_submit = {

    init: function () {

      $.validator.addMethod("greaterThan",
        function (value, element, params) {
          if (!/Invalid|NaN/.test(new Date(value))) {
            return new Date(value) > new Date($(params).val());
          }
          return isNaN(value) && isNaN($(params).val()) || (Number(value) > Number($(params).val()));
        }, ''
      );

      window.frontend_submit.initializeTabs();
      window.frontend_submit.initializeDropZones();
      window.frontend_submit.initializeDatepickers();

      if ($('.fes-delete-entity').length > 0) {
        $('.fes-delete-entity').on('click', function(e) {

          var ok = confirm(window.feConfirmDelete);

          if (ok) {
            var entityId = $(this).data("id");
            window.frontend_submit.deleteEntity(entityId);
          }
          e.preventDefault();
        });
      }
    },
    deleteEntity: function (entityId) {

      var dataObj = {
        'action': 'frontend_submit_delete_entity',
        'entityId': entityId,
        'nonce': $('#_wpnonce').val()
      }

      $.ajax({
        url: BYTAjax.ajaxurl,
        data: dataObj,
        async: false,
        success: function (data) {
          // This outputs the result of the ajax request
          var ok = JSON.parse(data);
          window.location.href = window.currentUrl;
        },
        error: function (errorThrown) {
          console.log(errorThrown);
        }
      });
    },
    errorPlacement: function (error, element) {
      if (element.tagName === "SELECT") {
        error.insertAfter(element.parent().parent());
      } else {
        error.insertAfter(element.parent());
      }
    },
    errorHighlight: function (element, errorClass, validClass) {
      if (element.tagName === "SELECT") {
        $(element).parent().parent().addClass(errorClass).removeClass(validClass);
      } else {
        $(element).addClass(errorClass).removeClass(validClass);
      }
    },
    errorUnHighlight: function (element, errorClass, validClass) {
      if (element.tagName === "SELECT") {
        $(element).parent().parent().removeClass(errorClass).addClass(validClass);
      } else {
        $(element).removeClass(errorClass).addClass(validClass);
      }
    },
    addParam: function (currentUrl, key, val) {
      var url = new URL(currentUrl);
      url.searchParams.set(key, val);
      return url.href; 
    },
    initializeDropZones: function () {

      Dropzone.autoDiscover = false;
      var entry_id = $('#fes_entry_id').val() !== undefined ? $('#fes_entry_id').val() : 0;

      var ajaxUrl = window.frontend_submit.addParam(BYTAjax.ajaxurl, 'action', 'frontend_featured_upload');
      ajaxUrl = window.frontend_submit.addParam(ajaxUrl, '_wpnonce', $('#_wpnonce').val());
      ajaxUrl = window.frontend_submit.addParam(ajaxUrl, 'entry_id', entry_id);
      ajaxUrl = window.frontend_submit.addParam(ajaxUrl, 'content_type', $('#fes_content_type').val());

      var featuredDropzone = $("#featured-image-uploader").dropzone({
        url: ajaxUrl,
        acceptedFiles: 'image/*',
        success: function (file, response) {
          file.previewElement.classList.add("dz-success");
          file.image_id = response; // push the id for future reference

          response = response.replace("[", "").replace("]", "");

          var imageIds = [];
          imageIds.push(parseInt(response));

          $('#featured-image-id').val(JSON.stringify(imageIds));
        },
        error: function (file, response) {
          file.previewElement.classList.add("dz-error");
        },
        // update the following section is for removing image from library
        addRemoveLinks: true,
        uploadMultiple: false,
        maxFiles: 1,
        removedfile: function (file) {

          var imageId = file.image_id;

          var ajaxUrl = window.frontend_submit.addParam(BYTAjax.ajaxurl, 'action', 'frontend_delete_featured_image');

          $.ajax({
            type: 'POST',
            url: ajaxUrl,
            data: {
              image_id: imageId,
              entry_id: $('#fes_entry_id').val(),
              _wpnonce: $('#_wpnonce').val(),
              content_type: $('#fes_content_type').val()
            },
            success: function (data) {
              var test = data;
            },
            error: function (errorThrown) {
              console.log(errorThrown);
            }
          });
          var _ref;
          return (_ref = file.previewElement) !== null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
        },
        init: function () {
          this.on("addedfile", function () {
            if (this.files.length > 1 && this.files[1] !== null && this.files[1] !== undefined) {
              this.removeFile(this.files[0]);
            }
          });
          this.on("maxfilesexceeded", function (file) {
            this.removeFile(file);
          });

          if (window.featuredImageUri) {

            var featuredFileName = window.featuredImageUri.filename();
            var myDropzone = this;
            var mockFile = {
              size: 12345,
              name: featuredFileName,
              status: Dropzone.ADDED,
              accepted: true,
              url: window.featuredImageUri,
              image_id: window.featuredImageId
            };

            myDropzone.emit("addedfile", mockFile);
            myDropzone.emit("complete", mockFile);
            myDropzone.emit("thumbnail", mockFile, window.featuredImageUri);
            myDropzone.files.push(mockFile);
          }
        }
      });

      var ajaxUrl = window.frontend_submit.addParam(BYTAjax.ajaxurl, 'action', 'frontend_gallery_upload');
      ajaxUrl = window.frontend_submit.addParam(ajaxUrl, '_wpnonce', $('#_wpnonce').val());
      ajaxUrl = window.frontend_submit.addParam(ajaxUrl, 'entry_id', entry_id);
      ajaxUrl = window.frontend_submit.addParam(ajaxUrl, 'content_type', $('#fes_content_type').val());

      $("#gallery-image-uploader").dropzone({
        url: ajaxUrl,
        acceptedFiles: 'image/*',
        parallelUploads: 1,
        success: function (file, response) {

          file.previewElement.classList.add("dz-success");
          file.image_id = response; // push the id for future reference

          response = response.replace("[", "").replace("]", "");

          var imageIds = [];
          if ($('#gallery-image-ids').val() !== undefined && $('#gallery-image-ids').val() !== '') {
            imageIds = JSON.parse($('#gallery-image-ids').val());
          }
          imageIds.push(parseInt(response));

          $('#gallery-image-ids').val(JSON.stringify(imageIds));
        },
        error: function (file, response) {
          file.previewElement.classList.add("dz-error");
        },
        // update the following section is for removing image from library
        addRemoveLinks: true,
        removedfile: function (file) {

          var imageId = file.image_id;

          var ajaxUrl = window.frontend_submit.addParam(BYTAjax.ajaxurl, 'action', 'frontend_delete_gallery_image');

          $.ajax({
            type: 'POST',
            url: ajaxUrl,
            data: {
              image_id: imageId,
              entry_id: $('#fes_entry_id').val(),
              _wpnonce: $('#_wpnonce').val(),
              content_type: $('#fes_content_type').val()
            },
            success: function (data) {
            },
            error: function (errorThrown) {
              console.log(errorThrown);
            }
          });

          var imageIds = $('#gallery-image-ids').val();
          // remove from middle
          imageIds = imageIds.replace(',' + imageId + ',', ',');
          // remove from left
          imageIds = imageIds.ltrim(imageId + ',');
          // remove from right
          imageIds = imageIds.rtrim(',' + imageId);

          if (imageIds == imageId) {
            imageIds = '';
          }

          $('#gallery-image-ids').val(imageIds);

          var _ref;
          return (_ref = file.previewElement) !== null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
        },
        init: function () {
          if (window.galleryImageUris !== null && window.galleryImageUris !== undefined && window.galleryImageUris.length > 0) {

            var myDropzone = this;

            $.each(window.galleryImageUris, function (index, image) {

              if (image.image_uri !== null) {
                var fileName = image.image_uri.filename();
                var mockFile = {
                  size: 12345,
                  name: fileName,
                  status: Dropzone.ADDED,
                  accepted: true,
                  url: image.image_uri,
                  image_id: image.image_id
                };

                myDropzone.emit("addedfile", mockFile);
                myDropzone.emit("complete", mockFile);
                myDropzone.emit("thumbnail", mockFile, image.image_uri);
                myDropzone.files.push(mockFile);
              }
            });
          }
        }
      });

      $(".extra-field-image-uploader").each(function () {
        var $imageInput = $(this).nextAll('input[type="hidden"]')[0];
        var extraFieldId = $imageInput.getAttribute('name');

        var ajaxUrl = window.frontend_submit.addParam(BYTAjax.ajaxurl, 'action', 'frontend_extra_field_image_upload');
        ajaxUrl = window.frontend_submit.addParam(ajaxUrl, '_wpnonce', $('#_wpnonce').val());
        ajaxUrl = window.frontend_submit.addParam(ajaxUrl, 'field_id', extraFieldId);
        ajaxUrl = window.frontend_submit.addParam(ajaxUrl, 'entry_id', entry_id);
        ajaxUrl = window.frontend_submit.addParam(ajaxUrl, 'content_type', $('#fes_content_type').val());

        $(this).dropzone({
          url: ajaxUrl,
          acceptedFiles: 'image/*',
          success: function (file, response) {
            file.previewElement.classList.add("dz-success");
            file.image_id = response; // push the id for future reference

            response = response.replace("[", "").replace("]", "");

            var imageIds = [];
            imageIds.push(parseInt(response));

            $('#fes_' + extraFieldId).val(JSON.stringify(imageIds));
          },
          error: function (file, response) {
            file.previewElement.classList.add("dz-error");
          },
          // update the following section is for removing image from library
          addRemoveLinks: true,
          uploadMultiple: false,
          maxFiles: 1,
          removedfile: function (file) {

            var imageId = file.image_id;

            var ajaxUrl = window.frontend_submit.addParam(BYTAjax.ajaxurl, 'action', 'frontend_delete_extra_field_image');

            $.ajax({
              type: 'POST',
              url: ajaxUrl,
              data: {
                image_id: imageId,
                field_id: extraFieldId,
                entry_id: $('#fes_entry_id').val(),
                _wpnonce: $('#_wpnonce').val(),
                content_type: $('#fes_content_type').val()
              },
              success: function (data) {
                var test = data;
              },
              error: function (errorThrown) {
                console.log(errorThrown);
              }
            });
            var _ref;
            return (_ref = file.previewElement) !== null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
          },
          init: function () {
            this.on("addedfile", function () {
              if (this.files.length > 1 && this.files[1] !== null && this.files[1] !== undefined) {
                this.removeFile(this.files[0]);
              }
            });
            this.on("maxfilesexceeded", function (file) {
              this.removeFile(file);
            });

            if (window['extraField_fes_' + extraFieldId + '_Uri'] && window['extraField_fes_' + extraFieldId + '_Uri'] !== undefined) {

              var imageUri = window['extraField_fes_' + extraFieldId + '_Uri'];
              var imageId = window['extraField_fes_' + extraFieldId + '_Id'];

              var fileName = imageUri.filename();
              var myDropzone = this;
              var mockFile = {
                size: 12345,
                name: fileName,
                status: Dropzone.ADDED,
                accepted: true,
                url: imageUri,
                image_id: imageId
              };

              myDropzone.emit("addedfile", mockFile);
              myDropzone.emit("complete", mockFile);
              myDropzone.emit("thumbnail", mockFile, imageUri);
              myDropzone.files.push(mockFile);
            }
          }
        });

      });
    },
    initializeTabs: function () {

      if ($(".field-tab-content").length > 0) {
        $(".field-tab-content").hide();
        $(".field-tab-content.initial").show();

        var activeIndex = $(".field-tabs li.active").index();
        if (activeIndex === -1) {
          $(".field-tabs li:first").addClass("active");
        }

        var initialTab = $(".field-tabs li:first a").attr("href").replace("#", "");
        $(".field-tab-content").hide();
        $(".field-tab-content." + initialTab).show();

        $(".field-tabs li a").on("click", function (e) {
          $(".field-tabs li").removeClass("active");
          $(this).parent().addClass("active");
          var currentTab = $(this).attr("href").replace("#", "");
          $(".field-tab-content").hide();
          $(".field-tab-content." + currentTab).show();
          e.preventDefault();
        });
      }
    },
    initializeDatepickers: function () {

      if ($('.fes-datepicker-control') !== undefined && $('.fes-datepicker-control').length > 0) {
        $('.fes-datepicker-control').each(function () {

          var $altInput = $(this).parent().nextAll('input[type="hidden"]')[0];
          var extraFieldId = $altInput.getAttribute('name');

          $(this).datepicker({
            dateFormat: window.datepickerDateFormat,
            numberOfMonths: 1,
            minDate: 0,
            showOn: 'button',
            altField: '#fes_' + extraFieldId,
            altFormat: window.datepickerAltFormat,
            buttonImage: window.themePath + '/images/ico/calendar.png',
            buttonImageOnly: true,
            beforeShowDay: function (d) {
              var dUtc = Date.UTC(d.getFullYear(), d.getMonth(), d.getDate());
              var today = new Date();
              var todayUtc = Date.UTC(today.getFullYear(), today.getMonth(), today.getDate());

              if (todayUtc.valueOf() > dUtc)
                return [false, "ui-datepicker-unselectable ui-state-disabled"];
              else
                return [true, "dp-highlight"];
            }
          });

          var that = this;

          $(this).off('click');
          $(this).on('click', function (e) {
            $(that).datepicker('show');
            e.preventDefault();
          });

          $(this).off('focus');
          $(this).on('focus', function (e) {
            $(that).datepicker('show');
            e.preventDefault();
          });

          if (window['datePicker_fes_' + extraFieldId] !== undefined) {
            $(this).datepicker('setDate', window['datePicker_fes_' + extraFieldId]);
          }
        });
      }
    }
  };
})(jQuery);
