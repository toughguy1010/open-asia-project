<?php
/**
 * Inner nav template part
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */
 
global $bookyourtravel_theme_globals, $bookyourtravel_theme_of_custom, $post, $first_display_tab, $layout_class;

$location_extra_fields = $bookyourtravel_theme_globals->get_location_extra_fields();
$tab_array = $bookyourtravel_theme_globals->get_location_tabs();
$location_obj = new BookYourTravel_Location($post);

$enable_accommodations = $bookyourtravel_theme_globals->enable_accommodations();
$enable_car_rentals = $bookyourtravel_theme_globals->enable_car_rentals();
$enable_cruises = $bookyourtravel_theme_globals->enable_cruises();
$enable_tours = $bookyourtravel_theme_globals->enable_tours();

 ?>
<!--inner navigation-->
<nav class="inner-nav <?php echo $layout_class; ?>">
	<ul>
		<?php do_action( 'bookyourtravel_show_single_location_tab_items_before' ); ?>
		<?php
		$first_display_tab = '';			
		if (is_array($tab_array) && count($tab_array) > 0) {
			foreach ($tab_array as $tab) {
				if (isset($tab['id']) && (!isset($tab['hide']) || $tab['hide'] != '1')) {
					$tab_label = '';
					if (isset($tab['label'])) {
						$tab_label = $tab['label'];
						$tab_label = $bookyourtravel_theme_of_custom->get_translated_dynamic_string($bookyourtravel_theme_of_custom->get_option_id_context('location_tabs') . ' ' . $tab['label'], $tab_label);
					}
					$tab_icon_class = isset($tab['icon_class']) ? ' ' . $tab['icon_class'] : '';
					$tab_icon_class = trim($tab_icon_class);
					
					$tab_icon_html = '';
					if (!empty($tab_icon_class)) {
						$tab_icon_html = sprintf("<span class='material-icons'>%s</span>", $tab_icon_class);
						$tab_icon_html = apply_filters('bookyourtravel_tab_icon_html', $tab_icon_html, $tab_icon_class);
					}
					
					$tab_class = $tab['id'];
					
					if ($tab['id'] == 'sports_and_nature' || $tab['id'] == 'culture' || $tab['id'] == 'nightlife') {
						$field_id = $tab['id'] == 'culture' ? 'culture_and_history' : $tab['id'];
						if ($location_obj->get_custom_field($field_id) && strlen ($location_obj->get_custom_field($field_id)) > 0) {
							if(empty($first_display_tab)) $first_display_tab = $tab['id'];
							BookYourTravel_Theme_Of_Custom::the_tab("location", $tab_class, '',  sprintf('<a class="%s" href="#%s" title="%s">%s%s</a>', esc_attr($tab_icon_class), esc_attr($tab['id']), esc_attr($tab_label), $tab_icon_html, esc_html($tab_label)));
						}
					} elseif ($tab['id'] == 'general_info') {
						if(empty($first_display_tab)) $first_display_tab = $tab['id'];
						BookYourTravel_Theme_Of_Custom::the_tab("location", $tab_class, '',  sprintf('<a class="%s" href="#%s" title="%s">%s%s</a>', esc_attr($tab_icon_class), esc_attr($tab['id']), esc_attr($tab_label), $tab_icon_html, esc_html($tab_label)));
					} elseif ($tab['id'] == 'accommodations' && $enable_accommodations) {
						if(empty($first_display_tab)) $first_display_tab = $tab['id'];
						BookYourTravel_Theme_Of_Custom::the_tab("location", $tab_class, '',  sprintf('<a class="%s" href="#%s" title="%s">%s%s</a>', esc_attr($tab_icon_class), esc_attr($tab['id']), esc_attr($tab_label), $tab_icon_html, esc_html($tab_label)));
					} elseif ($tab['id'] == 'car_rentals' && $enable_car_rentals) {
						if(empty($first_display_tab)) $first_display_tab = $tab['id'];
						BookYourTravel_Theme_Of_Custom::the_tab("location", $tab_class, '',  sprintf('<a class="%s" href="#%s" title="%s">%s%s</a>', esc_attr($tab_icon_class), esc_attr($tab['id']), esc_attr($tab_label), $tab_icon_html, esc_html($tab_label)));
					} elseif ($tab['id'] == 'cruises' && $enable_cruises) {
						if(empty($first_display_tab)) $first_display_tab = $tab['id'];
						BookYourTravel_Theme_Of_Custom::the_tab("location", $tab_class, '',  sprintf('<a class="%s" href="#%s" title="%s">%s%s</a>', esc_attr($tab_icon_class), esc_attr($tab['id']), esc_attr($tab_label), $tab_icon_html, esc_html($tab_label)));
					} elseif ($tab['id'] == 'tours' && $enable_tours) {
						if(empty($first_display_tab)) $first_display_tab = $tab['id'];
						BookYourTravel_Theme_Of_Custom::the_tab("location", $tab_class, '',  sprintf('<a class="%s" href="#%s" title="%s">%s%s</a>', esc_attr($tab_icon_class), esc_attr($tab['id']), esc_attr($tab_label), $tab_icon_html, esc_html($tab_label)));
					} else {
						if(empty($first_display_tab)) $first_display_tab = $tab['id'];
						$tab_has_fields = BookYourTravel_Theme_Of_Custom::tab_has_fields('location_extra_fields', $location_extra_fields, $tab['id'], $location_obj);
						if ($tab_has_fields) {
							BookYourTravel_Theme_Of_Custom::the_tab("location", $tab_class, '',  sprintf('<a class="%s" href="#%s" title="%s">%s%s</a>', esc_attr($tab_icon_class), esc_attr($tab['id']), esc_attr($tab_label), $tab_icon_html, esc_html($tab_label)));
						}
					}
				}
			}
		} 
		?>
		<?php do_action( 'bookyourtravel_show_single_location_tab_items_after' ); ?>
	</ul>
</nav> 