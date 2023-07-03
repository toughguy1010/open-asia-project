<?php
/**
 * The template for displaying Call to action widget
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */

// Add function to widgets_init that'll load our widget.
add_action( 'widgets_init', 'bookyourtravel_call_to_action_widgets' );

// Register widget.
function bookyourtravel_call_to_action_widgets() {
	register_widget( 'bookyourtravel_call_to_action_widget' );
}

// Widget class.
class bookyourtravel_call_to_action_widget extends WP_Widget {

	/*-----------------------------------------------------------------------------------*/
	/*	Widget Setup
	/*-----------------------------------------------------------------------------------*/
		
	function __construct() {
	
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'bookyourtravel_call_to_action_widget', 'description' => esc_html__('BookYourTravel: Call-To-Action Widget', 'bookyourtravel') );

		/* Widget control settings. */
		$control_ops = array( 'width' => 300, 'height' => 550, 'id_base' => 'bookyourtravel_call_to_action_widget' );

		/* Create the widget. */
		parent::__construct( 'bookyourtravel_call_to_action_widget', esc_html__('BookYourTravel: Call-To-Action Widget', 'bookyourtravel'), $widget_ops, $control_ops );
	}


	/*-----------------------------------------------------------------------------------*/
	/*	Display Widget
	/*-----------------------------------------------------------------------------------*/
	
	function widget( $args, $instance ) {
		extract( $args );

		/* Our variables from the widget settings. */
		$call_to_action_text = isset($instance['call_to_action_text']) ? $instance['call_to_action_text'] : __('Like what you see? Are you ready to stand out? You know what to do!', 'bookyourtravel');
		$button_text = isset($instance['button_text']) ? $instance['button_text'] : __('Purchase theme', 'bookyourtravel');	
		$button_url = isset($instance['button_url']) ? $instance['button_url'] : '#';	

		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Display Widget */
		/* Display the widget title if one was input (before and after defined by themes). */
		?>
		<!-- Call to action -->
		<div class="cta">
			<div class="wrap">
				<p>
					<?php
					$allowed_tags = BookYourTravel_Theme_Utils::get_allowed_content_tags_array();
					echo wp_kses($call_to_action_text, $allowed_tags); 
					?>
				</p>
				<a href="<?php echo esc_url($button_url); ?>" class="gradient-button"><?php echo esc_html($button_text); ?></a>
			</div>
		</div>
		<!-- //Call to action -->
		<?php
		/* After widget (defined by themes). */
		echo $after_widget;
	}


/*-----------------------------------------------------------------------------------*/
/*	Update Widget
/*-----------------------------------------------------------------------------------*/
	
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags to remove HTML (important for text inputs). */
		$instance['call_to_action_text'] = isset( $new_instance['call_to_action_text']) ? strip_tags( $new_instance['call_to_action_text'] ) : '';
		$instance['button_text'] = isset($new_instance['button_text']) ? strip_tags( $new_instance['button_text'] ) : '';
		$instance['button_url'] = isset($new_instance['button_url']) ? strip_tags( $new_instance['button_url'] ) : '';

		return $instance;
	}
	

/*-----------------------------------------------------------------------------------*/
/*	Widget Settings
/*-----------------------------------------------------------------------------------*/
	 
	function form( $instance ) {

		/* Set up some default widget settings. */
		
		$defaults = array(
			'call_to_action_text' => esc_html__('Like what you see? Are you ready to stand out? You know what to do!', 'bookyourtravel'),
			'button_text' => esc_html__('Purchase theme', 'bookyourtravel'),
			'button_url' => '#'
		);
		
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<!-- Widget Title: Text Input -->
		
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'call_to_action_text' ) ); ?>"><?php esc_html_e('Call to action text:', 'bookyourtravel') ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'call_to_action_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'call_to_action_text' ) ); ?>" value="<?php echo esc_attr( $instance['call_to_action_text'] ); ?>" />
		</p>
		
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'button_text' ) ); ?>"><?php esc_html_e('Button text', 'bookyourtravel') ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'button_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'button_text' ) ); ?>" value="<?php echo esc_attr( $instance['button_text'] ); ?>" />
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'button_url' ) ); ?>"><?php esc_html_e('Button url', 'bookyourtravel') ?></label>
			<input type="text" placeholder="http://" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'button_url' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'button_url' ) ); ?>" value="<?php echo esc_attr( $instance['button_url'] ); ?>" />
		</p>
		
	<?php
	}
}

add_shortcode( 'byt_widget_call_to_action', 'byt_widget_call_to_action' );
function byt_widget_call_to_action( $atts ) {

	extract( shortcode_atts( 
		array(
			'call_to_action_text' => esc_html__('Like what you see? Are you ready to stand out? You know what to do!', 'bookyourtravel'),
			'button_text' => esc_html__('Purchase theme', 'bookyourtravel'),
			'button_url' => '#',
			'css' => ''
		),
		$atts
	));
	
	$css_class = $css;
	if (function_exists('vc_shortcode_custom_css_class')) {
		$css_class = vc_shortcode_custom_css_class( $css, ' ' );
	}
	
	$widget_args = array( 
		'before_widget'  => sprintf('<div class="widget widget-sidebar %s">', $css_class),
		'after_widget' => '</div>',
		'before_title' => '<h2>',
		'after_title' => '</h2>'
	);
	
	ob_start();
	the_widget( 'bookyourtravel_call_to_action_widget', $atts, $widget_args ); 
	$output = ob_get_clean();

	return $output;
}