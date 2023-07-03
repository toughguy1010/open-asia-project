<?php
global $bookyourtravel_theme_globals, $post, $first_display_tab, $default_tour_tabs, $entity_obj, $layout_class, $tab;

$tour_obj = new BookYourTravel_Tour($post);
$entity_obj = $tour_obj;

$tour_extra_fields = $bookyourtravel_theme_globals->get_tour_extra_fields();
$tab_array = $bookyourtravel_theme_globals->get_tour_tabs();	
$nav_layout = $bookyourtravel_theme_globals->get_tour_single_layout();

ob_start();		

get_template_part('includes/parts/tour/single/javascript', 'vars');

$js_vars = ob_get_contents();
ob_end_clean();		
echo $js_vars;

ob_start();

$layout_class = '';
if ($nav_layout == 'left') {
	get_template_part('includes/parts/tour/single/inner', 'nav');
} else if ($nav_layout == 'above') {
	$layout_class = 'layout-above';
	get_template_part('includes/parts/tour/single/inner', 'nav');
} else {
	$layout_class = 'layout-right';
	get_template_part('includes/parts/tour/single/inner', 'nav');		
}

$nav_output = ob_get_contents();

ob_end_clean();		

ob_start();

$featured_element = $tour_obj->get_displayed_featured_element();

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
		if ($tab['id'] == 'description') {
			$description_visible = true;
		}
		if (count(BookYourTravel_Theme_Utils::custom_array_search($default_tour_tabs, 'id', $tab['id'])) == 0) {
			$tab_has_fields = BookYourTravel_Theme_Of_Custom::tab_has_fields('tour_extra_fields', $tour_extra_fields, $tab['id'], $tour_obj);
			if ($tab_has_fields) {
				get_template_part('includes/parts/tour/single/tab', 'content');
			}
		} else {
			get_template_part('includes/parts/tour/single/tab', 'content');
		}
	}
}			

if ($nav_layout == 'right') {
	echo $nav_output;
}

$content_and_description_match = $tour_obj->content_and_description_match();

if (!$content_and_description_match || !$description_visible || $bookyourtravel_theme_globals->is_inline_vc_editor() || $bookyourtravel_theme_globals->is_inside_elementor_editor()) {
	get_template_part('includes/parts/post/single/post', 'content');
}

$output = ob_get_contents();

ob_end_clean();

echo $output;