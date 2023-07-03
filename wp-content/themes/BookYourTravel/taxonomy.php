<?php
/**
 * The taxonomy archive template file
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */

get_header();
get_template_part('byt', 'header');
BookYourTravel_Theme_Controls::the_breadcrumbs();
get_sidebar('under-header');

global $bookyourtravel_theme_globals, $bookyourtravel_theme_post_types;

$taxonomy = get_query_var( 'taxonomy' );
$page_sidebar_positioning = $bookyourtravel_theme_globals->get_taxonomy_pages_sidebar_position();
$section_class = BookYourTravel_Theme_Utils::get_page_section_class($page_sidebar_positioning);
$posts_per_row = $bookyourtravel_theme_globals->get_taxonomy_pages_items_per_row();
$hide_item_titles = $bookyourtravel_theme_globals->taxonomy_pages_hide_item_titles();
$hide_item_descriptions = $bookyourtravel_theme_globals->taxonomy_pages_hide_item_descriptions();
$hide_item_images = $bookyourtravel_theme_globals->taxonomy_pages_hide_item_images();
$hide_item_actions = $bookyourtravel_theme_globals->taxonomy_pages_hide_item_actions();
$hide_item_prices = $bookyourtravel_theme_globals->taxonomy_pages_hide_item_prices();
$hide_item_address = $bookyourtravel_theme_globals->taxonomy_pages_hide_item_address();
$hide_item_stars = $bookyourtravel_theme_globals->taxonomy_pages_hide_item_stars();
$hide_item_ratings = $bookyourtravel_theme_globals->taxonomy_pages_hide_item_ratings();

$term_id = get_queried_object()->term_id;
$taxonomy_featured_image_id = $bookyourtravel_theme_post_types->get_taxonomy_image_id($term_id);

