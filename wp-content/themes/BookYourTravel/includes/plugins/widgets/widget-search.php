<?php
/**
 * The template for displaying the Search widget
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */

// Add function to widgets_init that'll load our widget.
add_action( 'widgets_init', 'bookyourtravel_search_widgets' );

// Register widget.
function bookyourtravel_search_widgets() {
	register_widget( 'bookyourtravel_search_widget' );
}

// Widget class.
class bookyourtravel_search_widget extends WP_Widget {

	/*-----------------------------------------------------------------------------------*/
	/*	Widget Setup
	/*-----------------------------------------------------------------------------------*/

	private $enable_reviews;
	private $enable_tours;
	private $enable_car_rentals;
	private $enable_accommodations;
	private $enable_cruises;
	private $widget_enable_search_for_accommodations;
	private $widget_enable_search_for_carrentals;
	private $widget_enable_search_for_cruises;
	private $widget_enable_search_for_tours;
	private $widget_uniqid;

	function __construct() {
		$this->widget_uniqid = uniqid();

		if (!is_admin()) {
			wp_register_script( 'bookyourtravel-search-widget', BookYourTravel_Theme_Utils::get_file_uri('/js/search-widget.js'), array('jquery', 'bookyourtravel-jquery-uniform', 'jquery-ui-spinner'), BOOKYOURTRAVEL_VERSION, true );
			wp_enqueue_script( 'bookyourtravel-search-widget' );
		}

		/* Widget settings. */
		$widget_ops = array( 'classname' => 'bookyourtravel_search_widget', 'description' => esc_html__('BookYourTravel: Search', 'bookyourtravel') );

		/* Widget control settings. */
		$control_ops = array( 'width' => 260, 'height' => 600, 'id_base' => 'bookyourtravel_search_widget' );

		/* Create the widget. */
		parent::__construct( 'bookyourtravel_search_widget', esc_html__('BookYourTravel: Search', 'bookyourtravel'), $widget_ops, $control_ops );

		global $bookyourtravel_theme_globals;

		$this->enable_reviews = $bookyourtravel_theme_globals->enable_reviews();
		$this->enable_tours = $bookyourtravel_theme_globals->enable_tours();
		$this->enable_car_rentals = $bookyourtravel_theme_globals->enable_car_rentals();
		$this->enable_accommodations = $bookyourtravel_theme_globals->enable_accommodations();
		$this->enable_cruises = $bookyourtravel_theme_globals->enable_cruises();
	}

	/*-----------------------------------------------------------------------------------*/
	/*	Display Widget
	/*-----------------------------------------------------------------------------------*/

	function widget( $args, $instance ) {

		global $bookyourtravel_theme_globals;

		extract( $args );

		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', isset($instance['title']) ? $instance['title'] : esc_html__('Refine search results', 'bookyourtravel') );

		$widget_width = isset($instance['widget_width']) ? $instance['widget_width'] : '';
		$widget_padding_left_right = isset($instance['widget_padding_left_right']) ? $instance['widget_padding_left_right'] : '';
		$widget_padding_top_bottom = isset($instance['widget_padding_top_bottom']) ? $instance['widget_padding_top_bottom'] : '';
		$widget_display_style = isset($instance['widget_display_style']) ? $instance['widget_display_style'] : 'dOver';
		$widget_position_x = isset($instance['widget_position_x']) ? $instance['widget_position_x'] : 'hCenter';
		$widget_position_y = isset($instance['widget_position_y']) ? $instance['widget_position_y'] : 'vMiddle';
		$widget_background_color = isset($instance['widget_background_color']) ? $instance['widget_background_color'] : '';
		$widget_text_color = isset($instance['widget_text_color']) ? $instance['widget_text_color'] : '';

		$widget_block_count = isset($instance['widget_block_count']) ? $instance['widget_block_count'] : 1;
		$widget_accommodation_filter_count = isset($instance['widget_accommodation_filter_count']) ? $instance['widget_accommodation_filter_count'] : 0;
		$widget_carrental_filter_count = isset($instance['widget_carrental_filter_count']) ? $instance['widget_carrental_filter_count'] : 0;
		$widget_cruise_filter_count = isset($instance['widget_cruise_filter_count']) ? $instance['widget_cruise_filter_count'] : 0;
		$widget_generic_filter_count = isset($instance['widget_generic_filter_count']) ? $instance['widget_generic_filter_count'] : 0;
		$widget_location_filter_count = isset($instance['widget_location_filter_count']) ? $instance['widget_location_filter_count'] : 0;
		$widget_tour_filter_count = isset($instance['widget_tour_filter_count']) ? $instance['widget_tour_filter_count'] : 0;

		$widget_blocks = array();
		for ($i = 1; $i <= $widget_block_count; $i++) {
			$widget_blocks[] = array(
				'index' => isset($instance['widget_block_index_' . $i]) ? intval($instance['widget_block_index_' . $i]) : 1,
				'width' => isset($instance['widget_block_width_' . $i]) ? $instance['widget_block_width_' . $i] : 'full-width',
				'order' => isset($instance['widget_block_order_' . $i]) ? intval($instance['widget_block_order_' . $i]) : 1
			);
		}

		$accommodation_filters = $this->populate_filters_array('accommodation', $widget_accommodation_filter_count, $instance);
		$carrental_filters = $this->populate_filters_array('carrental', $widget_carrental_filter_count, $instance);
		$cruise_filters = $this->populate_filters_array('cruise', $widget_cruise_filter_count, $instance);
		$location_filters = $this->populate_filters_array('location', $widget_location_filter_count, $instance);
		$tour_filters = $this->populate_filters_array('tour', $widget_tour_filter_count, $instance);
		$generic_filters = $this->populate_filters_array('generic', $widget_generic_filter_count, $instance);

		$widget_home_page_only = isset($instance['widget_home_page_only']) ? BookYourTravel_Theme_Utils::parseBool($instance['widget_home_page_only']) : false;

		$this->widget_enable_search_for_accommodations = isset($instance['widget_enable_search_for_accommodations']) ? BookYourTravel_Theme_Utils::parseBool($instance['widget_enable_search_for_accommodations']) : false;
		$this->widget_enable_search_for_carrentals = isset($instance['widget_enable_search_for_carrentals']) ? BookYourTravel_Theme_Utils::parseBool($instance['widget_enable_search_for_carrentals']) : false;
		$this->widget_enable_search_for_cruises = isset($instance['widget_enable_search_for_cruises']) ? BookYourTravel_Theme_Utils::parseBool($instance['widget_enable_search_for_cruises']) : false;
		$this->widget_enable_search_for_tours = isset($instance['widget_enable_search_for_tours']) ? BookYourTravel_Theme_Utils::parseBool($instance['widget_enable_search_for_tours']) : false;

		$widget_limit_page_ids = isset($instance['widget_limit_page_ids']) ? trim($instance['widget_limit_page_ids']) : '';
		$limit_page_ids = explode(",", $widget_limit_page_ids);

		$widget_search_for_width = isset($instance['widget_search_for_width']) ? $instance['widget_search_for_width'] : 'full-width';
		$widget_search_for_label = isset($instance['widget_search_for_label']) ? $instance['widget_search_for_label'] : esc_html__('What?', 'bookyourtravel');
		$widget_submit_button_text = isset($instance['widget_submit_button_text']) ? $instance['widget_submit_button_text'] : esc_html__('Search again', 'bookyourtravel');
		$widget_submit_button_width = isset($instance['widget_submit_button_width']) ? $instance['widget_submit_button_width'] : 'full-width';
		$widget_submit_button_order = isset($instance['widget_submit_button_order']) ? intval($instance['widget_submit_button_order']) : 10;

		$widget_show_clear_button = isset($instance['widget_show_clear_button']) ? BookYourTravel_Theme_Utils::parseBool($instance['widget_show_clear_button']) : false;
		$widget_clear_button_text = isset($instance['widget_clear_button_text']) ? $instance['widget_clear_button_text'] : esc_html__('Clear filters', 'bookyourtravel');
		$widget_clear_button_width = isset($instance['widget_clear_button_width']) ? $instance['widget_clear_button_width'] : 'full-width';
		$widget_clear_button_order = isset($instance['widget_clear_button_order']) ? intval($instance['widget_clear_button_order']) : 11;

		$search_page_id = 0;
		$permalinks_enabled = $bookyourtravel_theme_globals->permalinks_enabled();
		if (!$permalinks_enabled) {
			$search_page_id = $bookyourtravel_theme_globals->get_search_results_page_id();
		}

		$home_page_id = $bookyourtravel_theme_globals->get_home_page_id();
		$front_page_id = get_option( 'page_on_front' );


		$widget_search_results_page_id = isset($instance['widget_search_results_page']) ? intval($instance['widget_search_results_page']) : 0;
		$search_results_page_url = '';
		if ($widget_search_results_page_id > 0) {
			$search_results_page_url = get_permalink($widget_search_results_page_id);
		} else {
			$search_results_page_url = $bookyourtravel_theme_globals->get_custom_search_results_page_url();
		}

		global $post;
		if ($post && isset($post->ID) && (empty($widget_limit_page_ids) || in_array($post->ID, $limit_page_ids))) {

			if ($widget_home_page_only && ($post && $home_page_id != $post->ID && $front_page_id != $post->ID)) {

			} else {

				/* Before widget (defined by themes). */
				echo $before_widget;

				/* Display Widget */
				/* Display the widget title if one was input (before and after defined by themes). */
				?>
					<article class="byt-widget-search <?php echo esc_attr($this->render_widget_classes($widget_position_x, $widget_position_y, $widget_display_style)); ?>" <?php echo esc_attr($this->render_widget_outer_styles($widget_padding_top_bottom, $widget_padding_left_right)); ?>>
						<div class="byt-widget-search-inner" <?php echo esc_attr($this->render_widget_inner_styles($widget_background_color, $widget_text_color, $widget_width)); ?>>
							<form class="widget-search" method="get" action="<?php echo esc_url($search_results_page_url); ?>">
								<?php if ($search_page_id > 0) { ?>
								<input type="hidden" name="page_id" value="<?php echo esc_attr($search_page_id); ?>" />
								<?php } ?>
								<?php
								if ( $title ) {
									echo $before_title . $title . $after_title;
								}

								$total_blocks = count($widget_blocks);

								foreach ($widget_blocks as $widget_block) {
									$block_index = $widget_block['index'];
									$this->render_block_start($widget_block['order'], $widget_block['width'], $block_index);

									if ($block_index == 1) {
										$this->render_what_filter_controls($this->id, $widget_search_for_label, $widget_search_for_width);
									}

									$this->render_filter_controls($this->id, 'accommodation', $accommodation_filters, $block_index);
									$this->render_filter_controls($this->id, 'car_rental', $carrental_filters, $block_index);
									$this->render_filter_controls($this->id, 'cruise', $cruise_filters, $block_index);
									$this->render_filter_controls($this->id, 'location', $location_filters, $block_index);
									$this->render_filter_controls($this->id, 'tour', $tour_filters, $block_index);
									$this->render_filter_controls($this->id, 'generic', $generic_filters, $block_index);

									if ($block_index == $total_blocks) {
										$this->render_submit_button($this->id, $widget_submit_button_text, $widget_submit_button_width, $widget_submit_button_order, $block_index);
										if ($widget_show_clear_button) {
											$this->render_clear_button($this->id, $search_results_page_url, $widget_clear_button_text, $widget_clear_button_width, $widget_clear_button_order, $block_index);											
										}
									}

									$this->render_block_end();
								}
								?>
							</form>
						</div>
					</article>
				<?php

				/* After widget (defined by themes). */
				echo $after_widget;
			}
		}
	}

	function render_widget_classes($widget_position_x, $widget_position_y, $widget_display_style) {
		$classes = '';

		if (isset($widget_position_x)) {
			$classes .= $widget_position_x . ' ';
		}
		if (isset($widget_position_y)) {
			$classes .= $widget_position_y . ' ';
		}
		if (isset($widget_display_style)) {
			$classes .= $widget_display_style . ' ';
		}
		return $classes;
	}

	function render_widget_outer_styles($padding_top_bottom, $padding_left_right) {
		$style = '';
		if (isset($padding_top_bottom) && $padding_top_bottom > 0) {
			$style .= 'padding-top:' . $padding_top_bottom . 'px;';
			$style .= 'padding-bottom:' . $padding_top_bottom . 'px;';
		}
		if (isset($padding_left_right) && $padding_left_right > 0) {
			$style .= 'padding-left:' . $padding_left_right . 'px;';
			$style .= 'padding-right:' . $padding_left_right . 'px;';
		}

		if (!empty($style)) {
			$style = sprintf(" style=%s ", $style);
		}

		return $style;
	}

	function render_widget_inner_styles($background_color, $text_color, $width) {
		$style = '';
		if (isset($background_color) && !empty($background_color)) {
			$style .= 'background-color:' . $background_color . ';';
		}
		if (isset($text_color) && !empty($text_color)) {
			$style .= 'color:' . $text_color . ';';
		}
		if (isset($width) && $width > 0) {
			$style .= 'width:' . $width . '%;';
		}

		if (!empty($style)) {
			$style = sprintf(" style=%s ", $style);
		}

		return $style;
	}

	function render_submit_button($widget_id, $text, $width, $order, $block_index) {
		echo sprintf('<div class="filter filter-block-%d filter-order-%d filter-type-submit %s">', $block_index, $order, $width);
		echo sprintf("<input type='submit' value='%s' class='gradient-button' id='%s_search-submit' />", $text, $widget_id);
		echo '</div>';
	}

	function render_clear_button($widget_id, $search_results_page_url, $text, $width, $order, $block_index) {
		$requested_what = isset($_REQUEST["what"]) ? intval($_REQUEST["what"]) : 1;
		$search_results_page_url = add_query_arg( "what=$requested_what", '', $search_results_page_url );

		echo sprintf('<div class="filter filter-block-%d filter-order-%d filter-type-clear %s">', $block_index, $order, $width);
		echo sprintf("<a href='%s' class='gradient-button' id='%s_search-reset'>%s</a>", $search_results_page_url, $widget_id, $text);
		echo '</div>';
	}

	function render_what_filter_controls($widget_id, $filter_label, $filter_width) {

		ob_start();

		echo sprintf('<div class="filter filter-block-1 filter-order-1 filter-type-what %s">', $filter_width);
		echo '<span class="label">' . $filter_label . '</span>';

		$filter_start = ob_get_clean();

		$requested_what = isset($_REQUEST["what"]) ? intval($_REQUEST["what"]) : 1;
		$requested_what = $requested_what > 0 ? $requested_what : 1;

		ob_start();
		$found = 0;
		if ($this->widget_enable_search_for_accommodations) {
			$found++;
			$checked = $requested_what == 1 ? "checked" : "";
			echo "<div class='radio-wrap'>";
			echo sprintf('<input %s type="radio" id="%s-what-accommodation" name="what" value="1">', $checked, $widget_id);
			echo sprintf('<label for="%s-what-accommodation">' . __('Accommodation', 'bookyourtravel') . '</label>', $widget_id);
			echo '</div>';
		}

		if ($this->widget_enable_search_for_carrentals) {
			$checked = $requested_what == 2 || $found == 0 ? "checked" : "";
			$found++;
			echo "<div class='radio-wrap'>";
			echo sprintf('<input %s type="radio" id="%s-what-carrental" name="what" value="2">', $checked, $widget_id);
			echo sprintf('<label for="%s-what-carrental">' . __('Rent a car', 'bookyourtravel') . '</label>', $widget_id);
			echo '</div>';
		}

		if ($this->widget_enable_search_for_cruises) {
			$checked = $requested_what == 3 || $found == 0 ? "checked" : "";
			$found++;
			echo "<div class='radio-wrap'>";
			echo sprintf('<input %s type="radio" id="%s-what-cruise" name="what" value="3">', $checked, $widget_id);
			echo sprintf('<label for="%s-what-cruise">' . __('Cruise', 'bookyourtravel') . '</label>', $widget_id);
			echo '</div>';
		}

		if ($this->widget_enable_search_for_tours) {
			$checked = $requested_what == 4 || $found == 0 ? "checked" : "";
			$found++;
			echo "<div class='radio-wrap'>";
			echo sprintf('<input %s type="radio" id="%s-what-tour" name="what" value="4">', $checked, $widget_id);
			echo sprintf('<label for="%s-what-tour">' . __('Tour', 'bookyourtravel') . '</label>', $widget_id);
			echo '</div>';
		}

		if ($found == 0) {
			$found++;
			echo "<div class='radio-wrap'>";
			echo sprintf('<input checked type="radio" id="%s-what-accommodation" name="what" value="1">', $widget_id);
			echo sprintf('<label for="%s-what-accommodation">' . __('Accommodation', 'bookyourtravel') . '</label>', $widget_id);
			echo '</div>';
		}

		$what_filter = ob_get_clean();

		if ($found == 1) {
			// if there is only one display filter, hide it.
			$filter_start = str_replace('<div class="filter', '<div style="display:none;" class="filter', $filter_start);
		}

		echo $filter_start;
		echo $what_filter;
		echo "</div>";
	}

