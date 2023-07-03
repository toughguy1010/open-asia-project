<?php
/**
 * Tour tab content template part
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */
global $tab, $post, $first_display_tab, $layout_class, $bookyourtravel_theme_globals, $entity_obj, $default_location_extra_fields, $bookyourtravel_location_helper;

setup_postdata( $post );

$tour_obj = new BookYourTravel_Tour($post);
$entity_obj = $tour_obj;
$tour_extra_fields = $bookyourtravel_theme_globals->get_tour_extra_fields();
$location_extra_fields = $bookyourtravel_theme_globals->get_location_extra_fields();
$tour_locations = $tour_obj->get_locations();
$location_tab_array = $bookyourtravel_theme_globals->get_location_tabs();
$tour_map_code = $tour_obj->get_custom_field( 'map_code' );

if (isset($tab['id']) && (!isset($tab['hide']) || $tab['hide'] != '1')) {
	if ($tab['id'] == 'description') { ?>
<section id="description" class="tab-content <?php echo $first_display_tab == 'description' ? 'initial' : ''; ?> <?php echo esc_attr($layout_class); ?>" style="display:none">
	<article>
		<?php do_action( 'bookyourtravel_show_single_tour_description_before' ); ?>	
		<h2><?php the_title(); ?></h2>
		<div class="description">
			<div class="text-wrap">
				<?php
				if ($bookyourtravel_theme_globals->is_inline_vc_editor() || $bookyourtravel_theme_globals->is_inside_elementor_editor()) {
					echo wpautop($tour_obj->get_description()); 
				} else {
					echo wpautop(do_shortcode( $tour_obj->get_description()));
				}
				?>													
			</div>
		</div>
		<?php BookYourTravel_Theme_Of_Custom::the_tab_extra_fields('tour_extra_fields', $tour_extra_fields, 'description', $tour_obj, 'text-wrap', true); ?>
		<?php do_action( 'bookyourtravel_show_single_tour_description_after' ); ?>	
	</article>
</section>
<?php } elseif ($tab['id'] == 'availability') { ?>
<section id="availability" class="tab-content <?php echo $first_display_tab == 'availability' ? 'initial' : ''; ?> <?php echo esc_attr($layout_class); ?>" style="display:none">
	<article>
		<?php do_action( 'bookyourtravel_show_single_tour_availability_before' ); ?>
		<h2><?php echo esc_html($tab['label']); ?></h2>
		<?php BookYourTravel_Theme_Of_Custom::the_field_inner("text-wrap", "", "", $tour_obj->get_custom_field('availability_text'), '', false, true); ?>		

		<?php get_template_part('includes/parts/booking/form', 'calendar'); ?>		
		
		<div class="no-availability" style="display:none">
			<?php BookYourTravel_Theme_Of_Custom::the_field_inner('', '', '', esc_html__('We are sorry, this tour is not available to book at the moment', 'bookyourtravel'), '', true, true); ?>
		</div>
		<?php BookYourTravel_Theme_Of_Custom::the_tab_extra_fields('tour_extra_fields', $tour_extra_fields, 'availability', $tour_obj, 'text-wrap', true); ?>

		<?php do_action( 'bookyourtravel_show_single_tour_availability_after' ); ?>
	</article>
</section>
<?php } elseif ($tab['id'] == 'locations') { ?>
<?php 
if ($tour_locations && count($tour_locations) > 0) { ?>
<section id="locations" class="tab-content <?php echo $first_display_tab == 'locations' ? 'active' : ''; ?> <?php echo esc_attr($layout_class); ?>" style="display:none">
	<div>
		<?php do_action( 'bookyourtravel_show_single_tour_locations_before' ); ?>
		<div class="destinations">
			<div class="row">
			<?php foreach ($tour_locations as $location_id) {
				if ($location_id) {
					$location_id = BookYourTravel_Theme_Utils::get_current_language_post_id(intval($location_id), 'location');
					$location_obj = new BookYourTravel_Location((int)$location_id);
					$location_title = $location_obj->get_title();
					global $post, $location_item_args;
					// var_dump($location_obj->post);
					$post = $location_obj->post;
					setup_postdata( $post );
					if (isset($post)) {
						$location_item_args = array();
						$location_item_args['location_id'] = $post->ID;
						$location_item_args['post'] = $post;
		
						$location_item_args['item_class'] = "full-width";
						
						get_template_part('includes/parts/location/location', 'item');
					}					
				}				
			}
			wp_reset_postdata();
			?>
			</div>
		</div>
		<?php BookYourTravel_Theme_Of_Custom::the_tab_extra_fields('tour_extra_fields', $tour_extra_fields, 'locations', $tour_obj); ?>					
		<?php do_action( 'bookyourtravel_show_single_tour_locations_after' ); ?>
	</div>
</section>
<?php } // endif ($tour_locations) ?>		
<?php } elseif ($tab['id'] == 'map') { ?>
<section id="map" class="tab-content <?php echo $first_display_tab == 'map' ? 'active' : ''; ?> <?php echo esc_attr($layout_class); ?>" style="display:none">
	<article>
		<?php do_action( 'bookyourtravel_show_single_tour_map_before' ); ?>
		<?php 
		$google_maps_key = $bookyourtravel_theme_globals->get_google_maps_key();
		$allowed_tags = BookYourTravel_Theme_Utils::get_allowed_content_tags_array();
		if (!empty($google_maps_key)) { ?>
		<?php if (!empty($tour_map_code)) { ?>
		<!--map-->
		<div class="gmap" id="map_canvas"><?php echo $tour_map_code; ?></div>
		<!--//map-->
		<?php } else { ?>
		<p><?php echo wp_kses(__('You must enter a tour map code in the appropriate field when editing your tour in order for it to be displayed here', 'bookyourtravel'), $allowed_tags); ?></p>
		<?php } ?>
		<?php 
		} else {?>
		<p><?php echo wp_kses(__('Before using google maps you must go to <a href="https://developers.google.com/maps/documentation/javascript/get-api-key">Google maps api console</a> and get an api key. After you do, please proceed to Appearance -> Theme options -> Configuration settings and enter your key in the field labeled "Google maps api key"', 'bookyourtravel'), $allowed_tags); ?></p>
		<?php } ?>
		<?php BookYourTravel_Theme_Of_Custom::the_tab_extra_fields('tour_extra_fields', $tour_extra_fields, 'map', $tour_obj); ?>			
		<?php do_action( 'bookyourtravel_show_single_tour_map_after' ); ?>
	</article>
</section>
<?php } elseif ($tab['id'] == 'reviews') { ?>
<section id="reviews" class="tab-content <?php echo $first_display_tab == 'review' ? 'initial' : ''; ?> <?php echo esc_attr($layout_class); ?>" style="display:none">
	<?php 
	$entity_obj = $tour_obj;
	do_action( 'bookyourtravel_show_single_tour_reviews_before' );
	get_template_part('includes/parts/review/review', 'item'); 
	BookYourTravel_Theme_Of_Custom::the_tab_extra_fields('tour_extra_fields', $tour_extra_fields, 'reviews', $tour_obj); 
	do_action( 'bookyourtravel_show_single_tour_reviews_after' );
	?>
</section>
<?php
	} else {
	?>
<section id="<?php echo esc_attr($tab['id']); ?>" class="tab-content <?php echo $first_display_tab == $tab['id'] ? 'initial' : ''; ?> <?php echo esc_attr($layout_class); ?>" style="display:none">
	<article>
		<?php do_action( 'bookyourtravel_show_single_tour_' . $tab['id'] . '_before' ); ?>	
		<?php BookYourTravel_Theme_Of_Custom::the_tab_extra_fields('tour_extra_fields', $tour_extra_fields, $tab['id'], $tour_obj, 'text-wrap', true ); ?>	
		<?php do_action( 'bookyourtravel_show_single_tour_' . $tab['id'] . '_after' ); ?>	
	</article>
</section>
<?php 	
	}
}