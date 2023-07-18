<?php
global $found_post_content, $bookyourtravel_theme_globals, $location_extra_fields, $post, $entity_obj, $default_location_tabs, $layout_class, $tab, $location_obj;

$location_obj = new BookYourTravel_Location($post);
$entity_obj = $location_obj;

$location_id = get_the_ID();
$thumbnail_data = wp_get_attachment_image_src(get_post_thumbnail_id($location_id), 'full');
if ($thumbnail_data) {
    $thumbnail_url = $thumbnail_data[0];
} else {
    $thumbnail_url = '';
}
$hidden_gem = get_field('hidden_gem', get_the_ID());
$glance = get_field('glance', get_the_ID());
$faq = get_field('faq', get_the_ID());
$location_description = $location_obj->get_short_description();
set_query_var('location_description', $location_description);
set_query_var('thumbnail_url', $thumbnail_url);
set_query_var('hidden_gem', $hidden_gem);
set_query_var('glance', $glance);
set_query_var('location_id', $location_id);
set_query_var('faq', $faq);
// destination banner
get_template_part('includes/parts/location/single-parent/destination_banner');
// destination banner


// Hidden Gem
get_template_part('includes/parts/location/single-parent/hidden_gem');

// Hidden Gem

// Glance
get_template_part('includes/parts/location/single-parent/glance');
// Glance

// list child location
get_template_part('includes/parts/location/single-parent/list');
// list child location


// suggest tour
get_template_part('includes/parts/location/single-parent/suggest_tour');
// suggest tour


// FAQ
get_template_part('includes/parts/location/single-parent/faq');
// FAQ
?>

