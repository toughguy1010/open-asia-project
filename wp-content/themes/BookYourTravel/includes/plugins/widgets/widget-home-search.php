<?php
/**
 * The template for displaying Social widget
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */

// Add function to widgets_init that'll load our widget.
add_action( 'widgets_init', 'bookyourtravel_home_searching_widgets' );

// Register widget.
function bookyourtravel_home_searching_widgets() {
	register_widget( 'bookyourtravel_home_searching_widgets' );
}

// Widget class.
class bookyourtravel_home_searching_widget extends WP_Widget {

	/*-----------------------------------------------------------------------------------*/
	/*	Widget Setup
	/*-----------------------------------------------------------------------------------*/
		
	function __construct() {
	
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'bookyourtravel_home_searching_widgets', 'description' => esc_html__('BookYourTravel: Social Widget', 'bookyourtravel') );

		/* Widget control settings. */
		$control_ops = array( 'width' => 300, 'height' => 550, 'id_base' => 'bookyourtravel_home_searching_widgets' );

		/* Create the widget. */
		parent::__construct( 'bookyourtravel_home_searching_widgets', esc_html__('BookYourTravel: Social Widget', 'bookyourtravel'), $widget_ops, $control_ops );
	}


	/*-----------------------------------------------------------------------------------*/
	/*	Display Widget
	/*-----------------------------------------------------------------------------------*/
		
	function widget( $args, $instance ) {
		
		extract( $args );


		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', isset($instance['title']) ? $instance['title'] : '');

		$facebook_id = isset($instance['facebook_id']) ? $instance['facebook_id'] : '';
		$facebook_id = str_replace('http://www.facebook.com/', '', $facebook_id);
		
		$twitter_id = isset($instance['twitter_id']) ? $instance['twitter_id'] : '';
		$twitter_id = str_replace('http://twitter.com/', '', $twitter_id);
		
		$youtube_profile = isset($instance['youtube_profile']) ? $instance['youtube_profile'] : '';
		$rss_feed = isset($instance['rss_feed']) ? $instance['rss_feed'] : '';
		$linked_in_profile = isset($instance['linked_in_profile']) ? $instance['linked_in_profile'] : '';
		$gplus_profile = isset($instance['gplus_profile']) ? $instance['gplus_profile'] : '';
		$vimeo_profile = isset($instance['vimeo_profile']) ? $instance['vimeo_profile'] : '';
		$pinterest_profile = isset($instance['pinterest_profile']) ? $instance['pinterest_profile'] : '';
		$whatsapp_profile = isset($instance['whatsapp_profile']) ? $instance['whatsapp_profile'] : '';
		$instagram_profile = isset($instance['instagram_profile']) ? $instance['instagram_profile'] : '';
		$skype_profile = isset($instance['skype_profile']) ? $instance['skype_profile'] : '';
		$tripadvisor_profile = isset($instance['tripadvisor_profile']) ? $instance['tripadvisor_profile'] : '';

		echo $before_widget;

		/* Display Widget */
		/* Display the widget title if one was input (before and after defined by themes). */
		?>
			<article class="byt_social_widget BookYourTravel_Home_Searching_Widget">
		<?php
			if ( $title )
				echo $before_title . $title . $after_title;
		?>
				
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
		$instance['facebook_id'] = isset($new_instance['facebook_id']) ? strip_tags( $new_instance['facebook_id'] ) : '';
		$instance['twitter_id'] = isset($new_instance['twitter_id']) ? strip_tags( $new_instance['twitter_id'] ) : '';
		$instance['youtube_profile'] = isset($new_instance['youtube_profile']) ? strip_tags( $new_instance['youtube_profile'] ) : '';
		$instance['rss_feed'] = isset($new_instance['rss_feed']) ? strip_tags( $new_instance['rss_feed'] ) : '';
		$instance['linked_in_profile'] = isset($new_instance['linked_in_profile']) ? strip_tags( $new_instance['linked_in_profile'] ) : '';
		$instance['gplus_profile'] = isset($new_instance['gplus_profile']) ? strip_tags( $new_instance['gplus_profile'] ) : '';
		$instance['vimeo_profile'] = isset($new_instance['vimeo_profile']) ? strip_tags( $new_instance['vimeo_profile'] ) : '';
		$instance['pinterest_profile'] = isset($new_instance['pinterest_profile']) ? strip_tags( $new_instance['pinterest_profile'] ) : '';
		$instance['whatsapp_profile'] = isset($new_instance['whatsapp_profile']) ? strip_tags( $new_instance['whatsapp_profile'] ) : '';
		$instance['instagram_profile'] = isset($new_instance['instagram_profile']) ? strip_tags( $new_instance['instagram_profile'] ) : '';
		$instance['skype_profile'] = isset($new_instance['skype_profile']) ? strip_tags( $new_instance['skype_profile'] ) : '';
		$instance['tripadvisor_profile'] = isset($new_instance['tripadvisor_profile']) ? strip_tags( $new_instance['tripadvisor_profile'] ) : '';
		
		return $instance;
	}
	

/*-----------------------------------------------------------------------------------*/
/*	Widget Settings
/*-----------------------------------------------------------------------------------*/
	 
	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array(
			'title' => esc_html__('Follow us', 'bookyourtravel'),
			'tripadvisor_profile' => ''			
		);
		$instance = wp_parse_args( (array) $instance, $defaults ); 
		?>
		
		<!-- Widget Title: Text Input -->
		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'tripadvisor_profile' )); ?>"><?php esc_html_e('Tripadvisor url:', 'bookyourtravel') ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'tripadvisor_profile' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'tripadvisor_profile' ) ); ?>" value="<?php echo esc_attr( $instance['tripadvisor_profile'] ); ?>" />
		</p>
		
	<?php
	}
}

add_shortcode( 'byt_widget_home_searching', 'byt_widget_home_searching' );
function byt_widget_home_searching( $atts ) {

	extract( shortcode_atts( 
		array(
			'title' => esc_html__('Follow us', 'bookyourtravel'),
			'facebook_id' => '',
			'twitter_id' => '',
			'youtube_profile' => '',
			'rss_feed' => '',
			'linked_in_profile' => '',
			'gplus_profile' => '',
			'vimeo_profile' => '',
			'pinterest_profile' => '',
			'whatsapp_profile' => '',
			'instagram_profile' => '',
			'skype_profile' => '',
			'tripadvisor_profile' => '',
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
	the_widget( 'byt_widget_home_searching', $atts, $widget_args ); 
	$output = ob_get_clean();

	return $output;
}