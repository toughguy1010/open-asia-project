/*jslint browser: true*/ /*global jQuery*/ /*jslint this:true */ /*global window*/

(function ($) {

    "use strict";

    window.bookyourtravel_composer_scripts;

    $(document).ready(function () {
        window.bookyourtravel_composer_scripts.init();
    });

    window.bookyourtravel_composer_scripts = {

        init: function () {
			// disallow adding multiple single location elements to visual composer
			$("li").find("[data-element='single_location']").hide();
			$('.post-type-location iframe').on('load', function() {
				$('.post-type-location iframe').contents().find("head")
					.append($(
						"<style type='text/css'>\
							.vc_single_location .vc_control-btn-clone {display:none !important;} \
							.vc_single_location .vc_control-btn-delete {display:none !important;} \
							.vc_add-element-not-empty-button .vc-composer-icon {display:none !important;} \
							.vc_add-element-not-empty-button { background-color: transparent !important; } \
							.vc_add-element-not-empty-button:hover { background-color: transparent !important; } \
							.vc_add-element-not-empty-button { transition: none !important; } \
						</style>"
					));
			});
        }
    };

}(jQuery));

var vcBookYourTravelTourTagsDependencyCallback;
vcBookYourTravelTourTagsDependencyCallback = function () {
	(function ( $, that ) {

			$checboxList = $( '[data-vc-shortcode-param-name="tour_tag_ids"]', that.$content );
			$empty = $( '#tour_tag_ids-empty', $checboxList );
			if ( $empty.length ) {
				$empty.parent().remove();
			}

			var model = that.model.toJSON();

			$.ajax( {
				type: 'POST',
				dataType: 'json',
				url: window.ajaxurl,
				data: {
					action: 'vc_bookyourtravel_get_tour_tag_ids',
					_vcnonce: window.vcAdminNonce,
					tagIds: model.params['tour_tag_ids']
				},
				error: function (xhr, ajaxOptions, thrownError) {
					console.log(xhr.status);
					console.log(thrownError);
				}
			} ).done( function ( data ) {
				if ( 0 < data.length ) {
					$( '.edit_form_line', $checboxList ).prepend( $( data ) );
				}
			});

	}( window.jQuery, this ));
};

var vcBookYourTravelLocationTypesDependencyCallback;
vcBookYourTravelLocationTypesDependencyCallback = function () {
	(function ( $, that ) {

			$typeChecboxList = $( '[data-vc-shortcode-param-name="location_type_ids"]', that.$content );
			$empty = $( '#location_type_ids-empty', $typeChecboxList );
			if ( $empty.length ) {
				$empty.parent().remove();
			}

			var model = that.model.toJSON();

			$.ajax( {
				type: 'POST',
				dataType: 'json',
				url: window.ajaxurl,
				data: {
					action: 'vc_bookyourtravel_get_location_type_ids',
					_vcnonce: window.vcAdminNonce,
					typeIds: model.params['location_type_ids']
				},
				error: function (xhr, ajaxOptions, thrownError) {
					console.log(xhr.status);
					console.log(thrownError);
				}
			} ).done( function ( data ) {
				if ( 0 < data.length ) {
					$( '.edit_form_line', $typeChecboxList ).prepend( $( data ) );
				}
			});

	}( window.jQuery, this ));
};

var vcBookYourTravelLocationTagsDependencyCallback;
vcBookYourTravelLocationTagsDependencyCallback = function () {
	(function ( $, that ) {

			$checboxList = $( '[data-vc-shortcode-param-name="location_tag_ids"]', that.$content );
			$empty = $( '#location_tag_ids-empty', $checboxList );
			if ( $empty.length ) {
				$empty.parent().remove();
			}

			var model = that.model.toJSON();

			$.ajax( {
				type: 'POST',
				dataType: 'json',
				url: window.ajaxurl,
				data: {
					action: 'vc_bookyourtravel_get_location_tag_ids',
					_vcnonce: window.vcAdminNonce,
					tagIds: model.params['location_tag_ids']
				},
				error: function (xhr, ajaxOptions, thrownError) {
					console.log(xhr.status);
					console.log(thrownError);
				}
			} ).done( function ( data ) {
				if ( 0 < data.length ) {
					$( '.edit_form_line', $checboxList ).prepend( $( data ) );
				}
			});

	}( window.jQuery, this ));
};

