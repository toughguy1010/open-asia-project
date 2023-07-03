<?php
/**
/* Template Name: Custom Search Results */
/*
 *
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
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

global $post, $current_url, $bookyourtravel_theme_globals, $current_user, $frontend_submit,
$bookyourtravel_review_helper, $bookyourtravel_accommodation_helper, $bookyourtravel_car_rental_helper, $bookyourtravel_cruise_helper, $bookyourtravel_tour_helper,
$item_class, $date_from, $date_to, $guests, $rooms, $cabins;

$page_id = $post->ID;
$page_custom_fields = get_post_custom( $page_id);

$enable_reviews = $bookyourtravel_theme_globals->enable_reviews();
$enable_accommodations = $bookyourtravel_theme_globals->enable_accommodations();
$enable_cruises = $bookyourtravel_theme_globals->enable_cruises();
$enable_tours = $bookyourtravel_theme_globals->enable_tours();
$enable_car_rentals = $bookyourtravel_theme_globals->enable_car_rentals();

if (!isset($current_user)) {
	$current_user = wp_get_current_user();
}

$default_results_view = $bookyourtravel_theme_globals->get_search_results_default_view();
// $custom_search_results_page = $bookyourtravel_theme_globals->get_custom_search_results_page_url();
$custom_search_results_page = BookYourTravel_Theme_Utils::get_current_page_url_no_query();

$request_car_types = BookYourTravel_Theme_Utils::retrieve_array_of_values_from_query_string('car_types', true);
$request_car_rental_tags = BookYourTravel_Theme_Utils::retrieve_array_of_values_from_query_string('car_rental_tags', true);
$request_tour_types = BookYourTravel_Theme_Utils::retrieve_array_of_values_from_query_string('tour_types', true);
$request_tour_durations = BookYourTravel_Theme_Utils::retrieve_array_of_values_from_query_string('tour_durations', true);
$request_tour_tags= BookYourTravel_Theme_Utils::retrieve_array_of_values_from_query_string('tour_tags', true);
$request_cruise_types = BookYourTravel_Theme_Utils::retrieve_array_of_values_from_query_string('cruise_types', true);
$request_cruise_durations = BookYourTravel_Theme_Utils::retrieve_array_of_values_from_query_string('cruise_durations', true);
$request_cruise_tags = BookYourTravel_Theme_Utils::retrieve_array_of_values_from_query_string('cruise_tags', true);
$request_accommodation_types = BookYourTravel_Theme_Utils::retrieve_array_of_values_from_query_string('accommodation_types', true);
$request_accommodation_tags = BookYourTravel_Theme_Utils::retrieve_array_of_values_from_query_string('accommodation_tags', true);

$request_car_type = isset($_GET['car_type']) ? intval($_GET['car_type']) : 0;
$request_car_rental_tag = isset($_GET['car_rental_tag']) ? intval(wp_kses($_GET['car_rental_tag'], array())) : 0;
$request_tour_type = isset($_GET['tour_type']) ? intval(wp_kses($_GET['tour_type'], array())) : 0;
$request_tour_duration = isset($_GET['tour_duration']) ? intval(wp_kses($_GET['tour_duration'], array())) : 0;
$request_tour_tag = isset($_GET['tour_tag']) ? intval(wp_kses($_GET['tour_tag'], array())) : 0;
$request_cruise_type = isset($_GET['cruise_type']) ? intval(wp_kses($_GET['cruise_type'], array())) : 0;
$request_cruise_duration = isset($_GET['cruise_duration']) ? intval(wp_kses($_GET['cruise_duration'], array())) : 0;
$request_cruise_tag = isset($_GET['cruise_tag']) ? intval(wp_kses($_GET['cruise_tag'], array())) : 0;
$request_accommodation_type = isset($_GET['accommodation_type']) ? intval(wp_kses($_GET['accommodation_type'], array())) : 0;
$request_accommodation_tag = isset($_GET['accommodation_tag']) ? intval(wp_kses($_GET['accommodation_tag'], array())) : 0;

if ($request_car_type > 0 && !in_array($request_car_type, $request_car_types)) {
	$request_car_types[] = $request_car_type;
}
if ($request_car_rental_tag > 0 && !in_array($request_car_rental_tag, $request_car_rental_tags)) {
	$request_car_rental_tags[] = $request_car_rental_tag;
}
if ($request_tour_type > 0 && !in_array($request_tour_type, $request_tour_types)) {
	$request_tour_types[] = $request_tour_type;
}
if ($request_tour_duration > 0 && !in_array($request_tour_duration, $request_tour_durations)) {
	$request_tour_durations[] = $request_tour_duration;
}
if ($request_tour_tag > 0 && !in_array($request_tour_tag, $request_tour_tags)) {
	$request_tour_tags[] = $request_tour_tag;
}
if ($request_cruise_type > 0 && !in_array($request_cruise_type, $request_cruise_types)) {
	$request_cruise_types[] = $request_cruise_type;
}
if ($request_cruise_duration > 0 && !in_array($request_cruise_duration, $request_cruise_durations)) {
	$request_cruise_durations[] = $request_cruise_duration;
}
if ($request_cruise_tag > 0 && !in_array($request_cruise_tag, $request_cruise_tags)) {
	$request_cruise_tags[] = $request_cruise_tag;
}
if ($request_accommodation_type > 0 && !in_array($request_accommodation_type, $request_accommodation_types)) {
	$request_accommodation_types[] = $request_accommodation_type;
}
if ($request_accommodation_tag > 0 && !in_array($request_accommodation_tag, $request_accommodation_tags)) {
	$request_accommodation_tags[] = $request_accommodation_tag;
}

$request_prices = BookYourTravel_Theme_Utils::retrieve_array_of_values_from_query_string('price', true);
$request_facilities = BookYourTravel_Theme_Utils::retrieve_array_of_values_from_query_string('facilities', true);

$search_term = isset($_GET['term']) ? sanitize_text_field($_GET['term']) : '';
$location_ids = BookYourTravel_Theme_Utils::retrieve_array_of_values_from_query_string('l', true);

$stars = isset($_GET['stars']) ? intval(wp_kses($_GET['stars'], array())) : 0;
$rating = isset($_GET['rating']) ? intval(wp_kses($_GET['rating'], array())) : 0;
$guests = isset($_GET['guests']) ? intval(wp_kses($_GET['guests'], array())) : 1;
$cabins = isset($_GET['cabins']) ? intval(wp_kses($_GET['cabins'], array())) : 1;
$rooms = isset($_GET['rooms']) ? intval(wp_kses($_GET['rooms'], array())) : 1;
$what = isset($_GET['what']) ? intval(wp_kses($_GET['what'], array())) : 0;

if ($what == 0) {
	$search_list_default_results_post_type = isset($page_custom_fields['search_list_default_results_post_type']) ? $page_custom_fields['search_list_default_results_post_type'][0] : '';

	if (!empty($search_list_default_results_post_type)) {
		if ($search_list_default_results_post_type == 'accommodation')
			$what = 1;
		else if ($search_list_default_results_post_type == 'car_rental')
			$what = 2;
		else if ($search_list_default_results_post_type == 'cruise')
			$what = 3;
		else if ($search_list_default_results_post_type == 'tour')
			$what = 4;
	} else {
		$what = 1;
	}
}

$sort_by = isset($_GET['sb']) ? intval(wp_kses($_GET['sb'], array())) : 1;
$sort_order = isset($_GET['so']) ? intval(wp_kses($_GET['so'], array())) : 1;

$date_from = isset($_GET['from']) && !empty($_GET['from'])  ? date('Y-m-d', strtotime(sanitize_text_field($_GET['from']))) : null;
$date_to = isset($_GET['to']) && !empty($_GET['to']) ? date('Y-m-d', strtotime(sanitize_text_field($_GET['to']))) : null;

$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

$search_args = array();

$search_args['date_from'] = $date_from;
$search_args['date_to'] = $date_to;
$search_args['keyword'] = $search_term;
$search_args['prices'] = $request_prices;

$search_args['price_range_bottom'] = $bookyourtravel_theme_globals->get_price_range_bottom();
$search_args['price_range_increment'] = $bookyourtravel_theme_globals->get_price_range_increment();
$search_args['price_range_count'] = $bookyourtravel_theme_globals->get_price_range_count();
$search_args['search_only_available'] = $bookyourtravel_theme_globals->search_only_available_properties();

$posts_per_row = isset($page_custom_fields['search_list_posts_per_row']) ? intval($page_custom_fields['search_list_posts_per_row'][0]) : 4;
$posts_per_page = isset($page_custom_fields['search_list_posts_per_page']) ? intval($page_custom_fields['search_list_posts_per_page'][0]) : 12;

$q_args = array();

$current_url = add_query_arg('what', urlencode($what), $custom_search_results_page);
$q_args["what"] = $what;

if ($date_from) {
	$current_url = add_query_arg('from', urlencode($date_from), $current_url);
	$q_args["from"] = $date_from;
}
if ($date_to) {
	$current_url = add_query_arg('to', urlencode($date_to), $current_url);
	$q_args["to"] = $date_to;
}
if (!empty($search_term)) {
	$current_url = add_query_arg('term', urlencode($search_term), $current_url);
	$q_args["term"] = $search_term;
}
if (count($location_ids) > 0) {
	$current_url = add_query_arg('l', $location_ids, $current_url);
	$q_args["l"] = $location_ids;
}

$exclusive_search = count($location_ids) > 1;

if ($what == 1 ) {

	$sort_order = $sort_order == '1' ? 'ASC' : 'DESC';
	if (isset($sort_by)) {
		switch ($sort_by) {
			case '1' : $sort_by = 'min_price';break;// price
			case '2' : $sort_by = 'star_count';break;// star count
			case '3' : $sort_by = 'review_score';break;// star count
			default : $sort_by = 'accommodations.post_title';break;
		}
	} else {
		$sort_by = 'accommodations.post_title';
	}

	$search_args['rating'] = $rating;
	$search_args['rooms'] = $rooms;
	$search_args['stars'] = $stars;

	$results = $bookyourtravel_accommodation_helper->list_accommodations( $paged, $posts_per_page, $sort_by, $sort_order, $location_ids, $request_accommodation_types, $request_accommodation_tags, $request_facilities, $search_args, false);

	if ($rating > 0) {
		$current_url = add_query_arg('rating', $rating, $current_url);
		$q_args["rating"] = $rating;
	}
	if ($rooms > 1) {
		$current_url = add_query_arg('rooms', $rooms, $current_url);
		$q_args["rooms"] = $rooms;
	}
	if ($stars > 0) {
		$current_url = add_query_arg('stars', $stars, $current_url);
		$q_args["stars"] = $stars;
	}
	if (isset($_GET['price'])) {
		$current_url = add_query_arg('price', $request_prices, $current_url);
		$q_args["price"] = $request_prices;
	}
	if (isset($_GET['facilities'])) {
		$current_url = add_query_arg('facilities', $request_facilities, $current_url);
		$q_args["facilities"] = $request_facilities;
	}
	if (isset($_GET['accommodation_tags'])) {
		$current_url = add_query_arg('accommodation_tags', $request_accommodation_tags, $current_url);
		$q_args["accommodation_tags"] = $request_accommodation_tags;
	}
	if (isset($_GET['accommodation_types'])) {
		$current_url = add_query_arg('accommodation_types', $request_accommodation_types, $current_url);
		$q_args["accommodation_types"] = $request_accommodation_types;
	}
} else if ($what == 2) {

	$sort_by = $sort_by == '1' ? 'price' : 'car_rentals.post_title';
	$sort_order = $sort_order == '1' ? 'ASC' : 'DESC';

	$results = $bookyourtravel_car_rental_helper->list_car_rentals($paged, $posts_per_page, $sort_by, $sort_order, $location_ids, $exclusive_search, $request_car_types, $request_car_rental_tags, $search_args);

	if ($rating > 0) {
		$current_url = add_query_arg('rating', $rating, $current_url);
		$q_args["rating"] = $rating;
	}
	if (isset($_GET['price'])) {
		$current_url = add_query_arg('price', $request_prices, $current_url);
		$q_args["price"] = $request_prices;
	}
	if (isset($_GET['car_rental_tags'])) {
		$current_url = add_query_arg('car_rental_tags', $request_car_rental_tags, $current_url);
		$q_args["car_rental_tags"] = $request_car_rental_tags;
	}
	if (isset($_GET['car_types'])) {
		$current_url = add_query_arg('car_types', $request_car_types, $current_url);
		$q_args["car_types"] = $request_car_types;
	}
} else if ($what == 3) {
	$search_args['rating'] = $rating;
	$search_args['guests'] = $guests;
	$search_args['cabins'] = $cabins;
	$sort_by = $sort_by == '1' ? 'min_price' : 'cruises.post_title';
	$sort_order = $sort_order == '1' ? 'ASC' : 'DESC';

	$results = $bookyourtravel_cruise_helper->list_cruises($paged, $posts_per_page, $sort_by, $sort_order, $location_ids, $exclusive_search, $request_cruise_types, $request_cruise_durations, $request_cruise_tags, $request_facilities, $search_args);

	if ($rating > 0) {
		$current_url = add_query_arg('rating', $rating, $current_url);
		$q_args["rating"] = $rating;
	}
	if ($cabins > 1) {
		$current_url = add_query_arg('cabins', $cabins, $current_url);
		$q_args["cabins"] = $cabins;
	}
	if (isset($_GET['price'])) {
		$current_url = add_query_arg('price', $request_prices, $current_url);
		$q_args["price"] = $request_prices;
	}
	if (isset($_GET['facilities'])) {
		$current_url = add_query_arg('facilities', $request_facilities, $current_url);
		$q_args["facilities"] = $request_facilities;
	}
	if (isset($_GET['cruise_tags'])) {
		$current_url = add_query_arg('cruise_tags', $request_cruise_tags, $current_url);
		$q_args["cruise_tags"] = $request_cruise_tags;
	}
	if (isset($_GET['cruise_types'])) {
		$current_url = add_query_arg('cruise_types', $request_cruise_types, $current_url);
		$q_args["cruise_types"] = $request_cruise_types;
	}
	if (isset($_GET['cruise_durations'])) {
		$current_url = add_query_arg('cruise_durations', $request_cruise_durations, $current_url);
		$q_args["cruise_durations"] = $request_cruise_durations;
	}	
} else if ($what == 4) {

	$search_args['rating'] = $rating;
	$search_args['guests'] = $guests;
	$sort_by = $sort_by == '1' ? 'min_price' : 'tours.post_title';
	$sort_order = $sort_order == '1' ? 'ASC' : 'DESC';

	$results = $bookyourtravel_tour_helper->list_tours($paged, $posts_per_page, $sort_by, $sort_order, $location_ids, $exclusive_search, $request_tour_types, $request_tour_durations, $request_tour_tags, $search_args);

	if ($rating > 0) {
		$current_url = add_query_arg('rating', $rating, $current_url);
		$q_args["rating"] = $rating;
	}
	if (isset($_GET['price'])) {
		$current_url = add_query_arg('price', $request_prices, $current_url);
		$q_args["price"] = $request_prices;
	}
	if (isset($_GET['tour_tags'])) {
		$current_url = add_query_arg('tour_tags', $request_tour_tags, $current_url);
		$q_args["tour_tags"] = $request_tour_tags;
	}
	if (isset($_GET['tour_types'])) {
		$current_url = add_query_arg('tour_types', $request_tour_types, $current_url);
		$q_args["tour_types"] = $request_tour_types;
	}
	if (isset($_GET['tour_durations'])) {
		$current_url = add_query_arg('tour_durations', $request_tour_durations, $current_url);
		$q_args["tour_durations"] = $request_tour_durations;
	}	
}

$search_item_args = array();
$search_item_args['hide_title'] = isset($page_custom_fields['search_list_hide_item_titles']) && $page_custom_fields['search_list_hide_item_titles'][0] == '1' ? true : false;
$search_item_args['hide_image'] = isset($page_custom_fields['search_list_hide_item_images']) && $page_custom_fields['search_list_hide_item_images'][0] == '1' ? true : false;
$search_item_args['hide_description'] = isset($page_custom_fields['search_list_hide_item_descriptions']) && $page_custom_fields['search_list_hide_item_descriptions'][0] == '1' ? true : false;
$search_item_args['hide_actions'] = isset($page_custom_fields['search_list_hide_item_actions']) && $page_custom_fields['search_list_hide_item_actions'][0] == '1' ? true : false;
$search_item_args['hide_price'] = isset($page_custom_fields['search_list_hide_item_prices']) && $page_custom_fields['search_list_hide_item_prices'][0] == '1' ? true : false;
$search_item_args['item_class'] = BookYourTravel_Theme_Utils::get_item_class_by_row_posts($posts_per_row);

$page_sidebar_positioning = null;
if (isset($page_custom_fields['page_sidebar_positioning'])) {
	$page_sidebar_positioning = $page_custom_fields['page_sidebar_positioning'][0];
	$page_sidebar_positioning = empty($page_sidebar_positioning) ? '' : $page_sidebar_positioning;
}

$section_class = 'full-width';
if ($page_sidebar_positioning == 'both') {
	$section_class = 'one-half';
} else if ($page_sidebar_positioning == 'left' || $page_sidebar_positioning == 'right') {
	$section_class = 'three-fourth';
}
?>
<script>
	window.requestedDateFrom = <?php echo json_encode($date_from); ?>;
	window.requestedDateTo = <?php echo json_encode($date_to); ?>;
</script>
		<div class="row">
			<?php
			$page_content = '';
			if (have_posts()) { ?>
				<?php while ( have_posts() ) : the_post(); ?>
						<?php $page_content = get_the_content(); ?>
						<?php if (!empty($page_content)) { ?>
						<section class="full-width">
							<article <?php post_class(); ?> id="page-<?php the_ID(); ?>">
								<?php the_content( wp_kses(__( 'Continue reading <span class="meta-nav">&rarr;</span>', 'bookyourtravel' ), array('span' =>  array('class' => array()))) ); ?>
							</article>			
						</section>							
						<?php } ?>
				<?php endwhile;	?>
			<?php } ?>
			<?php
			if ($page_sidebar_positioning == 'both' || $page_sidebar_positioning == 'left')
				get_sidebar('left');
			?>
			<script>
				window.itemClass = <?php echo json_encode($search_item_args['item_class']); ?>;
			</script>
			<section class="section-search-results <?php echo esc_attr($section_class); ?>">
				<div class="sort-by">
					<h3><?php esc_html_e('Sort by', 'bookyourtravel'); ?></h3>
					<ul class="sort">
						<li><?php esc_html_e('Price', 'bookyourtravel'); ?> <a href="<?php echo esc_url($current_url) . '&sb=1&so=1'; ?>" title="<?php esc_attr_e('ascending', 'bookyourtravel'); ?>" class="ascending"><?php esc_html_e('ascending', 'bookyourtravel'); ?></a><a href="<?php echo esc_url($current_url) . '&sb=1&so=2'; ?>" title="<?php esc_attr_e('descending', 'bookyourtravel'); ?>" class="descending"><?php esc_html_e('descending', 'bookyourtravel'); ?></a></li>
						<?php if ($what == 1) { ?>
						<li><?php esc_html_e('Stars', 'bookyourtravel'); ?> <a href="<?php echo esc_url($current_url) . '&sb=2&so=1'; ?>" title="<?php esc_attr_e('ascending', 'bookyourtravel'); ?>" class="ascending"><?php esc_html_e('ascending', 'bookyourtravel'); ?></a><a href="<?php echo esc_url($current_url) . '&sb=2&so=2'; ?>" title="<?php esc_attr_e('descending', 'bookyourtravel'); ?>" class="descending"><?php esc_html_e('descending', 'bookyourtravel'); ?></a></li>
						<li><?php esc_html_e('Rating', 'bookyourtravel'); ?> <a href="<?php echo esc_url($current_url) . '&sb=3&so=1'; ?>" title="<?php esc_attr_e('ascending', 'bookyourtravel'); ?>" class="ascending"><?php esc_html_e('ascending', 'bookyourtravel'); ?></a><a href="<?php echo esc_url($current_url) . '&sb=3&so=2'; ?>" title="<?php esc_attr_e('descending', 'bookyourtravel'); ?>" class="descending"><?php esc_html_e('descending', 'bookyourtravel'); ?></a></li>
						<?php } ?>
					</ul>

					<ul class="view-type">
						<script>
							window.defaultResultsView = <?php echo json_encode($default_results_view); ?>;
						</script>
						<li class="grid-view <?php echo ($default_results_view === 0) ? 'active' : ''; ?>"><a href="#" title="grid view"><?php esc_html_e('grid view', 'bookyourtravel'); ?></a></li>
						<li class="list-view <?php echo ($default_results_view === 1) ? 'active' : ''; ?>"><a href="#" title="list view"><?php esc_html_e('list view', 'bookyourtravel'); ?></a></li>
					</ul>
				</div>

				<div class="deals">
					<!--deal-->
					<?php
					if (count($results) > 0 && $results['total'] > 0) { ?>
						<div class="row">
						<?php
						foreach ($results['results'] as $result) {
							global $post;
							$post = $result;
							setup_postdata( $post );
							if (isset($post)) {
								if ($what == 1) {
									global $accommodation_item_args;
									$accommodation_item_args = $search_item_args;
									$accommodation_item_args['accommodation_id'] = $post->ID;
									$accommodation_item_args['post'] = $post;
									get_template_part('includes/parts/accommodation/accommodation', 'item');
								} elseif ($what == 2) {
									global $car_rental_item_args;
									$car_rental_item_args = $search_item_args;
									$car_rental_item_args['car_rental_id'] = $post->ID;
									$car_rental_item_args['post'] = $post;
									get_template_part('includes/parts/car_rental/car_rental', 'item');
								} elseif ($what == 3) {
									global $cruise_item_args;
									$cruise_item_args = $search_item_args;
									$cruise_item_args['cruise_id'] = $post->ID;
									$cruise_item_args['post'] = $post;
									get_template_part('includes/parts/cruise/cruise', 'item');
								} elseif ($what == 4) {
									global $tour_item_args;
									$tour_item_args = $search_item_args;
									$tour_item_args['tour_id'] = $post->ID;
									$tour_item_args['post'] = $post;
									get_template_part('includes/parts/tour/tour', 'item');
								}
							}

						} ?>
						</div>
						<?php
						$total_results = $results['total'];
						if ($total_results > $posts_per_page && $posts_per_page > 0) {
						?>
						<nav class="page-navigation bottom-nav">
							<div class="pager">
							<?php
								BookYourTravel_Theme_Controls::the_pager( ceil($total_results/$posts_per_page), false, $q_args );
							?>
							</div>
						</nav>
						<?php
						}
						?>
					<?php } else { ?>
						<p><?php esc_html_e('Unfortunately no results match your search criteria. Please try searching for something else.', 'bookyourtravel'); ?></p>
					<?php } ?>
				</div>
			</section>
			<?php
			wp_reset_postdata();
			wp_reset_query();

			if ($page_sidebar_positioning == 'both' || $page_sidebar_positioning == 'right')
				get_sidebar('right');
			?>
		</div>
<?php
get_template_part('byt', 'footer');
get_footer();