	function render_filter_controls($widget_id, $filter_entity_type, $filters, $block_id) {
		if (isset($filters[$block_id])) {
			$internal_index = 0;
			foreach($filters[$block_id] as $fi => $filter) {
				$order = $filter['order'];
				$label = $filter['label'];
				$type = $filter['type'];
				$width = $filter['width'];

				$filter_entity_class = sprintf('filter-%s', $filter_entity_type);
				$filter_entity_classes = '';
				if (isset($filter['show_for_accommodations']) && $filter['show_for_accommodations']) {
					$filter_entity_classes .= ' filter-accommodation';
				}
				if (isset($filter['show_for_car_rentals']) && $filter['show_for_car_rentals']) {
					$filter_entity_classes .= ' filter-car_rental';
				}
				if (isset($filter['show_for_cruises']) && $filter['show_for_cruises']) {
					$filter_entity_classes .= ' filter-cruise';
				}
				if (isset($filter['show_for_tours']) && $filter['show_for_tours']) {
					$filter_entity_classes .= ' filter-tour';
				}
				if ($filter_entity_classes == '') {
					$filter_entity_classes = $filter_entity_class;
				}

				echo sprintf('<div class="filter filter-group filter-block-%d filter-order-%d filter-type-%s %s %s">', $block_id, $order, $type, $width, $filter_entity_classes);

				switch ($type) {
					case 'accommodation-tag-radios':
						$request_values = BookYourTravel_Theme_Utils::retrieve_array_of_values_from_query_string('accommodation_tags', true);
						echo '<span class="label">' . $label . '</span>';
						$this->render_filter_taxonomy_radios($widget_id, 'acc_tag', 'accommodation_tags[]', $request_values);
						break;
					case 'carrental-tag-radios':
						$request_values = BookYourTravel_Theme_Utils::retrieve_array_of_values_from_query_string('car_rental_tags', true);
						echo '<span class="label">' . $label . '</span>';
						$this->render_filter_taxonomy_radios($widget_id, 'car_rental_tag', 'car_rental_tags[]', $request_values);
						break;
					case 'cruise-tag-radios':
						$request_values = BookYourTravel_Theme_Utils::retrieve_array_of_values_from_query_string('cruise_tags', true);
						echo '<span class="label">' . $label . '</span>';
						$this->render_filter_taxonomy_radios($widget_id, 'cruise_tag', 'cruise_tags[]', $request_values);
						break;
					case 'tour-tag-radios':
						$request_values = BookYourTravel_Theme_Utils::retrieve_array_of_values_from_query_string('tour_tags', true);
						echo '<span class="label">' . $label . '</span>';
						$this->render_filter_taxonomy_radios($widget_id, 'tour_tag', 'tour_tags[]', $request_values);
						break;

					case 'accommodation-tag-select':
						$request_value = isset($_GET['accommodation_tag']) && !empty($_GET['accommodation_tag']) ? intval($_GET['accommodation_tag']) : 0;
						echo '<span class="label">' . $label . '</span>';
						$this->render_filter_taxonomy_select($widget_id, 'acc_tag', 'accommodation_tag', $request_value);
						break;
					case 'carrental-tag-select':
						$request_value = isset($_GET['car_rental_tag']) && !empty($_GET['car_rental_tag']) ? intval($_GET['car_rental_tag']) : 0;
						echo '<span class="label">' . $label . '</span>';
						$this->render_filter_taxonomy_select($widget_id, 'car_rental_tag', 'car_rental_tag', $request_value);
						break;
					case 'cruise-tag-select':
						$request_value = isset($_GET['cruise_tag']) && !empty($_GET['cruise_tag']) ? intval($_GET['cruise_tag']) : 0;
						echo '<span class="label">' . $label . '</span>';
						$this->render_filter_taxonomy_select($widget_id, 'cruise_tag', 'cruise_tag', $request_value);
						break;
					case 'tour-tag-select':
						$request_value = isset($_GET['tour_tag']) && !empty($_GET['tour_tag']) ? intval($_GET['tour_tag']) : 0;
						echo '<span class="label">' . $label . '</span>';
						$this->render_filter_taxonomy_select($widget_id, 'tour_tag', 'tour_tag', $request_value);
						break;

					case 'accommodation-tag-checkboxes':
						$request_values = BookYourTravel_Theme_Utils::retrieve_array_of_values_from_query_string('accommodation_tags', true);
						echo '<span class="label">' . $label . '</span>';
						$this->render_filter_taxonomy_checkboxes($widget_id, 'acc_tag', 'accommodation_tags[]', $request_values);
						break;
					case 'carrental-tag-checkboxes':
						$request_values = BookYourTravel_Theme_Utils::retrieve_array_of_values_from_query_string('car_rental_tags', true);
						echo '<span class="label">' . $label . '</span>';
						$this->render_filter_taxonomy_checkboxes($widget_id, 'car_rental_tag', 'car_rental_tags[]', $request_values);
						break;
					case 'cruise-tag-checkboxes':
						$request_values = BookYourTravel_Theme_Utils::retrieve_array_of_values_from_query_string('cruise_tags', true);
						echo '<span class="label">' . $label . '</span>';
						$this->render_filter_taxonomy_checkboxes($widget_id, 'cruise_tag', 'cruise_tags[]', $request_values);
						break;
					case 'tour-tag-checkboxes':
						$request_values = BookYourTravel_Theme_Utils::retrieve_array_of_values_from_query_string('tour_tags', true);
						echo '<span class="label">' . $label . '</span>';
						$this->render_filter_taxonomy_checkboxes($widget_id, 'tour_tag', 'tour_tags[]', $request_values);
						break;
					case 'accommodation-type-select':
						$request_value = isset($_GET['accommodation_type']) && !empty($_GET['accommodation_type']) ? intval($_GET['accommodation_type']) : 0;
						echo '<span class="label">' . $label . '</span>';
						$this->render_filter_taxonomy_select($widget_id, 'accommodation_type', 'accommodation_type', $request_value);
						break;
					case 'carrental-type-select':
						$request_value = isset($_GET['car_type']) && !empty($_GET['car_type']) ? intval($_GET['car_type']) : 0;
						echo '<span class="label">' . $label . '</span>';
						$this->render_filter_taxonomy_select($widget_id, 'car_type', 'car_type', $request_value);
						break;
					case 'cruise-type-select':
						$request_value = isset($_GET['cruise_type']) && !empty($_GET['cruise_type']) ? intval($_GET['cruise_type']) : 0;
						echo '<span class="label">' . $label . '</span>';
						$this->render_filter_taxonomy_select($widget_id, 'cruise_type', 'cruise_type', $request_value);
						break;
					case 'cruise-duration-select':
						$request_value = isset($_GET['cruise_duration']) && !empty($_GET['cruise_duration']) ? intval($_GET['cruise_duration']) : 0;
						echo '<span class="label">' . $label . '</span>';
						$this->render_filter_taxonomy_select($widget_id, 'cruise_duration', 'cruise_duration', $request_value);
						break;						
					case 'tour-type-select':
						$request_value = isset($_GET['tour_type']) && !empty($_GET['tour_type']) ? intval($_GET['tour_type']) : 0;
						echo '<span class="label">' . $label . '</span>';
						$this->render_filter_taxonomy_select($widget_id, 'tour_type', 'tour_type', $request_value);
						break;
					case 'tour-duration-select':
						$request_value = isset($_GET['tour_duration']) && !empty($_GET['tour_duration']) ? intval($_GET['tour_duration']) : 0;
						echo '<span class="label">' . $label . '</span>';
						$this->render_filter_taxonomy_select($widget_id, 'tour_duration', 'tour_duration', $request_value);
						break;						
					case 'accommodation-type-radios':
						$request_values = BookYourTravel_Theme_Utils::retrieve_array_of_values_from_query_string('accommodation_types', true);
						echo '<span class="label">' . $label . '</span>';
						$this->render_filter_taxonomy_radios($widget_id, 'accommodation_type', 'accommodation_types[]', $request_values);
						break;
					case 'carrental-type-radios':
						$request_values = BookYourTravel_Theme_Utils::retrieve_array_of_values_from_query_string('car_types', true);
						echo '<span class="label">' . $label . '</span>';
						$this->render_filter_taxonomy_radios($widget_id, 'car_type', 'car_types[]', $request_values);
						break;
					case 'cruise-type-radios':
						$request_values = BookYourTravel_Theme_Utils::retrieve_array_of_values_from_query_string('cruise_types', true);
						echo '<span class="label">' . $label . '</span>';
						$this->render_filter_taxonomy_radios($widget_id, 'cruise_type', 'cruise_types[]', $request_values);
						break;
					case 'cruise-duration-radios':
						$request_values = BookYourTravel_Theme_Utils::retrieve_array_of_values_from_query_string('cruise_durations', true);
						echo '<span class="label">' . $label . '</span>';
						$this->render_filter_taxonomy_radios($widget_id, 'cruise_duration', 'cruise_durations[]', $request_values);
						break;						
					case 'tour-type-radios':
						$request_values = BookYourTravel_Theme_Utils::retrieve_array_of_values_from_query_string('tour_types', true);
						echo '<span class="label">' . $label . '</span>';
						$this->render_filter_taxonomy_radios($widget_id, 'tour_type', 'tour_types[]', $request_values);
						break;
					case 'tour-duration-radios':
						$request_values = BookYourTravel_Theme_Utils::retrieve_array_of_values_from_query_string('tour_durations', true);
						echo '<span class="label">' . $label . '</span>';
						$this->render_filter_taxonomy_radios($widget_id, 'tour_duration', 'tour_durations[]', $request_values);
						break;						
					case 'accommodation-type-checkboxes':
						$request_values = BookYourTravel_Theme_Utils::retrieve_array_of_values_from_query_string('accommodation_types', true);
						echo '<span class="label">' . $label . '</span>';
						$this->render_filter_taxonomy_checkboxes($widget_id, 'accommodation_type', 'accommodation_types[]', $request_values);
						break;
					case 'carrental-type-checkboxes':
						$request_values = BookYourTravel_Theme_Utils::retrieve_array_of_values_from_query_string('car_types', true);
						echo '<span class="label">' . $label . '</span>';
						$this->render_filter_taxonomy_checkboxes($widget_id, 'car_type', 'car_types[]', $request_values);
						break;
					case 'cruise-type-checkboxes':
						$request_values = BookYourTravel_Theme_Utils::retrieve_array_of_values_from_query_string('cruise_types', true);
						echo '<span class="label">' . $label . '</span>';
						$this->render_filter_taxonomy_checkboxes($widget_id, 'cruise_type', 'cruise_types[]', $request_values);
						break;
					case 'cruise-duration-checkboxes':
						$request_values = BookYourTravel_Theme_Utils::retrieve_array_of_values_from_query_string('cruise_durations', true);
						echo '<span class="label">' . $label . '</span>';
						$this->render_filter_taxonomy_checkboxes($widget_id, 'cruise_duration', 'cruise_durations[]', $request_values);
						break;
					case 'tour-type-checkboxes':
						$request_values = BookYourTravel_Theme_Utils::retrieve_array_of_values_from_query_string('tour_types', true);
						echo '<span class="label">' . $label . '</span>';
						$this->render_filter_taxonomy_checkboxes($widget_id, 'tour_type', 'tour_types[]', $request_values);
						break;
					case 'tour-duration-checkboxes':
						$request_values = BookYourTravel_Theme_Utils::retrieve_array_of_values_from_query_string('tour_durations', true);
						echo '<span class="label">' . $label . '</span>';
						$this->render_filter_taxonomy_checkboxes($widget_id, 'tour_duration', 'tour_durations[]', $request_values);
						break;
					case 'accommodation-name':
						$search_term = isset($_GET['term']) ? sanitize_text_field($_GET['term']) : '';
						$this->render_filter_post_name_input($widget_id, $type, $label, 'term', $search_term);
						break;
					case 'carrental-name':
						$search_term = isset($_GET['term']) ? sanitize_text_field($_GET['term']) : '';
						$this->render_filter_post_name_input($widget_id, $type, $label, 'term', $search_term);
						break;
					case 'cruise-name':
						$search_term = isset($_GET['term']) ? sanitize_text_field($_GET['term']) : '';
						$this->render_filter_post_name_input($widget_id, $type, $label, 'term', $search_term);
						break;
					case 'tour-name':
						$search_term = isset($_GET['term']) ? sanitize_text_field($_GET['term']) : '';
						$this->render_filter_post_name_input($widget_id, $type, $label, 'term', $search_term);
						break;
					case 'accommodation-name-select':
						$search_term = isset($_GET['term']) ? sanitize_text_field($_GET['term']) : '';
						$this->render_filter_post_name_select($widget_id, $type, $label, 'term', $search_term);
						break;
					case 'carrental-name-select':
						$search_term = isset($_GET['term']) ? sanitize_text_field($_GET['term']) : '';
						$this->render_filter_post_name_select($widget_id, $type, $label, 'term', $search_term);
						break;
					case 'cruise-name-select':
						$search_term = isset($_GET['term']) ? sanitize_text_field($_GET['term']) : '';
						$this->render_filter_post_name_select($widget_id, $type, $label, 'term', $search_term);
						break;
					case 'tour-name-select':
						$search_term = isset($_GET['term']) ? sanitize_text_field($_GET['term']) : '';
						$this->render_filter_post_name_select($widget_id, $type, $label, 'term', $search_term);
						break;						
					case 'location-by-type':
						$type_ids = $filter['type_limiter'];
						$override_id = $filter['override_id'];
						$request_values = BookYourTravel_Theme_Utils::retrieve_array_of_values_from_query_string('l', true);
						$this->render_filter_location_by_type_select($widget_id, $label, 'l[]', $type_ids, $request_values, $internal_index, $override_id);
						break;
					case 'location-select':
						$request_value = isset($_GET['l']) && !empty($_GET['l']) ? intval($_GET['l']) : 0;
						$this->render_filter_location_select($widget_id, $label, 'l', $request_value, $internal_index);
						break;
					case 'facility-checkboxes':
						$request_values = BookYourTravel_Theme_Utils::retrieve_array_of_values_from_query_string('facilities', true);
						echo '<span class="label">' . $label . '</span>';
						$this->render_filter_taxonomy_checkboxes($widget_id, 'facility', 'facilities[]', $request_values);
						break;
					case 'calendar-date-from':
						$date_from = isset($_GET['from']) && !empty($_GET['from']) ? date('Y-m-d', strtotime(sanitize_text_field($_GET['from']))) : '';
						$this->render_filter_date_datepicker($widget_id, $type, 'from', $label, $order, $date_from, $internal_index);
						break;
					case 'calendar-date-to':
						$date_to = isset($_GET['to']) && !empty($_GET['to'])  ? date('Y-m-d', strtotime(sanitize_text_field($_GET['to']))) : '';
						$this->render_filter_date_datepicker($widget_id, $type, 'to', $label, $order, $date_to, $internal_index);
						break;
					case 'user-rating-slider':
						$rating = $this->get_request_int_value('rating', 0, 10, 0);
						echo '<span class="label">' . $label . '</span>';
						$this->render_filter_slider($widget_id, $type, 'rating', 0, 10, 1, $rating, $order);
						break;
					case 'star-rating-slider':
						$stars = $this->get_request_int_value('stars', 0, 5, 0);
						echo '<span class="label">' . $label . '</span>';
						$this->render_filter_stars($widget_id, $type, 'stars', 5, $stars, $order);
						break;
					case 'price-range-checkboxes':
						$request_prices = BookYourTravel_Theme_Utils::retrieve_array_of_values_from_query_string('price', true);
						echo '<span class="label">' . $label . '</span>';
						$this->render_filter_price_range_checkboxes($widget_id, $type, 'price', 'price[]', $order, $request_prices);
						break;
					case 'accommodation-room-count':
						$rooms = $this->get_request_int_value('rooms', 1, 20, 1);
						$this->render_filter_number_select($widget_id, $type, 'rooms', $label, $order, 1, 20, $rooms);
						break;
					case 'cruise-cabin-count':
						$cabins = $this->get_request_int_value('cabins', 1, 20, 1);
						$this->render_filter_number_select($widget_id, $type, 'cabins', $label, $order, 1, 20, $cabins);
						break;
					default:
						// echo $type;
						break;
				}

				echo '</div>';

				$internal_index++;
			}
		}
	}

