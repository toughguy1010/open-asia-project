<?php 

?>
<div class="row">
	<div class="text-wrap price_row two-third" style="display:none">
		<h3><?php esc_html_e('Who is checking in?', 'bookyourtravel') ?></h3>
		<p><?php esc_html_e('Please select number of adults and children checking into the cruise using the controls you see below.', 'bookyourtravel') ?></p>

		<div class="row">
			<div class="f-item one-half booking_form_adults_div">
				<label for="booking_form_adults"><?php esc_html_e('Adults', 'bookyourtravel') ?></label>
				<select class="dynamic_control" id="booking_form_adults" name="booking_form_adults"></select>
			</div>
			<div class="f-item one-half booking_form_children_div">
				<label for="booking_form_children"><?php esc_html_e('Children', 'bookyourtravel') ?></label>
				<select class="dynamic_control" id="booking_form_children" name="booking_form_children"></select>
			</div>
		</div>
	</div>
	<div class="text-wrap price_row cabin_row one-third" style="display:none">
		<h3><?php esc_html_e('Cabins', 'bookyourtravel') ?></h3>
		<p><?php esc_html_e('Select the amount of cabins you wish to book', 'bookyourtravel') ?></p>

		<div class="row">
			<div class="f-item full-width booking_form_cabins_div">
				<label for="booking_form_cabins"><?php esc_html_e('Cabins', 'bookyourtravel') ?></label>
				<select class="dynamic_control" id="booking_form_cabins" name="booking_form_cabins"></select>
			</div>
		</div>
	</div>
</div>