<?php
/**
 * Accommodation list template part
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */




// custom

if(isset($_GET['cruise-type'])){
	$cruise_type_slug = $_GET['cruise-type'];
	$cruise_type = get_term_by('slug', $cruise_type_slug, 'cruise_type');
	if ($cruise_type) {
        $cruise_type_id = $cruise_type->term_id;
    } else {
        echo 'Tour type không tồn tại.';
    }
}

$cruise_type_ids = $cruise_type_id;
$ports = get_term_meta($cruise_type_ids, 'port', true);
$caption =  get_term_meta($cruise_type_ids, 'caption_port_pick_up', true);
$why_us = get_field('why_us', 'term_' . $cruise_type_ids);
set_query_var('cruise_type_ids', $cruise_type_ids);
set_query_var('ports', $ports);
set_query_var('caption', $caption);
set_query_var('why_us', $why_us);
// banner
get_template_part('includes/parts/cruise/banner');
// banner

// our port
get_template_part('includes/parts/cruise/our_port');
//  our port
// why us
get_template_part('includes/parts/cruise/why_us');
//  why us
// our cruise
get_template_part('includes/parts/cruise/our_cruise');
//  our cruise


// cruise port
get_template_part('includes/parts/cruise/cruise_port');
//  cruise port

// contact
get_template_part('includes/parts/cruise/contact');
//  contact