var vcBookYourTravelCruiseTagsDependencyCallback;
vcBookYourTravelCruiseTagsDependencyCallback = function () {
	(function ( $, that ) {

			$checboxList = $( '[data-vc-shortcode-param-name="cruise_tag_ids"]', that.$content );
			$empty = $( '#cruise_tag_ids-empty', $checboxList );
			if ( $empty.length ) {
				$empty.parent().remove();
			}

			var model = that.model.toJSON();

			$.ajax( {
				type: 'POST',
				dataType: 'json',
				url: window.ajaxurl,
				data: {
					action: 'vc_bookyourtravel_get_cruise_tag_ids',
					_vcnonce: window.vcAdminNonce,
					tagIds: model.params['cruise_tag_ids']
				},
				error: function (xhr, ajaxOptions, thrownError) {
					console.log(xhr.status);
					console.log(thrownError);
				}
			} ).done( function ( data ) {
				if ( 0 < data.length ) {
					$( '.edit_form_line', $checboxList ).prepend( $( data ) );
				}
			});

	}( window.jQuery, this ));
};

var vcBookYourTravelCarRentalTagsDependencyCallback;
vcBookYourTravelCarRentalTagsDependencyCallback = function () {
	(function ( $, that ) {

			$checboxList = $( '[data-vc-shortcode-param-name="car_rental_tag_ids"]', that.$content );
			$empty = $( '#car_rental_tag_ids-empty', $checboxList );
			if ( $empty.length ) {
				$empty.parent().remove();
			}

			var model = that.model.toJSON();

			$.ajax( {
				type: 'POST',
				dataType: 'json',
				url: window.ajaxurl,
				data: {
					action: 'vc_bookyourtravel_get_car_rental_tag_ids',
					_vcnonce: window.vcAdminNonce,
					tagIds: model.params['car_rental_tag_ids']
				},
				error: function (xhr, ajaxOptions, thrownError) {
					console.log(xhr.status);
					console.log(thrownError);
				}
			} ).done( function ( data ) {
				if ( 0 < data.length ) {
					$( '.edit_form_line', $checboxList ).prepend( $( data ) );
				}
			});

	}( window.jQuery, this ));
};

var vcBookYourTravelAccommodationTagsDependencyCallback;
vcBookYourTravelAccommodationTagsDependencyCallback = function () {
	(function ( $, that ) {

			$checboxList = $( '[data-vc-shortcode-param-name="accommodation_tag_ids"]', that.$content );
			$empty = $( '#accommodation_tag_ids-empty', $checboxList );
			if ( $empty.length ) {
				$empty.parent().remove();
			}

			var model = that.model.toJSON();

			$.ajax( {
				type: 'POST',
				dataType: 'json',
				url: window.ajaxurl,
				data: {
					action: 'vc_bookyourtravel_get_accommodation_tag_ids',
					_vcnonce: window.vcAdminNonce,
					tagIds: model.params['accommodation_tag_ids']
				},
				error: function (xhr, ajaxOptions, thrownError) {
					console.log(xhr.status);
					console.log(thrownError);
				}
			} ).done( function ( data ) {
				if ( 0 < data.length ) {
					$( '.edit_form_line', $checboxList ).prepend( $( data ) );
				}
			});

	}( window.jQuery, this ));
};

var vcBookYourTravelPostCategoryDependencyCallback;
vcBookYourTravelPostCategoryDependencyCallback = function () {
	(function ( $, that ) {

			$checboxList = $( '[data-vc-shortcode-param-name="category_ids"]', that.$content );
			$empty = $( '#category_ids-empty', $checboxList );
			if ( $empty.length ) {
				$empty.parent().remove();
			}

			var model = that.model.toJSON();

			$.ajax( {
				type: 'POST',
				dataType: 'json',
				url: window.ajaxurl,
				data: {
					action: 'vc_bookyourtravel_get_category_ids',
					_vcnonce: window.vcAdminNonce,
					categoryIds: model.params['category_ids']
				},
				error: function (xhr, ajaxOptions, thrownError) {
					console.log(xhr.status);
					console.log(thrownError);
				}
			} ).done( function ( data ) {
				if ( 0 < data.length ) {
					$( '.edit_form_line', $checboxList ).prepend( $( data ) );
				}
			});

	}( window.jQuery, this ));
};


var vcBookYourTravelFeaturesDependencyCallback;

