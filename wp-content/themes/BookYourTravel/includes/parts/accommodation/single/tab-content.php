<?php
/**
 * Accommodation tab content template part
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */
global $tab, $post, $first_display_tab, $layout_class, $bookyourtravel_theme_globals, $entity_obj, $default_location_extra_fields, $bookyourtravel_location_helper, $bookyourtravel_accommodation_helper;

setup_postdata( $post );

$accommodation_obj = new BookYourTravel_Accommodation($post);
$accommodation_id = $post->ID;
$entity_obj = $accommodation_obj;
$accommodation_extra_fields = $bookyourtravel_theme_globals->get_accommodation_extra_fields();
$location_extra_fields = $bookyourtravel_theme_globals->get_location_extra_fields();
$accommodation_location_obj = $accommodation_obj->get_location();
$location_tab_array = $bookyourtravel_theme_globals->get_location_tabs();
$disabled_room_types = $accommodation_obj->get_disabled_room_types();
$accommodation_rent_type_str = $accommodation_obj->get_formatted_rent_type();

$date_from = isset($_GET['from']) && !empty($_GET['from'])  ? date('Y-m-d', strtotime(sanitize_text_field($_GET['from']))) : null;
$date_to = isset($_GET['to']) && !empty($_GET['to']) ? date('Y-m-d', strtotime(sanitize_text_field($_GET['to']))) : null;

