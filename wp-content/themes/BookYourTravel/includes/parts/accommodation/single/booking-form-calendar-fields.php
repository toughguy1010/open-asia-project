<?php 
	global $entity_obj;
	$disabled_room_types = $entity_obj->get_disabled_room_types();
?>
<?php if (!$disabled_room_types) {?>
<div class="row">
<?php } ?>
<?php if (!$disabled_room_types) {?>
<div class="text-wrap price_row two-third" style="display:none">
<?php } else {?>
<div class="text-wrap price_row" style="display:none">
<?php } ?>
	<h3><?php esc_html_e('Who is checking in?', 'bookyourtravel') ?></h3>
	<p><?php esc_html_e('Please select number of adults and children checking into the accommodation using the controls you see below.', 'bookyourtravel') ?></p>

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
<?php if (!$disabled_room_types) {?>
<div class="text-wrap price_row room_row one-third" style="display:none">
	<h3><?php esc_html_e('Rooms', 'bookyourtravel') ?></h3>
	<p><?php esc_html_e('Select the number of rooms you wish to book', 'bookyourtravel') ?></p>

	<div class="row">
		<div class="f-item full-width booking_form_rooms_div">
			<label for="booking_form_rooms"><?php esc_html_e('Rooms', 'bookyourtravel') ?></label>
			<select class="dynamic_control" id="booking_form_rooms" name="booking_form_rooms"></select>
		</div>
	</div>
</div>
<?php } ?>
<?php if (!$disabled_room_types) {?>
</div>
<?php } ?>