	function get_request_int_value($name, $min, $max, $default) {
		if (isset($_REQUEST[$name])) {
			$val = intval($_REQUEST[$name]);
			$val = $val >= $min ? $val : $default;
			$val = $val <= $max || $max == 0 ? $val : $default;
			return $val;
		}
		return $default;
	}

	function render_filter_price_range_checkboxes($widget_id, $filter_type, $filter_part_id, $filter_name, $block_order, $values) {
		global $bookyourtravel_theme_globals;

		$price_decimal_places = $bookyourtravel_theme_globals->get_price_decimal_places();
		$default_currency_symbol = $bookyourtravel_theme_globals->get_default_currency_symbol();
		$show_currency_symbol_after = $bookyourtravel_theme_globals->show_currency_symbol_after();
		$price_range_bottom = $bookyourtravel_theme_globals->get_price_range_bottom();
		$price_range_increment = $bookyourtravel_theme_globals->get_price_range_increment();
		$price_range_count = $bookyourtravel_theme_globals->get_price_range_count();

		if ($price_range_count > 0) {
			$bottom = 0;
			$top = 0;
			$out = '';
			for ( $i = 0; $i < $price_range_count; $i++ ) {
				$price_index = $i + 1;
				$bottom = ($i * $price_range_increment) + $price_range_bottom;
				$top = (($price_index) * $price_range_increment) + $price_range_bottom - 1;

				$bottom = BookYourTravel_Theme_Utils::get_price_in_current_currency($bottom);
				$top = BookYourTravel_Theme_Utils::get_price_in_current_currency($top);

				echo '<div class="checkbox-wrap">';
				$checked = in_array($price_index, $values) ? "checked" : "";
				echo sprintf('<input %s type="checkbox" id="price_%d_%s_%d" name="%s" value="%d" />', $checked, $i, $widget_id . '_' . $filter_type . '_' . $filter_part_id, $block_order, $filter_name, $price_index);
				echo sprintf('<label for="price_%d_%s_%d">', $i, $widget_id . '_' . $filter_type . '_' . $filter_part_id, $block_order);
				echo number_format_i18n( $bottom, $price_decimal_places );
				if ($i == ($price_range_count-1)) {
					echo ' <span class="curr">' . $default_currency_symbol . '</span> +';
				} else {
					echo " - " . number_format_i18n( $top, $price_decimal_places ) . ' <span class="curr">' . $default_currency_symbol . '</span>';
				}
				echo '</label>';
				echo '</div>';
			}
		}
	}

	function get_unique_widget_id($widget_id, $filter_type, $filter_name) {
		return $this->widget_uniqid . '_' . $widget_id . '_' . $filter_type . '_' . $filter_name;
	}

	function render_filter_stars($widget_id, $filter_type, $filter_name, $count, $value, $block_order) {
		echo "<script>";
		echo "
			if (window.searchWidgetStarFilters === undefined) {
				window.searchWidgetStarFilters = {};
			}
		";
		echo "window.searchWidgetStarFilters['" . esc_js($filter_name . '_' . $block_order) . "'] = {
			'id': '" . esc_js(sprintf("stars_%s_%d", $this->get_unique_widget_id($widget_id, $filter_type, $filter_name), $block_order)) . "',
			'class': '" . esc_js(sprintf("stars_%s_%d", $this->get_unique_widget_id($widget_id, $filter_type, $filter_name), $block_order)) . "',
			'id_input': '" . esc_js(sprintf("stars_input_%s_%d", $this->get_unique_widget_id($widget_id, $filter_type, $filter_name), $block_order)) . "',
			'count':" . esc_js($count) . ",
			'value':" . esc_js($value) . "
		};";
		echo "</script>";
		echo '<div class="stars-container">';
		echo '<span class="stars-info">' . sprintf(esc_html__('%d or more', 'bookyourtravel'), $value) . '</span>';
		echo sprintf('<div id="stars_%s_%d" class="stars_%s_%d" name="%s" data-stars="%d"></div>', $this->get_unique_widget_id($widget_id, $filter_type, $filter_name), $block_order, $this->get_unique_widget_id($widget_id, $filter_type, $filter_name), $block_order, $filter_name, $value);
		echo '</div>';
	}

	function render_filter_slider($widget_id, $filter_type, $filter_name, $min, $max, $step, $value, $block_order) {
		echo "<script>";
		echo "
			if (window.searchWidgetSliderFilters === undefined) {
				window.searchWidgetSliderFilters = {};
			}
		";
		echo "window.searchWidgetSliderFilters['" . esc_js($filter_name . '_' . $block_order) . "'] = {
			'id': '" . esc_js(sprintf("slider_%s_%d", $this->get_unique_widget_id($widget_id, $filter_type, $filter_name), $block_order)) . "',
			'class': '" . esc_js(sprintf("slider_%s_%d", $this->get_unique_widget_id($widget_id, $filter_type, $filter_name), $block_order)) . "',
			'id_input': '" . esc_js(sprintf("slider_input_%s_%d", $this->get_unique_widget_id($widget_id, $filter_type, $filter_name), $block_order)) . "',
			'class_input': '" . esc_js(sprintf("slider_input_%s_%d", $this->get_unique_widget_id($widget_id, $filter_type, $filter_name), $block_order)) . "',
			'min':" . esc_js($min) . ",
			'max':" . esc_js($max) . ",
			'step':" . esc_js($step) . ",
			'value':" . esc_js($value) . "
		};";
		echo "</script>";
		echo '<div class="slider-container">';
		echo sprintf('<div id="slider_%s_%d" class="slider_%s_%d" name="%s" data-block-order="%d"></div>', $this->get_unique_widget_id($widget_id, $filter_type, $filter_name), $block_order, $this->get_unique_widget_id($widget_id, $filter_type, $filter_name), $block_order, $filter_name, $block_order);
		echo sprintf('<input type="hidden" id="slider_input_%s_%d" class="slider_input_%s_%d" name="%s" value="%d" />', $this->get_unique_widget_id($widget_id, $filter_type, $filter_name), $block_order, $this->get_unique_widget_id($widget_id, $filter_type, $filter_name), $block_order, $filter_name, $value);
		echo sprintf('<span class="min">%d</span>', $min);
		echo sprintf('<span class="max">%d</span>', $max);
		echo '</div>';
	}

	function render_filter_date_datepicker($widget_id, $filter_type, $filter_name, $filter_label, $block_order, $value, $unique_index) {
		echo "<script>";
		echo "
			if (window.searchWidgetDatepickers === undefined) {
				window.searchWidgetDatepickers = {};
			}
		";
		echo "window.searchWidgetDatepickers['" . esc_js(sprintf("datepicker_%s_%d_%d", $this->get_unique_widget_id($widget_id, $filter_type, $filter_name), $block_order, $unique_index)) . "'] = {
			'id': '" . esc_js(sprintf("datepicker_%s_%d_%d", $this->get_unique_widget_id($widget_id, $filter_type, $filter_name), $block_order, $unique_index)) . "',
			'class': '" . esc_js(sprintf("datepicker_%s_%d_%d", $this->get_unique_widget_id($widget_id, $filter_type, $filter_name), $block_order, $unique_index)) . "',
			'id_input': '" . esc_js(sprintf("datepicker_alt_%s_%d_%d", $this->get_unique_widget_id($widget_id, $filter_type, $filter_name), $block_order, $unique_index)) . "',
			'value': '" . esc_js($value) . "'
		};";
		echo "</script>";

        $filter_special_class = "";
        if ($filter_name == "from") {
            $filter_special_class = "datepicker_from";
        } else if ($filter_name == "to") {
            $filter_special_class = "datepicker_to";
        }

		echo sprintf('<div class="datepicker %s">', $filter_special_class);
		echo sprintf('<label for="datepicker_%s_%d_%d">%s</label>', $this->get_unique_widget_id($widget_id, $filter_type, $filter_name), $block_order, $unique_index, $filter_label);
		echo sprintf('<input type="hidden" id="datepicker_alt_%s_%d_%d" class="datepicker_alt_%s_%d_%d" name="%s"/>', $this->get_unique_widget_id($widget_id, $filter_type, $filter_name), $block_order, $unique_index, $this->get_unique_widget_id($widget_id, $filter_type, $filter_name), $block_order, $unique_index, $filter_name);
		echo '<div class="datepicker-wrap">';
		echo sprintf('<input readonly type="text" id="datepicker_%s_%d_%d" class="datepicker_%s_%d_%d" data-alt-field="datepicker_alt_%s_%d_%d" />', $this->get_unique_widget_id($widget_id, $filter_type, $filter_name), $block_order, $unique_index, $this->get_unique_widget_id($widget_id, $filter_type, $filter_name), $block_order, $unique_index, $this->get_unique_widget_id($widget_id, $filter_type, $filter_name), $block_order, $unique_index);
		echo '</div>';
		echo '</div>';
	}

	function render_filter_number_select($widget_id, $filter_type, $filter_name, $filter_label, $block_order, $min, $max, $value) {
		if ($min <= $max) {
			echo '<div class="select">';
			echo sprintf('<label for="select_%s_%d">%s</label>', $this->get_unique_widget_id($widget_id, $filter_type, $filter_name), $block_order, $filter_label);
			echo sprintf('<select id="select_%s_%d" name="%s"/>', $this->get_unique_widget_id($widget_id, $filter_type, $filter_name), $block_order, $filter_name);
			for ($i=$min; $i<=$max; $i++) {
				$selected = $i == $value ? "selected" : "";
				echo sprintf('<option %s value="%d">%d</option>', $selected, $i, $i);
			}
			echo '</select>';
			echo '</div>';
		}
	}

    function render_filter_taxonomy_select($widget_id, $taxonomy_type, $filter_name, $value)
    {
		$terms = $this->get_taxonomy_terms_array($taxonomy_type);
		if ($terms && count($terms)) {
			echo '<div class="select-wrap">';
			echo sprintf('<select id="select_%s" name="%s"/>', $widget_id . '_' . $taxonomy_type, $filter_name);
			$selected = !$value || $value == 0 ? "selected" : "";
			echo sprintf('<option %s value=""></option>', $selected);

			foreach ($terms as $term_id => $term_name) {
				$selected = $term_id == $value ? "selected" : "";
				echo sprintf('<option %s value="%d">%s</option>', $selected, $term_id, $term_name);
			}
			echo '</select>';
			echo '</div>';
		}
    }

	function render_filter_taxonomy_radios($widget_id, $taxonomy_type, $filter_name, $values) {
		$terms = $this->get_taxonomy_terms_array($taxonomy_type);

		if ($terms && count($terms)) {
			$checked = empty($values) ? "checked" : "";
			echo '<div class="radio-wrap">';
			echo sprintf('<input %s type="radio" id="radio_%s_0" value="" name="%s"/>', $checked, $widget_id . '_' . $taxonomy_type, $filter_name);
			echo sprintf('<label for="radio_%s_%d">%s</label>', $widget_id . '_' . $taxonomy_type, 0, __("All", "bookyourtravel"));
			echo '</div>';
			foreach ($terms as $term_id => $term_name) {
				$checked = in_array($term_id, $values) ? "checked" : "";
				echo '<div class="radio-wrap">';
				echo sprintf('<input %s type="radio" id="radio_%s_%d" value="%d" name="%s"/>', $checked, $widget_id . '_' . $taxonomy_type, $term_id, $term_id, $filter_name);
				echo sprintf('<label for="radio_%s_%d">%s</label>', $widget_id . '_' . $taxonomy_type, $term_id, $term_name);
				echo '</div>';
			}
		}
	}

	function render_filter_taxonomy_checkboxes($widget_id, $taxonomy_type, $filter_name, $values) {
		$terms = $this->get_taxonomy_terms_array($taxonomy_type);

		if ($terms && count($terms)) {
			foreach ($terms as $term_id => $term_name) {
				$checked = in_array($term_id, $values) ? "checked" : "";
				echo '<div class="checkbox-wrap">';
				echo sprintf('<input %s type="checkbox" id="checkbox_%s_%d" value="%d" name="%s"/>', $checked, $widget_id . '_' . $taxonomy_type, $term_id, $term_id, $filter_name);
				echo sprintf('<label for="checkbox_%s_%d">%s</label>', $widget_id . '_' . $taxonomy_type, $term_id, $term_name);
				echo '</div>';
			}
		}
	}

	function render_filter_post_name_input($widget_id, $filter_type, $filter_label, $filter_name, $value) {
		echo '<div class="text">';
		echo sprintf('<label for="%s">%s</label>', $this->get_unique_widget_id($widget_id, $filter_type, $filter_name), $filter_label);
		echo sprintf('<input value="%s" type="text" id="%s" name="%s"/>', esc_attr($value), $this->get_unique_widget_id($widget_id, $filter_type, $filter_name), $filter_name);
		echo '</div>';
	}

	function render_filter_post_name_select($widget_id, $filter_type, $filter_label, $filter_name, $value) {

		$args = array(
			'posts_per_page'=> -1, 
			'orderby'          => 'title',
			'order'            => 'ASC',
			'post_status'      => array('publish'),
			'suppress_filters' => true
		);

		if ($filter_type == 'accommodation-name-select') {
			$args['post_type'] = 'accommodation';
		} else if ($filter_type == 'carrental-name-select') {
			$args['post_type'] = 'car_rental';
		} else if ($filter_type == 'cruise-name-select') {
			$args['post_type'] = 'cruise';
		} else if ($filter_type == 'tour-name-select') {
			$args['post_type'] = 'tour';
		}

		$posts_array = get_posts( $args );

		echo '<div class="select">';		
		echo sprintf('<label for="%s">%s</label>', $this->get_unique_widget_id($widget_id, $filter_type, $filter_name), $filter_label);
		echo sprintf('<select id="%s" name="%s"/>', $this->get_unique_widget_id($widget_id, $filter_type, $filter_name), $filter_name);
		echo '<option value=""></option>';
		foreach ($posts_array as $a_post) {
			$post_title = str_replace("'", "", $a_post->post_title);
			$selected = urldecode($value) == $post_title ? "selected" : "";
			echo sprintf("<option %s value='%s'>%s</option>", $selected, $post_title, $post_title);
		}		

		echo '</select>';
		echo '</div>';
	}	

	function render_filter_location_select($widget_id, $filter_label, $filter_name, $values_array, $filter_index) {
		global $bookyourtravel_location_helper;

		$file_name_short = str_replace("[]", "", $filter_name);

		$hidden_val = '';
		if (isset($_GET['hl' . $filter_index . '' . $file_name_short])) {
			$hidden_val = wp_kses($_GET['hl' . $filter_index . '' . $file_name_short], array());
		}

		echo '<div class="select">';
		echo sprintf('<label for="l_%s">%s</label>', $widget_id . '_' . $filter_index . '_' . $filter_name, $filter_label);
		echo sprintf('<input type="hidden" id="hl_%s" name="hl%s" value="%s">', $filter_index . '_' . $file_name_short, $filter_index . '' . $file_name_short, $hidden_val);
		echo sprintf('<select class="filter-locations" data-relid="hl_%s" id="l_%s" name="%s">', $filter_index . '_' . $file_name_short, $widget_id . '_' . $filter_index . '_' . $filter_name, $filter_name);
        echo '<option value=""></option>';

        $location_results = $bookyourtravel_location_helper->list_locations(-1, 0, -1, 'title', 'ASC', false, array(), array(), null, true, false);

        if (count($location_results) > 0 && $location_results['total'] > 0) {
            $this->build_location_select($location_results['results'], $hidden_val, 0);
        }

		echo '</select>';
		echo '</div>';
		wp_reset_postdata();
    }

    function build_location_select($location_results, $val, $parent_location_id = 0) {

        $immediate_children = array_filter(
            $location_results,
            function ($e) use ($parent_location_id) {
                return $e->post_parent == $parent_location_id;
            }
        );

        if (count($immediate_children) > 0 && count($immediate_children) < 20) {
            foreach ($immediate_children as $location_result) {
                $selected = $location_result->ID == intval($val) ? "selected" : "";
                $css_class = "";
                if ($parent_location_id == 0) {
                    $immediate_sub_children = array_filter(
                        $location_results,
                        function ($e) use ($location_result) {
                            return $e->post_parent == $location_result->ID;
                        }
                    );
                    if (count($immediate_sub_children) > 0) {
                        $css_class = 'class="option-parent"';
                    }
                }
                echo sprintf('<option %s value="%d" %s>%s</option>', $selected, $location_result->ID, $css_class, get_the_title($location_result->ID));
                $this->build_location_select($location_results, $val, $location_result->ID);
            }
        }
    }

	function render_filter_location_by_type_select($widget_id, $filter_label, $filter_name, $filter_type_ids, $values_array, $filter_index, $filter_override_id) {
		global $bookyourtravel_location_helper;

		$file_name_short = str_replace("[]", "", $filter_name);

		$filter_id_part = $filter_index;
		if ($filter_override_id) {
			$filter_override_id = trim(preg_replace("/[^A-Za-z0-9]/","", $filter_override_id));
			if ($filter_override_id) {
				$filter_id_part = $filter_override_id;
			}
		}				

		$hidden_val = '';
		if (isset($_GET['hlt' . $filter_id_part . '' . $file_name_short])) {
			$hidden_val = wp_kses($_GET['hlt' . $filter_id_part . '' . $file_name_short], array());
		}

		echo '<div class="select">';
		echo sprintf('<label for="lbt_%s">%s</label>', $widget_id . '_' . $filter_id_part . '_' . $filter_name, $filter_label);
		echo sprintf('<input type="hidden" id="hlt_%s" name="hlt%s" value="%s">', $filter_id_part . '_' . $file_name_short, $filter_id_part . '' . $file_name_short, $hidden_val);
		echo sprintf('<select class="filter-locations-by-type" data-relid="hlt_%s" id="lbt_%s" name="%s">', $filter_id_part . '_' . $file_name_short, $widget_id . '_' . $filter_index . '_' . $filter_name, $filter_name);
		echo '<option value=""></option>';

		$type_ids = array();
		foreach ($filter_type_ids as $type_id) {
			$type_ids[] = intval($type_id);
		}

		$location_results = $bookyourtravel_location_helper->list_locations(-1, 0, -1, 'title', 'ASC', false, $type_ids, array(), null, true, false);

        if (count($location_results) > 0 && $location_results['total'] > 0) {
            foreach ($location_results['results'] as $location_result) {
                $selected = $location_result->ID == intval($hidden_val) ? "selected" : "";
                echo sprintf('<option %s value="%d">%s</option>', $selected, $location_result->ID, get_the_title($location_result->ID));
            }
        }

		echo '</select>';
		echo '</div>';
		wp_reset_postdata();
	}

	function populate_filters_array($filter_partial_id, $filter_count, $instance) {
		$filters = array();
		for ($i = 1; $i <= $filter_count; $i++) {
			$block = isset($instance['widget_' . $filter_partial_id . '_filter_block_' . $i]) ? intval($instance['widget_' . $filter_partial_id . '_filter_block_' . $i]) : 0;
			if ($block > 0) {
				if (!isset($filters[$block])) {
					$filters[$block] = array();
				}
				$filters[$block][] = array(
					'block' => $block,
					'order' => isset($instance['widget_' . $filter_partial_id . '_filter_order_' . $i]) ? $instance['widget_' . $filter_partial_id . '_filter_order_' . $i] : 1,
					'label' => isset($instance['widget_' . $filter_partial_id . '_filter_label_' . $i]) ? $instance['widget_' . $filter_partial_id . '_filter_label_' . $i] : '',
					'type' => isset($instance['widget_' . $filter_partial_id . '_filter_type_' . $i]) ? $instance['widget_' . $filter_partial_id . '_filter_type_' . $i] : '',
					'width' => isset($instance['widget_' . $filter_partial_id . '_filter_width_' . $i]) ? $instance['widget_' . $filter_partial_id . '_filter_width_' . $i] : 'full-width',
					'type_limiter' => isset($instance['widget_' . $filter_partial_id . '_filter_type_limiter_' . $i]) ? (array)$instance['widget_' . $filter_partial_id . '_filter_type_limiter_' . $i] : array(),
					'override_id' => isset($instance['widget_' . $filter_partial_id . '_filter_override_id_' . $i]) ? $instance['widget_' . $filter_partial_id . '_filter_override_id_' . $i] : '',
					'show_for_accommodations' => isset($instance['widget_' . $filter_partial_id . '_filter_show_for_accommodations_' . $i]) ? BookYourTravel_Theme_Utils::parseBool($instance['widget_' . $filter_partial_id . '_filter_show_for_accommodations_' . $i]) : false,
					'show_for_car_rentals' => isset($instance['widget_' . $filter_partial_id . '_filter_show_for_car_rentals_' . $i]) ? BookYourTravel_Theme_Utils::parseBool($instance['widget_' . $filter_partial_id . '_filter_show_for_car_rentals_' . $i]) : false,
					'show_for_cruises' => isset($instance['widget_' . $filter_partial_id . '_filter_show_for_cruises_' . $i]) ? BookYourTravel_Theme_Utils::parseBool($instance['widget_' . $filter_partial_id . '_filter_show_for_cruises_' . $i]) : false,
					'show_for_tours' => isset($instance['widget_' . $filter_partial_id . '_filter_show_for_tours_' . $i]) ? BookYourTravel_Theme_Utils::parseBool($instance['widget_' . $filter_partial_id . '_filter_show_for_tours_' . $i]) : false,
				);
			}
		}
		return $filters;
	}

	function render_block_start($block_order, $block_width, $block_index) {
		?>
		<div class="block block-<?php echo esc_attr($block_index); ?> <?php echo esc_attr($block_width); ?> block-order-<?php echo esc_attr($block_order); ?>">
		<?php
	}

	function render_block_end() {
		?>
		</div>
		<?php
	}

