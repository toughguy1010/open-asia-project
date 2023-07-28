<?php
global $bookyourtravel_theme_globals, $post, $first_display_tab, $default_cruise_tabs, $entity_obj, $layout_class, $tab, $bookyourtravel_theme_post_types;

$cruise_obj = new BookYourTravel_Cruise($post);
$entity_obj = $cruise_obj;

$cruise_extra_fields = $bookyourtravel_theme_globals->get_cruise_extra_fields();
$tab_array = $bookyourtravel_theme_globals->get_cruise_tabs();
$nav_layout = $bookyourtravel_theme_globals->get_cruise_single_layout();

ob_start();

get_template_part('includes/parts/cruise/single/javascript', 'vars');

$js_vars = ob_get_contents();
ob_end_clean();
echo $js_vars;


// custom

$cruise_title = get_the_title();
$cruise_duration = $cruise_obj->get_cruise_durations();
$cruise_description = wpautop($cruise_obj->get_short_description());
$cruise_facilities = $cruise_obj->get_facilities();
$cruise_type_id = $cruise_obj->get_type_id();
$cruise_type = get_term_by('id', $cruise_type_id, 'cruise_type');
$cruise_slug = $cruise_type->slug;
// $images = $cruise_obj->get_images();
// var_dump(count($images));
?>
<div class="single-breadcrumbs">
	<nav class="breadcrumbs nano-container">
		<ul>
			<li><a href="<?php echo home_url() ?>" title="Home">Home</a></li>
			<span class="separator"> » </span>
			<li><a href="<?php echo home_url() ?>/cruise-list/?cruise-type=<?= $cruise_slug ?>" title="Cruises">Cruises</a></li>
			<span class="separator"> » </span>
			<li><?= $cruise_title ?></li>
		</ul>
	</nav>
</div>

<div class="single-tour-header">
	<div class="nano-container single-tour-container ">
		<h1><?= $cruise_title ?></h1>
		<div class="single-tour-meta">
			<?php
			if ($cruise_duration) {
			?>
				<div class="tour_item-duraiton tour_item-text">
					<?php
					foreach ($cruise_duration as $cruise_duration) {
					?>
						<div class="item-duraiton">
							<img src="<?php echo get_template_directory_uri() ?>/css/images/ico__clock.png" alt="">
							<?= $cruise_duration->name  ?>
						</div>
					<?php
					}
					?>
				</div>
			<?php
			}
			if ($cruise_facilities) {
			?>
				<div class="tour_item-tags">
					<?php
					foreach ($cruise_facilities as $facilities) {
						$id = $facilities->term_id;
						$icon = get_field('icon', 'term_' . $id);

					?>
						<div class="item-tags facilities-tags ">
							<img width="27" src="<?= $icon ?>" alt="">
							<div class="tag-name tour_item-text">
								<?= $facilities->name  ?>
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

$featured_element = $cruise_obj->get_displayed_featured_element();

if ($featured_element == 'gallery') {
	get_template_part('includes/parts/post/single/post', 'gallery');
} else {
	get_template_part('includes/parts/post/single/post', 'image');
}

?>

<div class="single-tour-body">
	<div class="single-tour-nav-wraper">
		<div class="hamburger-lines">
			<span class="line line1"></span>
			<span class="line line2"></span>
			<span class="line line3"></span>
		</div>
		<div class="single-tour-nav-container nano-container">
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
	<?php get_template_part('includes/parts/cruise/single/tour-overview') ?>
	<!-- over view -->

	<!-- customizable itinerary -->
	<?php get_template_part('includes/parts/cruise/single/customizable_itinerary') ?>
	<!-- customizable itinerary -->

	<!-- useful information -->
	<?php get_template_part('includes/parts/cruise/single/useful_information') ?>
	<!-- useful information -->

	<!-- trip single banner -->
	<?php get_template_part('includes/parts/tour/single/trip_single_banner') ?>
	<!-- trip single banner -->


	<!-- relate tour -->
	<?php
	set_query_var('cruise_type_id', $cruise_type_id);
	require_once BookYourTravel_Theme_Utils::get_file_path('/includes/parts/tour/single/relate_tour.php');
	?>
	<!-- relate tour -->


	<!-- relate post -->
	<?php get_template_part('includes/parts/tour/single/relate_post') ?>
	<!-- relate post -->


</div>