<?php
global $found_post_content, $bookyourtravel_theme_globals, $location_extra_fields, $post, $entity_obj, $default_location_tabs, $layout_class, $tab, $location_obj;

$location_obj = new BookYourTravel_Location($post);
$entity_obj = $location_obj;
$display_as_directory = $location_obj->get_custom_field('display_as_directory');
$exclude_descendant_locations = $location_obj->get_custom_field('directory_exclude_descendant_locations');
$hide_item_titles = $location_obj->get_custom_field('directory_hide_item_titles');
$hide_item_images = $location_obj->get_custom_field('directory_hide_item_images');
$hide_item_descriptions = $location_obj->get_custom_field('directory_hide_item_descriptions');
$hide_item_actions = $location_obj->get_custom_field('directory_hide_item_actions');
$hide_item_counts = $location_obj->get_custom_field('directory_hide_item_counts');
$hide_item_ribbons = $location_obj->get_custom_field('directory_hide_item_ribbons');

ob_start();

get_template_part('includes/parts/location/single/javascript', 'vars');
$js_vars = ob_get_contents();

ob_end_clean();

echo $js_vars;

if ($display_as_directory) {
	global $location_list_args, $location_item_args;

	$paged = 1;
	if ( get_query_var('paged-byt') ) {
		$paged = get_query_var('paged-byt');
	} else if ( get_query_var('paged') ) {
		$paged = get_query_var('paged');
	} else if ( get_query_var('page') ) {
		$paged = get_query_var('page');
	}

    $posts_per_row = $location_obj->get_custom_field('directory_posts_per_row');
    $posts_per_row = $posts_per_row ? $posts_per_row : 3;

	$location_list_args = array(
		'parent_location_id' => $post->ID,
		'paged' => $paged,
		'display_mode' => 'card',
		'is_list_page' => true,
        'exclude_descendant_locations' => $exclude_descendant_locations,
        'posts_per_row' => $posts_per_row
	);

	$page_sidebar_positioning = $bookyourtravel_theme_globals->get_location_single_sidebar_position();
	$section_class = BookYourTravel_Theme_Utils::get_page_section_class($page_sidebar_positioning);

	$location_item_args = array(
        'hide_title' => $hide_item_titles,
        'hide_image' => $hide_item_images,
        'hide_description' => $hide_item_descriptions,
		'hide_actions' => $hide_item_actions,
		'hide_counts' => $hide_item_counts,
		'hide_ribbon' => $hide_item_ribbons
	);

	ob_start();

	$featured_element = $location_obj->get_displayed_featured_element();

	if ($featured_element == 'gallery') {
		get_template_part('includes/parts/post/single/post', 'gallery');
	} else {
		get_template_part('includes/parts/post/single/post', 'image');
	}

	global $echo_post_title;
	if (!has_post_thumbnail() || $featured_element == 'gallery') {
		$echo_post_title = true;
	}

	get_template_part('includes/parts/post/single/post', 'content');
	$echo_post_title = false;
	get_template_part('includes/parts/location/location', 'list');

	$output = ob_get_contents();

	ob_end_clean();
} else {

	ob_start();

	$location_extra_fields = $bookyourtravel_theme_globals->get_location_extra_fields();
	$tab_array = $bookyourtravel_theme_globals->get_location_tabs();
	$nav_layout = $bookyourtravel_theme_globals->get_location_single_layout();

	$layout_class = '';
	if ($nav_layout == 'left') {
		get_template_part('includes/parts/location/single/inner', 'nav');
	} else if ($nav_layout == 'above') {
		$layout_class = 'layout-above';
		get_template_part('includes/parts/location/single/inner', 'nav');
	} else {
		$layout_class = 'layout-right';
		get_template_part('includes/parts/location/single/inner', 'nav');
	}

	$nav_output = ob_get_contents();

	ob_end_clean();

	ob_start();

	$featured_element = $location_obj->get_displayed_featured_element();

	if ($featured_element == 'gallery') {
		get_template_part('includes/parts/post/single/post', 'gallery');
	} else {
		get_template_part('includes/parts/post/single/post', 'image');
	}

	if ($nav_layout == 'left' || $nav_layout == 'above' || empty($nav_layout)) {
		echo $nav_output;
	}

	$description_visible = false;
	foreach ($tab_array as $tab) {
		if (isset($tab['id']) && (!isset($tab['hide']) || $tab['hide'] != '1')) {
			if ($tab['id'] == 'general_info') {
				$description_visible = true;
			}
			if (count(BookYourTravel_Theme_Utils::custom_array_search($default_location_tabs, 'id', $tab['id'])) == 0) {
				$tab_has_fields = BookYourTravel_Theme_Of_Custom::tab_has_fields('location_extra_fields', $location_extra_fields, $tab['id'], $location_obj);
				if ($tab_has_fields) {
					get_template_part('includes/parts/location/single/tab', 'content');
				}
			} else {
				get_template_part('includes/parts/location/single/tab', 'content');
			}
		}
	}

	if ($nav_layout == 'right') {
		echo $nav_output;
	}

	$content_and_description_match = $location_obj->content_and_description_match();

	if (!$content_and_description_match || !$description_visible || $bookyourtravel_theme_globals->is_inline_vc_editor() || $bookyourtravel_theme_globals->is_inside_elementor_editor()) {
		get_template_part('includes/parts/post/single/post', 'content');
	}

	$output = ob_get_contents();
	ob_end_clean();
}

echo $output;
