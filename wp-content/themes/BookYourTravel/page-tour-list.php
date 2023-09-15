<?php

/**
/* Template Name: Tour list
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */

global $bookyourtravel_theme_globals, $post, $tour_list_args, $tour_item_args;

get_header();
get_template_part('byt', 'header');
BookYourTravel_Theme_Controls::the_breadcrumbs();
get_sidebar('under-header');

$page_id = $post->ID;
$page_custom_fields = get_post_custom($page_id);

$parent_location_id = isset($page_custom_fields['tour_list_location_post_id']) ? intval($page_custom_fields['tour_list_location_post_id'][0]) : 0;
$sort_by = isset($page_custom_fields['tour_list_sort_by']) && !empty($page_custom_fields['tour_list_sort_by'][0]) ? $page_custom_fields['tour_list_sort_by'][0] : 'title';
$sort_descending = isset($page_custom_fields['tour_list_sort_descending']) && $page_custom_fields['tour_list_sort_descending'][0] == '1' ? true : false;
$sort_order = $sort_descending ? 'DESC' : 'ASC';
$show_featured_only = isset($page_custom_fields['tour_list_show_featured_only']) && $page_custom_fields['tour_list_show_featured_only'][0] == '1' ? true : false;
$posts_per_row = isset($page_custom_fields['tour_list_posts_per_row']) ? intval($page_custom_fields['tour_list_posts_per_row'][0]) : 4;
$posts_per_page = isset($page_custom_fields['tour_list_posts_per_page']) ? intval($page_custom_fields['tour_list_posts_per_page'][0]) : 12;

$hide_item_titles = isset($page_custom_fields['tour_list_hide_item_titles']) && $page_custom_fields['tour_list_hide_item_titles'][0] == '1' ? true : false;
$hide_item_images = isset($page_custom_fields['tour_list_hide_item_images']) && $page_custom_fields['tour_list_hide_item_images'][0] == '1' ? true : false;
$hide_item_descriptions = isset($page_custom_fields['tour_list_hide_item_descriptions']) && $page_custom_fields['tour_list_hide_item_descriptions'][0] == '1' ? true : false;
$hide_item_actions = isset($page_custom_fields['tour_list_hide_item_actions']) && $page_custom_fields['tour_list_hide_item_actions'][0] == '1' ? true : false;
$hide_item_rating = isset($page_custom_fields['tour_list_hide_item_rating']) && $page_custom_fields['tour_list_hide_item_rating'][0] == '1' ? true : false;
$hide_item_address = isset($page_custom_fields['tour_list_hide_item_address']) && $page_custom_fields['tour_list_hide_item_address'][0] == '1' ? true : false;
$hide_item_prices = isset($page_custom_fields['tour_list_hide_item_prices']) && $page_custom_fields['tour_list_hide_item_prices'][0] == '1' ? true : false;

$tour_tags = wp_get_post_terms($page_id, 'tour_tag', array("fields" => "all"));
$tour_tag_ids = array();
if (isset($tour_tags) && !is_wp_error($tour_tags) && count($tour_tags) > 0) {
	foreach ($tour_tags as $tour_tag) {
		$tour_tag_ids[] = $tour_tag->term_id;
	}
}



// $tour_types = wp_get_post_terms($page_id, 'tour_type', array("fields" => "all"));

// $tour_type_ids = array();
// if (!is_wp_error($tour_types) && count($tour_types) > 0) {
// 	foreach ($tour_types as $tour_type) {
// 		$tour_type_ids[] = $tour_type->term_id;
// 	}
// }

$tour_durations = wp_get_post_terms($page_id, 'tour_duration', array("fields" => "all"));
$tour_duration_ids = array();
if (!is_wp_error($tour_durations) && count($tour_durations) > 0) {
	foreach ($tour_durations as $tour_duration) {
		$tour_duration_ids[] = $tour_duration->term_id;
	}
}
$page_sidebar_positioning = BookYourTravel_Theme_Utils::get_page_sidebar_positioning($page_id);
$section_class = BookYourTravel_Theme_Utils::get_page_section_class($page_sidebar_positioning);
global $bookyourtravel_theme_globals, $bookyourtravel_theme_post_types;
if (isset($_GET['tour-type'])) {
	$tour_type_slug = $_GET['tour-type'];

	$tour_type = get_term_by('slug', $tour_type_slug, 'tour_type');

	if ($tour_type) {

		$tour_type_id = $tour_type->term_id;
	} else {
		$tour_type_id = "";
	}
}
$thumbnail_id = $bookyourtravel_theme_post_types->get_taxonomy_image_id($tour_type_id);
$thumbnail_url = wp_get_attachment_image_src($thumbnail_id, "byt-featured")[0];
?>
<style>
	.page-banner-wrap {
		min-height: 650px;
		background-repeat: no-repeat;
		background-size: cover;
		background-position: center;
		display: flex;
		justify-content: center;
		align-items: center;
	}
