<?php
global $bookyourtravel_theme_globals, $post, $first_display_tab, $default_tour_tabs, $entity_obj, $layout_class, $tab, $tour_item_args, $tour_list_args, $bookyourtravel_theme_post_types, $bookyourtravel_tour_helper;

$tour_obj = new BookYourTravel_Tour($post);
$entity_obj = $tour_obj;

$tour_extra_fields = $bookyourtravel_theme_globals->get_tour_extra_fields();
$tab_array = $bookyourtravel_theme_globals->get_tour_tabs();
$nav_layout = $bookyourtravel_theme_globals->get_tour_single_layout();

// custom

$tour_title = get_the_title();
$tour_duration = $tour_obj->get_tour_durations();
$tour_tag = $tour_obj->get_tags();
$tour_description = wpautop($tour_obj->get_short_description());
if (get_field('tour_price', get_the_ID())) {
	$tour_price = get_field('tour_price', get_the_ID());
	if ($tour_price) {
		$have_price = $tour_price['have_price'];

		if (isset($have_price[0])) {
			$value_have_price = $have_price[0];
		} else {
			$value_have_price = "";
		}
	}
}
$tour_type_id = $tour_obj->get_type_id();


if (get_field('tour_itinerary', get_the_ID())) {
	$tour_itinerary = get_field('tour_itinerary', get_the_ID());
}

$user_information = $tour_obj->get_custom_field('user_information');

if (get_field('customizable_itinerary', get_the_ID())) {
	$customizable_itinerary = get_field('customizable_itinerary', get_the_ID());
}

// get field tour content
// overview
if (get_field('overview', get_the_ID())) {
	$overview = get_field('overview', get_the_ID());
?>
<?php
} else {
}
// overview

ob_start();
get_template_part('includes/parts/tour/single/javascript', 'vars');

$js_vars = ob_get_contents();
ob_end_clean();
echo $js_vars;

ob_start();
?>
<div class="single-tour-header">
	<div class="nano-container single-tour-container ">
		<h1><?= $tour_title ?></h1>
		<div class="single-tour-meta">
			<?php
			if ($tour_duration) {
			?>
				<div class="tour_item-duraiton tour_item-text">
					<?php
					foreach ($tour_duration as $duration) {
					?>
						<div class="item-duraiton">
							<img src="<?php echo get_template_directory_uri() ?>/css/images/ico__clock.png" alt="">
							<?= $duration->name  ?>
						</div>
					<?php
					}
					?>
				</div>
			<?php
			}
			if ($value_have_price == 'Yes') {
				$static_price = $tour_price['static_price'];
				$market_price = $tour_price['market_price'];
				$save_price = $market_price - $static_price;
			?>
				<div class="tour_item-price">
					<div class="static_price">
						Deal: <strong>US$<?= $static_price ?></strong>
					</div>
					<div class="market_price">
						Typically: <strong>US$<?= $market_price ?></strong>
					</div>
					<div class="save_price">
						You save: <strong>US$<?= $save_price ?></strong>
					</div>
				</div>
			<?php
			}
			if ($tour_tag) {
			?>
				<div class="tour_item-tags">
					<?php
					foreach ($tour_tag as $tag) {
						$thumbnail_id = $bookyourtravel_theme_post_types->get_taxonomy_image_id($tag->term_id);
						$thumbnail_url = wp_get_attachment_image_src($thumbnail_id, "byt-featured")[0];
					?>
						<div class="item-tags">
							<div class="tags-icon">
								<img src="<?= $thumbnail_url ?>" alt="">
							</div>
							<div class="tag-name tour_item-text">
								<?= $tag->name  ?>
							</div>
						</div>
					<?php
					}
					?>
				</div>
			<?php
			}
			?>
		</div>
	</div>
</div>
<?php
// $featured_element = $tour_obj->get_displayed_featured_element();
// if ($featured_element == 'gallery') {
// 	get_template_part('includes/parts/post/single/post', 'gallery');
// } else {
// 	get_template_part('includes/parts/post/single/post', 'image');
// }
?>
<!-- slider -->
<?php get_template_part('includes/parts/tour/single/slider') ?>
<!-- slider -->



<div class="single-tour-body">
	<div class="single-tour-nav-wraper">
		<div class="single-tour-nav-container nano-container">
			<div class="hamburger-lines">
				<span class="line line1"></span>
				<span class="line line2"></span>
				<span class="line line3"></span>
			</div>
			<ul class="nav-tour-list">
				<li class="nav-tour-item">
					<a href="#tour-overview">Overview</a>
				</li>
				<li class="nav-tour-item">
					<a href="#customizable_itinerary">Customizable itinerary</a>
				</li>
				<li class="nav-tour-item">
					<a href="#useful_information">Useful information</a>
				</li>
				<li class="nav-tour-item">
					<a href="#relate_tour">Related tours</a>
				</li>
				<li class="nav-tour-item">
					<a href="<?= home_url() ?>/tailor-made-your-tour/" class="tour-nav-btn">Request a free quote</a>
				</li>
			</ul>
		</div>
	</div>
	<!-- over view -->
	<?php get_template_part('includes/parts/tour/single/tour-overview') ?>
	<!-- over view -->

	<!-- customizable itinerary -->
	<?php get_template_part('includes/parts/tour/single/customizable_itinerary') ?>
	<!-- customizable itinerary -->

	<!-- useful information -->
	<?php get_template_part('includes/parts/tour/single/useful_information') ?>
	<!-- useful information -->

	<!-- trip single banner -->
	<?php get_template_part('includes/parts/tour/single/trip_single_banner') ?>
	<!-- trip single banner -->


	<!-- relate tour -->
	<?php
	set_query_var('tour_type_id', $tour_type_id);
	require_once BookYourTravel_Theme_Utils::get_file_path('/includes/parts/tour/single/relate_tour.php');
	// get_template_part('includes/parts/tour/single/relate_tour')
	?>
	<!-- relate tour -->


	<!-- relate post -->
	<?php get_template_part('includes/parts/tour/single/relate_post') ?>
	<!-- relate post -->


</div>