/*-----------------------------------------------------------------------------------*/
/*	Update Widget
/*-----------------------------------------------------------------------------------*/

	function update_filter_options($filter_entity_type, $new_instance, $instance) {

		$instance['widget_' . $filter_entity_type . '_filter_count'] = isset($new_instance['widget_' . $filter_entity_type . '_filter_count']) ? intval(strip_tags( $new_instance['widget_' . $filter_entity_type . '_filter_count'])) : 0;
		$widget_filter_count = intval($instance['widget_' . $filter_entity_type . '_filter_count']);

		for ($i = 1; $i <= $widget_filter_count; $i++) {
			$instance['widget_' . $filter_entity_type . '_filter_block_' . $i] = isset($new_instance['widget_' . $filter_entity_type . '_filter_block_' . $i]) ? intval(strip_tags($new_instance['widget_' . $filter_entity_type . '_filter_block_' . $i])) : 1;
			$instance['widget_' . $filter_entity_type . '_filter_order_' . $i] = isset($new_instance['widget_' . $filter_entity_type . '_filter_order_' . $i]) ? intval(strip_tags($new_instance['widget_' . $filter_entity_type . '_filter_order_' . $i])) : 1;
			$instance['widget_' . $filter_entity_type . '_filter_label_' . $i] = isset($new_instance['widget_' . $filter_entity_type . '_filter_label_' . $i]) ? strip_tags($new_instance['widget_' . $filter_entity_type . '_filter_label_' . $i]) : '';
			$instance['widget_' . $filter_entity_type . '_filter_type_' . $i] = isset($new_instance['widget_' . $filter_entity_type . '_filter_type_' . $i]) ? strip_tags($new_instance['widget_' . $filter_entity_type . '_filter_type_' . $i]) : '';
			$instance['widget_' . $filter_entity_type . '_filter_width_' . $i] = isset($new_instance['widget_' . $filter_entity_type . '_filter_width_' . $i]) ? strip_tags($new_instance['widget_' . $filter_entity_type . '_filter_width_' . $i]) : 'full-width';
			$instance['widget_' . $filter_entity_type . '_filter_override_id_' . $i] = isset($new_instance['widget_' . $filter_entity_type . '_filter_override_id_' . $i]) ? strip_tags($new_instance['widget_' . $filter_entity_type . '_filter_override_id_' . $i]) : '';

			$instance['widget_' . $filter_entity_type . '_filter_show_for_accommodations_' . $i] = isset($new_instance['widget_' . $filter_entity_type . '_filter_show_for_accommodations_' . $i]) ? BookYourTravel_Theme_Utils::parseBool(strip_tags($new_instance['widget_' . $filter_entity_type . '_filter_show_for_accommodations_' . $i])) : false;
			$instance['widget_' . $filter_entity_type . '_filter_show_for_car_rentals_' . $i] = isset($new_instance['widget_' . $filter_entity_type . '_filter_show_for_car_rentals_' . $i]) ? BookYourTravel_Theme_Utils::parseBool(strip_tags($new_instance['widget_' . $filter_entity_type . '_filter_show_for_car_rentals_' . $i])) : false;
			$instance['widget_' . $filter_entity_type . '_filter_show_for_cruises_' . $i] = isset($new_instance['widget_' . $filter_entity_type . '_filter_show_for_cruises_' . $i]) ? BookYourTravel_Theme_Utils::parseBool(strip_tags($new_instance['widget_' . $filter_entity_type . '_filter_show_for_cruises_' . $i])) : false;
			$instance['widget_' . $filter_entity_type . '_filter_show_for_tours_' . $i] = isset($new_instance['widget_' . $filter_entity_type . '_filter_show_for_tours_' . $i]) ? BookYourTravel_Theme_Utils::parseBool(strip_tags($new_instance['widget_' . $filter_entity_type . '_filter_show_for_tours_' . $i])) : false;
			$instance['widget_' . $filter_entity_type . '_filter_type_limiter_' . $i] = isset($new_instance['widget_' . $filter_entity_type . '_filter_type_limiter_' . $i]) ? (array)$new_instance['widget_' . $filter_entity_type . '_filter_type_limiter_' . $i] : array();
		}

		return $instance;
	}

	function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		/* Strip tags to remove HTML (important for text inputs). */
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['widget_width'] = isset($new_instance['widget_width']) ? intval(strip_tags( $new_instance['widget_width'])) : 100;
		$instance['widget_search_results_page'] = isset($new_instance['widget_search_results_page']) ? intval($new_instance['widget_search_results_page']) : 0;
		$instance['widget_padding_left_right'] = isset($new_instance['widget_padding_left_right']) ? strip_tags( $new_instance['widget_padding_left_right']) : "";
		$instance['widget_padding_top_bottom'] = isset($new_instance['widget_padding_top_bottom']) ? strip_tags( $new_instance['widget_padding_top_bottom']) : "";

		$instance['widget_block_count'] = isset($new_instance['widget_block_count']) ? intval(strip_tags( $new_instance['widget_block_count'])) : 1;
		$widget_block_count = intval($instance['widget_block_count']);
		for ($i = 1; $i <= $widget_block_count; $i++) {
			$instance['widget_block_index_' . $i] = isset($new_instance['widget_block_index_' . $i]) ? intval(strip_tags($new_instance['widget_block_index_' . $i])) : 1;
			$instance['widget_block_width_' . $i] = isset($new_instance['widget_block_width_' . $i]) ? strip_tags($new_instance['widget_block_width_' . $i]) : "full-width";
			$instance['widget_block_order_' . $i] = isset($new_instance['widget_block_order_' . $i]) ? intval(strip_tags($new_instance['widget_block_order_' . $i])) : 1;
		}

		$instance = $this->update_filter_options('accommodation', $new_instance, $instance);
		$instance = $this->update_filter_options('carrental', $new_instance, $instance);
		$instance = $this->update_filter_options('cruise', $new_instance, $instance);
		$instance = $this->update_filter_options('location', $new_instance, $instance);
		$instance = $this->update_filter_options('tour', $new_instance, $instance);
		$instance = $this->update_filter_options('generic', $new_instance, $instance);

		$instance['widget_background_color'] = isset($new_instance['widget_background_color']) ? strip_tags( $new_instance['widget_background_color']) : "";
		$instance['widget_text_color'] = isset($new_instance['widget_text_color']) ? strip_tags( $new_instance['widget_text_color']) : "";
		$instance['widget_limit_page_ids'] = isset($new_instance['widget_limit_page_ids']) ? strip_tags( $new_instance['widget_limit_page_ids']) : '';
		$instance['widget_position_x'] = isset($new_instance['widget_position_x']) ? strip_tags( $new_instance['widget_position_x']) : 'hCenter';
		$instance['widget_position_y'] = isset($new_instance['widget_position_y']) ? strip_tags( $new_instance['widget_position_y']) : 'vMiddle';
		$instance['widget_display_style'] = isset($new_instance['widget_display_style']) ? strip_tags( $new_instance['widget_display_style']) : 'dOver';
		$instance['widget_home_page_only'] = isset($new_instance['widget_home_page_only']) ? BookYourTravel_Theme_Utils::parseBool(strip_tags( $new_instance['widget_home_page_only'])) : false;

		$instance['widget_enable_search_for_accommodations'] = isset($new_instance['widget_enable_search_for_accommodations']) ? BookYourTravel_Theme_Utils::parseBool(strip_tags( $new_instance['widget_enable_search_for_accommodations'])) : false;
		$instance['widget_enable_search_for_carrentals'] = isset($new_instance['widget_enable_search_for_carrentals']) ? BookYourTravel_Theme_Utils::parseBool(strip_tags( $new_instance['widget_enable_search_for_carrentals'])) : false;
		$instance['widget_enable_search_for_cruises'] = isset($new_instance['widget_enable_search_for_cruises']) ? BookYourTravel_Theme_Utils::parseBool(strip_tags( $new_instance['widget_enable_search_for_cruises'])) : false;
		$instance['widget_enable_search_for_tours'] = isset($new_instance['widget_enable_search_for_tours']) ? BookYourTravel_Theme_Utils::parseBool(strip_tags( $new_instance['widget_enable_search_for_tours'])) : false;

		$instance['widget_search_for_label'] = isset($new_instance['widget_search_for_label']) ? strip_tags( $new_instance['widget_search_for_label']) : '';
		$instance['widget_search_for_width'] = isset($new_instance['widget_search_for_width']) ? strip_tags( $new_instance['widget_search_for_width']) : '';
		$instance['widget_submit_button_text'] = isset($new_instance['widget_submit_button_text']) ? strip_tags( $new_instance['widget_submit_button_text']) : '';
		$instance['widget_submit_button_order'] = isset($new_instance['widget_submit_button_order']) ? strip_tags( $new_instance['widget_submit_button_order']) : '';
		$instance['widget_submit_button_width'] = isset($new_instance['widget_submit_button_width']) ? strip_tags( $new_instance['widget_submit_button_width']) : '';

		$instance['widget_show_clear_button'] = isset($new_instance['widget_show_clear_button']) ? BookYourTravel_Theme_Utils::parseBool(strip_tags( $new_instance['widget_show_clear_button'])) : false;
		$instance['widget_clear_button_text'] = isset($new_instance['widget_clear_button_text']) ? strip_tags( $new_instance['widget_clear_button_text']) : '';
		$instance['widget_clear_button_order'] = isset($new_instance['widget_clear_button_order']) ? strip_tags( $new_instance['widget_clear_button_order']) : '';
		$instance['widget_clear_button_width'] = isset($new_instance['widget_clear_button_width']) ? strip_tags( $new_instance['widget_clear_button_width']) : '';

		return $instance;
	}

