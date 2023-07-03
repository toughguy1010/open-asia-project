<?php
/**
 * The template for displaying Iconic features widget
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */

// Add function to widgets_init that'll load our widget.
add_action( 'widgets_init', 'bookyourtravel_iconic_features_widgets' );

// Register widget.
function bookyourtravel_iconic_features_widgets() {
	register_widget( 'bookyourtravel_iconic_features_widget' );
}

// Widget class.
class bookyourtravel_iconic_features_widget extends WP_Widget {


	/*-----------------------------------------------------------------------------------*/
	/*	Widget Setup
	/*-----------------------------------------------------------------------------------*/
		
	function __construct() {
	
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'bookyourtravel_iconic_features_widget', 'description' => esc_html__('BookYourTravel: Iconic Features Widget', 'bookyourtravel') );

		/* Widget control settings. */
		$control_ops = array( 'width' => 300, 'height' => 550, 'id_base' => 'bookyourtravel_iconic_features_widget' );

		/* Create the widget. */
		parent::__construct( 'bookyourtravel_iconic_features_widget', esc_html__('BookYourTravel: Iconic Features Widget', 'bookyourtravel'), $widget_ops, $control_ops );
	}


	/*-----------------------------------------------------------------------------------*/
	/*	Display Widget
	/*-----------------------------------------------------------------------------------*/
		
	function widget( $args, $instance ) {

		extract( $args );
	
		global $bookyourtravel_theme_globals;
		$iconic_features_icon_classes = $bookyourtravel_theme_globals->get_iconic_features_icon_classes();
		
		$card_layout_classes = array(
			'full-width',
			'one-half',
			'one-third',
			'one-fourth',
			'one-fifth'
		);
		
		$vc_serialized_features = '';
		
		if (isset($instance['features']) && is_string($instance['features'])) {
			if (!empty($instance['features'])) {
				$vc_serialized_features = $instance['features'];
				$features_string = apply_filters('bookyourtravel_vc_decode_string_from_editor', $vc_serialized_features);			
				$instance['features'] = json_decode($features_string, TRUE);
			} else {
				$instance['features'] = array();
			}
		}
		
		$widget_default_features = array(
			array('class' => 'card_travel', 'title' => esc_html__('Lorem ipsum dolor', 'bookyourtravel'), 'text' => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy tinc dolore magna'),
			array('class' => 'directions', 'title' => esc_html__('Lorem ipsum dolor', 'bookyourtravel'), 'text' => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy tinc dolore magna'),
			array('class' => 'dialpad', 'title' => esc_html__('Lorem ipsum dolor', 'bookyourtravel'), 'text' => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy tinc dolore magna'),
			array('class' => 'event', 'title' => esc_html__('Lorem ipsum dolor', 'bookyourtravel'), 'text' => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy tinc dolore magna'),
			array('class' => 'explore', 'title' => esc_html__('Lorem ipsum dolor', 'bookyourtravel'), 'text' => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy tinc dolore magna'),
			array('class' => 'high_quality ', 'title' => esc_html__('Lorem ipsum dolor', 'bookyourtravel'), 'text' => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy tinc dolore magna'),
		);
		
		/* Our variables from the widget settings. */
		$number_of_features = isset($instance['number_of_features']) ? (int)$instance['number_of_features'] : 6;		
		$features_per_row = isset($instance['features_per_row']) ? (int)$instance['features_per_row'] : 3;		

		$feature_class = 'one-third';
		if (isset($card_layout_classes[$features_per_row - 1]))
			$feature_class = $card_layout_classes[$features_per_row - 1];
		
		global $display_mode;
		
		$display_mode = isset($instance['display_mode']) ? $instance['display_mode'] : 'card';
		$widget_features = isset($instance['features']) && is_array($instance['features']) ? $instance['features'] : $widget_default_features;
		
		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Display Widget */
		?>
		
		<!-- Services iconic -->
		<?php if ($display_mode == 'card') { ?>
		<div class="iconic">
		<?php } else { ?>
		<div class="iconic small-list">
		<?php } ?>
			<div class="wrapper">
				<div class="row">
<?php
					$i = 1;
					foreach ($widget_features as $widget_feature) {
						$delay = ($i % 3) * 1.5;
						?>
						<!-- Item -->
						<div class="<?php echo esc_attr($feature_class); ?>">
							<?php if (isset($widget_feature['class']) && !empty($widget_feature['class'])) { ?>
								<span class="circle"><span class="icon material-icons"><?php echo esc_attr(trim($widget_feature['class'])); ?></span></span>
							<?php } ?>
							<?php if (isset($widget_feature['title'])) { ?>
							<h4><?php echo esc_html($widget_feature['title']); ?></h4>
							<?php } ?>
							<?php if (isset($widget_feature['text'])) { ?>
							<div class="desc"><?php echo esc_html($widget_feature['text']); ?></div>
							<?php } ?>
						</div>
						<!-- //Item -->
						<?php
						$i++;
						$i = $i == 3 ? 0 : $i;
					}
?>				
				</div>
			</div>
		</div>
		<!-- //Services iconic -->

		<?php
		/* After widget (defined by themes). */
		echo $after_widget;
		
		// set back to default since this is a global variable
		$display_mode = 'card';
	}


/*-----------------------------------------------------------------------------------*/
/*	Update Widget
/*-----------------------------------------------------------------------------------*/
	
	function update( $new_instance, $old_instance ) {
	
		$instance = $old_instance;
		
		/* Strip tags to remove HTML (important for text inputs). */
		$instance['number_of_features'] = isset($new_instance['number_of_features']) ? intval($new_instance['number_of_features']) : 4;
		$instance['features_per_row'] = isset($new_instance['features_per_row']) ? intval($new_instance['features_per_row']) : 4;
		$instance['display_mode'] = isset($new_instance['display_mode']) ? $new_instance['display_mode'] : '';
        $instance['features'] = isset($new_instance['features']) ? $new_instance['features'] : array(); 	
		
		return $instance;
	}
	

/*-----------------------------------------------------------------------------------*/
/*	Widget Settings
/*-----------------------------------------------------------------------------------*/
	 
	function form( $instance ) {
	
		global $bookyourtravel_theme_globals;
		
		$iconic_features_icon_classes = $bookyourtravel_theme_globals->get_iconic_features_icon_classes();
		?>
		<script type="text/javascript">
			<?php
			echo 'window.themeenergyIconsString = ' . json_encode( $iconic_features_icon_classes ) . ';';
			echo 'window.themeenergyIconsContainerClass = "feature";';
			?>
		</script>
		<?php	
		/* Set up some default widget settings. */
		
		$defaults = array(
			'number_of_features' => '6',
			'display_mode' => 'card',
			'features_per_row' => '3',
			'features' => array()
		);
		
		$instance = wp_parse_args( (array) $instance, $defaults ); 

		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'number_of_features' ) ); ?>"><?php esc_html_e('How many features do you want to display?', 'bookyourtravel') ?></label>
			<select id="<?php echo esc_attr( $this->get_field_id( 'number_of_features' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'number_of_features' ) ); ?>">
				<?php for ($i=1;$i<13;$i++) { ?>
				<option <?php echo ($i == $instance['number_of_features'] ? 'selected="selected"' : ''); ?> value="<?php echo esc_attr ( $i ); ?>"><?php echo esc_html($i); ?></option>
				<?php } ?>
			</select>
		</p>
		
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'display_mode' ) ); ?>"><?php esc_html_e('Display mode?', 'bookyourtravel') ?></label>
			<select class="posts_widget_display_mode" id="<?php echo esc_attr( $this->get_field_id( 'display_mode' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'display_mode') ); ?>">
				<option <?php echo 'small' == $instance['display_mode'] ? 'selected="selected"' : ''; ?> value="small"><?php esc_html_e('Small (usually sidebar)', 'bookyourtravel') ?></option>
				<option <?php echo 'card' == $instance['display_mode'] ? 'selected="selected"' : ''; ?> value="card"><?php esc_html_e('Card (usually in grid view)', 'bookyourtravel') ?></option>
			</select>
		</p>
		
		<p class="cards" <?php echo ( $instance['display_mode'] != 'card' ? 'style="display:none"' : '' ); ?>>
			<label for="<?php echo esc_attr ( $this->get_field_id( 'features_per_row' ) ); ?>"><?php esc_html_e('How many features do you want to display per row?', 'bookyourtravel') ?></label>
			<select id="<?php echo esc_attr ( $this->get_field_id( 'features_per_row' ) ); ?>" name="<?php echo esc_attr ( $this->get_field_name( 'features_per_row' ) ); ?>">
				<?php for ($i=1;$i<6;$i++) { ?>
				<option <?php echo ($i == $instance['features_per_row'] ? 'selected="selected"' : ''); ?> value="<?php echo esc_attr ( $i ); ?>"><?php echo esc_html($i); ?></option>
				<?php } ?>
			</select>
		</p>
		
		<div id="widgets_icon_preview_div" class="icon-preview" style="display:none;">
			<div class="icons">
			</div>
		</div>
		
		<?php 
		$features = $instance['features'];
		
		for ($i=0;$i<$instance['number_of_features'];$i++) { 
			$feature = isset($features[$i]) ? $features[$i] : null;
			$class = isset($feature) && isset($feature['class']) ? trim($feature['class']) : '';
			$title = isset($feature) && isset($feature['title']) ? trim($feature['title']) : '';
			$text = isset($feature) && isset($feature['text']) ? trim($feature['text']) : ''; ?>
			<div class="feature feature<?php echo esc_attr($i); ?>">
				<h3><?php echo sprintf(__("Feature %d", 'bookyourtravel'), $i+1); ?></h3>
				<p>
					<input class="icon_class" id="<?php echo esc_attr($this->get_field_id('features') . '[' . $i . '][class]') ?>" name="<?php echo esc_attr($this->get_field_name('features') . '[' . $i . '][class]') ?>" type="text" value="<?php echo esc_attr($class); ?>" />
					<?php if (!empty($class)) {?>
					<span class="lightbox-icon icon material-icons"><?php echo esc_html($class); ?></span>
					<?php } else { ?>
					<span class="lightbox-icon icon"></span>
					<?php } ?>
					<a href="#TB_inline?height=700&width=700&inlineId=widgets_icon_preview_div" class="thickbox thickbox_link thickbox<?php echo esc_attr($i); ?>"><?php _e('Select icon', 'bookyourtravel'); ?></a>	
				</p>
				<p>
					<label><?php esc_html_e("Feature title", 'bookyourtravel'); ?></label>
					<input type="text" class="widefat" id="<?php echo esc_attr($this->get_field_id('features') . '[' . $i . '][title]') ?>" name="<?php echo esc_attr($this->get_field_name('features') . '[' . $i . '][title]') ?>" value="<?php echo esc_attr($title); ?>" />
				</p>
				<p>
					<label><?php esc_html_e("Feature text", 'bookyourtravel'); ?></label>
					<input type="text" class="widefat" id="<?php echo esc_attr($this->get_field_id('features') . '[' . $i . '][text]') ?>" name="<?php echo esc_attr($this->get_field_name('features') . '[' . $i . '][text]') ?>" value="<?php echo esc_attr($text); ?>" />
				</p>			
			</div>		
		<?php 
		} 
	}
}

add_shortcode( 'byt_widget_iconic_features', 'byt_widget_iconic_features' );
function byt_widget_iconic_features( $atts ) {

	if (isset($atts['features']) && $atts['features'] == '') {
		unset($atts['features']);
	}

	extract( shortcode_atts( 
		array(
			'number_of_features' => '6',
			'display_mode' => 'card',
			'features_per_row' => '3',
			'features' => array(),
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
	the_widget( 'bookyourtravel_iconic_features_widget', $atts, $widget_args ); 
	$output = ob_get_clean();

	return $output;
}