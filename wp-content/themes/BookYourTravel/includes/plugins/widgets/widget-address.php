<?php
/**
 * The template for displaying Address widget
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */

// Add function to widgets_init that'll load our widget.
add_action( 'widgets_init', 'bookyourtravel_address_widgets' );

// Register widget.
function bookyourtravel_address_widgets() {
	register_widget( 'bookyourtravel_address_widget' );
}

// Widget class.
class bookyourtravel_address_widget extends WP_Widget {

	/*-----------------------------------------------------------------------------------*/
	/*	Widget Setup
	/*-----------------------------------------------------------------------------------*/

	function __construct() {

		/* Widget settings. */
		$widget_ops = array( 'classname' => 'bookyourtravel_address_widget', 'description' => esc_html__('BookYourTravel: Address Widget', 'bookyourtravel') );

		/* Widget control settings. */
		$control_ops = array( 'width' => 300, 'height' => 550, 'id_base' => 'bookyourtravel_address_widget' );

		/* Create the widget. */
		parent::__construct( 'bookyourtravel_address_widget', esc_html__('BookYourTravel: Address Widget', 'bookyourtravel'), $widget_ops, $control_ops );
	}


	/*-----------------------------------------------------------------------------------*/
	/*	Display Widget
	/*-----------------------------------------------------------------------------------*/

	function widget( $args, $instance ) {
		extract( $args );


		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', isset($instance['title']) ? $instance['title'] : '' );
		$company_name = isset($instance['company_name']) ? $instance['company_name'] : '';
		$company_address = isset($instance['company_address']) ? $instance['company_address'] : '';
		$company_phone = isset($instance['company_phone']) ? $instance['company_phone'] : '';
		$company_email = isset($instance['company_email']) ? $instance['company_email'] : '';

		echo $before_widget;

		/* Display Widget */
		/* Display the widget title if one was input (before and after defined by themes). */
		if ( $title )
			echo $before_title . $title . $after_title;
		?>
			<article class="byt_address_widget BookYourTravel_Address_Widget">
				<?php if (!empty($company_name)) { ?>
				<h6><?php echo esc_html($company_name); ?></h6>
				<?php } ?>
				<?php if (!empty($company_address)) { ?>
				<p><em><i class="material-icons md-16">place</i></em> <?php echo esc_html($company_address); ?></p>
				<?php } ?>
				<?php if (!empty($company_phone)) { ?>
				<p><em><i class="material-icons md-16">call</i></em> <?php echo esc_html($company_phone); ?></p>
				<?php } ?>
				<?php if (!empty($company_email)) { ?>
				<p><em><i class="material-icons md-16">email</i></em> <a href="mailto:<?php echo esc_attr( $company_email ); ?>" title="<?php echo esc_attr( $company_email ); ?>"><?php echo esc_html($company_email); ?></a></p>
				<?php } ?>
			</article>
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
		$instance['title'] = isset($new_instance['title']) ? strip_tags( $new_instance['title'] ) : '';
		$instance['company_name'] = isset($new_instance['company_name']) ? strip_tags( $new_instance['company_name'] ) : '';
		$instance['company_address'] = isset($new_instance['company_address']) ? strip_tags( $new_instance['company_address'] ) : '';
		$instance['company_phone'] = isset($new_instance['company_phone']) ? strip_tags( $new_instance['company_phone'] ) : '';
		$instance['company_email'] = isset($new_instance['company_email']) ? strip_tags( $new_instance['company_email'] ) : '';

		return $instance;
	}


/*-----------------------------------------------------------------------------------*/
/*	Widget Settings
/*-----------------------------------------------------------------------------------*/

	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array(
			'title' => '',
			'company_name' => 'Book Your Travel LLC',
			'company_address' => '1400 Pennsylvania Ave. Washington, DC',
			'company_phone' => '1-555-555-5555',
			'company_email' => 'info@bookyourtravel.com',
			'layout_class' => ''
		);

		$instance = wp_parse_args( (array) $instance, $defaults );
		?>

		<!-- Widget Title: Text Input -->
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e('Title:', 'bookyourtravel') ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'company_name' ) ); ?>"><?php esc_html_e('Company name:', 'bookyourtravel') ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'company_name' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'company_name' ) ); ?>" value="<?php echo esc_attr( $instance['company_name']); ?>" />
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'company_address' ) ); ?>"><?php esc_html_e('Company address:', 'bookyourtravel') ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'company_address' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'company_address' ) ); ?>" value="<?php echo esc_attr( $instance['company_address'] ); ?>" />
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'company_phone' ) ); ?>"><?php esc_html_e('Company phone:', 'bookyourtravel') ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'company_phone' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'company_phone' ) ); ?>" value="<?php echo esc_attr( $instance['company_phone'] ); ?>" />
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'company_email' ) ); ?>"><?php esc_html_e('Company email:', 'bookyourtravel') ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'company_email' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'company_email' ) ); ?>" value="<?php echo esc_attr( $instance['company_email'] ); ?>" />
		</p>

	<?php
	}
}

add_shortcode( 'byt_widget_address', 'byt_widget_address' );
function byt_widget_address( $atts ) {

	extract( shortcode_atts(
		array(
			'title' => '',
			'company_name' => 'Book Your Travel LLC',
			'company_address' => '1400 Pennsylvania Ave. Washington, DC',
			'company_phone' => '1-555-555-5555',
			'company_email' => 'info@bookyourtravel.com',
			'layout_class' => '',
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
	the_widget( 'bookyourtravel_address_widget', $atts, $widget_args );
	$output = ob_get_clean();
	return $output;
}