if (isset($tab['id']) && (!isset($tab['hide']) || $tab['hide'] != '1')) {
	if ($tab['id'] == 'description') { ?>
<section id="description" class="tab-content <?php echo $first_display_tab == 'description' ? 'initial' : ''; ?> <?php echo esc_attr($layout_class); ?>" style="display:none">
	<article>
		<?php do_action( 'bookyourtravel_show_single_accommodation_description_before' ); ?>
		<h2><?php the_title(); ?></h2>
		<div class="description">
			<div class="text-wrap">
				<?php 
				if ($bookyourtravel_theme_globals->is_inline_vc_editor() || $bookyourtravel_theme_globals->is_inside_elementor_editor()) {
					echo wpautop($accommodation_obj->get_description());
				} else {
					echo wpautop(do_shortcode($accommodation_obj->get_description())); 					
				}
				?>
			</div>
		</div>
		<?php BookYourTravel_Theme_Of_Custom::the_tab_extra_fields('accommodation_extra_fields', $accommodation_extra_fields, 'description', $accommodation_obj, 'text-wrap', true); ?>
		<?php do_action( 'bookyourtravel_show_single_accommodation_description_after' ); ?>
	</article>
</section>
<?php } elseif ($tab['id'] == 'availability') { ?>
<section id="availability" class="tab-content <?php echo $first_display_tab == 'availability' ? 'initial' : ''; ?> <?php echo esc_attr($layout_class); ?>" style="display:none">
	<article>
		<?php do_action( 'bookyourtravel_show_single_accommodation_availability_before' ); ?>
		<h2><?php echo esc_html($tab['label']); ?></h2>
		<?php BookYourTravel_Theme_Of_Custom::the_field_inner("text-wrap", "", "", $accommodation_obj->get_custom_field('availability_text'), '', false, true); ?>
		<?php if (!$disabled_room_types) {
			$room_types = [];
			$room_type_ids = $accommodation_obj->get_room_types();
			if ($room_type_ids && count($room_type_ids) > 0) {
		?>
		<ul class="room-types">
		<?php
				for ( $z = 0; $z < count($room_type_ids); $z++ ) {
					$room_type_id = $room_type_ids[$z];
					$room_type_obj = new BookYourTravel_Room_Type(intval($room_type_id));
					$room_type_price = $bookyourtravel_accommodation_helper->get_min_future_price($accommodation_id, $room_type_id, $date_from, $date_to, true);

					if (!isset($room_types[$room_type_price])) {
						$room_types[$room_type_price] = array();
					}
					$room_types[$room_type_price][] = $room_type_obj;
				}

				ksort($room_types);

				foreach ($room_types as $room_type_price => $room_type_obj_array) {
					foreach ($room_type_obj_array as $room_type_obj) {
						$min_adult_count = $room_type_obj->get_min_adult_count();
						$min_child_count = $room_type_obj->get_min_child_count();
						$max_adult_count = $room_type_obj->get_max_adult_count();
						$max_child_count = $room_type_obj->get_max_child_count();

						$room_type_id = $room_type_obj->get_id();
				?>
					<li id="room_type_<?php echo esc_attr($room_type_id); ?>">
						<figure class="left">					
						<?php if ($room_type_obj->get_main_image('medium')) { ?>
							<img src="<?php echo esc_url($room_type_obj->get_main_image('medium')) ?>" alt="<?php echo esc_attr($room_type_obj->get_title()) ?>" /><a href="<?php echo esc_url($room_type_obj->get_main_image('full')); ?>" class="image-overlay" rel="prettyPhoto[gallery<?php echo esc_attr($room_type_id); ?>]"></a>
						<?php } ?>
						<?php
							$room_type_images = $room_type_obj->get_images();
							if ($room_type_images && count($room_type_images) > 0) {
								for ( $i = 0; $i < count($room_type_images); $i++ ) {
									$image = $room_type_images[$i];
									$image_meta_id = $image['image'];
									$image_src = wp_get_attachment_image_src($image_meta_id, 'full');
									$image_src = is_array($image_src) && count($image_src) > 0 ? $image_src[0] : '';
									if (!empty($image_src)) {
						?>
						<a href="<?php echo esc_url($image_src); ?>" rel="prettyPhoto[gallery<?php echo esc_attr($room_type_id); ?>]"></a>
						<?php 		}
								}
							}?>
						</figure>
						<div class="meta room_type">
							<h3><?php echo esc_html($room_type_obj->get_title()); ?></h3>
							<?php BookYourTravel_Theme_Of_Custom::the_field_inner('text-wrap room_meta', '', '', $room_type_obj->get_custom_field('meta'), '', true, true); ?>
							<?php BookYourTravel_Theme_Controls::the_link_button("#", "more-info more-info-accommodation", "", esc_html__('+ more info', 'bookyourtravel')); ?>
						</div>
						<div class="room-information">
							<div>
								<span class="first"><?php esc_html_e('Max:', 'bookyourtravel'); ?></span>
								<span class="second">
									<?php for ( $j = 0; $j < $max_adult_count; $j++ ) { ?>
									<i class="material-icons">&#xE7FD;</i>
									<?php } ?>
									<?php for ( $j = 0; $j < $max_child_count; $j++ ) { ?>
									<i class="material-icons material-icons-small">&#xE7FD;</i>
									<?php } ?>
								</span>
							</div>
							<input type="hidden" class="max_adult_count" value="<?php echo esc_attr($max_adult_count); ?>" />
							<input type="hidden" class="max_child_count" value="<?php echo esc_attr($max_child_count); ?>" />
							<input type="hidden" class="min_adult_count" value="<?php echo esc_attr($min_adult_count); ?>" />
							<input type="hidden" class="min_child_count" value="<?php echo esc_attr($min_child_count); ?>" />

							<?php $force_disable_calendar = $accommodation_obj->get_force_disable_calendar(); ?>

							<?php if ($room_type_price > 0) { ?>
								<?php BookYourTravel_Theme_Controls::the_entity_price($room_type_price, esc_html__('From', 'bookyourtravel')); ?>
								<?php if (!$force_disable_calendar) { ?>
									<?php BookYourTravel_Theme_Controls::the_link_button("#", "gradient-button book-accommodation-select-dates", "book-accommodation-$room_type_id", esc_html__('Select dates', 'bookyourtravel')); ?>
								<?php } ?>
							<?php } ?>
						</div>
						<div class="more-information">
							<?php BookYourTravel_Theme_Of_Custom::the_field_inner('text-wrap room_facitilies', '', esc_html__('Room facilities:', 'bookyourtravel'), $room_type_obj->get_facilities_string(), '', true, true); ?>
							<?php BookYourTravel_Theme_Of_Custom::the_field_inner('text-wrap bed_size', '', esc_html__('Bed size:', 'bookyourtravel'), $room_type_obj->get_custom_field('bed_size'), '', true, true); ?>
							<?php BookYourTravel_Theme_Of_Custom::the_field_inner('text-wrap room_size', '', esc_html__('Room size:', 'bookyourtravel'), $room_type_obj->get_custom_field('room_size'), '', true, true); ?>
							<?php
							if ($bookyourtravel_theme_globals->is_inline_vc_editor() || $bookyourtravel_theme_globals->is_inside_elementor_editor()) {
								echo do_shortcode( $room_type_obj->post->post_content );
							} else {
								echo $room_type_obj->get_content(); 
							}
							?>
						</div>
						<div class="booking_form_controls" style="display:none"></div>
					</li>
				<?php
					}
				}

				wp_reset_postdata();
			}
		?>
		</ul>
		<?php
		} ?>
		<?php
		$accommodation_calendar_style = "";
		if (!$disabled_room_types) {
			$accommodation_calendar_style = "display:none;";
		}
		?>
		<div class="accommodation_calendar" style="<?php echo esc_attr($accommodation_calendar_style); ?>">
			<?php get_template_part('includes/parts/booking/form', 'calendar'); ?>
		</div>
		<div class="no-availability" style="display:none">
			<?php BookYourTravel_Theme_Of_Custom::the_field_inner('', '', '', esc_html__('We are sorry, this accommodation is not available to book at the moment', 'bookyourtravel'), '', true, true); ?>
		</div>
		<?php BookYourTravel_Theme_Of_Custom::the_tab_extra_fields('accommodation_extra_fields', $accommodation_extra_fields, 'availability', $accommodation_obj, 'text-wrap', true); ?>
		<?php do_action( 'bookyourtravel_show_single_accommodation_availability_after' ); ?>
	</article>
</section>
<?php } elseif ($tab['id'] == 'facilities') { ?>
<section id="facilities" class="tab-content <?php echo $first_display_tab == 'facilities' ? 'initial' : ''; ?> <?php echo esc_attr($layout_class); ?>" style="display:none">
	<article>
		<?php do_action( 'bookyourtravel_show_single_accommodation_facilites_before' ); ?>
		<?php
		$facilities = $accommodation_obj->get_facilities();

		if ($facilities && count($facilities) > 0) { ?>
		<div class="text-wrap facilities">
			<h2><?php echo esc_html($tab['label']); ?></h2>
			<ul class="three-col">
			<?php
			for( $i = 0; $i < count($facilities); $i++) {
				$accommodation_facility = $facilities[$i];
				echo '<li>' . $accommodation_facility->name . '</li>';
			} ?>
			</ul>
		</div>
		<?php } // endif (!empty($accommodation_facilities)) ?>
		<?php BookYourTravel_Theme_Of_Custom::the_tab_extra_fields('accommodation_extra_fields', $accommodation_extra_fields, $tab['id'], $accommodation_obj, 'text-wrap', true); ?>
	</article>
</section>
<?php } elseif ($tab['id'] == 'location') { 
	$accommodation_longitude = $accommodation_obj->get_custom_field('longitude');
	$accommodation_latitude = $accommodation_obj->get_custom_field('latitude');
	if ($accommodation_longitude && $accommodation_latitude) {
?>
<section id="location" class="tab-content <?php echo $first_display_tab == 'location' ? 'initial' : ''; ?> <?php echo esc_attr($layout_class); ?>" style="display:none">
	<article>
		<?php
		$google_maps_key = $bookyourtravel_theme_globals->get_google_maps_key();
		$allowed_tags = BookYourTravel_Theme_Utils::get_allowed_content_tags_array();
		if (!empty($google_maps_key)) {?>
		<!--map-->
		<div class="gmap" id="map_canvas"></div>
		<!--//map-->
		<?php
		} else {?>
		<p><?php echo wp_kses(__('Before using google maps you must go to <a href="https://developers.google.com/maps/documentation/javascript/get-api-key">Google maps api console</a> and get an api key. After you do, please proceed to Appearance -> Theme options -> Configuration settings and enter your key in the field labeled "Google maps api key"', 'bookyourtravel'), $allowed_tags); ?></p>
		<?php } ?>
		<?php BookYourTravel_Theme_Of_Custom::the_tab_extra_fields('accommodation_extra_fields', $accommodation_extra_fields, 'location', $accommodation_obj); ?>
	</article>
</section>
<?php 
	}
} elseif ($tab['id'] == 'reviews') { ?>
<section id="reviews" class="tab-content <?php echo $first_display_tab == 'review' ? 'initial' : ''; ?> <?php echo esc_attr($layout_class); ?>" style="display:none">
	<?php
	$entity_obj = $accommodation_obj;
	get_template_part('includes/parts/review/review', 'item');
	BookYourTravel_Theme_Of_Custom::the_tab_extra_fields('accommodation_extra_fields', $accommodation_extra_fields, 'reviews', $accommodation_obj);
	?>
</section>
<?php } elseif ($tab['id'] == 'things-to-do') { ?>
<section id="things-to-do" class="tab-content <?php echo $first_display_tab == 'things-to-do' ? 'initial' : ''; ?> <?php echo esc_attr($layout_class); ?>" style="display:none">
	<article>
		<?php if ($accommodation_location_obj != null) { ?>
		<h2><?php echo esc_html($accommodation_location_obj->get_title()); ?></h2>
		<?php if ( has_post_thumbnail($accommodation_location_obj->id) ) { ?>
		<figure class="left_pic">
			<?php
			$thumbnail_id = get_post_thumbnail_id($accommodation_location_obj->get_id());
			$attachment = get_post($thumbnail_id);
			if ($attachment) {
				$image_title = $attachment->post_title; //The Title
				echo get_the_post_thumbnail($accommodation_location_obj->get_id(), "thumbnail", array('title' => $image_title));
			}
			?>
		</figure>
		<?php } ?>
		<div class="text-wrap">
			<?php 
				if ($bookyourtravel_theme_globals->is_inline_vc_editor() || $bookyourtravel_theme_globals->is_inside_elementor_editor()) {
					echo wpautop($accommodation_location_obj->get_description());
				} else {
					echo wpautop(do_shortcode($accommodation_location_obj->get_description())); 
				}
			?>
			
		</div>
		<?php
		foreach ($location_extra_fields as $location_field) {
			$default_field = BookYourTravel_Theme_Utils::custom_array_search($default_location_extra_fields, 'id', $location_field['id']);
			$default_show_in_referenced = count($default_field) > 0 && isset($default_field[0]['show_in_referenced']) ? $default_field[0]['show_in_referenced'] : 0;
			$show_in_referenced = isset($location_field['show_in_referenced']) ? $location_field['show_in_referenced'] : false; //$default_show_in_referenced;
			$hide_in_frontend = isset($location_field['hide_front']) ? $location_field['hide_front'] : false;
			if ($show_in_referenced && !$hide_in_frontend) {
				BookYourTravel_Theme_Of_Custom::the_field('location_extra_fields', $location_field, $accommodation_location_obj, '', true);
			}
		}
		?>
		<?php } ?>
		<?php
		BookYourTravel_Theme_Of_Custom::the_tab_extra_fields('accommodation_extra_fields', $accommodation_extra_fields, 'things-to-do', $accommodation_obj);
		?>
		<?php if ($accommodation_location_obj != null) { ?>
		<hr />
		<?php
		BookYourTravel_Theme_Controls::the_link_button(get_permalink($accommodation_location_obj->get_id()), "gradient-button right", "", esc_html__('Read more', 'bookyourtravel'));
		?>
		<?php } ?>
	</article>
</section>
<?php
	} else {
?>
<section id="<?php echo esc_attr($tab['id']); ?>" class="tab-content <?php echo $first_display_tab == $tab['id'] ? 'initial' : ''; ?> <?php echo esc_attr($layout_class); ?>" style="display:none">
	<article>
		<?php do_action( 'bookyourtravel_show_single_accommodation_' . $tab['id'] . '_before' ); ?>
		<?php BookYourTravel_Theme_Of_Custom::the_tab_extra_fields('accommodation_extra_fields', $accommodation_extra_fields, $tab['id'], $accommodation_obj, 'text-wrap', true ); ?>
		<?php do_action( 'bookyourtravel_show_single_accommodation_' . $tab['id'] . '_after' ); ?>
	</article>
</section>
<?php
	}
}