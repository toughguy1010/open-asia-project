<?php
/**
 * Car Rental tab content template part
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */
global $bookyourtravel_theme_of_custom, $tab, $post, $first_display_tab, $layout_class, $bookyourtravel_theme_globals, $entity_obj, $default_location_extra_fields, $bookyourtravel_location_helper;

setup_postdata( $post );

$car_rental_obj = new BookYourTravel_Car_Rental($post);
$entity_obj = $car_rental_obj;
$car_rental_extra_fields = $bookyourtravel_theme_globals->get_car_rental_extra_fields();
$location_extra_fields = $bookyourtravel_theme_globals->get_location_extra_fields();
$location_tab_array = $bookyourtravel_theme_globals->get_location_tabs();
$car_rental_locations = $car_rental_obj->get_locations();
$force_disable_calendar = $car_rental_obj->get_force_disable_calendar();

if (isset($tab['id']) && (!isset($tab['hide']) || $tab['hide'] != '1')) {
	if ($tab['id'] == 'description') { ?>
<section id="description" class="tab-content <?php echo $first_display_tab == 'description' ? 'initial' : ''; ?> <?php echo esc_attr($layout_class); ?>" style="display:none">
	<article>
		<?php do_action( 'bookyourtravel_show_single_car_rental_description_before' ); ?>
		<h2><?php the_title(); ?></h2>
		<div class="description">
			<div class="text-wrap">
				<?php 
				if ($bookyourtravel_theme_globals->is_inline_vc_editor() || $bookyourtravel_theme_globals->is_inside_elementor_editor()) {
					echo wpautop($car_rental_obj->get_description());
				} else {
					echo wpautop(do_shortcode( $car_rental_obj->get_description() ));
				}
				?>
			</div>
			<div class="text-wrap">
				<?php BookYourTravel_Theme_Of_Custom::the_field_inner("location", "", esc_html__('Locations', 'bookyourtravel'), $car_rental_obj->get_formatted_locations(), '', false, true); ?>
				<?php BookYourTravel_Theme_Of_Custom::the_field_inner("car_type", "", esc_html__('Car type', 'bookyourtravel'), $car_rental_obj->get_type_name(), '', false, true); ?>
				<?php
				if ($car_rental_extra_fields && count($car_rental_extra_fields) > 0) {
					foreach ($car_rental_extra_fields as $extra_field) {
						$field_id = $extra_field['id'];
						$field_type = $extra_field['type'];
						$tab_id = $extra_field['tab_id'];
						$field_label = $extra_field['label'];
						$field_label = $bookyourtravel_theme_of_custom->get_translated_dynamic_string($bookyourtravel_theme_of_custom->get_option_id_context('car_rental_extra_fields') . ' ' . $field_label, $field_label);						
						$field_is_hidden = isset($extra_field['hide']) ? intval($extra_field['hide']) : 0;
						if ($tab_id == 'description' && !$field_is_hidden) {
							$field_value = $entity_obj->get_custom_field($field_id);
							if ($field_type == 'checkbox') {
								$field_value = $field_value == "1" ? __('Yes', 'bookyourtravel') : __('No', 'bookyourtravel');
							} else if ($field_type == 'select') {
								if ($field_value == 'manual') $field_value = __('Manual transmission', 'bookyourtravel');
								if ($field_value == 'auto') $field_value = __('Auto transmission', 'bookyourtravel');
							}

							BookYourTravel_Theme_Of_Custom::the_field_inner($field_id, "", $field_label, $field_value, '', false, true);
						}
					}
				}
				?>
			</div>
		</div>
		<?php do_action( 'bookyourtravel_show_single_car_rental_description_after' ); ?>
	</article>
</section>
<?php } elseif ($tab['id'] == 'availability') { ?>
<section id="availability" class="tab-content <?php echo $first_display_tab == 'availability' ? 'initial' : ''; ?> <?php echo esc_attr($layout_class); ?>" style="display:none">
	<article>
		<?php do_action( 'bookyourtravel_show_single_car_rental_availability_before' ); ?>
		<h2><?php echo esc_html($tab['label']); ?></h2>
		<?php if (!$force_disable_calendar) { ?>
		<div class="pickup-location">
			<p><strong><?php esc_html_e('Pick-up and drop-off', 'bookyourtravel'); ?></strong></p>
			<div class="row">
				<div class="one-half f-item">
					<label for="book-car_rental-location-from"><?php esc_html_e('Where do you wish to pick the car up from?', 'bookyourtravel'); ?></label>
					<select id="book-car_rental-location-from" class="book-car_rental-location-from">
						<option value=""><?php echo esc_html__('Select pickup location', 'bookyourtravel'); ?></option>
					<?php if ($car_rental_locations && count($car_rental_locations) > 0) { ?>
						<?php foreach ($car_rental_locations as $location_id) { 
							$location_id = BookYourTravel_Theme_Utils::get_current_language_post_id(intval($location_id), 'location');
							$location_obj = new BookYourTravel_Location((int)$location_id);
							$location_title = $location_obj->get_title();
							if ($location_obj->get_status() == "publish") {
						?>
						<option value="<?php echo esc_attr($location_id); ?>"><?php echo $location_title; ?></option>
						<?php }
							} ?>
					<?php } ?>
					</select>
				</div>
				<div class="one-half f-item">				
					<label for="book-car_rental-location-to"><?php esc_html_e('Where do you wish to drop the car off?', 'bookyourtravel'); ?></label>
					<select id="book-car_rental-location-to" class="book-car_rental-location-to">
						<option value=""><?php echo esc_html__('Select drop-off location', 'bookyourtravel'); ?></option>
					<?php if ($car_rental_locations && count($car_rental_locations) > 0) { ?>
						<?php foreach ($car_rental_locations as $location_id) { 
							$location_obj = new BookYourTravel_Location((int)$location_id);
							$location_title = $location_obj->get_title();
							if ($location_obj->get_status() == "publish") {
						?>
						<option value="<?php echo esc_attr($location_id); ?>"><?php echo $location_title; ?></option>
						<?php } 
							}?>
					<?php } ?>
					</select>
				</div>
			</div>
		</div>
		<?php BookYourTravel_Theme_Of_Custom::the_field_inner("text-wrap", "", "", $car_rental_obj->get_custom_field('availability_text'), '', false, true); ?>
		<?php get_template_part('includes/parts/booking/form', 'calendar'); ?>
		<div class="no-availability" style="display:none">
			<?php BookYourTravel_Theme_Of_Custom::the_field_inner('', '', '', esc_html__('We are sorry, this car rental is not available to book at the moment', 'bookyourtravel'), '', true, true); ?>
		</div>
		<?php } ?>
		<?php BookYourTravel_Theme_Of_Custom::the_tab_extra_fields('car_rental_extra_fields', $car_rental_extra_fields, 'availability', $car_rental_obj, 'text-wrap', true); ?>
		<?php do_action( 'bookyourtravel_show_single_car_rental_availability_after' ); ?>
	</article>
</section>
<?php } elseif ($tab['id'] == 'reviews') { ?>
<section id="reviews" class="tab-content <?php echo $first_display_tab == 'review' ? 'initial' : ''; ?> <?php echo esc_attr($layout_class); ?>" style="display:none">
	<?php do_action( 'bookyourtravel_show_single_car_rental_reviews_before' ); ?>
	<?php
	get_template_part('includes/parts/review/review', 'item');
	BookYourTravel_Theme_Of_Custom::the_tab_extra_fields('car_rental_extra_fields', $car_rental_extra_fields, 'reviews', $car_rental_obj);
	?>
	<?php do_action( 'bookyourtravel_show_single_car_rental_reviews_after' ); ?>
</section>
<?php
	} else {
	?>
<section id="<?php echo esc_attr($tab['id']); ?>" class="tab-content <?php echo $first_display_tab == $tab['id'] ? 'initial' : ''; ?> <?php echo esc_attr($layout_class); ?>" style="display:none">
	<article>
		<?php do_action( 'bookyourtravel_show_single_car_rental_' . $tab['id'] . '_before' ); ?>
		<?php BookYourTravel_Theme_Of_Custom::the_tab_extra_fields('car_rental_extra_fields', $car_rental_extra_fields, $tab['id'], $car_rental_obj, 'text-wrap', true ); ?>
		<?php do_action( 'bookyourtravel_show_single_car_rental_' . $tab['id'] . '_after' ); ?>
	</article>
</section>
<?php

	}
}