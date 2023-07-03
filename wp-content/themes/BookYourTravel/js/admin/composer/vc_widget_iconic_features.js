/*jslint browser: true*/ /*global jQuery*/ /*jslint this:true */ /*global window*/ /*global window.InlineShortcodeView*/

(function ($) {

    "use strict";

	if (window.InlineShortcodeView !== undefined) {
		window.InlineShortcodeView_byt_widget_iconic_features = window.InlineShortcodeView.extend({
			parent_view: false,			
			initialize: function (params) {		
				this.model.on('change:params', this.changeParams, this);
				this.model.on('destroy', this.removeView, this);		

				window.InlineShortcodeViewContainer.__super__.initialize.call( this, params );
				if ( this.model.get( 'parent_id' ) ) {
					this.parent_view = vc.shortcodes.get( this.model.get( 'parent_id' ) ).view;
				}			
			},
			removeView:function () {
				vc.closeActivePanel(this.model);
				this.remove();
			},			
			changeParams: function (model) {

				var params = model.get('params');
				var number_of_features = 6;
				if (typeof(params.number_of_features) !== 'undefined') {
					number_of_features = params.number_of_features;
				}
					
				var $featuresDiv = $( '[data-vc-shortcode-param-name="features"]', this.$content );
				var $editFormLine = $( '.edit_form_line', $featuresDiv );
				
				var featuresArray = [];
				for (var i=0;i < number_of_features; i++) {
					var $titleInput = $( '#input-title' + i, $editFormLine );
					var titleVal = $titleInput.val();
					var $textInput = $( '#input-text' + i, $editFormLine );
					var textVal = $textInput.val();
					var $classInput = $( '#input-class' + i, $editFormLine );
					var iconClassVal = $classInput.val();
					var featuresObj = { title: titleVal, text: textVal, class: iconClassVal };
					featuresArray.push(featuresObj);
				}

				params.features = JSON.stringify(featuresArray);
				vc.builder.update(this.model);
				
			},
			updated: function() {
				vc.edit_element_block_view.render(this.model);
			},
		});
	}
}(jQuery));