<?php
/**
 * Location tab content template part
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */
global $post, $tab, $location_obj, $location_extra_fields, $first_display_tab, $layout_class, $bookyourtravel_accommodation_helper, $bookyourtravel_theme_globals;
$location_id = $location_obj->get_id();

$enable_accommodations = $bookyourtravel_theme_globals->enable_accommodations();
$enable_car_rentals = $bookyourtravel_theme_globals->enable_car_rentals();
$enable_cruises = $bookyourtravel_theme_globals->enable_cruises();
$enable_tours = $bookyourtravel_theme_globals->enable_tours();

setup_postdata( $post );

if (isset($tab['id']) && (!isset($tab['hide']) || $tab['hide'] != '1')) {
	if ($tab['id'] == 'general_info') { ?>
		<!--General information-->
		<section id="general_info" class="tab-content <?php echo $first_display_tab == 'general_info' ? 'initial' : ''; ?> <?php echo esc_attr($layout_class); ?>" style="display:none">
			<article>
				<h2><?php the_title(); ?></h2>
				<div class="description">
					<div class="text-wrap">
						<?php 
						if ($bookyourtravel_theme_globals->is_inline_vc_editor() || $bookyourtravel_theme_globals->is_inside_elementor_editor()) {
							echo wpautop($location_obj->get_description()); 
						} else {
							echo wpautop(do_shortcode( $location_obj->get_description()));
						}
						?>													
					</div>
				</div>
				<table>
					<?php BookYourTravel_Theme_Of_Custom::the_tab_extra_fields('location_extra_fields', $location_extra_fields, 'general_info', $location_obj, '', false, true); ?>
				</table>	
			</article>
		</section>
		<!--//General information-->
<?php	
	} else if ($tab['id'] == 'accommodations') {
		if ($enable_accommodations) {
			global $accommodation_item_args, $accommodation_list_args;
			
			$accommodation_list_args = array('parent_location_id' => $location_id, 'sort_by' => 'post_title', 'sort_order' => 'ASC', 'posts_per_page' => -1,
				'posts_per_row' => 1, 'is_list_page' => false, 'display_mode' => 'card');
			
			$accommodation_item_args = array('hide_title' => false, 'hide_image' => false, 'hide_description' => false, 'hide_actions' => false, 'hide_stars' => false,
				'hide_rating' => false,	'hide_address' => false, 'hide_price' => false);
			?>
		<section id="accommodations" class="tab-content <?php echo $first_display_tab == 'accommodations' ? 'initial' : ''; ?> <?php echo esc_attr($layout_class); ?>" style="display:none">
			<?php
			get_template_part('includes/parts/accommodation/accommodation', 'list');		
			?>
		</section>
	<?php } 
	} else if ($tab['id'] == 'car_rentals') {
		if ($enable_car_rentals) {
			global $car_rental_item_args, $car_rental_list_args;
			
			$car_rental_list_args = array('parent_location_id' => $location_id, 'sort_by' => 'post_title', 'sort_order' => 'ASC', 'posts_per_page' => -1,
				'posts_per_row' => 1, 'is_list_page' => false, 'display_mode' => 'card');
			
			$car_rental_item_args = array('hide_title' => false, 'hide_image' => false, 'hide_description' => false, 'hide_actions' => false, 'hide_stars' => false,
				'hide_rating' => false,	'hide_address' => false, 'hide_price' => false);
			?>
		<section id="car_rentals" class="tab-content <?php echo $first_display_tab == 'car_rentals' ? 'initial' : ''; ?> <?php echo esc_attr($layout_class); ?>" style="display:none">
			<?php
			get_template_part('includes/parts/car_rental/car_rental', 'list');		
			?>
		</section>
	<?php }
	} else if ($tab['id'] == 'cruises') {
		if ($enable_cruises) {
			global $cruise_item_args, $cruise_list_args;
			
			$cruise_list_args = array('parent_location_id' => $location_id, 'sort_by' => 'post_title', 'sort_order' => 'ASC', 'posts_per_page' => -1,
				'posts_per_row' => 1, 'is_list_page' => false, 'display_mode' => 'card');
			
			$cruise_item_args = array('hide_title' => false, 'hide_image' => false, 'hide_description' => false, 'hide_actions' => false, 'hide_stars' => false,
				'hide_rating' => false,	'hide_address' => false, 'hide_price' => false);
			?>
		<section id="cruises" class="tab-content <?php echo $first_display_tab == 'cruises' ? 'initial' : ''; ?> <?php echo esc_attr($layout_class); ?>" style="display:none">
			<?php
			get_template_part('includes/parts/cruise/cruise', 'list');		
			?>
		</section>
	<?php }	
	} else if ($tab['id'] == 'tours') {
		if ($enable_tours) {
			global $tour_item_args, $tour_list_args;
			
			$tour_list_args = array('parent_location_id' => $location_id, 'sort_by' => 'post_title', 'sort_order' => 'ASC', 'posts_per_page' => -1,
				'posts_per_row' => 1, 'is_list_page' => false, 'display_mode' => 'card');
			
			$tour_item_args = array('hide_title' => false, 'hide_image' => false, 'hide_description' => false, 'hide_actions' => false, 'hide_stars' => false,
				'hide_rating' => false,	'hide_address' => false, 'hide_price' => false);
			?>
		<section id="tours" class="tab-content <?php echo $first_display_tab == 'tours' ? 'initial' : ''; ?> <?php echo esc_attr($layout_class); ?>" style="display:none">
			<?php
			get_template_part('includes/parts/tour/tour', 'list');		
			?>
		</section>
	<?php }		
	} else {
	?>
		<section id="<?php echo esc_attr($tab['id']); ?>" class="tab-content <?php echo $first_display_tab == $tab['id'] ? 'initial' : ''; ?> <?php echo esc_attr($layout_class); ?>" style="display:none">
			<article>
				<?php do_action( 'bookyourtravel_show_single_location_' . $tab['id'] . '_before' ); ?>	
				<?php BookYourTravel_Theme_Of_Custom::the_tab_extra_fields('location_extra_fields', $location_extra_fields, $tab['id'], $location_obj, '', true ); ?>	
				<?php do_action( 'bookyourtravel_show_single_location_' . $tab['id'] . '_after' ); ?>	
			</article>
		</section>
<?php 	
	}
}