</style>
<div class="row">
	<?php
	if ($page_sidebar_positioning == 'both' || $page_sidebar_positioning == 'left')
		get_sidebar('left');
	?>

	<section class="<?php echo esc_attr($section_class); ?>">
		<div class="single-breadcrumbs">
			<nav class="breadcrumbs nano-container">
				<ul>
					<li><a href="<?php echo home_url() ?>" title="Home">Home</a></li>
					<span class="separator"> » </span>
					<li><a href="<?php echo home_url() ?>/tours/?tour-type=<?= $tour_type_slug ?>" title="Cruises"><?= $tour_type->name ?></a></li>
				</ul>
			</nav>
		</div>

		<?php while (have_posts()) : the_post(); ?>
			<article <?php post_class(); ?> id="page-<?php the_ID(); ?>">
				<div class="page-banner-wrap" style="background-image: url(<?php echo $thumbnail_url ?>);">
					<div class="page-banner-container">
						<h1 class="page-banner-title"><?= $tour_type->name ?></h1>
						<div class="plan-banner-wrap">
							<h3>How to Plan a Perfect trip</h3>
							<div class="step-plan-list">
								<div class="step-plan-item">
									<div class="step-img">
										<img src="<?php echo get_template_directory_uri(); ?>/css/images/step1.svg ?>" alt="">
									</div>
									<div class="step-plan-body">
										<div class="step-plan-title">
											1. Discovery
										</div>
										<div class="step-plan-content">
											Browse our website. Tell us what kind of trip you want. Complete our simple request form.
										</div>
									</div>
								</div>
								<div class="step-plan-item">
									<div class="step-img">
										<img src="<?php echo get_template_directory_uri(); ?>/css/images/step2.svg ?>" alt="">
									</div>
									<div class="step-plan-body">
										<div class="step-plan-title">
											2. Customize
										</div>
										<div class="step-plan-content">
											Together work with our Experienced Travel Specialists to tweak the itinerary.
										</div>
									</div>
								</div>
								<div class="step-plan-item">
									<div class="step-img">
										<img src="<?php echo get_template_directory_uri(); ?>/css/images/step-3.png ?>" alt="">
									</div>
									<div class="step-plan-body">
										<div class="step-plan-title">
											3. Book your Trip
										</div>
										<div class="step-plan-content">
											Once you're satisfied with our personalized proposal, book your chosen trip securely backed ASTA.
										</div>
									</div>
								</div>
							</div>
							<div class="step-plan-btn">
								<a href="<?= home_url() ?>/tailor-made-your-tour/">Start Designing You Trip Now</a>
							</div>
							<span style="text-align: center; display: block;">
								Free service. No credit card required
							</span>
						</div>
					</div>
				</div>
			</article>

		<?php endwhile; ?>
		<?php

		$paged = 1;
		if (get_query_var('paged-byt')) {
			$paged = get_query_var('paged-byt');
		} else if (get_query_var('paged')) {
			$paged = get_query_var('paged');
		} else if (get_query_var('page')) {
			$paged = get_query_var('page');
		}

		$tour_list_args = array(
			'parent_location_id' => $parent_location_id,
			'sort_by' => $sort_by,
			'sort_order' => $sort_order,
			'show_featured_only' => $show_featured_only,
			'posts_per_page' => $posts_per_page,
			'posts_per_row' => $posts_per_row,
			'is_list_page' => true,
			'display_mode' => 'card',
			'tour_tag_ids' => $tour_tag_ids,
			// 'tour_type_ids' => $tour_type_ids,
			'tour_duration_ids' => $tour_duration_ids,
			'paged' => $paged,
			// 'found_post_content' => $has_post_thumbnail
		);

		$tour_item_args = array(
			'hide_title' => $hide_item_titles,
			'hide_image' => $hide_item_images,
			'hide_description' => $hide_item_descriptions,
			'hide_actions' => $hide_item_actions,
			'hide_rating' => $hide_item_rating,
			'hide_address' => $hide_item_address,
			'hide_price' => $hide_item_prices,
		);

		do_action('bookyourtravel_page_tour_list_before');
		?>
		<div class="page-tour-wrapper">
			<div class="taxonomy-header">
				<div class="taxonomy-name">
					<?= $tour_type->name ?>
				</div>
				<div class="taxonomy-description">
					<?= $tour_type->description ?>
				</div>
			</div>
			<?php
			get_template_part('includes/parts/tour/tour', 'list');
			?>
		</div>

		<?php

		do_action('bookyourtravel_page_tour_list_after');
		?>
		<?php //wp_link_pages('before=<div class="pagination">&after=</div>'); 
		?>
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
