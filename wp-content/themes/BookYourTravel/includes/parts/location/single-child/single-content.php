<?php 
global $found_post_content, $bookyourtravel_theme_globals, $location_extra_fields, $post, $entity_obj, $default_location_tabs, $layout_class, $tab, $location_obj;

$location_obj = new BookYourTravel_Location($post);
$entity_obj = $location_obj;


$location_id = get_the_ID();
$introduction = $location_obj->get_custom_field('introduction');
$map = $location_obj->get_custom_field('map');
$travel_guide = $location_obj->get_custom_field('travel_guide');
$places_of_interest = $location_obj->get_custom_field('places_of_interest');
set_query_var('location_id', $location_id);
set_query_var('introduction', $introduction);
set_query_var('map', $map);
set_query_var('travel_guide', $travel_guide);
set_query_var('places_of_interest', $places_of_interest);


// introduction
get_template_part('includes/parts/location/single-child/introduction');
// introduction


// travel_guide
get_template_part('includes/parts/location/single-child/travel_guide');
// travel_guide



// places_of_interest
get_template_part('includes/parts/location/single-child/places_of_interest');
// places_of_interest


// suggest tour
get_template_part('includes/parts/location/single-child/suggest_tour');
// suggest tour
?>