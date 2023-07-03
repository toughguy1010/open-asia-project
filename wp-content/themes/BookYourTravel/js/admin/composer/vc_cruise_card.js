/*jslint browser: true*/ /*global jQuery*/ /*jslint this:true */ /*global window*/ /*global window.InlineShortcodeView*/

(function ($) {

    "use strict";

	if (window.InlineShortcodeView !== undefined) {
		window.InlineShortcodeView_byt_cruise_card = window.InlineShortcodeView.extend({
			initialize: function (params) {
				this.model.on('change:params', this.paramsChanged, this);
				this.model.on('destroy', this.removeView, this);

				window.InlineShortcodeViewContainer.__super__.initialize.call( this, params );
				if ( this.model.get( 'parent_id' ) ) {
					this.parent_view = vc.shortcodes.get( this.model.get( 'parent_id' ) ).view;
				}				
			},
			paramsChanged: function (model) {
				var params = model.get('params');
				vc.builder.update(this.model);
			},
			removeView:function () {
				vc.closeActivePanel(this.model);
				this.remove();
			},			
			updated: function() {
				vc.edit_element_block_view.render(this.model);
			}
		});
	}
}(jQuery));