if ($taxonomy == 'acc_tag' || $taxonomy == 'accommodation_type' ||
	$taxonomy == 'tour_tag' || $taxonomy == 'tour_type' ||
	$taxonomy == 'cruise_tag' || $taxonomy == 'cruise_type' ||
	$taxonomy == 'tour_duration' || $taxonomy == 'cruise_duration' ||	
	$taxonomy == 'car_rental_tag' || $taxonomy == 'car_type' ||
	$taxonomy == 'location_type' || $taxonomy == 'location_tag' ||
	($taxonomy == 'facility' && ($post->post_type == 'accommodation' || $post->post_type == 'cruise'))) {
?>
		<div class="row">
			<?php
			if ($page_sidebar_positioning == 'both' || $page_sidebar_positioning == 'left')
				get_sidebar('left');
			?>
			<section class="<?php echo esc_attr($section_class) ;?>">
				<?php if ($taxonomy_featured_image_id) { ?>
				<div class="page-featured-image">
					<?php $featured_img_url = wp_get_attachment_image_src($taxonomy_featured_image_id, "byt-featured")[0]; ?>
					<div class="keyvisual" style="background-image:url(<?php echo esc_url($featured_img_url); ?>)"></div>
					<div class="wrap">
						<?php
						the_archive_title( '<h1 class="entry-title">', '</h1>' );
						the_archive_description( '<div class="taxonomy-description">', '</div>' );
						?>
					</div>
				</div>
				<?php } else { ?>
				<header class="page-header">
					<?php
						the_archive_title( '<h1 class="entry-title">', '</h1>' );
						the_archive_description( '<div class="taxonomy-description">', '</div>' );
					?>
				</header><!-- .page-header -->
				<?php } ?>
				<?php if (have_posts()) { 
				$wrapper_class = "deals";
				if ($taxonomy == 'location_tag' || $taxonomy == 'location_type') {
					$wrapper_class = "destinations";
				}
				?>
				<div class="<?php echo esc_attr($wrapper_class); ?>">
					<div class="row">
						<?php
						while (have_posts()) {
							the_post();
							global $post;

							$template_part = '';
							if ($taxonomy == 'acc_tag' || $taxonomy == 'accommodation_type') {
								global $accommodation_item_args;
								if (!isset($accommodation_item_args) || !is_array($accommodation_item_args)) {
									$accommodation_item_args = array();
								}
								$accommodation_item_args['accommodation_id'] = $post->ID;
								$accommodation_item_args['post'] = $post;
								$accommodation_item_args['item_class'] = BookYourTravel_Theme_Utils::get_item_class_by_row_posts($posts_per_row);
								$accommodation_item_args = array_merge($accommodation_item_args, array(
									'hide_title' => $hide_item_titles,
									'hide_image' => $hide_item_images,
									'hide_description' => $hide_item_descriptions,
									'hide_actions' => $hide_item_actions,
									'hide_stars' => $hide_item_stars,
									'hide_rating' => $hide_item_ratings,			
									'hide_address' => $hide_item_address,
									'hide_price' => $hide_item_prices,
								));

								$template_part = 'includes/parts/accommodation/accommodation';
							} else if ($taxonomy == 'tour_tag' || $taxonomy == 'tour_type' || $taxonomy == 'tour_duration') {
								global $tour_item_args;
								if (!isset($tour_item_args) || !is_array($tour_item_args)) {
									$tour_item_args = array();
								}
								$tour_item_args['tour_id'] = $post->ID;
								$tour_item_args['post'] = $post;
								$tour_item_args['item_class'] = BookYourTravel_Theme_Utils::get_item_class_by_row_posts($posts_per_row);
								$tour_item_args = array_merge($tour_item_args, array(
									'hide_title' => $hide_item_titles,
									'hide_image' => $hide_item_images,
									'hide_description' => $hide_item_descriptions,
									'hide_actions' => $hide_item_actions,
									'hide_stars' => $hide_item_stars,
									'hide_rating' => $hide_item_ratings,			
									'hide_address' => $hide_item_address,
									'hide_price' => $hide_item_prices,
								));

								$template_part = 'includes/parts/tour/tour';
							} else if ($taxonomy == 'cruise_tag' || $taxonomy == 'cruise_type' || $taxonomy == 'cruise_duration') {
								global $cruise_item_args;
								if (!isset($cruise_item_args) || !is_array($cruise_item_args)) {
									$cruise_item_args = array();
								}
								$cruise_item_args['cruise_id'] = $post->ID;
								$cruise_item_args['post'] = $post;
								$cruise_item_args['item_class'] = BookYourTravel_Theme_Utils::get_item_class_by_row_posts($posts_per_row);
								$cruise_item_args = array_merge($cruise_item_args, array(
									'hide_title' => $hide_item_titles,
									'hide_image' => $hide_item_images,
									'hide_description' => $hide_item_descriptions,
									'hide_actions' => $hide_item_actions,
									'hide_stars' => $hide_item_stars,
									'hide_rating' => $hide_item_ratings,			
									'hide_address' => $hide_item_address,
									'hide_price' => $hide_item_prices,
								));

								$template_part = 'includes/parts/cruise/cruise';
							} else if ($taxonomy == 'car_rental_tag' || $taxonomy == 'car_type') {
								global $car_rental_item_args;
								if (!isset($car_rental_item_args) || !is_array($car_rental_item_args)) {
									$car_rental_item_args = array();
								}
								$car_rental_item_args['car_rental_id'] = $post->ID;
								$car_rental_item_args['post'] = $post;
								$car_rental_item_args['item_class'] = BookYourTravel_Theme_Utils::get_item_class_by_row_posts($posts_per_row);
								$car_rental_item_args = array_merge($car_rental_item_args, array(
									'hide_title' => $hide_item_titles,
									'hide_image' => $hide_item_images,
									'hide_description' => $hide_item_descriptions,
									'hide_actions' => $hide_item_actions,
									'hide_stars' => $hide_item_stars,
									'hide_rating' => $hide_item_ratings,			
									'hide_address' => $hide_item_address,
									'hide_price' => $hide_item_prices,
								));

								$template_part = 'includes/parts/car_rental/car_rental';
							} else if ($taxonomy == 'location_tag' || $taxonomy == 'location_type') {
								global $location_item_args;
								if (!isset($location_item_args) || !is_array($location_item_args)) {
									$location_item_args = array();
								}
								$location_item_args['location_id'] = $post->ID;
								$location_item_args['post'] = $post;
								$location_item_args['item_class'] = BookYourTravel_Theme_Utils::get_item_class_by_row_posts($posts_per_row);
								$location_item_args = array_merge($location_item_args, array(
									'hide_title' => $hide_item_titles,
									'hide_image' => $hide_item_images,
									'hide_description' => $hide_item_descriptions,
									'hide_actions' => $hide_item_actions,
									'hide_stars' => $hide_item_stars,
									'hide_rating' => $hide_item_ratings,			
									'hide_address' => $hide_item_address,
									'hide_price' => $hide_item_prices,
								));

								$template_part = 'includes/parts/location/location';
							} else if ($taxonomy == 'facility') {
								if ($post->post_type == 'accommodation') {
									global $accommodation_item_args;
									if (!isset($accommodation_item_args) || !is_array($accommodation_item_args)) {
										$accommodation_item_args = array();
									}
									$accommodation_item_args['accommodation_id'] = $post->ID;
									$accommodation_item_args['post'] = $post;
									$accommodation_item_args['item_class'] = BookYourTravel_Theme_Utils::get_item_class_by_row_posts($posts_per_row);
									$accommodation_item_args = array_merge($accommodation_item_args, array(
										'hide_title' => $hide_item_titles,
										'hide_image' => $hide_item_images,
										'hide_description' => $hide_item_descriptions,
										'hide_actions' => $hide_item_actions,
										'hide_stars' => $hide_item_stars,
										'hide_rating' => $hide_item_ratings,			
										'hide_address' => $hide_item_address,
										'hide_price' => $hide_item_prices,
									));
									
									$template_part = 'includes/parts/accommodation/accommodation';
								} else if ($post->post_type == 'cruise') {
									global $cruise_item_args;
									if (!isset($cruise_item_args) || !is_array($cruise_item_args)) {
										$cruise_item_args = array();
									}
									$cruise_item_args['cruise_id'] = $post->ID;
									$cruise_item_args['post'] = $post;
									$cruise_item_args['item_class'] = BookYourTravel_Theme_Utils::get_item_class_by_row_posts($posts_per_row);
									$cruise_item_args = array_merge($cruise_item_args, array(
										'hide_title' => $hide_item_titles,
										'hide_image' => $hide_item_images,
										'hide_description' => $hide_item_descriptions,
										'hide_actions' => $hide_item_actions,
										'hide_stars' => $hide_item_stars,
										'hide_rating' => $hide_item_ratings,			
										'hide_address' => $hide_item_address,
										'hide_price' => $hide_item_prices,
									));

									$template_part = 'includes/parts/cruise/cruise';
								}
							}

							get_template_part($template_part, 'item');
						}
						?>
					</div>
					<?php
					if ($wp_query->max_num_pages > 1) {
					?>
					<!--bottom navigation-->
					<div class="full-width">
						<nav class="page-navigation bottom-nav">
							<div class="pager">
							<?php
								global $wp_query;
								BookYourTravel_Theme_Controls::the_pager($wp_query->max_num_pages);
							?>
							</div>
						</nav>
					</div>
					<!--//bottom navigation-->
					<?php
					}
					?>
				</div>
				<?php } else { ?>
				<div class="row">
					<div class="full-width">
						<article class="static-content post">
							<header class="entry-header">
								<br /><h3><?php esc_html_e('Welcome to Book Your Travel!', 'bookyourtravel'); ?></h3>
							</header>
							<div class="entry-content">
								<p><?php esc_html_e('There is currently no content to show here, so start creating content.', 'bookyourtravel'); ?></p>
							</div>
						</article>
					</div>
				</div>
				<?php } ?>
			</section>
			<!--//three-fourth content-->
		<?php
		if ($page_sidebar_positioning == 'both' || $page_sidebar_positioning == 'right')
			get_sidebar('right');
		?>
		</div>
<?php
}
get_template_part('byt', 'footer');
get_footer();