vcBookYourTravelFeaturesDependencyCallback = function () {
	(function ( $, that ) {

		$featuresDiv = $( '[data-vc-shortcode-param-name="features"]', that.$content );

		var model = that.model.toJSON();
		var features = model.params['features'];
		if (features !== undefined && features.length > 0) {
		features = features.replace('`]}`', '"}]'); // bug in visual composer leaves behind a ` when preparing attributes...
			features = JSON.parse(features);
		}
		var number_of_features = model.params['number_of_features'];

		if (number_of_features.length > 0) {
			number_of_features = parseInt(number_of_features);
			if (number_of_features > 0) {
				for (var i=0;i < number_of_features; i++) {
					var $fDiv = $('<div />');
					$fDiv.attr('class', 'feature-div');

					var titleVal = '';
					var textVal = '';
					var classVal = '';

					if ( features[i] !== void 0 ) {
						titleVal = features[i].title;
						textVal = features[i].text;
						classVal = features[i].class;
					}

					var $featureH1 = $('<h2 />', { text: 'Feature ' + (i+1) });

					var $featuredP1 = $('<p />');
					var $featuredTitle = ($('<input />', { type: "text", value: titleVal, class: 'input-title', id: 'input-title' + i, name: 'input-title' + i}));
					var $featuredTitleLabel = ($('<label />', { for: 'input-title' + i, text: JSON.parse(vc_byt_iconic.iconicTitleLabel) }));
					$featuredP1.append($featuredTitle, $featuredTitleLabel);

					var $featuredP2 = $('<p />');
					var $featuredText = ($('<input />', { type: "text", value: textVal, class: 'input-text', id: 'input-text' + i, name: 'input-title' + i}));
					var $featuredTextLabel = ($('<label />', { for: 'input-text' + i, text: JSON.parse(vc_byt_iconic.iconicTextLabel) }));
					$featuredP2.append($featuredText, $featuredTextLabel);

					var $featuredP3 = $('<p />');
					var $featuredClass = ($('<input />', { type: "text", value: classVal, class: 'input-class', id: 'input-class' + i, name: 'input-title' + i}));
					var $featuredClassIcon = ($('<span />', { class: 'icon-class icon material-icons' }));
					$featuredClassIcon.html(classVal);
					$featuredP3.append($featuredClass, $featuredClassIcon);

					var $featuredClassLink = $('<a />', { text: JSON.parse(vc_byt_iconic.iconicClassLinkLabel), href: '#', class: 'input-class-link', id: 'input-class-link' + i, name: 'input-title-link' + i});
					$featuredP3.append($featuredClassLink);

					$fDiv.append($featuredP1);
					$fDiv.append($featuredP2);
					$fDiv.append($featuredP3);
					$fDiv.prepend($featureH1);
					$( '.edit_form_line', $featuresDiv ).append($fDiv);

					$featuredClassLink.on('click', function(e) {
						e.preventDefault();
						loadIconicIcons($(this).parent().parent());
					});
				}
			}
		}


		function loadIconicIcons(container) {
			var icons = JSON.parse(vc_byt_iconic.iconicClasses).split(/\r?\n/);
			var $featuresIconicContainer = $('.features-iconic-container');
			if ($featuresIconicContainer.length > 0) {
				$featuresIconicContainer.empty();
				$featuresIconicContainer.remove();
			}

			$featuresIconicContainer = $('<div />', { class: 'features-iconic-container' });
			container.append($featuresIconicContainer);

			processIconicIcons(icons, $featuresIconicContainer);
		}

		function processIconicIcons(array, container) {
			// set this to whatever number of items you can process at once
			var chunk = 100;
			var index = 0;
			function doChunk() {
				var cnt = chunk;
				var iconClass = "";
				var $iconSpan = null;
				var $iconAnchor = null;
				if (container.length > 0) {
					while (cnt && index < array.length) {
						// process array[index] here
						index += 1;
						if (array[index] !== undefined && array[index].length > 0) {
							iconClass = array[index].trim();
							if (iconClass.length > 0) {
								$iconSpan = $("<span/>");
								$iconAnchor = $("<a/>");
								$iconAnchor.attr("class", "widgets_select_icon");
								$iconAnchor.attr("href", "#");
								$iconSpan.attr("class", "icon material-icons");
								$iconSpan.html(iconClass);
								$iconAnchor.append($iconSpan);
								container.append($iconAnchor);
							}
						}

						// rebind every 100 to conserve resources but still allow selection while loading
						if (index % 100 === 0) {
							bindIconicIcons();
						}

						cnt -= 1;
					}

					// rebind one last time when all done
					bindIconicIcons();

					if (index < array.length) {
						// set Timeout for async iteration
						setTimeout(doChunk, 10);
					}
				}
			}
			doChunk();
		}

		function bindIconicIcons() {
			$(".widgets_select_icon").off("click");
			$(".widgets_select_icon").on("click", function (e) {

				$featuresIconicContainer = $(this).parent();
				$featureDiv = $featuresIconicContainer.parent();
				$iconSpan = $('span.icon-class', $featureDiv);
				$iconInput = $('input.input-class', $featureDiv);
				var selectedIcon = $(this).find(".icon").html();

				$iconInput.val(selectedIcon);
				$iconSpan.attr("class", "icon-class icon material-icons");
				$iconSpan.html(selectedIcon);

				$featuresIconicContainer.remove();

				e.preventDefault();
			});
		}

	}( window.jQuery, this ));
};