/*-----------------------------------------------------------------------------------*/
/*	Widget Settings
/*-----------------------------------------------------------------------------------*/

	function form( $instance ) {

		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker');
		wp_register_script( 'bookyourtravel-search-widget-admin', BookYourTravel_Theme_Utils::get_file_uri('/js/admin/search_widget_admin.js'), array('jquery', 'wp-color-picker'), BOOKYOURTRAVEL_VERSION, true );
		wp_enqueue_script( 'bookyourtravel-search-widget-admin' );

		global $bookyourtravel_theme_globals;

		/* Set up some default widget settings. */
		$defaults = array(
			'title' => esc_html__('Refine search results', 'bookyourtravel'),
			'widget_search_results_page' => 0,
			'widget_padding_left_right' => '',
			'widget_padding_top_bottom' => '',
			'widget_width' => 100,
			'widget_background_color' => '',
			'widget_text_color' => '',
			'widget_block_count' => 1,
			'widget_search_for_label' => __('What?', 'bookyourtravel'),
			'widget_accommodation_filter_count' => 0,
			'widget_carrental_filter_count' => 0,
			'widget_cruise_filter_count' => 0,
			'widget_generic_filter_count' => 0,
			'widget_location_filter_count' => 0,
			'widget_tour_filter_count' => 0,
			'widget_block_index_1' => 1,
			'widget_block_order_1' => 1,
			'widget_block_width_1' => 'full-width',
			'widget_home_page_only' => false,
			'widget_limit_page_ids' => '',
			'widget_display_style' => 'dOver',
			'widget_position_x' => 'hCenter',
			'widget_position_y' => 'vMiddle',
			'widget_submit_button_text' => esc_html__('Search again', 'bookyourtravel'),
			'widget_submit_button_width' => 'full-width',
			'widget_submit_button_order' => 10,
			'widget_show_clear_button' => false,
			'widget_clear_button_text' => esc_html__('Clear filters', 'bookyourtravel'),
			'widget_clear_button_order' => 11,
			'widget_clear_button_width' => 'full-width',
			'widget_enable_search_for_accommodations' => $this->enable_accommodations,
			'widget_enable_search_for_carrentals' => $this->enable_car_rentals,
			'widget_enable_search_for_cruises' => $this->enable_cruises,
			'widget_enable_search_for_tours' => $this->enable_tours,
		);

		$instance = wp_parse_args( (array) $instance, $defaults );
		?>

		<script>

			window.genericFilterCalendarDateFrom = <?php echo json_encode(__('Calendar date from', 'bookyourtravel')); ?>;
			window.genericFilterCalendarDateTo = <?php echo json_encode(__('Calendar date to', 'bookyourtravel')); ?>;
			window.genericFilterUserRatingSlider = <?php echo json_encode(__('User rating slider', 'bookyourtravel')); ?>;
			window.genericFilterStarRatingSlider = <?php echo json_encode(__('Star rating slider', 'bookyourtravel')); ?>;
			window.genericFilterPriceRangeCheckboxes = <?php echo json_encode(__('Price range checkboxes', 'bookyourtravel')); ?>;
			window.genericFilterFacilityCheckboxes = <?php echo json_encode(__('Facility checkboxes', 'bookyourtravel')); ?>;

			window.enableAccommodations = <?php echo json_encode($this->enable_accommodations); ?>;
			window.accommodationFilterTagRadios = <?php echo json_encode(__('Accommodation tag radios', 'bookyourtravel')); ?>;
			window.accommodationFilterTagCheckboxes = <?php echo json_encode(__('Accommodation tag checkboxes', 'bookyourtravel')); ?>;
			window.accommodationFilterTagSelect = <?php echo json_encode(__('Accommodation tag select', 'bookyourtravel')); ?>;
			window.accommodationFilterTypeRadios = <?php echo json_encode(__('Accommodation type radios', 'bookyourtravel')); ?>;
			window.accommodationFilterTypeCheckboxes = <?php echo json_encode(__('Accommodation type checkboxes', 'bookyourtravel')); ?>;
			window.accommodationFilterTypeSelect = <?php echo json_encode(__('Accommodation type select', 'bookyourtravel')); ?>;
			window.accommodationFilterName = <?php echo json_encode(__('Accommodation name', 'bookyourtravel')); ?>;
			window.accommodationFilterNameSelect = <?php echo json_encode(__('Accommodation name select', 'bookyourtravel')); ?>;
			window.accommodationRoomCount = <?php echo json_encode(__('Room count select', 'bookyourtravel')); ?>;

			window.enableCarRentals = <?php echo json_encode($this->enable_car_rentals); ?>;
			window.carRentalFilterTagRadios = <?php echo json_encode(__('Car rental tag radios', 'bookyourtravel')); ?>;
			window.carRentalFilterTagCheckboxes = <?php echo json_encode(__('Car rental tag checkboxes', 'bookyourtravel')); ?>;
			window.carRentalFilterTagSelect = <?php echo json_encode(__('Car rental tag select', 'bookyourtravel')); ?>;
			window.carRentalFilterTypeRadios = <?php echo json_encode(__('Car rental type radios', 'bookyourtravel')); ?>;
			window.carRentalFilterTypeCheckboxes = <?php echo json_encode(__('Car rental type checkboxes', 'bookyourtravel')); ?>;
			window.carRentalFilterTypeSelect = <?php echo json_encode(__('Car rental type select', 'bookyourtravel')); ?>;
			window.carRentalFilterName = <?php echo json_encode(__('Car rental name', 'bookyourtravel')); ?>;
			window.carRentalFilterNameSelect = <?php echo json_encode(__('Car rental name select', 'bookyourtravel')); ?>;			

			window.enableCruises = <?php echo json_encode($this->enable_cruises); ?>;
			window.cruiseFilterTagRadios = <?php echo json_encode(__('Cruise tag radios', 'bookyourtravel')); ?>;
			window.cruiseFilterTagCheckboxes = <?php echo json_encode(__('Cruise tag checkboxes', 'bookyourtravel')); ?>;
			window.cruiseFilterTagSelect = <?php echo json_encode(__('Cruise tag select', 'bookyourtravel')); ?>;
			window.cruiseFilterTypeRadios = <?php echo json_encode(__('Cruise type radios', 'bookyourtravel')); ?>;
			window.cruiseFilterTypeCheckboxes = <?php echo json_encode(__('Cruise type checkboxes', 'bookyourtravel')); ?>;
			window.cruiseFilterTypeSelect = <?php echo json_encode(__('Cruise type select', 'bookyourtravel')); ?>;
			window.cruiseFilterDurationRadios = <?php echo json_encode(__('Cruise duration radios', 'bookyourtravel')); ?>;
			window.cruiseFilterDurationCheckboxes = <?php echo json_encode(__('Cruise duration checkboxes', 'bookyourtravel')); ?>;
			window.cruiseFilterDurationSelect = <?php echo json_encode(__('Cruise duration select', 'bookyourtravel')); ?>;			
			window.cruiseFilterName = <?php echo json_encode(__('Cruise name', 'bookyourtravel')); ?>;
			window.cruiseFilterNameSelect = <?php echo json_encode(__('Cruise name select', 'bookyourtravel')); ?>;
			window.cruiseCabinCount = <?php echo json_encode(__('Cabin count select', 'bookyourtravel')); ?>;

			window.locationFilterByTypeSelect = <?php echo json_encode(__('Type-filtered location select', 'bookyourtravel')); ?>;
			window.locationFilterSelect = <?php echo json_encode(__('Location select', 'bookyourtravel')); ?>;

			window.enableTours = <?php echo json_encode($this->enable_tours); ?>;
			window.tourFilterTagRadios = <?php echo json_encode(__('Tour tag radios', 'bookyourtravel')); ?>;
			window.tourFilterTagCheckboxes = <?php echo json_encode(__('Tour tag checkboxes', 'bookyourtravel')); ?>;
			window.tourFilterTagSelect = <?php echo json_encode(__('Tour tag select', 'bookyourtravel')); ?>;
			window.tourFilterTypeRadios = <?php echo json_encode(__('Tour type radios', 'bookyourtravel')); ?>;
			window.tourFilterTypeCheckboxes = <?php echo json_encode(__('Tour type checkboxes', 'bookyourtravel')); ?>;
			window.tourFilterTypeSelect = <?php echo json_encode(__('Tour type select', 'bookyourtravel')); ?>;
			window.tourFilterDurationRadios = <?php echo json_encode(__('Tour duration radios', 'bookyourtravel')); ?>;
			window.tourFilterDurationCheckboxes = <?php echo json_encode(__('Tour duration checkboxes', 'bookyourtravel')); ?>;
			window.tourFilterDurationSelect = <?php echo json_encode(__('Tour duration select', 'bookyourtravel')); ?>;			
			window.tourFilterName = <?php echo json_encode(__('Tour name', 'bookyourtravel')); ?>;
			window.tourFilterNameSelect = <?php echo json_encode(__('Tour name select', 'bookyourtravel')); ?>;			

			window.removeFilterText = <?php echo json_encode(__('Remove', 'bookyourtravel')); ?>;

			window.showForLabel = <?php echo json_encode(__('Show only for?', 'bookyourtravel')); ?>;
			window.showForAccommodationsLabel = <?php echo json_encode(__('Accommodations', 'bookyourtravel')); ?>;
			window.showForCarRentalsLabel = <?php echo json_encode(__('Car rentals', 'bookyourtravel')); ?>;
			window.showForCruisesLabel = <?php echo json_encode(__('Cruises', 'bookyourtravel')); ?>;
			window.showForToursLabel = <?php echo json_encode(__('Tours', 'bookyourtravel')); ?>;

			window.showInBlockLabel =  <?php echo json_encode(__('Show in block', 'bookyourtravel')); ?>;
		</script>

		<div class="byt-admin-widget">
			<?php wp_nonce_field('bookyourtravel_nonce'); ?>
			<ul class="byt-widget-tabs top-widget-tabs">
				<li><a title="<?php esc_html_e("General", "bookyourtravel"); ?>" href="#general"><span class="tab-icon general"></span><?php esc_html_e("General", "bookyourtravel"); ?></a></li>
				<li><a title="<?php esc_html_e("Layout", "bookyourtravel"); ?>" href="#layout"><span class="tab-icon layout"></span><?php esc_html_e("Layout", "bookyourtravel"); ?></a></li>
				<li><a title="<?php esc_html_e("Generic filters", "bookyourtravel"); ?>" href="#generic"><span class="tab-icon filters"></span><?php esc_html_e("Generic filters", "bookyourtravel"); ?></a></li>
				<li><a title="<?php esc_html_e("Location filters", "bookyourtravel"); ?>" href="#locations"><span class="tab-icon locations"></span><?php esc_html_e("Location filters", "bookyourtravel"); ?></a></li>
				<?php if ($this->enable_accommodations) { ?>
				<li><a title="<?php esc_html_e("Accommodation filters", "bookyourtravel"); ?>" href="#accommodations"><span class="tab-icon accommodations"></span><?php esc_html_e("Accommodation filters", "bookyourtravel"); ?></a></li>
				<?php } ?>
				<?php if ($this->enable_car_rentals) { ?>
				<li><a title="<?php esc_html_e("Car rental filters", "bookyourtravel"); ?>" href="#carrentals"><span class="tab-icon carrentals"></span><?php esc_html_e("Car rental filters", "bookyourtravel"); ?></a></li>
				<?php } ?>
				<?php if ($this->enable_cruises) { ?>
				<li><a title="<?php esc_html_e("Cruise filters", "bookyourtravel"); ?>" href="#cruises"><span class="tab-icon cruises"></span><?php esc_html_e("Cruise filters", "bookyourtravel"); ?></a></li>
				<?php } ?>
				<?php if ($this->enable_tours) { ?>
				<li><a title="<?php esc_html_e("Tour filters", "bookyourtravel"); ?>" href="#tours"><span class="tab-icon tours"></span><?php esc_html_e("Tour filters", "bookyourtravel"); ?></a></li>
				<?php } ?>
			</ul>

			<div class="byt-widget-tabs-content">
				<div id="general" class="tab-content">
					<div class="inner">
						<h3><?php esc_html_e("General", "bookyourtravel"); ?></h3>
						<p>
							<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e('Widget title:', 'bookyourtravel') ?></label>
							<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" />
						</p>
						<p>
							<label for="<?php echo esc_attr( $this->get_field_id( 'widget_search_results_page' ) ); ?>"><?php esc_html_e('Search results page', 'bookyourtravel') ?></label>
							<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'widget_search_results_page' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'widget_search_results_page' ) ); ?>">
								<?php
									$selected_search_results_page_id = isset($instance['widget_search_results_page']) ? intval($instance['widget_search_results_page']) : 0;

									$args = array(
										'post_type' => 'page',
										'meta_query' => array(
											array(
												'key' => '_wp_page_template',
												'value' => 'page-custom-search-results.php',
											),
										),
										'orderby' => 'title',
										'post_status' => 'publish',
										'order' => 'ASC',
										'posts_per_page'=> -1
									);

									$search_pages = get_posts($args);

									$search_pages_options = "<option value=''>" . esc_html__("Select search results page", "bookyourtravel") . "</option>";
									if (count($search_pages) > 0) {
										foreach ($search_pages as $search_page) {
											$search_pages_options .= "<option value='" . $search_page->ID . "' " . ($selected_search_results_page_id == $search_page->ID ? "selected" : "") . ">" . $search_page->post_title . "</option>";
										}
									}
								?>
								<?php echo $search_pages_options; ?>
							</select>
						</p>
						<p>
							<label for="<?php echo esc_attr( $this->get_field_id( 'widget_submit_button_text' ) ); ?>"><?php esc_html_e('Search submit button text', 'bookyourtravel') ?></label>
							<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'widget_submit_button_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'widget_submit_button_text' ) ); ?>" value="<?php echo esc_attr( $instance['widget_submit_button_text'] ); ?>" />
						</p>
						<p>
							<label for="<?php echo esc_attr( $this->get_field_id( 'widget_submit_button_width' ) ); ?>"><?php esc_html_e('Search submit button width', 'bookyourtravel') ?></label>
							<select id="<?php echo esc_attr( $this->get_field_id( 'widget_submit_button_width' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'widget_submit_button_width' ) ); ?>">
								<option <?php echo isset($instance['widget_submit_button_width']) && $instance['widget_submit_button_width'] == "one-sixth" ? "selected" : "" ?> value="one-sixth"><?php esc_html_e("1/6", "bookyourtravel"); ?></option>
								<option <?php echo isset($instance['widget_submit_button_width']) && $instance['widget_submit_button_width'] == "one-fifth" ? "selected" : "" ?> value="one-fifth"><?php esc_html_e("1/5", "bookyourtravel"); ?></option>
								<option <?php echo isset($instance['widget_submit_button_width']) && $instance['widget_submit_button_width'] == "one-fourth" ? "selected" : "" ?> value="one-fourth"><?php esc_html_e("1/4", "bookyourtravel"); ?></option>
								<option <?php echo isset($instance['widget_submit_button_width']) && $instance['widget_submit_button_width'] == "one-third" ? "selected" : "" ?> value="one-third"><?php esc_html_e("1/3", "bookyourtravel"); ?></option>
								<option <?php echo isset($instance['widget_submit_button_width']) && $instance['widget_submit_button_width'] == "one-half" ? "selected" : "" ?> value="one-half"><?php esc_html_e("1/2", "bookyourtravel"); ?></option>
								<option <?php echo isset($instance['widget_submit_button_width']) && $instance['widget_submit_button_width'] == "full-width" ? "selected" : "" ?> value="full-width"><?php esc_html_e("1/1", "bookyourtravel"); ?></option>
							</select>
						</p>
						<p>
							<label for="<?php echo esc_attr( $this->get_field_id( 'widget_submit_button_order' ) ); ?>"><?php esc_html_e('Search submit button order', 'bookyourtravel') ?></label>
							<input min="1" max="20" type="number" placeholder="1" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'widget_submit_button_order' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'widget_submit_button_order' ) ); ?>" value="<?php echo esc_attr( $instance['widget_submit_button_order'] ); ?>" />
						</p>

						<p>
							<input class="show_clear_button" type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'widget_show_clear_button' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'widget_show_clear_button' ) ); ?>" value="1" <?php echo isset($instance['widget_show_clear_button']) && BookYourTravel_Theme_Utils::parseBool($instance['widget_show_clear_button']) ? 'checked' : ''; ?> />
							<label for="<?php echo esc_attr( $this->get_field_id( 'widget_show_clear_button' ) ); ?>"><?php esc_html_e('Show clear button?', 'bookyourtravel') ?></label>
						</p>

						<?php $widget_show_clear_button = isset($instance['widget_show_clear_button']) ? BookYourTravel_Theme_Utils::parseBool($instance['widget_show_clear_button']) : false; ?>

						<p class="clear-button-controls" style="<?php if (!$widget_show_clear_button) { echo 'display:none;'; } ?>">
							<label for="<?php echo esc_attr( $this->get_field_id( 'widget_clear_button_text' ) ); ?>"><?php esc_html_e('Clear button text', 'bookyourtravel') ?></label>
							<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'widget_clear_button_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'widget_clear_button_text' ) ); ?>" value="<?php echo esc_attr( $instance['widget_clear_button_text'] ); ?>" />
						</p>

						<p class="clear-button-controls" style="<?php if (!$widget_show_clear_button) { echo 'display:none;'; } ?>">
							<label for="<?php echo esc_attr( $this->get_field_id( 'widget_clear_button_width' ) ); ?>"><?php esc_html_e('Clear button width', 'bookyourtravel') ?></label>
							<select id="<?php echo esc_attr( $this->get_field_id( 'widget_clear_button_width' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'widget_clear_button_width' ) ); ?>">
								<option <?php echo isset($instance['widget_clear_button_width']) && $instance['widget_clear_button_width'] == "one-sixth" ? "selected" : "" ?> value="one-sixth"><?php esc_html_e("1/6", "bookyourtravel"); ?></option>
								<option <?php echo isset($instance['widget_clear_button_width']) && $instance['widget_clear_button_width'] == "one-fifth" ? "selected" : "" ?> value="one-fifth"><?php esc_html_e("1/5", "bookyourtravel"); ?></option>
								<option <?php echo isset($instance['widget_clear_button_width']) && $instance['widget_clear_button_width'] == "one-fourth" ? "selected" : "" ?> value="one-fourth"><?php esc_html_e("1/4", "bookyourtravel"); ?></option>
								<option <?php echo isset($instance['widget_clear_button_width']) && $instance['widget_clear_button_width'] == "one-third" ? "selected" : "" ?> value="one-third"><?php esc_html_e("1/3", "bookyourtravel"); ?></option>
								<option <?php echo isset($instance['widget_clear_button_width']) && $instance['widget_clear_button_width'] == "one-half" ? "selected" : "" ?> value="one-half"><?php esc_html_e("1/2", "bookyourtravel"); ?></option>
								<option <?php echo isset($instance['widget_clear_button_width']) && $instance['widget_clear_button_width'] == "full-width" ? "selected" : "" ?> value="full-width"><?php esc_html_e("1/1", "bookyourtravel"); ?></option>
							</select>
						</p>						

						<p class="clear-button-controls" style="<?php if (!$widget_show_clear_button) { echo 'display:none;'; } ?>">
							<label for="<?php echo esc_attr( $this->get_field_id( 'widget_clear_button_order' ) ); ?>"><?php esc_html_e('Clear button order', 'bookyourtravel') ?></label>
							<input min="1" max="20" type="number" placeholder="1" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'widget_clear_button_order' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'widget_clear_button_order' ) ); ?>" value="<?php echo esc_attr( $instance['widget_clear_button_order'] ); ?>" />
						</p>

						<p>
							<input type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'widget_home_page_only' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'widget_home_page_only' ) ); ?>" value="1" <?php echo isset($instance['widget_home_page_only']) && BookYourTravel_Theme_Utils::parseBool($instance['widget_home_page_only']) ? 'checked' : ''; ?> />
							<label for="<?php echo esc_attr( $this->get_field_id( 'widget_home_page_only' ) ); ?>"><?php esc_html_e('Home page only?', 'bookyourtravel') ?></label>
						</p>
						<p>
							<label for="<?php echo esc_attr( $this->get_field_id( 'widget_limit_page_ids' ) ); ?>"><?php esc_html_e('Pages: (e.g. 2,10)', 'bookyourtravel') ?></label>
							<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'widget_limit_page_ids' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'widget_limit_page_ids' ) ); ?>" value="<?php echo esc_attr( $instance['widget_limit_page_ids'] ); ?>" />
						</p>
						<p>
							<label for="<?php echo esc_attr( $this->get_field_id( 'widget_background_color' ) ); ?>"><?php esc_html_e('Background color', 'bookyourtravel') ?></label>
							<input type="text" id="<?php echo esc_attr( $this->get_field_id( 'widget_background_color' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'widget_background_color' ) ); ?>" value="<?php echo esc_attr( $instance['widget_background_color'] ); ?>" class="widget-background-color-field" data-default-color="#fff" />
						</p>
						<p>
							<label for="<?php echo esc_attr( $this->get_field_id( 'widget_text_color' ) ); ?>"><?php esc_html_e('Text color', 'bookyourtravel') ?></label>
							<input type="text" id="<?php echo esc_attr( $this->get_field_id( 'widget_text_color' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'widget_text_color' ) ); ?>" value="<?php echo esc_attr( $instance['widget_text_color'] ); ?>" class="widget-text-color-field" data-default-color="#fff" />
						</p>
						<p>
							<label for="<?php echo esc_attr( $this->get_field_id( 'widget_search_for_label' ) ); ?>"><?php esc_html_e('Label for "What are you searching for?"', 'bookyourtravel') ?></label>
							<input class="widefat" value="<?php echo isset($instance['widget_search_for_label']) ? esc_attr( $instance['widget_search_for_label'] ) : ''; ?>" type="text" id="<?php echo esc_attr( $this->get_field_id( 'widget_search_for_label' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'widget_search_for_label' ) ); ?>" />
						</p>
						<p>
							<label for="<?php echo esc_attr( $this->get_field_id( 'widget_search_for_width' ) ); ?>"><?php esc_html_e('Width of "What are you searching for?"', 'bookyourtravel') ?></label>
							<select id="<?php echo esc_attr( $this->get_field_id( 'widget_search_for_width' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'widget_search_for_width' ) ); ?>">
								<option <?php echo isset($instance['widget_search_for_width']) && $instance['widget_search_for_width'] == "one-sixth" ? "selected" : "" ?> value="one-sixth"><?php esc_html_e("1/6", "bookyourtravel"); ?></option>
								<option <?php echo isset($instance['widget_search_for_width']) && $instance['widget_search_for_width'] == "one-fifth" ? "selected" : "" ?> value="one-fifth"><?php esc_html_e("1/5", "bookyourtravel"); ?></option>
								<option <?php echo isset($instance['widget_search_for_width']) && $instance['widget_search_for_width'] == "one-fourth" ? "selected" : "" ?> value="one-fourth"><?php esc_html_e("1/4", "bookyourtravel"); ?></option>
								<option <?php echo isset($instance['widget_search_for_width']) && $instance['widget_search_for_width'] == "one-third" ? "selected" : "" ?> value="one-third"><?php esc_html_e("1/3", "bookyourtravel"); ?></option>
								<option <?php echo isset($instance['widget_search_for_width']) && $instance['widget_search_for_width'] == "one-half" ? "selected" : "" ?> value="one-half"><?php esc_html_e("1/2", "bookyourtravel"); ?></option>
								<option <?php echo isset($instance['widget_search_for_width']) && $instance['widget_search_for_width'] == "full-width" ? "selected" : "" ?> value="full-width"><?php esc_html_e("1/1", "bookyourtravel"); ?></option>
							</select>
						</p>
						<p>
							<span class="label"><?php echo esc_html__("Enable search for?", "bookyourtravel"); ?></span>
							<ul>
								<?php if ($this->enable_accommodations) { ?>
								<li>
									<input <?php echo isset($instance['widget_enable_search_for_accommodations']) && intval($instance['widget_enable_search_for_accommodations']) == '1' ? "checked" : "" ?> value="1" type="checkbox" id="<?php echo esc_attr( $this->get_field_id('widget_enable_search_for_accommodations') ); ?>" name="<?php echo esc_attr( $this->get_field_name('widget_enable_search_for_accommodations') ); ?>">
									<label for="<?php echo esc_attr( $this->get_field_id( 'widget_enable_search_for_accommodations' ) ); ?>"><?php esc_html_e('Accommodations', 'bookyourtravel') ?></label>
								</li>
								<?php } ?>
								<?php if ($this->enable_car_rentals) { ?>
								<li>
									<input <?php echo isset($instance['widget_enable_search_for_carrentals']) && intval($instance['widget_enable_search_for_carrentals']) == '1' ? "checked" : "" ?> value="1" type="checkbox" id="<?php echo esc_attr( $this->get_field_id('widget_enable_search_for_carrentals') ); ?>" name="<?php echo esc_attr( $this->get_field_name('widget_enable_search_for_carrentals') ); ?>">
									<label for="<?php echo esc_attr( $this->get_field_id( 'widget_enable_search_for_carrentals' ) ); ?>"><?php esc_html_e('Car rentals', 'bookyourtravel') ?></label>
								</li>
								<?php } ?>
								<?php if ($this->enable_cruises) { ?>
								<li>
									<input <?php echo isset($instance['widget_enable_search_for_cruises']) && intval($instance['widget_enable_search_for_cruises']) == '1' ? "checked" : "" ?> value="1" type="checkbox" id="<?php echo esc_attr( $this->get_field_id('widget_enable_search_for_cruises') ); ?>" name="<?php echo esc_attr( $this->get_field_name('widget_enable_search_for_cruises') ); ?>">
									<label for="<?php echo esc_attr( $this->get_field_id('widget_enable_search_for_cruises') ); ?>"><?php esc_html_e('Cruises', 'bookyourtravel') ?></label>
								</li>
								<?php } ?>
								<?php if ($this->enable_tours) { ?>
								<li>
									<input <?php echo isset($instance['widget_enable_search_for_tours']) && intval($instance['widget_enable_search_for_tours']) == '1' ? "checked" : "" ?> value="1" type="checkbox" id="<?php echo esc_attr( $this->get_field_id('widget_enable_search_for_tours') ); ?>" name="<?php echo esc_attr( $this->get_field_name('widget_enable_search_for_tours') ); ?>">
									<label for="<?php echo esc_attr( $this->get_field_id('widget_enable_search_for_tours') ); ?>"><?php esc_html_e('Tours', 'bookyourtravel') ?></label>
								</li>
								<?php } ?>
							</ul>
						</p>
					</div>
				</div>
				<div id="layout" class="tab-content">
					<div class="inner">
						<h3><?php esc_html_e("Layout", "bookyourtravel"); ?></h3>
						<p class="sidebar-hero" style="display:none">
							<label for="<?php echo esc_attr( $this->get_field_id( 'widget_width' ) ); ?>"><?php esc_html_e('Width (%)', 'bookyourtravel') ?></label>
							<input placeholder="100" min="1" max="100" type="number" id="<?php echo esc_attr( $this->get_field_id( 'widget_width' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'widget_width' ) ); ?>" value="<?php echo esc_attr( $instance['widget_width'] ); ?>" />
						</p>
						<h4 class="sidebar-hero" style="display:none"><?php esc_html_e("Offset position", "bookyourtravel"); ?></h4>
						<p class="sidebar-hero" style="display:none"><?php esc_html_e("You can use the fields below to define the offset position of the widget (in pixels) in the Hero widget sidebar.", "bookyourtravel"); ?></p>
						<div class="sidebar-hero" style="display:none">
							<span class="label"><?php esc_html_e('Display style', 'bookyourtravel') ?></span>
							<ul>
								<li>
									<input <?php echo isset($instance['widget_display_style']) && ($instance['widget_display_style'] == 'dOver') ? 'checked' : ''; ?> type="radio" id="<?php echo esc_attr( $this->get_field_id( 'widget_display_style' ) ); ?>_over" name="<?php echo esc_attr( $this->get_field_name( 'widget_display_style' ) ); ?>" value="dOver" />
									<label for="<?php echo esc_attr( $this->get_field_id( 'widget_display_style' ) ); ?>_over"><?php esc_html_e('Over', 'bookyourtravel') ?></label>
								</li>
								<li>
									<input <?php echo isset($instance['widget_display_style']) && ($instance['widget_display_style'] == 'dInline') ? 'checked' : ''; ?> type="radio" id="<?php echo esc_attr( $this->get_field_id( 'widget_display_style' ) ); ?>_inline" name="<?php echo esc_attr( $this->get_field_name( 'widget_display_style' ) ); ?>" value="dInline" />
									<label for="<?php echo esc_attr( $this->get_field_id( 'widget_display_style' ) ); ?>_inline"><?php esc_html_e('Inline', 'bookyourtravel') ?></label>
								</li>
							</ul>
						</div>
						<div class="sidebar-hero" style="display:none">
							<span class="label"><?php esc_html_e('Horizontal position', 'bookyourtravel') ?></span>
							<ul>
								<li>
									<input <?php echo isset($instance['widget_position_x']) && ($instance['widget_position_x'] == 'hLeft') ? 'checked' : ''; ?> type="radio" id="<?php echo esc_attr( $this->get_field_id( 'widget_position_x' ) ); ?>_left" name="<?php echo esc_attr( $this->get_field_name( 'widget_position_x' ) ); ?>" value="hLeft" />
									<label for="<?php echo esc_attr( $this->get_field_id( 'widget_position_x' ) ); ?>_left"><?php esc_html_e('Left', 'bookyourtravel') ?></label>
								</li>
								<li>
									<input <?php echo isset($instance['widget_position_x']) && ($instance['widget_position_x'] == 'hCenter') ? 'checked' : ''; ?> type="radio" id="<?php echo esc_attr( $this->get_field_id( 'widget_position_x' ) ); ?>_center" name="<?php echo esc_attr( $this->get_field_name( 'widget_position_x' ) ); ?>" value="hCenter" />
									<label for="<?php echo esc_attr( $this->get_field_id( 'widget_position_x' ) ); ?>_center"><?php esc_html_e('Center', 'bookyourtravel') ?></label>
								</li>
								<li>
									<input <?php echo isset($instance['widget_position_x']) && ($instance['widget_position_x'] == 'hRight') ? 'checked' : ''; ?> type="radio" id="<?php echo esc_attr( $this->get_field_id( 'widget_position_x' ) ); ?>_right" name="<?php echo esc_attr( $this->get_field_name( 'widget_position_x' ) ); ?>" value="hRight" />
									<label for="<?php echo esc_attr( $this->get_field_id( 'widget_position_x' ) ); ?>_right"><?php esc_html_e('Right', 'bookyourtravel') ?></label>
								</li>
							</ul>
						</div>

						<div class="sidebar-hero" style="display:none">
							<span class="label"><?php esc_html_e('Vertical position', 'bookyourtravel') ?></span>
							<ul>
								<li>
									<input <?php echo isset($instance['widget_position_y']) && ($instance['widget_position_y'] == 'vTop') ? 'checked' : ''; ?> type="radio" id="<?php echo esc_attr( $this->get_field_id( 'widget_position_y' ) ); ?>_top" name="<?php echo esc_attr( $this->get_field_name( 'widget_position_y' ) ); ?>" value="vTop" />
									<label for="<?php echo esc_attr( $this->get_field_id( 'widget_position_y' ) ); ?>_top"><?php esc_html_e('Top', 'bookyourtravel') ?></label>
								</li>
								<li>
									<input <?php echo isset($instance['widget_position_y']) && ($instance['widget_position_y'] == 'vMiddle') ? 'checked' : ''; ?> type="radio" id="<?php echo esc_attr( $this->get_field_id( 'widget_position_y' ) ); ?>_middle" name="<?php echo esc_attr( $this->get_field_name( 'widget_position_y' ) ); ?>" value="vMiddle" />
									<label for="<?php echo esc_attr( $this->get_field_id( 'widget_position_y' ) ); ?>_middle"><?php esc_html_e('Middle', 'bookyourtravel') ?></label>
								</li>
								<li>
									<input <?php echo isset($instance['widget_position_y']) && ($instance['widget_position_y'] == 'vBottom') ? 'checked' : ''; ?> type="radio" id="<?php echo esc_attr( $this->get_field_id( 'widget_position_y' ) ); ?>_bottom" name="<?php echo esc_attr( $this->get_field_name( 'widget_position_y' ) ); ?>" value="vBottom" />
									<label for="<?php echo esc_attr( $this->get_field_id( 'widget_position_y' ) ); ?>_bottom"><?php esc_html_e('Bottom', 'bookyourtravel') ?></label>
								</li>
							</ul>
						</div>

						<p class="sidebar-hero" style="display:none">
							<label for="<?php echo esc_attr( $this->get_field_id( 'widget_padding_left_right' ) ); ?>"><?php esc_html_e('Horizontal offset - padding left/right (px)', 'bookyourtravel') ?></label>
							<input placeholder="0" min="0" type="number" id="<?php echo esc_attr( $this->get_field_id( 'widget_padding_left_right' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'widget_padding_left_right' ) ); ?>" value="<?php echo esc_attr( $instance['widget_padding_left_right'] ); ?>" />
						</p>
						<p class="sidebar-hero" style="display:none">
							<label for="<?php echo esc_attr( $this->get_field_id( 'widget_padding_top_bottom' ) ); ?>"><?php esc_html_e('Vertical offset - padding top/bottom (px)', 'bookyourtravel') ?></label>
							<input placeholder="0" min="0" type="number" id="<?php echo esc_attr( $this->get_field_id( 'widget_padding_top_bottom' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'widget_padding_top_bottom' ) ); ?>" value="<?php echo esc_attr( $instance['widget_padding_top_bottom'] ); ?>" />
						</p>

						<h4><?php esc_html_e("Blocks", "bookyourtravel"); ?></h4>
						<p>
							<label for="<?php echo esc_attr( $this->get_field_id( 'widget_block_count' ) ); ?>"><?php esc_html_e('Number of blocks', 'bookyourtravel') ?></label>
							<select class="widget_block_count" id="<?php echo esc_attr( $this->get_field_id( 'widget_block_count' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'widget_block_count' ) ); ?>">
							<?php for ($i = 1; $i <= 20; $i++) {?>
								<option <?php echo isset($instance['widget_block_count']) && intval($instance['widget_block_count']) == $i ? "selected" : "" ?> value="<?php echo sprintf("%d", $i); ?>"><?php echo sprintf("%d", $i); ?></option>
							<?php } ?>
							</select>
						</p>
						<dl class="blocks">
							<?php
							$widget_block_count = isset($instance['widget_block_count']) ? intval($instance['widget_block_count']) : 1;
							for ($i = 1; $i <= $widget_block_count; $i++) {
							?>
							<dt data-block="<?php echo sprintf("%d", $i); ?>" class="block-name">
								<span><?php echo sprintf(esc_html__("Block %d", "bookyourtravel"), $i); ?></span>
								<input class="block-index" value="<?php echo sprintf("%d", $i); ?>" id="<?php echo esc_attr( $this->get_field_id( 'widget_block_index_' . $i ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'widget_block_index_' . $i ) ); ?>" type="hidden" />
							</dt>
							<dd data-block="<?php echo sprintf("%d", $i); ?>">
								<label for="<?php echo esc_attr( $this->get_field_id( 'widget_block_width_' . $i ) ); ?>"><?php esc_html_e('Width', 'bookyourtravel') ?></label>
								<select id="<?php echo esc_attr( $this->get_field_id( 'widget_block_width_' . $i ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'widget_block_width_' . $i ) ); ?>">
									<option <?php echo isset($instance['widget_block_width_' . $i]) && $instance['widget_block_width_' . $i] == "one-sixth" ? "selected" : "" ?> value="one-sixth"><?php esc_html_e("1/6", "bookyourtravel"); ?></option>
									<option <?php echo isset($instance['widget_block_width_' . $i]) && $instance['widget_block_width_' . $i] == "one-fifth" ? "selected" : "" ?> value="one-fifth"><?php esc_html_e("1/5", "bookyourtravel"); ?></option>
									<option <?php echo isset($instance['widget_block_width_' . $i]) && $instance['widget_block_width_' . $i] == "one-fourth" ? "selected" : "" ?> value="one-fourth"><?php esc_html_e("1/4", "bookyourtravel"); ?></option>
									<option <?php echo isset($instance['widget_block_width_' . $i]) && $instance['widget_block_width_' . $i] == "one-third" ? "selected" : "" ?> value="one-third"><?php esc_html_e("1/3", "bookyourtravel"); ?></option>
									<option <?php echo isset($instance['widget_block_width_' . $i]) && $instance['widget_block_width_' . $i] == "two-fifth" ? "selected" : "" ?> value="two-fifth"><?php esc_html_e("2/5", "bookyourtravel"); ?></option>
									<option <?php echo isset($instance['widget_block_width_' . $i]) && $instance['widget_block_width_' . $i] == "one-half" ? "selected" : "" ?> value="one-half"><?php esc_html_e("1/2", "bookyourtravel"); ?></option>
									<option <?php echo isset($instance['widget_block_width_' . $i]) && $instance['widget_block_width_' . $i] == "three-fifth" ? "selected" : "" ?> value="three-fifth"><?php esc_html_e("3/5", "bookyourtravel"); ?></option>
									<option <?php echo isset($instance['widget_block_width_' . $i]) && $instance['widget_block_width_' . $i] == "two-third" ? "selected" : "" ?> value="two-third"><?php esc_html_e("2/3", "bookyourtravel"); ?></option>
									<option <?php echo isset($instance['widget_block_width_' . $i]) && $instance['widget_block_width_' . $i] == "three-fourth" ? "selected" : "" ?> value="three-fourth"><?php esc_html_e("3/4", "bookyourtravel"); ?></option>
									<option <?php echo isset($instance['widget_block_width_' . $i]) && $instance['widget_block_width_' . $i] == "four-fifth" ? "selected" : "" ?> value="four-fifth"><?php esc_html_e("4/5", "bookyourtravel"); ?></option>
									<option <?php echo isset($instance['widget_block_width_' . $i]) && $instance['widget_block_width_' . $i] == "full-width" ? "selected" : "" ?> value="full-width"><?php esc_html_e("1/1", "bookyourtravel"); ?></option>
								</select>
							</dd>
							<dd data-block="<?php echo sprintf("%d", $i); ?>">
								<label for="<?php echo esc_attr( $this->get_field_id( 'widget_block_order_' . $i ) ); ?>"><?php esc_html_e('Display order', 'bookyourtravel') ?></label>
								<input value="<?php echo isset($instance['widget_block_order_' . $i]) ? esc_attr( $instance['widget_block_order_' . $i] ) : 0; ?>" min="1" max="20" type="number" placeholder="1" id="<?php echo esc_attr( $this->get_field_id( 'widget_block_order_' . $i ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'widget_block_order_' . $i ) ); ?>" />
							</dd>
							<?php } ?>
						</dl>
					</div>
				</div>

				<?php

				$filter_args = array(
					'tab_id' => 'generic',
					'tab_label' => esc_html__('Generic filters', 'bookyourtravel'),
					'filter_entity_type' => 'generic',
					'filter_count_min' => 0,
					'filter_count_max' => 20,
					'filters_addon_class' => 'generic-filters',
					'filter_show_post_type_note' => false,
					'filter_include_show_for_controls' => true,
					'filter_types' => array(
						'calendar-date-from' => __('Calendar date from', 'bookyourtravel'),
						'calendar-date-to' => __('Calendar date to', 'bookyourtravel'),
						'star-rating-slider' => __('Star rating slider', 'bookyourtravel'),
						'user-rating-slider' => __('User rating slider', 'bookyourtravel'),
						'price-range-checkboxes' => __('Price range checkboxes', 'bookyourtravel'),
						'facility-checkboxes' => __('Facility checkboxes', 'bookyourtravel'),
					)
				);

				$this->render_filter_admin_tab($instance, $filter_args);

				$filter_args = array(
					'tab_id' => 'locations',
					'tab_label' => esc_html__('Location filters', 'bookyourtravel'),
					'filter_entity_type' => 'location',
					'filter_count_min' => 0,
					'filter_count_max' => 20,
					'filters_addon_class' => 'location-filters',
					'filter_include_show_for_controls' => true,
					'filter_types' => array(
						// 'location-tag-radios' => __('Location tag radios', 'bookyourtravel'),
						// 'location-tag-checkboxes' => __('Location tag checkboxes', 'bookyourtravel'),
						// 'location-type-radios' => __('Location type radios', 'bookyourtravel'),
						// 'location-type-checkboxes' => __('Location type checkboxes', 'bookyourtravel'),
						// 'location-name' => __('Location name', 'bookyourtravel'),
						'location-select' => __('Location select', 'bookyourtravel'),
						'location-by-type' => array(
							'label' => __('Type-filtered location select', 'bookyourtravel'),
							'callback' => 'list_location_types_callback'
						)
					)
				);

				$this->render_filter_admin_tab($instance, $filter_args);

				if ($this->enable_accommodations) {

					$filter_args = array(
						'tab_id' => 'accommodations',
						'tab_label' => esc_html__('Accommodation filters', 'bookyourtravel'),
						'filter_entity_type' => 'accommodation',
						'filter_count_min' => 0,
						'filter_count_max' => 20,
						'filters_addon_class' => 'accommodation-filters',
						'filter_types' => array(
							'accommodation-tag-radios' => __('Accommodation tag radios', 'bookyourtravel'),
							'accommodation-tag-checkboxes' => __('Accommodation tag checkboxes', 'bookyourtravel'),
							'accommodation-tag-select' => __('Accommodation tag select', 'bookyourtravel'),
							'accommodation-type-radios' => __('Accommodation type radios', 'bookyourtravel'),
							'accommodation-type-checkboxes' => __('Accommodation type checkboxes', 'bookyourtravel'),
							'accommodation-type-select' => __('Accommodation type select', 'bookyourtravel'),
							'accommodation-name' => __('Accommodation name', 'bookyourtravel'),
							'accommodation-name-select' => __('Accommodation name-select', 'bookyourtravel'),
							'accommodation-room-count' => __('Room count select', 'bookyourtravel'),
						)
					);

					$this->render_filter_admin_tab($instance, $filter_args);
				}

				if ($this->enable_car_rentals) {

					$filter_args = array(
						'tab_id' => 'carrentals',
						'tab_label' => esc_html__('Car rental filters', 'bookyourtravel'),
						'filter_entity_type' => 'carrental',
						'filter_count_min' => 0,
						'filter_count_max' => 20,
						'filters_addon_class' => 'carrental-filters',
						'filter_types' => array(
							'carrental-tag-radios' => __('Car rental tag radios', 'bookyourtravel'),
							'carrental-tag-checkboxes' => __('Car rental tag checkboxes', 'bookyourtravel'),
							'carrental-tag-select' => __('Car rental tag select', 'bookyourtravel'),
							'carrental-type-radios' => __('Car rental type radios', 'bookyourtravel'),
							'carrental-type-checkboxes' => __('Car rental type checkboxes', 'bookyourtravel'),
							'carrental-type-select' => __('Car rental type select', 'bookyourtravel'),							
							'carrental-name' => __('Car rental name', 'bookyourtravel'),
							'carrental-name-select' => __('Car rental name select', 'bookyourtravel'),
						)
					);

					$this->render_filter_admin_tab($instance, $filter_args);
				}

				if ($this->enable_cruises) {

					$filter_args = array(
						'tab_id' => 'cruises',
						'tab_label' => esc_html__('Cruise filters', 'bookyourtravel'),
						'filter_entity_type' => 'cruise',
						'filter_count_min' => 0,
						'filter_count_max' => 20,
						'filters_addon_class' => 'cruise-filters',
						'filter_types' => array(
							'cruise-tag-radios' => __('Cruise tag radios', 'bookyourtravel'),
							'cruise-tag-checkboxes' => __('Cruise tag checkboxes', 'bookyourtravel'),
							'cruise-tag-select' => __('Cruise tag select', 'bookyourtravel'),
							'cruise-type-radios' => __('Cruise type radios', 'bookyourtravel'),
							'cruise-type-checkboxes' => __('Cruise type checkboxes', 'bookyourtravel'),
							'cruise-type-select' => __('Cruise type select', 'bookyourtravel'),
							'cruise-duration-radios' => __('Cruise duration radios', 'bookyourtravel'),
							'cruise-duration-checkboxes' => __('Cruise duration checkboxes', 'bookyourtravel'),
							'cruise-duration-select' => __('Cruise duration select', 'bookyourtravel'),
							'cruise-name' => __('Cruise name', 'bookyourtravel'),
							'cruise-name-select' => __('Cruise name select', 'bookyourtravel'),
							'cruise-cabin-count' => __('Cabin count select', 'bookyourtravel'),
						)
					);

					$this->render_filter_admin_tab($instance, $filter_args);
				}

				if ($this->enable_tours) {

					$filter_args = array(
						'tab_id' => 'tours',
						'tab_label' => esc_html__('Tour filters', 'bookyourtravel'),
						'filter_entity_type' => 'tour',
						'filter_count_min' => 0,
						'filter_count_max' => 20,
						'filters_addon_class' => 'tour-filters',
						'filter_types' => array(
							'tour-tag-radios' => __('Tour tag radios', 'bookyourtravel'),
							'tour-tag-checkboxes' => __('Tour tag checkboxes', 'bookyourtravel'),
							'tour-tag-select' => __('Tour tag select', 'bookyourtravel'),
							'tour-type-radios' => __('Tour type radios', 'bookyourtravel'),
							'tour-type-checkboxes' => __('Tour type checkboxes', 'bookyourtravel'),
							'tour-type-select' => __('Tour type select', 'bookyourtravel'),
							'tour-duration-radios' => __('Tour duration radios', 'bookyourtravel'),
							'tour-duration-checkboxes' => __('Tour duration checkboxes', 'bookyourtravel'),
							'tour-duration-select' => __('Tour duration select', 'bookyourtravel'),
							'tour-name' => __('Tour name', 'bookyourtravel'),
							'tour-name-select' => __('Tour name select', 'bookyourtravel'),
						)
					);

					$this->render_filter_admin_tab($instance, $filter_args);
				} ?>
			</div>
		</div>
	<?php
	}

	function render_filter_admin_tab($instance, $filter_args) {

		$widget_block_count = isset($instance['widget_block_count']) ? intval($instance['widget_block_count']) : 1;

		$defaults = array(
			'tab_id' => 'post_types',
			'tab_label' => esc_html__('Post type filters', 'bookyourtravel'),
			'filter_entity_type' => 'post_type',
			'filter_count_label' => esc_html__('Number of filters', 'bookyourtravel'),
			'filter_count_min' => 0,
			'filter_count_max' => 20,
			'filter_show_post_type_note' => true,
			'filters_addon_class' => 'post_type-filters',
			'filter_include_show_for_controls' => false,
			'filter_types' => array(
				'post-type-tags-radios' => __('Post type tag radios', 'bookyourtravel'),
				'post-type-tags-checkboxes' => __('Post type tag checkboxes', 'bookyourtravel'),
				'post-type-types-radios' => __('Post type type radios', 'bookyourtravel'),
				'post-type-types-checkboxes' => __('Post type type checkboxes', 'bookyourtravel'),
				'post-type-name' => __('Post type name', 'bookyourtravel'),
			)
		);

		$args = wp_parse_args($filter_args, $defaults);

		extract($args);

		$field_id_count = sprintf('widget_%s_filter_count', $filter_entity_type);
		$field_id_index = sprintf('widget_%s_filter_index_%s', $filter_entity_type, '%d');
		$field_id_block = sprintf('widget_%s_filter_block_%s', $filter_entity_type, '%d');
		$field_id_order = sprintf('widget_%s_filter_order_%s', $filter_entity_type, '%d');
		$field_id_width = sprintf('widget_%s_filter_width_%s', $filter_entity_type, '%d');
		$field_id_label = sprintf('widget_%s_filter_label_%s', $filter_entity_type, '%d');
		$field_id_override_id = sprintf('widget_%s_filter_override_id_%s', $filter_entity_type, '%d');
		$field_id_type = sprintf('widget_%s_filter_type_%s', $filter_entity_type, '%d');

		$field_id_show_for_accommodations = sprintf('widget_%s_filter_show_for_accommodations_%s', $filter_entity_type, '%d');
		$field_id_show_for_car_rentals = sprintf('widget_%s_filter_show_for_car_rentals_%s', $filter_entity_type, '%d');
		$field_id_show_for_cruises = sprintf('widget_%s_filter_show_for_cruises_%s', $filter_entity_type, '%d');
		$field_id_show_for_tours = sprintf('widget_%s_filter_show_for_tours_%s', $filter_entity_type, '%d');
		$field_id_type_limiter = sprintf('widget_%s_filter_type_limiter_%s', $filter_entity_type, '%d');
	?>
	<div id="<?php echo esc_attr($tab_id); ?>" class="tab-content">
		<div class="inner">
			<h3><?php echo esc_html($tab_label); ?></h3>
			<label for="<?php echo esc_attr( $this->get_field_id( $field_id_count) ); ?>"><?php echo esc_html($filter_count_label); ?></label>
			<select class="<?php echo esc_attr($field_id_count); ?>" id="<?php echo esc_attr( $this->get_field_id( $field_id_count ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( $field_id_count ) ); ?>">
			<?php for ($i = $filter_count_min; $i <= $filter_count_max; $i++) {?>
				<option <?php echo isset($instance[$field_id_count]) && intval($instance[$field_id_count]) == $i ? "selected" : "" ?> value="<?php echo sprintf("%d", $i); ?>"><?php echo sprintf("%d", $i); ?></option>
			<?php } ?>
			</select>
			<?php if ($filter_show_post_type_note) {?>
			<p class="note"><?php esc_html_e("Note: These filters will only shown when the user is searching for this specific post type", "bookyourtravel"); ?></p>
			<?php } ?>
			<dl class="filters <?php echo esc_attr($filters_addon_class); ?>">
				<dt class="placeholder" data-<?php echo esc_attr($filter_entity_type); ?>-filter="-1"></dt>
				<dd class="placeholder" data-<?php echo esc_attr($filter_entity_type); ?>-filter="-1"></dd>

				<?php
				$widget_filter_count = isset($instance[$field_id_count]) ? intval($instance[$field_id_count]) : 0;
				for ($i = 1; $i <= $widget_filter_count; $i++) {
				?>

				<dt data-<?php echo esc_attr($filter_entity_type); ?>-filter="<?php echo sprintf("%d", $i); ?>" class="filter-name">
					<span><?php echo sprintf(esc_html__("Filter %d", "bookyourtravel"), $i); ?></span>
					<input class="<?php echo esc_attr($filter_entity_type); ?>-filter-index" value="<?php echo sprintf("%d", $i); ?>" id="<?php echo esc_attr( $this->get_field_id( sprintf($field_id_index, $i) ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( sprintf($field_id_index, $i) ) ); ?>" type="hidden" />
					<a href="#" class="remove-filter" data-filter-type="<?php echo esc_attr($filter_entity_type); ?>" data-filter-index="<?php echo sprintf("%d", $i); ?>"><?php _e("Remove", "bookyourtravel"); ?></a>
				</dt>

				<?php if ($filter_include_show_for_controls && ($this->enable_accommodations || $this->enable_car_rentals || $this->enable_cruises || $this->enable_tours)) {?>

				<dd data-<?php echo esc_attr($filter_entity_type); ?>-filter="<?php echo sprintf("%d", $i); ?>" class="show-for">
					<span class="label"><?php echo sprintf(esc_html__("Show only for?", "bookyourtravel"), $i); ?></span>
					<ul>
						<?php if ($this->enable_accommodations) { ?>
						<li>
							<input <?php echo isset($instance[sprintf($field_id_show_for_accommodations, $i)]) && intval($instance[sprintf($field_id_show_for_accommodations, $i)]) == '1' ? "checked" : "" ?> value="1" type="checkbox" id="<?php echo esc_attr( $this->get_field_id( sprintf($field_id_show_for_accommodations, $i) ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( sprintf($field_id_show_for_accommodations, $i) ) ); ?>">
							<label for="<?php echo esc_attr( $this->get_field_id( sprintf($field_id_show_for_accommodations, $i) ) ); ?>"><?php esc_html_e('Accommodations', 'bookyourtravel') ?></label>
						</li>
						<?php } ?>
						<?php if ($this->enable_car_rentals) { ?>
						<li>
							<input <?php echo isset($instance[sprintf($field_id_show_for_car_rentals, $i)]) && intval($instance[sprintf($field_id_show_for_car_rentals, $i)]) == '1' ? "checked" : "" ?> value="1" type="checkbox" id="<?php echo esc_attr( $this->get_field_id( sprintf($field_id_show_for_car_rentals, $i) ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( sprintf($field_id_show_for_car_rentals, $i) ) ); ?>">
							<label for="<?php echo esc_attr( $this->get_field_id( sprintf($field_id_show_for_car_rentals, $i) ) ); ?>"><?php esc_html_e('Car rentals', 'bookyourtravel') ?></label>
						</li>
						<?php } ?>
						<?php if ($this->enable_cruises) { ?>
						<li>
							<input <?php echo isset($instance[sprintf($field_id_show_for_cruises, $i)]) && intval($instance[sprintf($field_id_show_for_cruises, $i)]) == '1' ? "checked" : "" ?> value="1" type="checkbox" id="<?php echo esc_attr( $this->get_field_id( sprintf($field_id_show_for_cruises, $i) ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( sprintf($field_id_show_for_cruises, $i) ) ); ?>">
							<label for="<?php echo esc_attr( $this->get_field_id( sprintf($field_id_show_for_cruises, $i) ) ); ?>"><?php esc_html_e('Cruises', 'bookyourtravel') ?></label>
						</li>
						<?php } ?>
						<?php if ($this->enable_tours) { ?>
						<li>
							<input <?php echo isset($instance[sprintf($field_id_show_for_tours, $i)]) && intval($instance[sprintf($field_id_show_for_tours, $i)]) == '1' ? "checked" : "" ?> value="1" type="checkbox" id="<?php echo esc_attr( $this->get_field_id( sprintf($field_id_show_for_tours, $i) ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( sprintf($field_id_show_for_tours, $i) ) ); ?>">
							<label for="<?php echo esc_attr( $this->get_field_id( sprintf($field_id_show_for_tours, $i) ) ); ?>"><?php esc_html_e('Tours', 'bookyourtravel') ?></label>
						</li>
						<?php } ?>
					</ul>
				</dd>

				<?php } ?>

				<dd data-<?php echo esc_attr($filter_entity_type); ?>-filter="<?php echo sprintf("%d", $i); ?>" class="filter-show-in-block">
					<label for="<?php echo esc_attr( $this->get_field_id( sprintf($field_id_block, $i) ) ); ?>"><?php esc_html_e('Show in block', 'bookyourtravel') ?></label>
					<select id="<?php echo esc_attr( $this->get_field_id( sprintf($field_id_block, $i) ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( sprintf($field_id_block, $i) ) ); ?>">
					<?php for ($j = 1; $j <= $widget_block_count; $j++) {?>
						<option <?php echo isset($instance[sprintf($field_id_block, $i)]) && intval($instance[sprintf($field_id_block, $i)]) == $j ? "selected" : "" ?> value="<?php echo esc_attr($j); ?>"><?php echo esc_attr($j); ?></option>
					<?php } ?>
					</select>
				</dd>
				<dd data-<?php echo esc_attr($filter_entity_type); ?>-filter="<?php echo sprintf("%d", $i); ?>" class="filter-width">
					<label for="<?php echo esc_attr( $this->get_field_id( sprintf($field_id_width, $i) ) ); ?>"><?php esc_html_e('Width', 'bookyourtravel') ?></label>
					<select id="<?php echo esc_attr( $this->get_field_id( sprintf($field_id_width, $i) ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( sprintf($field_id_width, $i) ) ); ?>">
						<option <?php echo isset($instance[sprintf($field_id_width, $i)]) && $instance[sprintf($field_id_width, $i)] == "full-width" ? "selected" : "" ?> value="full-width"><?php esc_html_e("1/1", "bookyourtravel"); ?></option>
						<option <?php echo isset($instance[sprintf($field_id_width, $i)]) && $instance[sprintf($field_id_width, $i)] == "four-fifth" ? "selected" : "" ?> value="four-fifth"><?php esc_html_e("4/5", "bookyourtravel"); ?></option>
						<option <?php echo isset($instance[sprintf($field_id_width, $i)]) && $instance[sprintf($field_id_width, $i)] == "three-fourth" ? "selected" : "" ?> value="three-fourth"><?php esc_html_e("3/4", "bookyourtravel"); ?></option>
						<option <?php echo isset($instance[sprintf($field_id_width, $i)]) && $instance[sprintf($field_id_width, $i)] == "two-third" ? "selected" : "" ?> value="two-third"><?php esc_html_e("2/3", "bookyourtravel"); ?></option>
						<option <?php echo isset($instance[sprintf($field_id_width, $i)]) && $instance[sprintf($field_id_width, $i)] == "three-fifth" ? "selected" : "" ?> value="three-fifth"><?php esc_html_e("3/5", "bookyourtravel"); ?></option>
						<option <?php echo isset($instance[sprintf($field_id_width, $i)]) && $instance[sprintf($field_id_width, $i)] == "one-half" ? "selected" : "" ?> value="one-half"><?php esc_html_e("1/2", "bookyourtravel"); ?></option>
						<option <?php echo isset($instance[sprintf($field_id_width, $i)]) && $instance[sprintf($field_id_width, $i)] == "two-fifth" ? "selected" : "" ?> value="two-fifth"><?php esc_html_e("2/5", "bookyourtravel"); ?></option>
						<option <?php echo isset($instance[sprintf($field_id_width, $i)]) && $instance[sprintf($field_id_width, $i)] == "one-third" ? "selected" : "" ?> value="one-third"><?php esc_html_e("1/3", "bookyourtravel"); ?></option>
						<option <?php echo isset($instance[sprintf($field_id_width, $i)]) && $instance[sprintf($field_id_width, $i)] == "one-fourth" ? "selected" : "" ?> value="one-fourth"><?php esc_html_e("1/4", "bookyourtravel"); ?></option>
						<option <?php echo isset($instance[sprintf($field_id_width, $i)]) && $instance[sprintf($field_id_width, $i)] == "one-fifth" ? "selected" : "" ?> value="one-fifth"><?php esc_html_e("1/5", "bookyourtravel"); ?></option>
						<option <?php echo isset($instance[sprintf($field_id_width, $i)]) && $instance[sprintf($field_id_width, $i)] == "one-sixth" ? "selected" : "" ?> value="one-sixth"><?php esc_html_e("1/6", "bookyourtravel"); ?></option>
					</select>
				</dd>
				<dd data-<?php echo esc_attr($filter_entity_type); ?>-filter="<?php echo sprintf("%d", $i); ?>" class="filter-order">
					<label for="<?php echo esc_attr( $this->get_field_id( sprintf($field_id_order, $i) ) ); ?>"><?php esc_html_e('Display order', 'bookyourtravel') ?></label>
					<input value="<?php echo isset($instance[sprintf($field_id_order, $i)]) ? esc_attr( $instance[sprintf($field_id_order, $i)] ) : 0; ?>" type="number" min="1" max="20" placeholder="1" id="<?php echo esc_attr( $this->get_field_id( sprintf($field_id_order, $i) ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( sprintf($field_id_order, $i) ) ); ?>" />
				</dd>
				<dd data-<?php echo esc_attr($filter_entity_type); ?>-filter="<?php echo sprintf("%d", $i); ?>" class="filter-label">
					<label for="<?php echo esc_attr( $this->get_field_id( sprintf($field_id_label, $i) ) ); ?>"><?php esc_html_e('Label', 'bookyourtravel') ?></label>
					<input value="<?php echo isset($instance[sprintf($field_id_label, $i)]) ? esc_attr( $instance[sprintf($field_id_label, $i)] ) : ''; ?>" type="text" id="<?php echo esc_attr( $this->get_field_id( sprintf($field_id_label, $i) ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( sprintf($field_id_label, $i) ) ); ?>" />
				</dd>
				<dd data-<?php echo esc_attr($filter_entity_type); ?>-filter="<?php echo sprintf("%d", $i); ?>" class="filter-type">
					<?php $filter_callback = ''; ?>
					<label for="<?php echo esc_attr( $this->get_field_id( sprintf($field_id_type, $i) ) ); ?>"><?php esc_html_e('Type', 'bookyourtravel') ?></label>
					<select id="<?php echo esc_attr( $this->get_field_id( sprintf($field_id_type, $i) ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( sprintf($field_id_type, $i) ) ); ?>">
					<?php 
					$instance_key = isset($instance[sprintf($field_id_type, $i)]) ? $instance[sprintf($field_id_type, $i)] : '';
					foreach ($filter_types as $filter_key => $filter_value) {						
						$filter_label = '';
						if (is_array($filter_value) && isset($filter_value['label'])) {
							$filter_label = $filter_value['label'];
						} else {
							$filter_label = $filter_value;
						}
						$filter_callback = '';
						if ($instance_key == $filter_key && (is_array($filter_value) && isset($filter_value['callback']))) {
							$filter_callback = $filter_value['callback'];
						}
						?>
						<option <?php echo $instance_key == $filter_key ? "selected" : "" ?> value="<?php echo esc_attr($filter_key); ?>">
							<?php echo esc_html($filter_label); ?>
						</option>
					<?php } ?>
					</select>
					<?php
					if (!empty($filter_callback) && method_exists($this, $filter_callback)) {
						$key_label_array = $this->$filter_callback();
						if (count($key_label_array) > 0) {?>
						<div class="limitedto">
							<span class="label"><?php esc_html_e('Limited to location types', 'bookyourtravel') ?></span>
						<?php
							$key_id = sprintf($field_id_type_limiter, $i);
							$limiter_instance_values = isset($instance[sprintf($field_id_type_limiter, $i)]) ? (array)$instance[sprintf($field_id_type_limiter, $i)] : array();
							foreach ($key_label_array as $limiter_key => $limiter_label) {
								$checked = in_array($limiter_key, $limiter_instance_values) ? "checked" : "";
							?>
							<input type="checkbox" <?php echo esc_attr($checked); ?>
							 id="<?php echo esc_attr( $this->get_field_id( sprintf($field_id_type_limiter, $i) ) ); ?>_<?php echo esc_attr($limiter_key); ?>"
							 name="<?php echo esc_attr( $this->get_field_name( sprintf($field_id_type_limiter, $i) ) ); ?>[]"
							 value="<?php echo esc_attr($limiter_key); ?>">
							 <label for="<?php echo esc_attr( $this->get_field_id( sprintf($field_id_type_limiter, $i) ) ); ?>_<?php echo esc_attr($limiter_key); ?>"><?php echo esc_html($limiter_label); ?></label>
							<?php
							}
							?>
						</div>
						<?php
						}
					}
					?>
				</dd>
				<?php 
				if ($instance_key == 'location-by-type') { ?>
				<dd data-<?php echo esc_attr($filter_entity_type); ?>-filter="<?php echo sprintf("%d", $i); ?>" class="filter-override-id">
					<label for="<?php echo esc_attr( $this->get_field_id( sprintf($field_id_override_id, $i) ) ); ?>"><?php esc_html_e('Override field id', 'bookyourtravel') ?></label>
					<input value="<?php echo isset($instance[sprintf($field_id_override_id, $i)]) ? esc_attr( $instance[sprintf($field_id_override_id, $i)] ) : ''; ?>" type="text" id="<?php echo esc_attr( $this->get_field_id( sprintf($field_id_override_id, $i) ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( sprintf($field_id_override_id, $i) ) ); ?>" />
				</dd>
				<?php } ?>

				<?php } ?>
			</dl>
		</div>
	</div>
	<?php
	}

	function get_taxonomy_terms_array($taxonomy_type) {
		$terms = get_categories( array(
			'taxonomy'		=> $taxonomy_type,
			'orderby' 		=> 'name',
			'order'   		=> 'ASC',
			'hide_empty'    => 0
		) );

		$key_label_array = array();

		foreach ($terms as $term) {
			$key_label_array[$term->term_id] = $term->name;
		}

		asort($key_label_array);

		return $key_label_array;
	}

	function list_location_types_callback() {
		return $this->get_taxonomy_terms_array('location_type');
	}
}
