<?php
/**
 * BookYourTravel_Theme_Of_Custom class
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class BookYourTravel_Theme_Of_Custom extends BookYourTravel_BaseSingleton {
	
	protected function __construct() {
	
        // our parent class might contain shared code in its constructor
        parent::__construct();		
    }
	
    public function init() {
		
		add_filter( 'optionsframework_repeat_tab', array( $this, 'repeat_tab_option_type' ), 10, 3 );
		add_filter( 'optionsframework_repeat_extra_field', array( $this, 'repeat_extra_field_option_type' ), 10, 3 );
		add_filter( 'optionsframework_file_status_info_field', array( $this, 'file_status_info_field_option_type' ), 10, 3 );
		add_filter( 'optionsframework_page_status_info_field', array( $this, 'page_status_info_field_option_type' ), 10, 3 );
		add_filter( 'optionsframework_link_button_field', array( $this, 'link_button_field_option_type' ), 10, 3 );
		add_filter( 'optionsframework_dummy_text', array( $this, 'dummy_text_option_type' ), 10, 3 );
		add_filter( 'optionsframework_sub_heading', array( $this, 'sub_heading_option_type' ), 10, 3 );
		add_filter( 'optionsframework_repeat_review_field', array( $this, 'repeat_review_field_option_type' ), 10, 3 );
		add_filter( 'optionsframework_repeat_form_field', array( $this, 'repeat_form_field_option_type' ), 10, 3 );
		add_filter( 'of_sanitize_repeat_form_field', array( $this, 'sanitize_repeat_form_field' ), 10, 2 );
		add_filter( 'of_sanitize_repeat_review_field', array( $this, 'sanitize_repeat_review_field' ), 10, 2 );
		add_filter( 'of_sanitize_repeat_extra_field', array( $this, 'sanitize_repeat_extra_field' ), 10, 2 );
		add_filter( 'of_sanitize_repeat_tab', array( $this, 'sanitize_repeat_tab' ), 10, 2 );

		add_action( 'optionsframework_custom_scripts', array( $this, 'of_bookyourtravel_options_script' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_optionsframework_scripts_styles' ) );
		add_action( 'optionsframework_after', array($this, 'optionsframework_after'));		
	}
	
	public function optionsframework_after() {
		$output = '<div id="tabs_icon_preview_div" class="icon-preview" style="display:none;">';
		$output .= '<div class="icons">';
		$output .= '</div>';
		$output .= '</div>';
		echo $output;		
	}
	
	public static function the_field($option_id, $field, $entity_obj, $container_class = "text-wrap", $label_is_header = true, $container_is_tr = false) {
		global $bookyourtravel_theme_of_custom;
		
		if (is_array($field)) {
			$field_is_hidden = isset($field['hide']) ? intval($field['hide']) : 0;
			$field_is_hidden_front = isset($field['hide_front']) ? intval($field['hide_front']) : 0;
			
			if (!$field_is_hidden && !$field_is_hidden_front) {
			
				$field_id = isset($field['id']) ? $field['id'] : '';
				$field_label = isset($field['label']) ? $field['label'] : '';
				$field_label = $bookyourtravel_theme_of_custom->get_translated_dynamic_string($bookyourtravel_theme_of_custom->get_option_id_context($option_id) . ' ' . $field_label, $field_label);
				$field_type = isset($field['type']) ? $field['type'] : ''; 
				
				if ($field_type == 'text' || $field_type == 'textarea' || $field_type == 'select' || $field_type == 'checkbox' || $field_type == 'slider' ) {
					if (!empty($field_id) && !empty($field_label)) {
					
						$field_value = $entity_obj->get_custom_field($field_id);

						if ($field_type == 'checkbox') {
							$field_value = intval($field_value) == 1 ? __('Yes', 'bookyourtravel') : __('No', 'bookyourtravel');
						}

						if ($label_is_header)
							self::the_field_inner($container_class . ' ' . $field_id, 	"", "", $field_value, $field_label, false, true, $container_is_tr, $field_type);
						else
							self::the_field_inner($container_class . ' ' . $field_id, 	"", $field_label, $field_value, "", false, true, $container_is_tr, $field_type);
					}
				} elseif ($field_type == 'image') {
					$field_image_uri = $entity_obj->get_custom_field_image_uri($field_id, 'medium');
					if (!empty($field_image_uri)) 
						echo '<img src="' . $field_image_uri . '" alt="' . $field_label . '" />';
				}
			}
		}
	}	
	
	public static function the_field_inner($container_css_class, $label_css_class, $label_text, $field_value, $header_text = '', $is_paragraph = false, $hide_empty = false, $container_is_tr = false, $field_type = 'text') {

		$render = !empty($field_value) || (!empty($label_text) && !$hide_empty);
		
		if ($render) {

			$ret_val = '';
		
			$container_started = false;
			if (!empty($container_css_class)) {
				if ($container_is_tr)
					$ret_val .= sprintf("<tr class='%s'>", $container_css_class);
				else
					$ret_val .= sprintf("<div class='%s'>", $container_css_class);
				$container_started = true;
			}

			if (!empty($header_text) && !$container_is_tr)
				$ret_val .= sprintf("<h2>%s</h2>", $header_text);
			
			if ($is_paragraph && !$container_is_tr)
				$ret_val .= '<p>';

			if (!empty($label_text) || !empty($label_css_class)) {
				if ($container_is_tr)
					$ret_val .= sprintf("<th class='%s'>%s</th>", $label_css_class, $label_text);
				else 
					$ret_val .= sprintf("<span class='%s'>%s</span>", $label_css_class, $label_text);
			}
			
			$field_value = BookyourTravel_Theme_Utils::cleanup_shortcodes_in_tab_field_content($field_value);
		
			if (!($container_is_tr) && !$is_paragraph && ($field_type == 'textarea' || $field_type == 'text')) {
				$field_value = wpautop($field_value, true);
				if (!strpos($container_css_class, 'text-wrap')) {				
					$container_css_class .= ' text-wrap';
				}
			}

			$field_value = BookyourTravel_Theme_Utils::fix_shortcodes_autop($field_value);
			
			$shortcode_html = do_shortcode($field_value);

			if (!empty($shortcode_html) && $shortcode_html != $field_value) {
				$field_value = $shortcode_html;
			}
			
			if (!empty($field_value)) {
				if ($container_is_tr)
					$ret_val .= sprintf('<td>%s</td>', $field_value);
				else
					$ret_val .= $field_value;
			} else {
				if ($container_is_tr)
					$ret_val .= '<td></td>';
			}
			
			if ($is_paragraph && !$container_is_tr)
				$ret_val .= '</p>';
				
			if (!empty($container_css_class) && $container_started) {
				if ($container_is_tr)
					$ret_val .= '</tr>';
				else
					$ret_val .= '</div>';
			}

			$ret_val = apply_filters('bookyourtravel_the_field', $ret_val, $container_css_class, $label_css_class, $label_text, $field_value, $header_text, $is_paragraph);

			echo $ret_val;
		}
	}	
	
	public static function find_element($extra_fields, $field_id) {
	
		$found_field = null;
	
		if (isset($extra_fields) && isset($field_id)) {
			foreach ($extra_fields as $extra_field) {				
				if (isset($extra_field['id'])) {					
					if ($extra_field['id'] == $field_id) {
						$found_field = $extra_field;
						break;
					}					
				}				
			}
		}
	
		return $found_field;
	}	

	public static function tab_has_fields($option_id, $extra_fields, $tab_id, $entity_obj) {
	
		global $bookyourtravel_theme_of_custom;
		$count = 0;
		
		$extra_fields = BookYourTravel_Theme_Utils::custom_array_search($extra_fields, 'tab_id', $tab_id); 
		
		if (is_array($extra_fields) && count($extra_fields) > 0) {
		
			foreach ($extra_fields as $extra_field) {
		
				$field_is_hidden = isset($extra_field['hide']) ? intval($extra_field['hide']) : 0;
				$field_is_hidden_front = isset($extra_field['hide_front']) ? intval($extra_field['hide_front']) : 0;
		
				if (!$field_is_hidden && !$field_is_hidden_front) {					
					$field_id = isset($extra_field['id']) ? $extra_field['id'] : '';
					$value = $entity_obj->get_custom_field($field_id);
					
					if (!empty($value)) {
						$count++;
					}
				}
			}
		}
		
		return $count > 0;	
	}
	
	/**
	 * 	Function that renders all extra fields tied to an entity tab, as labeled field in the form of
	 * 	<div class="container_css_class">
	 *		<span class="label_css_class">$label_text</span> $field_value
	 *	</div>
	 */
	public static function the_tab_extra_fields($option_id, $extra_fields, $tab_id, $entity_obj, $container_class = "text-wrap", $label_is_header = true, $container_is_tr = false) {
		
		global $bookyourtravel_theme_of_custom;
		
		$extra_fields = BookYourTravel_Theme_Utils::custom_array_search($extra_fields, 'tab_id', $tab_id); 
		
		if (is_array($extra_fields)) {
			foreach ($extra_fields as $extra_field) {
				self::the_field($option_id, $extra_field, $entity_obj, $container_class, $label_is_header, $container_is_tr);
			}
		}
	}
	
	/**
	 * Function that renders tab in the form of
	 * <li class="tab_css_class" id="$tab_id">$tab_content</li>
	 */
	public static function the_tab($page_post_type, $tab_css_class, $tab_id, $tab_content, $echo = true) {
		$ret_val = sprintf("<li class='%s'", $tab_css_class);
		if (!empty($tab_id)) {
			$ret_val .= sprintf(" id='%s'", $tab_id);
		}
		$ret_val .= sprintf(">%s</li>", $tab_content);
		$ret_val = apply_filters('bookyourtravel_render_tab', $ret_val, $page_post_type, $tab_css_class, $tab_id, $tab_content);
		if ($echo)
			echo $ret_val;
		else
			return $ret_val;
	}	
	
	function enqueue_admin_optionsframework_scripts_styles() {
		wp_register_script('bookyourtravel-optionsframework-custom', BookYourTravel_Theme_Utils::get_file_uri('/js/admin/optionsframework_custom.js'), array('jquery', 'jquery-ui-core', 'jquery-ui-sortable', 'bookyourtravel-admin-script'), '1.0.0');
		wp_enqueue_script('bookyourtravel-optionsframework-custom');
	}
	
	public function register_dynamic_string_for_translation($name, $value) {
		if (function_exists('icl_register_string')) {
			icl_register_string('BookYourTravel Theme', $name, $value);
		}
	}
	
	public function get_translated_dynamic_string($name, $value) {
		if (function_exists('icl_t')) {
			return icl_t('BookYourTravel Theme', $name, $value);
		}
		return $value;
	}
	
	public static function of_element_exists($element_array, $element_id) {
		
		$exists = false;
		foreach ($element_array as $element) {		
			if (isset($element['id']) && $element['id'] == $element_id) {
				$exists = true;
				break;
			}		
		}
		return $exists;
	}

	/*
	 * Add custom, repeatable input fields to options framework thanks to HelgaTheViking
	 * https://gist.github.com/helgatheviking/6022215
	 */
	public function repeat_tab_option_type( $option_name, $option, $values ) {

		global $bookyourtravel_theme_of_default_fields, $repeatable_field_types, $bookyourtravel_theme_globals;
		$max_tab_index = -1;
	
		$counter = 0;
		$used_indices = array();
		
		$default_values = $bookyourtravel_theme_of_default_fields->get_default_tab_array($option['id']);
		
		$values = BookYourTravel_Theme_Of_Default_Fields::merge_fields_and_defaults($values, $default_values);
		
		$output = '<div class="of-repeat-loop">';
		$output .= '<ul class="sortable of-repeat-tabs">';
	 
		if( is_array( $values ) ) { 
		
			foreach ((array)$values as $value ) {
				
				if (isset($value['label']) && isset($value['id'])) {
				
					$tab_label 	= $value['label'];
					$tab_id		= $value['id'];
					$tab_icon_class = isset($value['icon_class']) ? $value['icon_class'] : '';
					$tab_hidden = isset($value['hide']) && $value['hide'] == '1' ? true : false;
					$tab_index 	= isset($value['index']) && strlen(trim($value['index'])) > 0 ? intval($value['index']) : $counter;					
					
					$is_default = (count(BookYourTravel_Theme_Utils::custom_array_search($default_values, 'id', $tab_id)) > 0);
					
					if (in_array($tab_index, $used_indices)) {
						$tab_index = $this->find_available_index($tab_index, $used_indices);
					}
					$used_indices[] = $tab_index;
					
					$output .= '<li class="ui-state-default of-repeat-group of-repeat-tab' . esc_attr($tab_id) . '">';
					
					$output .= '<div class="of-input-wrap">';
					$output .= '<label for="' . $option_name . '[' . $option['id'] . ']' . '['.$tab_index.'][label]" class="label-tab-label">' . esc_html__('Tab name', 'bookyourtravel') . '</label>';					
					$output .= $this->render_dynamic_field_input($option_name . '[' . $option['id'] . ']', 'of-input input-tab-label input-label-for-dynamic-id', $option_name . '[' . $option['id'] . ']['.$tab_index.'][label]', '', 'text', $tab_label, esc_html__('Enter tab name', 'bookyourtravel'), ' data-is-default="' . ($is_default ? '1' : '0') . '" data-original-id="' . esc_attr($tab_id) . '"');
					$output .= '</div>';
					$output .= '<div class="of-input-wrap of-modify-id">';
					$output .= '<label for="' . $option_name . '[' . $option['id'] . ']' . '['.$tab_index.'][id]" class="label-tab-id">' . esc_html__('Tab ID', 'bookyourtravel') . '</label>';					
					$output .= $this->render_dynamic_field_input($option_name . '[' . $option['id'] . ']', 'of-input input-tab-id input-dynamic-id', $option_name . '[' . $option['id'] . ']['.$tab_index.'][id]', '', 'text', $tab_id, esc_html__('Tab id is generated automatically.', 'bookyourtravel'), 'readonly="readonly" data-is-default="' . ($is_default ? '1' : '0') . '" data-original-id="' . esc_attr($tab_id) . '" data-id="' . esc_attr($tab_id) . '" data-parent="' . esc_attr($option['id']) . '"');
					$output .= '<div class="loading" style="display:none;"></div>';
					$output .= '</div>';					
					$output .= '<div class="of-input-wrap">';
					$output .= '<label for="' . $option_name . '[' . $option['id'] . ']' . '['.$tab_index.'][icon_class]" class="label-tab-icon_class">' . esc_html__('Tab icon', 'bookyourtravel') . '</label>';										
					$output .= '<div class="of-icon-select">';
					$output .= $this->render_dynamic_field_input($option_name . '[' . $option['id'] . ']', 'of-input input-tab-icon-class icon_class input-icon-class', $option_name . '[' . $option['id'] . ']['.$tab_index.'][icon_class]', '', 'text', $tab_icon_class, esc_html__('Tab icon', 'bookyourtravel'), ' data-is-default="' . ($is_default ? '1' : '0') . '" data-original-id="' . esc_attr($tab_id) . '"');
					if (!empty($tab_icon_class)) {
						$output .= '<span class="lightbox-icon icon material-icons">' . esc_attr($tab_icon_class) . '</span>';
					} else {
						$output .= '<span class="lightbox-icon icon"></span>';
					}
					$output .= '<a href="#TB_inline?height=700&width=700&inlineId=tabs_icon_preview_div" class="thickbox thickbox_link thickbox' . esc_attr($tab_id) . '">' . __('Select icon', 'bookyourtravel') . '</a>';
					$output .= '</div>';					
					$output .= '</div>';
					
					$output .= '<div class="checboxes">';
					$output .= $this->render_dynamic_checkbox($option_name, $option, '['.$tab_index.'][modify]', 'label-tab-modify', 'checkbox-tab-modify modify-dynamic-element-id', esc_html__('Modify id?', 'bookyourtravel'));
					$output .= $this->render_dynamic_checkbox($option_name, $option, '['.$tab_index.'][hide]', 'label-tab-hide', 'checkbox-tab-hide', esc_html__('Hidden?', 'bookyourtravel'), ($tab_hidden ? 'checked' : ''));
					$output .= '</div>';
					$output .= '<input data-rel="' . esc_attr( $option_name . '[' . $option['id'] . ']' ) . '" class="input-index" name="' . esc_attr( $option_name . '[' . $option['id'] . ']['.$tab_index.'][index]' ) . '" type="hidden" value="' . $tab_index . '" />';
					
					if (!$is_default) {
						$output .= '<span class="ui-icon ui-icon-close"></span>';
					}
					$output .= '</li><!--.of-repeat-group-->';
			 
					$max_tab_index = $tab_index > $max_tab_index ? $tab_index : $max_tab_index;
					
					$counter++;
				}
			}
		}
	 
		$output .= '</ul><!--.sortable-->';
		$output .= '<input type="hidden" class="max_tab_index" value="' . $max_tab_index . '" />';
		$output .= '<a href="#" class="docopy_tab button icon add">' . esc_html__('Add tab', 'bookyourtravel') . '</a>';
		$output .= '</div><!--.of-repeat-loop-->';
	 
		return $output;
	}

	function get_gdpr_checkbox_field() {
		global $bookyourtravel_theme_globals;
		$basic_gdpr_agree_text = $bookyourtravel_theme_globals->get_basic_gdpr_agreement_text();
		return array('label' => $basic_gdpr_agree_text, 'id' => 'agree_gdpr', 'type' => 'checkbox', 'hide' => 0, 'required' => 1);
	}
	
	function repeat_form_field_option_type( $option_name, $option, $values ) {

		global $bookyourtravel_theme_of_default_fields, $form_field_types, $bookyourtravel_theme_globals;
	
		$max_field_index = -1;
		
		$counter = 0;
		
		$default_values = $bookyourtravel_theme_of_default_fields->get_default_form_fields_array($option['id']);
		
		$values = BookYourTravel_Theme_Of_Default_Fields::merge_fields_and_defaults($values, $default_values);		
		
		$form_type = '';
		if ($option['id'] == 'inquiry_form_fields') {
			$form_type = 'inquiry';
		} else if ($option['id'] == 'booking_form_fields') {
			$form_type = 'booking';
		}
			
		$used_indices = array();
			
		$output = '<div class="of-repeat-loop">';		
		$output .= '<ul class="sortable of-repeat-form-fields">';
		
		if ( is_array( $values ) ) {

			foreach ( (array)$values as $key => $value ) {

				if (isset($value['label']) && isset($value['id'])) {
			
					$field_label 	= $value['label'];
					$field_id		= $value['id'];
					$field_hidden 	= isset($value['hide']) && $value['hide'] == '1' ? true : false;
					$field_required = isset($value['required']) && $value['required'] == '1' ? true : false;

					$field_index 	= isset($value['index']) && strlen(trim($value['index'])) > 0 ? intval($value['index']) : $counter;

					if (in_array($field_index, $used_indices)) {
						$field_index = $this->find_available_index($field_index, $used_indices);
					}
					
					if (isset($value['options'])) {
						if (is_array($value['options'])) {
							foreach ($value['options'] as $sub) {
								if (isset($sub['value']) && isset($sub['label'])) 
									$field_options .= $sub['value'] . ':' . $sub['label'] . PHP_EOL;
							}
						} else {
							$field_options = $value['options'];
						}
					} else {
						$field_options = '';
					}
					
					$used_indices[] = $field_index;
					$field_type		= isset($value['type']) ? $value['type'] : 'text';
					
					$is_default = (count(BookYourTravel_Theme_Utils::custom_array_search($default_values, 'id', $field_id)) > 0);
			
					$output .= '<li class="ui-state-default of-repeat-group">';

					$output .= '<div class="of-input-wrap">';
					$output .= '<label for="' . $option_name . '[' . $option['id'] . ']' . '['.$field_index.'][label]" class="label-field-label">' . esc_html__('Field name', 'bookyourtravel') . '</label>';
					$output .= $this->render_dynamic_field_input($option_name . '[' . $option['id'] . ']', 'of-input input-field-label input-label-for-dynamic-id', $option_name . '[' . $option['id'] . ']['.$field_index.'][label]', $option_name . '[' . $option['id'] . ']['.$field_index.'][label]', 'text', $field_label, esc_html__('Enter field name', 'bookyourtravel'), ' data-is-default="' . ($is_default ? '1' : '0') . '" data-original-id="' . esc_attr($field_id) . '"');
					$output .= '</div>';										
					$output .= '<div class="of-input-wrap of-modify-id">';
					$output .= '<label for="' . $option_name . '[' . $option['id'] . ']' . '['.$field_index.'][id]" class="label-field-id">' . esc_html__('Field ID', 'bookyourtravel') . '</label>';
					$output .= $this->render_dynamic_field_input($option_name . '[' . $option['id'] . ']', 'of-input input-field-id input-' . $form_type . '-form-field-id input-dynamic-id', $option_name . '[' . $option['id'] . ']['.$field_index.'][id]', $option_name . '[' . $option['id'] . ']['.$field_index.'][id]', 'text', $field_id, esc_html__('Field id is generated automatically.', 'bookyourtravel'), 'readonly="readonly" data-is-default="' . ($is_default ? '1' : '0') . '" data-original-id="' . esc_attr($field_id) . '" data-id="' . esc_attr($field_id) . '" data-parent="' . esc_attr($option['id']) . '"');
					$output .= '<div class="loading" style="display:none;"></div>';					
					$output .= '</div>';					
					
					$output .= '<div class="checboxes">';
					$output .= $this->render_dynamic_checkbox($option_name, $option, '['.$field_index.'][modify]', 'label-field-modify', 'checkbox-field-modify modify-dynamic-element-id', esc_html__('Modify id?', 'bookyourtravel'));
					$output .= $this->render_dynamic_checkbox($option_name, $option, '['.$field_index.'][hide]', 'label-field-hide', 'checkbox-field-hide', esc_html__('Hidden?', 'bookyourtravel'), ($field_hidden ? 'checked' : ''));
					$output .= $this->render_dynamic_checkbox($option_name, $option, '['.$field_index.'][required]', 'label-field-required', 'checkbox-field-required', esc_html__('Is Required?', 'bookyourtravel'), ($field_required ? 'checked' : ''));
					$output .= '</div>';				
					
					$output .= '<div class="field-type-div">';					
					$output .= $this->render_dynamic_select($option_name, $option, '['.$field_index.'][type]', 'label-field-type', 'select-field-type', esc_html__('Field type', 'bookyourtravel'), $field_type, $form_field_types);
					
					$output .= '<div class="of-textarea-wrap of-textarea-options" ' . ($field_type == 'select' ? '' : 'style="display:none"') . '>';
					$output .= '<label for="' . $option_name . '[' . $option['id'] . ']' . '['.$field_index.'][options]" class="label-field-options">' . esc_html__('Options (one per line, optional value/text separated by semi-colon ;)', 'bookyourtravel') . '</label>';
					$output .= $this->render_dynamic_field_textarea(
						$option_name . '[' . $option['id'] . ']',
						'of-textarea textarea-field-options', 
						$option_name . '[' . $option['id'] . ']['.$field_index.'][options]', 
						$option_name . '[' . $option['id'] . ']['.$field_index.'][options]', 
						$field_options,
						' data-original-id="' . esc_attr($field_id) . '"');
					$output .= '</div>';					
					$output .= '</div>';	
					
					$output .= '<input data-rel="' . esc_attr( $option_name . '[' . $option['id'] . ']' ) . '" class="input-index" name="' . esc_attr( $option_name . '[' . $option['id'] . ']['.$field_index.'][index]' ) . '" type="hidden" value="' . $field_index . '" />';

					if (count(BookYourTravel_Theme_Utils::custom_array_search($default_values, 'id', $field_id)) == 0) {
						$output .= '<span class="ui-icon ui-icon-close"></span>';
					}
					$output .= '</li><!--.of-repeat-group-->';
			 
					$max_field_index = $field_index > $max_field_index ? $field_index : $max_field_index;
			 
					$counter++;
				}
			}
		}
	 
		$output .= '</ul><!--.sortable-->';
		$output .= '<input type="hidden" class="max_field_index" value="' . $max_field_index . '" />';
		$output .= '<a href="#" class="docopy_form_field button icon add">' . esc_html__('Add form field', 'bookyourtravel') . '</a>';
		$output .= '</div><!--.of-repeat-loop-->';

		return $output;
	}	

	function find_available_index($current_index, $indexes) {
		if (!in_array($current_index, $indexes)) {
			return $current_index;
		}
		$current_index++;
		return $this->find_available_index($current_index, $indexes);
	}
	
	function repeat_review_field_option_type( $option_name, $option, $values ) {

		global $bookyourtravel_theme_of_default_fields;
	
		$max_field_index = -1;
		$counter = 0;
		$used_indices = array();
		
		$default_values = $bookyourtravel_theme_of_default_fields->get_default_review_fields_array($option['id']);
		
		$values = BookYourTravel_Theme_Of_Default_Fields::merge_fields_and_defaults($values, $default_values);
		
		$post_type = '';
		if ($option['id'] == 'accommodation_review_fields') {
			$post_type = 'accommodation';
		} elseif ($option['id'] == 'tour_review_fields') {
			$post_type = 'tour';
		} elseif ($option['id'] == 'cruise_review_fields') {
			$post_type = 'cruise';
		} elseif ($option['id'] == 'car_rental_review_fields') {
			$post_type = 'car_rental';
		}
			
		$output = '<div class="of-repeat-loop">';		
		$output .= '<ul class="sortable of-repeat-review-fields">';

		if ( is_array( $values ) ) {

			foreach ( (array)$values as $key => $value ) {

				if (isset($value['label']) && isset($value['post_type']) &&	isset($value['id'])) {
			
					$field_label 	= $value['label'];
					$field_id		= $value['id'];
					$field_post_type= $value['post_type'];
					$field_hidden 	= isset($value['hide']) && $value['hide'] == '1' ? true : false;
					$field_index 	= isset($value['index']) && strlen(trim($value['index'])) > 0 ? intval($value['index']) : $counter;

					if (in_array($field_index, $used_indices)) {
						$field_index = $this->find_available_index($field_index, $used_indices);
					}
					$used_indices[] = $field_index;
			
					$is_default = (count(BookYourTravel_Theme_Utils::custom_array_search($default_values, 'id', $field_id)) > 0);			
			
					$output .= '<li class="ui-state-default of-repeat-group">';

					$output .= $this->render_dynamic_field_input($option_name . '[' . $option['id'] . ']', 'input-field-post-type', $option_name . '[' . $option['id'] . ']['.$field_index.'][post_type]', '', 'hidden', $field_post_type);

					$output .= '<div class="of-input-wrap">';
					$output .= '<label for="' . $option_name . '[' . $option['id'] . ']' . '['.$field_index.'][label]" class="label-review-field-label">' . esc_html__('Field name', 'bookyourtravel') . '</label>';										
					$output .= $this->render_dynamic_field_input($option_name . '[' . $option['id'] . ']', 'of-input input-field-label input-label-for-dynamic-id', $option_name . '[' . $option['id'] . ']['.$field_index.'][label]', '', 'text', $field_label, esc_html__('Enter field name', 'bookyourtravel'), ' data-is-default="' . ($is_default ? '1' : '0') . '" data-original-id="' . esc_attr($field_id) . '"');
					$output .= '</div>';
					
					$output .= '<div class="of-input-wrap of-modify-id">';
					$output .= '<label for="' . $option_name . '[' . $option['id'] . ']' . '['.$field_index.'][id]" class="label-review-field-id">' . esc_html__('Field ID', 'bookyourtravel') . '</label>';										
					$output .= $this->render_dynamic_field_input($option_name . '[' . $option['id'] . ']', 'of-input input-field-id input-review-field-id input-dynamic-id', $option_name . '[' . $option['id'] . ']['.$field_index.'][id]', '', 'text', $field_id, esc_html__('Field id is generated automatically.', 'bookyourtravel'), 'readonly="readonly" data-is-default="' . ($is_default ? '1' : '0') . '" data-original-id="' . esc_attr($field_id) . '" data-id="' . esc_attr($field_id) . '" data-parent="' . esc_attr($option['id']) . '"');
					$output .= '<div class="loading" style="display:none;"></div>';
					$output .= '</div>';
					
					$output .= '<div class="checboxes">';
					$output .= $this->render_dynamic_checkbox($option_name, $option, '['.$field_index.'][modify]', 'label-field-modify', 'checkbox-field-modify modify-dynamic-element-id', esc_html__('Modify id?', 'bookyourtravel'));
					$output .= $this->render_dynamic_checkbox($option_name, $option, '['.$field_index.'][hide]', 'label-field-hide', 'checkbox-field-hide', esc_html__('Hidden?', 'bookyourtravel'), ($field_hidden ? 'checked' : ''));
					$output .= '</div>';
					
					$output .= '<input data-rel="' . esc_attr( $option_name . '[' . $option['id'] . ']' ) . '" class="input-index" name="' . esc_attr( $option_name . '[' . $option['id'] . ']['.$field_index.'][index]' ) . '" type="hidden" value="' . $field_index . '" />';

					
					if (count(BookYourTravel_Theme_Utils::custom_array_search($default_values, 'id', $field_id)) == 0) {
						$output .= '<span class="ui-icon ui-icon-close"></span>';
					}
					$output .= '</li><!--.of-repeat-group-->';
			 
					$max_field_index = $field_index > $max_field_index ? $field_index : $max_field_index;
			 
					$counter++;
				}
			}
		}
	 
		$output .= '</ul><!--.sortable-->';
		$output .= '<input type="hidden" class="max_field_index" value="' . $max_field_index . '" />';
		$output .= '<a href="#" class="docopy_review_field button icon add">' . esc_html__('Add review field', 'bookyourtravel') . '</a>';
		$output .= '</div><!--.of-repeat-loop-->';

		return $output;
	}
	
	function repeat_extra_field_option_type( $option_name, $option, $values ) {

		global $bookyourtravel_theme_of_default_fields, $repeatable_field_types, $default_accommodation_extra_fields, $default_tour_extra_fields, $default_car_rental_extra_fields, $default_location_extra_fields, $default_cruise_extra_fields;
		
		$counter = 0;
		$max_field_index = -1;
		$used_indices = array();
		
		$default_values = array();
		$tab_array = array();
		
		if ($option['id'] == 'accommodation_extra_fields') {
			$default_values = $default_accommodation_extra_fields;
			$tab_key = 'accommodation_tabs';
		} elseif ($option['id'] == 'tour_extra_fields') {
			$default_values = $default_tour_extra_fields;
			$tab_key = 'tour_tabs';
		} elseif ($option['id'] == 'car_rental_extra_fields') {
			$default_values = $default_car_rental_extra_fields;
			$tab_key = 'car_rental_tabs';
		} elseif ($option['id'] == 'location_extra_fields') {
			$default_values = $default_location_extra_fields;
			$tab_key = 'location_tabs';
		} elseif ($option['id'] == 'cruise_extra_fields') {
			$default_values = $default_cruise_extra_fields;
			$tab_key = 'cruise_tabs';
		}

		$tab_array = of_get_option($tab_key);
		$default_tab_array = $bookyourtravel_theme_of_default_fields->get_default_tab_array($tab_key);
		
		if (!is_array( $tab_array ) || count($tab_array) == 0 || count($tab_array) < count($default_tab_array)) {
			$tab_array = $default_tab_array;
		}
		
		$values = BookYourTravel_Theme_Of_Default_Fields::merge_fields_and_defaults($values, $default_values);
		
		$output = '<div class="of-repeat-loop">';
		
		if ($tab_array && count($tab_array) > 0) {

			$output .= '<ul class="sortable of-repeat-extra-fields">';

			if( is_array( $values ) && is_array($tab_array) ) {

				foreach ( (array)$values as $key => $field ) {
					if (isset($field['label']) && 
						isset($field['type']) &&
						isset($field['tab_id']) &&
						isset($field['id'])) {
						
						$field_label 	= $field['label'];
						$field_id		= $field['id'];
						$field_type		= $field['type'];
						$field_hidden	= isset($field['hide']) && $field['hide'] == '1' ? true : false;
						$field_hidden_front	= isset($field['hide_front']) && $field['hide_front'] == '1' ? true : false;
						$field_min	= isset($field['min']) ? $field['min'] : 1;
						$field_max	= isset($field['max']) ? $field['max'] : 10;
						$field_step	= isset($field['step']) ? $field['step'] : 1;
						
						if (isset($field['options'])) {
							if (is_array($field['options'])) {
								foreach ($field['options'] as $sub) {
									if (isset($sub['value']) && isset($sub['label'])) 
										$field_options .= $sub['value'] . ':' . $sub['label'] . PHP_EOL;
								}
							} else {
								$field_options = $field['options'];
							}
						} else {
							$field_options = '';
						}						
						
						$default_field_collection = BookYourTravel_Theme_Utils::custom_array_search($default_values, 'id', $field_id);
						$is_default_field = (count($default_field_collection) > 0);

						$default_show_in_referenced = $is_default_field && isset($default_field_collection[0]['show_in_referenced']) ? $default_field_collection[0]['show_in_referenced'] : '0';			
						
						$field_show_in_referenced = '0';
						if (isset($field['show_in_referenced'])) {
							$field_show_in_referenced = $field['show_in_referenced'];
						}
						
						$field_show_in_referenced = $field_show_in_referenced == '1' ? true : false;
						
						$field_index 	= isset($field['index']) && strlen(trim($field['index'])) > 0 ? intval($field['index']) : $counter;
						if (in_array($field_index, $used_indices)) {
							$field_index = $this->find_available_index($field_index, $used_indices);
						}
						$used_indices[] = $field_index;
						$tab_id			= $field['tab_id'];
						
						$output .= '<li class="ui-state-default of-repeat-group">';
						
						$output .= '<div class="of-input-wrap">';
						$output .= '<label for="' . $option_name . '[' . $option['id'] . ']' . '['.$field_index.'][label]" class="label-field-label">' . esc_html__('Field name', 'bookyourtravel') . '</label>';
						$output .= $this->render_dynamic_field_input($option_name . '[' . $option['id'] . ']', 'of-input input-field-label input-label-for-dynamic-id', $option_name . '[' . $option['id'] . ']['.$field_index.'][label]', '', 'text', $field_label, esc_html__('Enter field name', 'bookyourtravel'), ' data-is-default="' . ($is_default_field ? '1' : '0') . '" data-original-id="' . esc_attr($field_id) . '"');
						$output .= '</div>';
						$output .= '<div class="of-input-wrap of-modify-id">';
						$output .= '<label for="' . $option_name . '[' . $option['id'] . ']' . '['.$field_index.'][id]" class="label-field-id">' . esc_html__('Field ID', 'bookyourtravel') . '</label>';
						$output .= $this->render_dynamic_field_input($option_name . '[' . $option['id'] . ']', 'of-input input-field-id input-dynamic-id', $option_name . '[' . $option['id'] . ']['.$field_index.'][id]', '', 'text', $field_id, esc_html__('Field id is generated automatically.', 'bookyourtravel'), 'readonly="readonly" data-is-default="' . ($is_default_field ? '1' : '0') . '" data-original-id="' . esc_attr($field_id) . '" data-id="' . esc_attr($field_id) . '" data-parent="' . esc_attr($option['id']) . '"');
						$output .= '<div class="loading" style="display:none;"></div>';
						$output .= '</div>';
						$output .= '<div class="checboxes">';
						$output .= $this->render_dynamic_checkbox($option_name, $option, '['.$field_index.'][modify]', 'label-field-modify', 'checkbox-field-modify modify-dynamic-element-id', esc_html__('Modify id?', 'bookyourtravel'));
						$output .= $this->render_dynamic_checkbox($option_name, $option, '['.$field_index.'][hide]', 'label-field-hide', 'checkbox-field-hide', esc_html__('Hidden?', 'bookyourtravel'), ($field_hidden ? 'checked' : ''));
						$output .= $this->render_dynamic_checkbox($option_name, $option, '['.$field_index.'][hide_front]', 'label-field-hide-front', 'checkbox-field-hide-front', esc_html__('Hidden in frontend?', 'bookyourtravel'), ($field_hidden_front ? 'checked' : ''));
						if ($tab_key == 'location_tabs') {
							$output .= $this->render_dynamic_checkbox($option_name, $option, '['.$field_index.'][show_in_referenced]', 'label-field-show-in-referenced', 'checkbox-field-show-in-referenced', esc_html__('Show when referenced (e.g. things to do)?', 'bookyourtravel'), ($field_show_in_referenced ? 'checked' : ''));						
						}
						$output .= '</div>';
						$output .= '<div class="field-type-div">';
						$output .= $this->render_dynamic_select($option_name, $option, '['.$field_index.'][type]', 'label-field-type', 'select-field-type', esc_html__('Field type', 'bookyourtravel'), $field_type, $repeatable_field_types);
						
						$output .= '<div class="of-textarea-wrap of-textarea-options" ' . ($field_type == 'select' ? '' : 'style="display:none"') . '>';
						$output .= '<label for="' . $option_name . '[' . $option['id'] . ']' . '['.$field_index.'][options]" class="label-field-options">' . esc_html__('Options (one per line)', 'bookyourtravel') . '</label>';
						$output .= $this->render_dynamic_field_textarea(
							$option_name . '[' . $option['id'] . ']',
							'of-textarea textarea-field-options', 
							$option_name . '[' . $option['id'] . ']['.$field_index.'][options]', 
							$option_name . '[' . $option['id'] . ']['.$field_index.'][options]', 
							$field_options,
							' data-is-default="' . ($is_default_field ? '1' : '0') . '" data-original-id="' . esc_attr($field_id) . '"');
						$output .= '</div>';							
						
						$output .= '<div class="of-input-wrap of-input-range" ' . ($field_type == 'slider' ? '' : 'style="display:none"') . '>';						

						$output .= $this->render_dynamic_textbox($option_name, $option, '['.$field_index.'][min]', 'label-field-min', 'of-input input-field-min', esc_html__('Min value', 'bookyourtravel'), $field_min, 'number');
						$output .= $this->render_dynamic_textbox($option_name, $option, '['.$field_index.'][max]', 'label-field-max', 'of-input input-field-max', esc_html__('Max value', 'bookyourtravel'), $field_max, 'number');
						$output .= $this->render_dynamic_textbox($option_name, $option, '['.$field_index.'][step]', 'label-field-step', 'of-input input-field-min', esc_html__('Step value', 'bookyourtravel'), $field_step, 'number');
						
						$output .= '</div>';
						$output .= '</div>';
						
						$output .= $this->render_dynamic_select($option_name, $option, '['.$field_index.'][tab_id]', 'label-field-tab', 'select-field-tab', esc_html__('Assign to', 'bookyourtravel'), $tab_id, $tab_array, 'label', 'id');

						$output .= '<input data-rel="' . esc_attr( $option_name . '[' . $option['id'] . ']' ) . '" class="input-index" name="' . esc_attr( $option_name . '[' . $option['id'] . ']['.$field_index.'][index]' ) . '" type="hidden" value="' . $field_index . '" />';
						
						if (count(BookYourTravel_Theme_Utils::custom_array_search($default_values, 'id', $field_id)) == 0) {
							$output .= '<span class="ui-icon ui-icon-close"></span>';
						}
						$output .= '</li><!--.of-repeat-group-->';
				 
						$max_field_index = $field_index > $max_field_index ? $field_index : $max_field_index;
						
						$counter++;
					}
				}
			}

			$output .= '</ul><!--.sortable-->';
			$output .= '<input type="hidden" class="max_field_index" value="' . $max_field_index . '" />';
			$output .= '<a href="#" class="docopy_field button icon add">' . esc_html__('Add field', 'bookyourtravel') . '</a>';
			
		} else {
			$output .= '<p>' . esc_html__('Please hit the "Save Options" button to create the initial collection of tabs so that extra fields can be associated with tabs correctly.', 'bookyourtravel') . '</p>';
		}
		$output .= '</div><!--.of-repeat-loop-->';

		return $output;
	}
	
	function render_dynamic_textarea($option_name, $option, $name_postfix, $label_css, $input_css, $label_text, $value = '') {

		$output = '';

		$output .= '<div class="of-input-wrap">';
		$output .= $this->render_dynamic_field_label( $option_name . '[' . $option['id'] . ']', 'of-label ' . $label_css, $option_name . '[' . $option['id'] . ']' . $name_postfix, $label_text);				
		$output .= $this->render_dynamic_field_textarea( $option_name . '[' . $option['id'] . ']', 'of-input ' . $input_css, $option_name . '[' . $option['id'] . ']' . $name_postfix, '', 'text', $value, $label_text);
		$output .= '</div>';

		return $output;
	}	
	
	function render_dynamic_textbox($option_name, $option, $name_postfix, $label_css, $input_css, $label_text, $value = '', $input_type = '') {

		$output = '';

		$output .= '<div class="of-input-wrap">';
		$output .= $this->render_dynamic_field_label( $option_name . '[' . $option['id'] . ']', 'of-label ' . $label_css, $option_name . '[' . $option['id'] . ']' . $name_postfix, $label_text);				
		
		$inp_type = isset($input_type) ? $input_type : 'text';
		$output .= $this->render_dynamic_field_input( $option_name . '[' . $option['id'] . ']', 'of-input ' . $input_css, $option_name . '[' . $option['id'] . ']' . $name_postfix, '', $inp_type, $value, $label_text);
		$output .= '</div>';

		return $output;
	}
	
	function render_dynamic_checkbox($option_name, $option, $name_postfix, $label_css, $input_css, $label_text, $extra_input_attributes = '') {
	
		$output = '';

		$output .= '<div class="of-check-wrap">';
		$output .= $this->render_dynamic_field_label($option_name . '[' . $option['id'] . ']', 'of-label ' . $label_css, $option_name . '[' . $option['id'] . ']' . $name_postfix, $label_text);
		$output .= $this->render_dynamic_field_input($option_name . '[' . $option['id'] . ']', 'of-checkbox ' . $input_css, $option_name . '[' . $option['id'] . ']' . $name_postfix, '', 'checkbox', '1', '', $extra_input_attributes);
		$output .= '</div>';

		return $output;
	}
	
	function render_dynamic_select($option_name, $option, $name_postfix, $label_css, $select_css, $label_text, $selected_value, $option_array, $text_key = '', $value_key = '') {
	
		$output = '';
		$output .= '<div class="of-select-wrap">';
		$output .= $this->render_dynamic_field_label(  $option_name . '[' . $option['id'] . ']', 'of-label ' . $label_css, $option_name . '[' . $option['id'] . ']' . $name_postfix, $label_text);		
		$output .= $this->render_dynamic_field_select( $option_name . '[' . $option['id'] . ']', 'of-select ' . $select_css, $option_name . '[' . $option['id'] . ']' . $name_postfix, '', $selected_value, $option_array, $text_key, $value_key );
		$output .= '</div>';

		return $output;
	}

	function page_status_info_field_option_type( $option_name, $option, $values ) {
	
		$text = trim($option['text']);
	
		$output = '<div class="of-status">';
		if (empty($text)) {
			$output .= '<p class="allgood">' . __('All good', 'bookyourtravel') . '</p>';
		} else {
			$output .= $text;
		}
		$output .= '</div>';

		return $output;
	}	
	
	function file_status_info_field_option_type( $option_name, $option, $values ) {
	
		$text = trim($option['text']);
	
		$output = '<div class="of-status">';
		if (empty($text)) {
			$output .= '<p class="allgood">' . __('All good', 'bookyourtravel') . '</p>';
		} else {
			$output .= $text;
		}
		$output .= '</div>';

		return $output;
	}
	
	function link_button_field_option_type ( $option_name, $option, $values ) {

		$button_text = $option['name'];
		if (isset($option['text'])) {
			$button_text = $option['text'];
		}
	
		$output = '<div class="of-input">';
		$output .= '<a href="#" class="button-secondary of-button-field ' . esc_attr($option['id']) . '">' . esc_html($button_text) . '</a>';
		if ($option['id'] == 'synchronise_reviews' || $option['id'] == 'upgrade_bookyourtravel_db') {
			$output .= '<div style="display:none" class="loading"></div>';
		}
		$output .= '</div>';

		return $output;
	}
	
	function dummy_text_option_type ( $option_name, $option, $values) {
		return '';
	}
	
	function sub_heading_option_type( $option_name, $option, $values) {
		if (!empty($option['std'])) {
			return "<h4>" . $option['std'] . "</h4>";
		} else {
			return "";
		}		
	}
	
	function render_dynamic_field_select( $data_rel, $css_class, $name, $id, $selected_value, $options_array, $text_key = '', $value_key = '' ) {
	
		$output = '<select class="' . esc_attr($css_class) . '" name="' . esc_attr( $name ) . '" data-rel="' . esc_attr( $data_rel ) . '">';
		
		if (is_array($options_array) && count($options_array)) {
			
			if (!empty($text_key) && !empty($value_key)) {
				foreach($options_array as $option) {
				
					$option_text = isset($option[$text_key]) ? trim($option[$text_key]) : '';
					$option_value = isset($option[$value_key]) ? trim($option[$value_key]) : '';
					
					if (!empty($option_text) && !empty($option_value)) {
						$output .= '<option value="' . $option_value . '" ' . ($option_value == $selected_value ? 'selected' : '') . '>' . $option_text . '</option>';
					}
				} 
			} else {
				foreach($options_array as $key => $text) {
					$output .= '<option value="' . $key . '" ' . ($key == $selected_value ? 'selected' : '') . '>' . $text . '</option>';
				}
			}
			
		}		
		
		$output .= '</select>';
		
		return $output;
	}
	
	function render_dynamic_field_label( $data_rel, $css_class, $for, $text ) {
		return '<label data-rel="' . esc_attr( $data_rel ) . '" class="' . esc_attr($css_class) . '" for="' . esc_attr( $for ) . '">' . $text . '</label>';
	}

	function render_dynamic_field_input( $data_rel, $css_class, $name, $id, $type, $value, $placeholder_text = '', $extra_attributes = '' ) {
		return '<input ' . (!empty($placeholder_text) ? ' placeholder="' . esc_attr($placeholder_text). '"' : '') .' data-rel="' . esc_attr( $data_rel ) . '" class="' . esc_attr($css_class) . '" name="' . esc_attr( $name ) . '" id="' . esc_attr( $id ) . '" type="' . esc_attr($type) . '" value="' . esc_attr( $value ) . '" ' . $extra_attributes . ' />';
	}
	
	function render_dynamic_field_textarea( $data_rel, $css_class, $name, $id, $value, $extra_attributes = '' ) {
		return '<textarea id="' . esc_attr( $id ) . '" rows="5" cols="20" data-rel="' . esc_attr( $data_rel ) . '" class="' . esc_attr($css_class) . '" name="' . esc_attr( $name ) . '" ' . $extra_attributes . '>' . $value . '</textarea>';
	}	
	
	function get_option_id_context($option_id) {

		$option_id_context = '';
		
		if ($option_id == 'location_extra_fields')
			$option_id_context = 'Location extra field';
		elseif ($option_id == 'location_tabs')
			$option_id_context = 'Location tab';
		elseif ($option_id == 'accommodation_extra_fields')
			$option_id_context = 'Accommodation extra field';
		elseif ($option_id == 'accommodation_tabs')
			$option_id_context = 'Accommodation tab';
		elseif ($option_id == 'tour_extra_fields')
			$option_id_context = 'Tour extra field';
		elseif ($option_id == 'tour_tabs')
			$option_id_context = 'Tour tab';
		elseif ($option_id == 'car_rental_extra_fields')
			$option_id_context = 'Car rental extra field';
		elseif ($option_id == 'car_rental_tabs')
			$option_id_context = 'Car rental tab';
		elseif ($option_id == 'cruise_extra_fields')
			$option_id_context = 'Cruise extra field';
		elseif ($option_id == 'cruise_tabs')
			$option_id_context = 'Cruise tab';
		elseif ($option_id == 'accommodation_review_fields')
			$option_id_context = 'Accommodation review field';
		elseif ($option_id == 'tour_review_fields')
			$option_id_context = 'Tour review field';	
		elseif ($option_id == 'cruise_review_fields')
			$option_id_context = 'Cruise review field';
		elseif ($option_id == 'car_rental_review_fields')
			$option_id_context = 'Car rental review field';
		elseif ($option_id == 'inquiry_form_fields')
			$option_id_context = 'Inquiry form field';
		elseif ($option_id == 'booking_form_fields')
			$option_id_context = 'Booking form field';
			
		return $option_id_context;
	}
	
	/*
	 * Sanitize Repeat review inputs
	 */
	function sanitize_repeat_review_field( $fields, $option ) {	
		
		$results = array();
		
		if (is_array($fields)) {
		
			foreach ($fields as $field) {
			
				if (!isset($field['id']) && isset($field['label'])) {
					$field['id'] = 'review_' . URLify::filter($field['label']);
				}
				
				if (isset($field['label'])) {
					$this->register_dynamic_string_for_translation($this->get_option_id_context($option['id']) . ' ' . $field['label'], $field['label']);
				}
				
				$results[] = $field;
			}
		}
		
		return $results;
	}
	
	/*
	 * Sanitize Repeat inputs
	 */
	function sanitize_repeat_extra_field( $fields, $option ) {
	
		$results = array();
		
		if (is_array($fields)) {
		
			foreach ($fields as $field) {
			
				$field_id = isset($field['id']) ? trim($field['id']) : '';
				$field_label = isset($field['label']) ? $field['label'] : '';
				$field_index = isset($field['index']) && strlen(trim($field['index'])) > 0 ? intval($field['index']) : 0;					
				
				if (empty($field_id) && !empty($field_label)) {
					$field_id = URLify::filter($field_label . '-' . $field_index);
					$field_id = str_replace("-", "_", $field_id);
					$field['id'] = $field_id;
				}
					
				if (isset($field['label'])) {
					$this->register_dynamic_string_for_translation($this->get_option_id_context($option['id']) . ' ' . $field['label'], $field['label']);
				}
					
				$results[] = $field;
			}
		}
		return $results;
	}
	
	/*
	 * Sanitize Repeat inputs
	 */
	function sanitize_repeat_form_field( $fields, $option ) {
	
		$results = array();
		
		if (is_array($fields)) {
		
			foreach ($fields as $field) {
					
				$field_id = isset($field['id']) ? trim($field['id']) : '';
				$field_label = isset($field['label']) ? $field['label'] : '';
				$field_index = isset($field['index']) ? $field['index'] : 0;
				
				if (empty($field_id) && !empty($field_label)) {
					$field_id = URLify::filter($field_label . '-' . $field_index);
					$field_id = str_replace("-", "_", $field_id);
					$field['id'] = $field_id;
				}
					
				if (isset($field['label'])) {
					$this->register_dynamic_string_for_translation($this->get_option_id_context($option['id']) . ' ' . $field['label'], $field['label']);
				}
					
				$results[] = $field;
			}
		}
		return $results;
	}
	
	/*
	 * Sanitize Repeat tabs
	 */
	function sanitize_repeat_tab( $tabs, $option ) {
		
		$results = array();
		
		if (is_array($tabs)) {
		
			foreach ($tabs as $tab) { 
			
				$tab_id = isset($tab['id']) ? trim($tab['id']) : '';
				$tab_label = isset($tab['label']) ? $tab['label'] : '';
				$tab_index = isset($tab['index']) ? $tab['index'] : 0;
				
				if (empty($tab_id) && !empty($tab_label)) {
					$tab_id = URLify::filter($tab_label . '-' . $tab_index);
					$tab_id = str_replace("-", "_", $tab_id);
					$tab['id'] = $tab_id;
				}
					
				if (isset($tab['label'])) {
					$this->register_dynamic_string_for_translation($this->get_option_id_context($option['id']) . ' ' . $tab['label'], $tab['label']);
				}
				
				$results[] = $tab;
			}
		}
		
		return $results;
	}
	
	/*
	 * Custom repeating field scripts
	 * Add and Delete buttons
	 */
	function of_bookyourtravel_options_script() {	
		global $bookyourtravel_theme_globals;
		
		$iconic_features_icon_classes = $bookyourtravel_theme_globals->get_iconic_features_icon_classes();
		?>
		<style>
			#optionsframework .to-copy {display: none;}
			#optionsframework .controls .of-input-wrap { width: 70% !important;display:inline-block }
			#optionsframework .controls .of-input-wrap .of-input { width: 100% !important; display:inline-block; }
			#optionsframework .controls .of-check-wrap { width: 30% !important;display:inline-block }
		</style>
		<script type="text/javascript"><?php
			echo 'window.templateDir = ' . json_encode( get_template_directory_uri() ) . ';';
			echo 'window.adminSiteUrl = ' . json_encode( admin_url( 'themes.php?page=options-framework' ) ) . ';';
			echo 'window.themeenergyIconsString = ' . json_encode( $iconic_features_icon_classes ) . ';';
			echo 'window.themeenergyIconsContainerClass = "of-repeat-tab";';?>	
		</script>
	<?php
	}
}

// store the instance in a variable to be retrieved later and call init
$bookyourtravel_theme_of_custom = BookYourTravel_Theme_Of_Custom::get_instance();