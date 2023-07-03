<?php
/**
 * Cruise tab content template part
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */
global $tab, $post, $first_display_tab, $layout_class, $bookyourtravel_theme_globals, $entity_obj, $default_location_extra_fields, $bookyourtravel_location_helper, $bookyourtravel_cruise_helper;

setup_postdata( $post );

$cruise_obj = new BookYourTravel_Cruise($post);
$cruise_id = $cruise_obj->get_id();
$entity_obj = $cruise_obj;
$cruise_extra_fields = $bookyourtravel_theme_globals->get_cruise_extra_fields();
$location_extra_fields = $bookyourtravel_theme_globals->get_location_extra_fields();
$cruise_locations = $cruise_obj->get_locations();
$location_tab_array = $bookyourtravel_theme_globals->get_location_tabs();

$date_from = isset($_GET['from']) && !empty($_GET['from'])  ? date('Y-m-d', strtotime(sanitize_text_field($_GET['from']))) : null;
$date_to = isset($_GET['to']) && !empty($_GET['to']) ? date('Y-m-d', strtotime(sanitize_text_field($_GET['to']))) : null;

if (isset($tab['id']) && (!isset($tab['hide']) || $tab['hide'] != '1')) {
	if ($tab['id'] == 'description') { ?>
<section id="description" class="tab-content <?php echo $first_display_tab == 'description' ? 'initial' : ''; ?> <?php echo esc_attr($layout_class); ?>" style="display:none">
	<article>
		<?php do_action( 'bookyourtravel_show_single_cruise_description_before' ); ?>
		<h2><?php the_title(); ?></h2>
		<div class="description">
			<div class="text-wrap">
				<?php 
				if ($bookyourtravel_theme_globals->is_inline_vc_editor() || $bookyourtravel_theme_globals->is_inside_elementor_editor()) {
					echo wpautop($cruise_obj->get_description()); 
				} else {
					echo wpautop(do_shortcode($cruise_obj->get_description()));
				}
				?>							
			</div>
		</div>
		<?php BookYourTravel_Theme_Of_Custom::the_tab_extra_fields('cruise_extra_fields', $cruise_extra_fields, 'description', $cruise_obj, 'text-wrap', true); ?>
		<?php do_action( 'bookyourtravel_show_single_cruise_description_after' ); ?>
	</article>
</section>
<?php } elseif ($tab['id'] == 'availability') { ?>
<section id="availability" class="tab-content <?php echo $first_display_tab == 'availability' ? 'initial' : ''; ?> <?php echo esc_attr($layout_class); ?>" style="display:none">
	<article>
		<?php do_action( 'bookyourtravel_show_single_cruise_availability_before' ); ?>
		<h2><?php echo esc_html($tab['label']); ?></h2>
		<?php BookYourTravel_Theme_Of_Custom::the_field_inner("text-wrap", "", "", $cruise_obj->get_custom_field('availability_text'), '', false, true); ?>		
		<?php
		
		$cabin_types = [];
		$cabin_type_ids = $cruise_obj->get_cabin_types();
		if ($cabin_type_ids && count($cabin_type_ids) > 0) {
		?>
		<ul class="cabin-types room-types">
		<?php
		
			for ( $z = 0; $z < count($cabin_type_ids); $z++ ) {
				$cabin_type_id = $cabin_type_ids[$z];
				$cabin_type_obj = new BookYourTravel_Cabin_Type(intval($cabin_type_id));
				$cabin_type_price = $bookyourtravel_cruise_helper->get_min_future_price($cruise_id, $cabin_type_id, $date_from, $date_to, true);				

				if (!isset($cabin_types[$cabin_type_price])) {
					$cabin_types[$cabin_type_price] = array();
				}
				$cabin_types[$cabin_type_price][] = $cabin_type_obj;
			}
			
			ksort($cabin_types);

			foreach ($cabin_types as $cabin_type_price => $cabin_type_obj_array) {
				foreach ($cabin_type_obj_array as $cabin_type_obj) {			
					$cabin_type_id = $cabin_type_obj->get_id();
					$min_adult_count = $cabin_type_obj->get_min_adult_count();
					$min_child_count = $cabin_type_obj->get_min_child_count();
					$max_adult_count = $cabin_type_obj->get_max_adult_count();
					$max_child_count = $cabin_type_obj->get_max_child_count();				
			?>
			<li id="cabin_type_<?php echo $cabin_type_id; ?>">
				<figure class="left">			
				<?php if ($cabin_type_obj->get_main_image('medium')) { ?>
				<img src="<?php echo esc_url($cabin_type_obj->get_main_image('medium')) ?>" alt="<?php echo esc_attr($cabin_type_obj->get_title()); ?>" /><a href="<?php echo esc_url($cabin_type_obj->get_main_image('full')); ?>" class="image-overlay" rel="prettyPhoto[gallery<?php echo esc_attr($cabin_type_id); ?>]"></a>
				<?php } ?>				
				<?php
					$cabin_type_images = $cabin_type_obj->get_images();
					if ($cabin_type_images && count($cabin_type_images) > 0) {
						for ( $i = 0; $i < count($cabin_type_images); $i++ ) {
							$image = $cabin_type_images[$i];
							$image_meta_id = $image['image'];
							$image_src = wp_get_attachment_image_src($image_meta_id, 'full');
							$image_src = is_array($image_src) && count($image_src) > 0 ? $image_src[0] : '';
							if (!empty($image_src)) {
				?>
				<a href="<?php echo esc_url($image_src); ?>" rel="prettyPhoto[gallery<?php echo esc_attr($cabin_type_id); ?>]"></a>
				<?php 		}
						}
					}?>
				</figure>
				<div class="meta cabin_type room_type">
					<h3><?php echo esc_html($cabin_type_obj->get_title()); ?></h3>
					<?php BookYourTravel_Theme_Of_Custom::the_field_inner('', '', '', $cabin_type_obj->get_custom_field('meta'), '', true, true); ?>
					<?php BookYourTravel_Theme_Controls::the_link_button("#", "more-info more-info-cruise", "", esc_html__('+ more info', 'bookyourtravel')); ?>
				</div>
				<div class="cabin-information room-information">
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

					<?php $force_disable_calendar = $cruise_obj->get_force_disable_calendar(); ?>

					<?php if ($cabin_type_price > 0) { ?>
					<?php BookYourTravel_Theme_Controls::the_entity_price($cabin_type_price, esc_html__('From', 'bookyourtravel')); ?>
						<?php if (!$force_disable_calendar) { ?>
							<?php BookYourTravel_Theme_Controls::the_link_button("#", "gradient-button book-cruise-select-dates", "book-cruise-$cabin_type_id", esc_html__('Select dates', 'bookyourtravel')); ?>
						<?php } ?>
					<?php } ?>
				</div>
				<div class="more-information">
					<?php BookYourTravel_Theme_Of_Custom::the_field_inner('', '', esc_html__('Cabin facilities:', 'bookyourtravel'), $cabin_type_obj->get_facilities_string(), '', true, true); ?>
					<?php
					if ($bookyourtravel_theme_globals->is_inline_vc_editor() || $bookyourtravel_theme_globals->is_inside_elementor_editor()) {
						echo do_shortcode( $cabin_type_obj->post->post_content );
					} else {
						echo $cabin_type_obj->get_content(); 
					}
					?>
					<?php BookYourTravel_Theme_Of_Custom::the_field_inner('', '', esc_html__('Bed size:', 'bookyourtravel'), $cabin_type_obj->get_custom_field('bed_size'), '', true, true); ?>
					<?php BookYourTravel_Theme_Of_Custom::the_field_inner('', '', esc_html__('Cabin size:', 'bookyourtravel'), $cabin_type_obj->get_custom_field('cabin_size'), '', true, true); ?>
				</div>
				<div class="booking_form_controls" style="display:none"></div>
			</li>	
			<?php	
				}
			}		
		?>
		</ul>
		<?php			
		}
		?>
		<div class="cruise_calendar" style="display:none">
			<?php get_template_part('includes/parts/booking/form', 'calendar'); ?>		
		</div>
		<div class="no-availability" style="display:none">
			<?php BookYourTravel_Theme_Of_Custom::the_field_inner('', '', '', esc_html__('We are sorry, this cruise is not available to book at the moment', 'bookyourtravel'), '', true, true); ?>
		</div>
		<?php BookYourTravel_Theme_Of_Custom::the_tab_extra_fields('cruise_extra_fields', $cruise_extra_fields, 'availability', $cruise_obj, 'text-wrap', true); ?>

		<?php do_action( 'bookyourtravel_show_single_cruise_availability_after' ); ?>
	</article>
</section>
<?php } elseif ($tab['id'] == 'facilities') { ?>
<section id="facilities" class="tab-content <?php echo $first_display_tab == 'facilities' ? 'initial' : ''; ?> <?php echo esc_attr($layout_class); ?>" style="display:none">
	<article>
		<?php do_action( 'bookyourtravel_show_single_cruise_facilites_before' ); ?>
		<?php 
		$facilities = $cruise_obj->get_facilities();
		
		if ($facilities && count($facilities) > 0) { ?>
		<div class="text-wrap facilities">	
			<h2><?php echo esc_html($tab['label']); ?></h2>
			<ul class="three-col">
			<?php
			for( $i = 0; $i < count($facilities); $i++) {
				$cruise_facility = $facilities[$i];
				echo '<li>' . $cruise_facility->name . '</li>';
			} ?>					
			</ul>
		</div>
		<?php } // endif (!empty($cruise_facilities)) ?>	
		<?php BookYourTravel_Theme_Of_Custom::the_tab_extra_fields('cruise_extra_fields', $cruise_extra_fields, $tab['id'], $cruise_obj, 'text-wrap', true); ?>					
		<?php do_action( 'bookyourtravel_show_single_cruise_facilities_after' ); ?>
	</article>		
</section>
<?php } elseif ($tab['id'] == 'locations') { ?>
<?php if ($cruise_locations && count($cruise_locations) > 0) { ?>		
<section id="locations" class="tab-content <?php echo $first_display_tab == 'locations' ? 'initial' : ''; ?> <?php echo esc_attr($layout_class); ?>" style="display:none">
	<div>
		<?php do_action( 'bookyourtravel_show_single_cruise_locations_before' ); ?>
		<div class="destinations">
			<div class="row">		
			<?php foreach ($cruise_locations as $location_id) {
				$location_id = BookYourTravel_Theme_Utils::get_current_language_post_id(intval($location_id), 'location');
				$location_obj = new BookYourTravel_Location((int)$location_id);
				global $post, $location_item_args;
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
			wp_reset_postdata();
			?>
			</div>
		</div>			
		<?php BookYourTravel_Theme_Of_Custom::the_tab_extra_fields('cruise_extra_fields', $cruise_extra_fields, 'locations', $cruise_obj); ?>			
		<?php do_action( 'bookyourtravel_show_single_cruise_locations_after' ); ?>
	</div>
</section>
<?php } ?>
<?php } elseif ($tab['id'] == 'reviews') { ?>
<section id="reviews" class="tab-content <?php echo $first_display_tab == 'review' ? 'initial' : ''; ?> <?php echo esc_attr($layout_class); ?>" style="display:none">
	<?php 
	$entity_obj = $cruise_obj;
	do_action( 'bookyourtravel_show_single_cruise_reviews_before' );
	get_template_part('includes/parts/review/review', 'item'); 
	BookYourTravel_Theme_Of_Custom::the_tab_extra_fields('cruise_extra_fields', $cruise_extra_fields, 'reviews', $cruise_obj); 
	do_action( 'bookyourtravel_show_single_cruise_reviews_after' );
	?>
</section>
<?php	
	} else {
?>
<section id="<?php echo esc_attr($tab['id']); ?>" class="tab-content <?php echo $first_display_tab == $tab['id'] ? 'initial' : ''; ?> <?php echo esc_attr($layout_class); ?>" style="display:none">
	<article>
		<?php do_action( 'bookyourtravel_show_single_cruise_' . $tab['id'] . '_before' ); ?>	
		<?php BookYourTravel_Theme_Of_Custom::the_tab_extra_fields('cruise_extra_fields', $cruise_extra_fields, $tab['id'], $cruise_obj, 'text-wrap', true ); ?>	
		<?php do_action( 'bookyourtravel_show_single_cruise_' . $tab['id'] . '_after' ); ?>	
	</article>
</section>
<?php 	
	}
}