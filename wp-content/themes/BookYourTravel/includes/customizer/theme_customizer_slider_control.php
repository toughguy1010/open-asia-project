<?php
/**
 * Customize_Slider_Control class
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WP_Customize_Control' ) ) {
    return NULL;
}

/**
 * Class to create a slider control for WordPress Customize API
 */
class Customize_Slider_Control extends WP_Customize_Control {

    public $type = 'byt_slider';

    /**
    * Enqueue the styles and scripts
    */
    public function enqueue()
    {
        wp_enqueue_script('jquery-ui-slider');
        wp_enqueue_style('bookyourtravel-customize-controls', BookYourTravel_Theme_Utils::get_file_uri('/css/admin/customize_controls.css'), BOOKYOURTRAVEL_VERSION, "screen,print");

        wp_add_inline_script( 'jquery-ui-slider', '
        if (typeof window.bytCustomizeControls === "undefined") {
            window.bytCustomizeControls = [];
            if (!("sliders" in window.bytCustomizeControls)) {
                window.bytCustomizeControls["sliders"] = [];
            }
        }
        window.bytCustomizeControls["sliders"].push({' .
            'setting_id: "' . esc_js($this->id) . '", ' .
            'value: ' . esc_js($this->value()) . ', ' .
            'min: ' . esc_js($this->min) . ', ' .
            'max: ' . esc_js($this->max) . ', ' .
			'step: ' . esc_js($this->step) .
        '});');
    }

	public $step = 1.0;
    public $min = 0;
    public $max = 10;

    public function __construct($manager, $id, $args = array(), $options = array())
    {
        parent::__construct( $manager, $id, $args );
    }

    /**
    * Render the content on the theme customizer page
    */
    public function render_content()
    {
        ?>
        <label>
            <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
            <div class="customize-control-content">
                <div id="<?php echo esc_attr($this->id); ?>_tooltip" class="customize-tooltip ui-widget"></div>
                <div id="<?php echo esc_attr($this->id); ?>_slider">
                    <span class="min"><?php echo esc_html($this->min); ?></span><span class="max"><?php echo esc_attr($this->max); ?></span>
                </div>
                <input <?php $this->link(); ?> type="hidden" id="<?php echo esc_attr($this->id); ?>" name="<?php echo esc_attr($this->id); ?>" value="<?php echo esc_attr($this->value()); ?>" />
            </div>
        </label>
        <?php
    }
}