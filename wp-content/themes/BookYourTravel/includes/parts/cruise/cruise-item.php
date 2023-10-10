<?php
/**
 * Cruise item template part
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */

global $current_url, $date_from, $date_to, $guests, $cabins, $bookyourtravel_theme_globals, $bookyourtravel_cruise_helper, $cruise_list_args, $cruise_item_args;

if (empty($current_url)) {
	$current_url = BookYourTravel_Theme_Utils::get_current_page_url_no_query();
}

$price_decimal_places = $bookyourtravel_theme_globals->get_price_decimal_places();
$list_user_cruises_url = $bookyourtravel_theme_globals->get_list_user_cruises_url();
$submit_user_cruises_url = $bookyourtravel_theme_globals->get_submit_user_cruises_url();

$display_mode = isset($cruise_list_args['display_mode']) ? $cruise_list_args['display_mode'] : 'card';
$item_class = isset($cruise_item_args['item_class']) ? $cruise_item_args['item_class'] : '';

$cruise_id = $cruise_item_args['cruise_id'];
$cruise_permalink = '';
$cruise_description = '';
$cruise_title = '';
$cruise_thumbnail_html = '';
$cruise_ribbon_text = '';
$cruise_base_id = 0;
$cruise_status = '';
$cruise_locations = '';
$cruise_price = '';
$cruise_review_score = 0;
$link_external = false;
$cruise_use_referral_url = false;

if ($cruise_id > 0) {
	$cruise_post = $cruise_item_args['post'];
	global $post;
	if (!$post || $post->ID != $cruise_id) {
		$post = $cruise_post;
		setup_postdata($post);
	}

	$cruise_obj = new BookYourTravel_Cruise($post);
	$cruise_description = wpautop($cruise_obj->get_short_description());
	$cruise_ribbon_text = $cruise_obj->get_ribbon_text();
	$cruise_use_referral_url = $cruise_obj->use_referral_url();
	$cruise_referral_url = $cruise_obj->get_referral_url();
	$cruise_referral_price = $cruise_obj->get_referral_price();

	$thumbnail_id = get_post_thumbnail_id($post->ID);
	$attachment = get_post($thumbnail_id);
	if ($attachment) {
		$image_title = $attachment->post_title; //The Title
		$cruise_thumbnail_html = get_the_post_thumbnail($post->ID, "full", array('title' => $image_title));
	}

	if ($cruise_use_referral_url && !empty($cruise_referral_url)) {
		$cruise_permalink = $cruise_referral_url;
		$cruise_price = $cruise_referral_price;
		$link_external = true;
	} else {
		$cruise_permalink = get_the_permalink();

		if ($date_from || $date_to || $cabins || $guests) {
			if(strpos($cruise_permalink,'?') !== false) {
				$cruise_permalink .= sprintf("&from=%s&to=%s&guests=%d&cabins=%d", $date_from, $date_to, $guests, $cabins);
			} else {
				$cruise_permalink .= sprintf("?from=%s&to=%s&guests=%d&cabins=%d", $date_from, $date_to, $guests, $cabins);
			}
		}

		if ($bookyourtravel_theme_globals->show_static_prices_in_grids()) {
			$cruise_price = $cruise_obj->get_static_from_price();
		}
	}

	$cruise_title = get_the_title();
	$cruise_base_id = $cruise_obj->get_base_id();
	$cruise_status = $cruise_obj->get_status();
	$cruise_review_score = $cruise_obj->get_custom_field('review_score', false, true);
	$cruise_locations = $cruise_obj->get_formatted_locations();
	$cruise_address = $cruise_obj->get_custom_field('address');

} else {
	$cruise_description = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit';
	$cruise_title = 'Lorem ipsum';
	$dummy_image_source = BookYourTravel_Theme_Utils::get_file_uri('/images/uploads/img.jpg');
	$cruise_thumbnail_html = sprintf('<img src="%s" alt="Lorem ipsum">', $dummy_image_source);
}

$item_extra_class = '';
if ($bookyourtravel_theme_globals->show_static_prices_in_grids()) {
	$item_extra_class = 'skip-ajax-call';
}

?>
<div class="our_cruise-item" style= "min-height:350px">
	<a href="<?= $cruise_permalink ?>" class="cruise-thumb">
		<img src="<?=$attachment->guid ?>" alt="">
	</a>
	<div class="cruise-content">
		<div class="cruise-title">
			<?= $cruise_title ?>
		</div>
		<a href="<?= $cruise_permalink ?>"> view tour</a>
	</div>
</div>