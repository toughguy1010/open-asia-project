<?php
/**
 * BookYourTravel_Theme_Admin_Controls class
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class BookYourTravel_Theme_Admin_Controls {
	public static function the_dynamic_field_checkbox_control($field, $field_value = '') {
		if ($field != null && is_array($field) && isset($field['id']) ) {
            $field_name = trim($field['id']);
            $field_id = trim($field['id']);
            if (isset($field["unique_id"])) {
                $field_id = trim($field['unique_id']);
            }
			$field_required = isset($field['required']) && $field['required'] == '1' ? true : false;

			echo '<input type="checkbox" ' . ($field_value == 1 ? 'checked' : '') . ' ' . ($field_required ? 'data-required' : '') . ' name="' . esc_attr($field_name) . '" id="' . esc_attr($field_id) . '">';
		}
	}

	public static function the_dynamic_field_select_control($field, $field_value = '') {
		if ($field != null && is_array($field) && isset($field['options']) && isset($field['id'])) {

			$field_options = isset($field['options']) ? trim($field['options']) : "";
			$field_id = trim($field['id']);
			$field_required = isset($field['required']) && $field['required'] == '1' ? true : false;

			$options_array = preg_split("/(\r\n|\n|\r)/", $field_options);
			if (count($options_array) > 0) {
				echo '<select ' . ($field_required ? 'data-required' : '') . ' name="' . esc_attr($field_id) . '" id="' . esc_attr($field_id) . '">';
				foreach ($options_array as $option) {
					$option_array = preg_split("/[,;]+/", $option);
					if (count($option_array) > 0) {
						$option_value = $option_array[0];
						$option_text = trim($option_array[0]);
						if (count($option_array) > 1) {
							$option_text = trim($option_array[1]);
						}
						echo '<option ' . ($option_value == $field_value ? 'selected' : '') . ' value="' . $option_value . '">' . $option_text . '</option>';
					}
				}
				echo '</select>';

			} else {
				echo '<input value="' . esc_attr($field_value) . '" ' . ($field_required ? 'data-required' : '') . ' type="text" id="' . esc_attr($field_id) . '" name="' . esc_attr($field_id) . '" />';
			}
		}
	}
}