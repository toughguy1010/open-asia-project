<?php
/**
 * The template for displaying Cruise list widget
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */

// Add function to widgets_init that'll load our widget.
add_action( 'widgets_init', 'bookyourtravel_cruise_lists_widgets' );

// Register widget.
function bookyourtravel_cruise_lists_widgets() {
	global $bookyourtravel_theme_globals;
	if ($bookyourtravel_theme_globals->enable_cruises()) {
		register_widget( 'bookyourtravel_cruise_list_widget' );
	}
}

// Widget class.
class bookyourtravel_cruise_list_widget extends WP_Widget {

	/*-----------------------------------------------------------------------------------*/
	/*	Widget Setup
	/*-----------------------------------------------------------------------------------*/

	function __construct() {

		/* Widget settings. */
		$widget_ops = array( 'classname' => 'bookyourtravel_cruise_list_widget', 'description' => esc_html__('BookYourTravel: Cruise List', 'bookyourtravel') );

		/* Widget control settings. */
		$control_ops = array( 'width' => 260, 'height' => 400, 'id_base' => 'bookyourtravel_cruise_list_widget' );

		/* Create the widget. */
		parent::__construct( 'bookyourtravel_cruise_list_widget', esc_html__('BookYourTravel: Cruise List', 'bookyourtravel'), $widget_ops, $control_ops );
	}


	/*-----------------------------------------------------------------------------------*/
	/*	Display Widget
	/*-----------------------------------------------------------------------------------*/

	function widget( $args, $instance ) {

		global $bookyourtravel_theme_globals, $bookyourtravel_cruise_helper;

		extract( $args );

		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', isset($instance['title']) ? $instance['title'] : esc_html__('Explore our latest cruises', 'bookyourtravel') );

		global $bookyourtravel_theme_globals, $cruise_list_args, $cruise_item_args;

		$cruise_list_args = array();

		$cruise_list_args['posts_per_page'] = isset($instance['number_of_posts']) ? (int)$instance['number_of_posts'] : 4;
		$cruise_list_args['sort_by'] = isset($instance['sort_by']) ? $instance['sort_by'] : 'title';
		$sort_descending = isset($instance['sort_by']) && $instance['sort_descending'] == '1';
		$cruise_list_args['sort_order'] = $sort_descending ? 'DESC' : 'ASC';
		$cruise_list_args['posts_per_row'] = isset($instance['posts_per_row']) ? (int)$instance['posts_per_row'] : 4;
		$cruise_list_args['show_featured_only'] = isset($instance['show_featured_only']) && $instance['show_featured_only'] == '1';
		$cruise_list_args['cruise_type_ids'] = isset($instance['cruise_type_ids']) ? (array)$instance['cruise_type_ids'] : array();
		$cruise_list_args['cruise_duration_ids'] = isset($instance['cruise_duration_ids']) ? (array)$instance['cruise_duration_ids'] : array();
		$cruise_list_args['cruise_tag_ids'] = isset($instance['cruise_tag_ids']) ? (array)$instance['cruise_tag_ids'] : array();
		$cruise_list_args['display_mode'] = isset($instance['display_mode']) ? $instance['display_mode'] : 'card';
		$cruise_list_args['paged'] = 1;

		$display_mode = $cruise_list_args['display_mode'];

		$cruise_item_args = array();
		$cruise_item_args['hide_title'] = isset($instance['hide_title']) && $instance['hide_title'] == '1';
		$cruise_item_args['hide_image'] = isset($instance['hide_image']) && $instance['hide_image'] == '1';
		$cruise_item_args['hide_description'] = isset($instance['hide_description']) && $instance['hide_description'] == '1';
		$cruise_item_args['hide_actions'] = isset($instance['hide_actions']) && $instance['hide_actions'] == '1';
		$cruise_item_args['hide_rating'] = isset($instance['hide_rating']) && $instance['hide_rating'] == '1';
		$cruise_item_args['hide_price'] = isset($instance['hide_price']) && $instance['hide_price'] == '1';
		$cruise_item_args['hide_address'] = (isset($instance['hide_address']) && $instance['hide_address'] == '1');

		echo $before_widget;

		if ($display_mode == 'card') {
			echo '<div class="s-title">' . $before_title . $title . $after_title . '</div>';
		} else {
			echo $before_title . $title . $after_title;
		}

		do_action( 'bookyourtravel_widget_cruise_list_before' );
		get_template_part('includes/parts/cruise/cruise', 'list');
		do_action( 'bookyourtravel_widget_cruise_list_after' );

		/* After widget (defined by themes). */
		echo $after_widget;

		// set back to default since this is a global variable
		$cruise_list_args['display_mode'] = 'card';

		wp_reset_postdata();
		wp_reset_query();
	}

	/*-----------------------------------------------------------------------------------*/
	/*	Update Widget
	/*-----------------------------------------------------------------------------------*/

	function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		/* Strip tags to remove HTML (important for text inputs). */
		$instance['title'] = isset($new_instance['title']) ? strip_tags( $new_instance['title'] ) : '';
		$instance['number_of_posts'] = isset($new_instance['number_of_posts']) ? intval( $new_instance['number_of_posts']) : 12;
		$instance['sort_by'] = isset($new_instance['sort_by']) ? strip_tags( $new_instance['sort_by']) : '';
		$instance['sort_descending'] = isset($new_instance['sort_descending']) ? strip_tags( $new_instance['sort_descending']) : false;
		$instance['display_mode'] = isset($new_instance['display_mode']) ? strip_tags( $new_instance['display_mode']) : '';
		$instance['posts_per_row'] = isset($new_instance['posts_per_row']) ? intval( $new_instance['posts_per_row']) : 3;
		$instance['show_featured_only'] = isset($new_instance['show_featured_only']) ? strip_tags( $new_instance['show_featured_only']) : false;
		$instance['cruise_type_ids'] = isset($new_instance['cruise_type_ids']) ? $new_instance['cruise_type_ids'] : array();
		$instance['cruise_duration_ids'] = isset($new_instance['cruise_duration_ids']) ? $new_instance['cruise_duration_ids'] : array();
		$instance['cruise_tag_ids'] = isset($new_instance['cruise_tag_ids']) ? $new_instance['cruise_tag_ids'] : array();

		$instance['hide_title'] = isset($new_instance['hide_title']) ? $new_instance['hide_title'] : false;
		$instance['hide_image'] = isset($new_instance['hide_image']) ? $new_instance['hide_image'] : false;
		$instance['hide_description'] = isset($new_instance['hide_description']) ? $new_instance['hide_description'] : false;
		$instance['hide_actions'] = isset($new_instance['hide_actions']) ? $new_instance['hide_actions'] : false;
		$instance['hide_rating'] = isset($new_instance['hide_rating']) ? $new_instance['hide_rating'] : false;
		$instance['hide_price'] = isset($new_instance['hide_price']) ? $new_instance['hide_price'] : false;
		$instance['hide_address'] = isset($new_instance['hide_address']) ? $new_instance['hide_address'] : false;

		return $instance;
	}

	/*-----------------------------------------------------------------------------------*/
	/*	Widget Settings
	/*-----------------------------------------------------------------------------------*/

	function form( $instance ) {

		$cat_args = array(
			'taxonomy'=>'cruise_type',
			'hide_empty'=>'0'
		);
		$cruise_types = get_categories($cat_args);

		$cat_args = array(
			'taxonomy'=>'cruise_duration',
			'hide_empty'=>'0'
		);
		$cruise_durations = get_categories($cat_args);		

		$cat_args = array(
			'taxonomy'=>'cruise_tag',
			'hide_empty'=>'0'
		);
		$cruise_tags = get_categories($cat_args);

		/* Set up some default widget settings. */
		$defaults = array(
			'title' => esc_html__('Explore our latest cruises', 'bookyourtravel'),
			'number_of_posts' => '4',
			'sort_by' => 'title',
			'sort_descending' => '1',
			'display_mode' => 'card',
			'posts_per_row' => 4,
			'show_featured_only' => '0',
			'cruise_type_ids' => array(),
			'cruise_duration_ids' => array(),
			'cruise_tag_ids' => array(),
			'hide_title' => '0',
			'hide_image' => '0',
			'hide_description' => '0',
			'hide_actions' => '0',
    		'hide_rating' => '0',
			'hide_price' => '0',
			'hide_address' => '0',
		);
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<!-- Widget Title: Text Input -->

		<div class="wp-widget-styling">
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e('Title:', 'bookyourtravel') ?></label>
				<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo esc_attr ($instance['title']); ?>" />
			</p>

			<div class="w-group">
				<p class="section-title"><?php esc_html_e('Widget layout', 'bookyourtravel') ?></p>
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'number_of_posts' ) ); ?>"><?php esc_html_e('How many cruises do you want to display?', 'bookyourtravel') ?></label>
					<select id="<?php echo esc_attr( $this->get_field_id( 'number_of_posts' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'number_of_posts' ) ); ?>">
						<?php for ($i=1;$i<13;$i++) { ?>
						<option <?php echo ($i == $instance['number_of_posts'] ? 'selected="selected"' : ''); ?> value="<?php echo esc_attr ( $i ); ?>"><?php echo esc_html($i); ?></option>
						<?php } ?>
					</select>
				</p>

				<p class="cards" <?php echo ( $instance['display_mode'] != 'card' ? 'style="display:none"' : '' ); ?>>
					<label for="<?php echo esc_attr ( $this->get_field_id( 'posts_per_row' ) ); ?>"><?php esc_html_e('How many cruises do you want to display per row?', 'bookyourtravel') ?></label>
					<select id="<?php echo esc_attr ( $this->get_field_id( 'posts_per_row' ) ); ?>" name="<?php echo esc_attr ( $this->get_field_name( 'posts_per_row' ) ); ?>">
						<?php for ($i=1;$i<6;$i++) { ?>
						<option <?php echo ($i == $instance['posts_per_row'] ? 'selected="selected"' : ''); ?> value="<?php echo esc_attr ( $i ); ?>"><?php echo esc_html($i); ?></option>
						<?php } ?>
					</select>
				</p>

				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'display_mode' ) ); ?>"><?php esc_html_e('Display mode?', 'bookyourtravel') ?></label>
					<select class="posts_widget_display_mode" id="<?php echo esc_attr( $this->get_field_id( 'display_mode' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'display_mode') ); ?>">
						<option <?php echo 'small' == $instance['display_mode'] ? 'selected="selected"' : ''; ?> value="small"><?php esc_html_e('List', 'bookyourtravel') ?></option>
						<option <?php echo 'card' == $instance['display_mode'] ? 'selected="selected"' : ''; ?> value="card"><?php esc_html_e('Grid', 'bookyourtravel') ?></option>
					</select>
				</p>
			</div>

			<div class="w-group">
				<p class="section-title"><?php esc_html_e('Widget content', 'bookyourtravel') ?></p>
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'sort_by' ) ); ?>"><?php esc_html_e('What do you want to sort the cruises by?', 'bookyourtravel') ?></label>
					<select id="<?php echo esc_attr( $this->get_field_id( 'sort_by' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'sort_by') ); ?>">
						<option <?php echo 'title' == $instance['sort_by'] ? 'selected="selected"' : ''; ?> value="title"><?php esc_html_e('Post Title', 'bookyourtravel') ?></option>
						<option <?php echo 'ID' == $instance['sort_by'] ? 'selected="selected"' : ''; ?> value="ID"><?php esc_html_e('Post ID', 'bookyourtravel') ?></option>
						<option <?php echo 'rand' == $instance['sort_by'] ? 'selected="selected"' : ''; ?> value="rand"><?php esc_html_e('Random', 'bookyourtravel') ?></option>
						<option <?php echo 'date' == $instance['sort_by'] ? 'selected="selected"' : ''; ?> value="date"><?php esc_html_e('Publish Date', 'bookyourtravel') ?></option>
						<option <?php echo 'comment_count' == $instance['sort_by'] ? 'selected="selected"' : ''; ?> value="comment_count"><?php esc_html_e('Comment Count', 'bookyourtravel') ?></option>
						<option <?php echo 'menu_order' == $instance['sort_by'] ? 'selected="selected"' : ''; ?> value="menu_order"><?php esc_html_e('Order attribute', 'bookyourtravel');?></option>
					</select>
				</p>

				<p>
					<input type="checkbox"  <?php echo ($instance['sort_descending'] == '1' ? 'checked="checked"' : ''); ?> class="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'sort_descending' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'sort_descending') ); ?>" value="1" />
					<label for="<?php echo esc_attr( $this->get_field_id( 'sort_descending' ) ); ?>"><?php esc_html_e('Sort cruises in descending order?', 'bookyourtravel') ?></label>
				</p>

				<p>
					<input type="checkbox"  <?php echo ( $instance['show_featured_only'] == '1' ? 'checked="checked"' : '' ); ?> class="checkbox" id="<?php echo esc_attr ( $this->get_field_id( 'show_featured_only' ) ); ?>" name="<?php echo esc_attr ( $this->get_field_name( 'show_featured_only' ) ); ?>" value="1" />
					<label for="<?php echo esc_attr( $this->get_field_id( 'show_featured_only') ); ?>"><?php esc_html_e('Show only featured cruises?', 'bookyourtravel') ?></label>
				</p>

				<p>
					<label><?php esc_html_e('Cruise type (leave blank to ignore)', 'bookyourtravel') ?></label>
					<div>
						<?php for ($j=0;$j<count($cruise_types);$j++) {
							$type = $cruise_types[$j];
							$checked = false;
							if (isset($instance['cruise_type_ids'])) {
								if (in_array($type->term_id, $instance['cruise_type_ids']))
									$checked = true;
							}
						?>
						<input <?php echo ($checked ? 'checked="checked"' : ''); ?> type="checkbox" id="<?php echo esc_attr ( $this->get_field_name( 'cruise_type_ids' ) ); ?>_<?php echo esc_attr ($type->term_id); ?>" name="<?php echo esc_attr ( $this->get_field_name( 'cruise_type_ids' ) ); ?>[]" value="<?php echo esc_attr ($type->term_id); ?>">
						<label for="<?php echo esc_attr ( $this->get_field_name( 'cruise_type_ids' ) ); ?>_<?php echo esc_attr ($type->term_id); ?>"><?php echo esc_html($type->name); ?></label>
						<br />
						<?php } ?>
					</div>
				</p>

				<p>
					<label><?php esc_html_e('Cruise duration (leave blank to ignore)', 'bookyourtravel') ?></label>
					<div>
						<?php for ($j=0;$j<count($cruise_durations);$j++) {
							$duration = $cruise_durations[$j];
							$checked = false;
							if (isset($instance['cruise_duration_ids'])) {
								if (in_array($duration->term_id, $instance['cruise_duration_ids']))
									$checked = true;
							}
						?>
						<input <?php echo ($checked ? 'checked="checked"' : ''); ?> type="checkbox" id="<?php echo esc_attr ( $this->get_field_name( 'cruise_duration_ids' ) ); ?>_<?php echo esc_attr ($duration->term_id); ?>" name="<?php echo esc_attr ( $this->get_field_name( 'cruise_duration_ids' ) ); ?>[]" value="<?php echo esc_attr ($duration->term_id); ?>">
						<label for="<?php echo esc_attr ( $this->get_field_name( 'cruise_duration_ids' ) ); ?>_<?php echo esc_attr ($duration->term_id); ?>"><?php echo esc_html($duration->name); ?></label>
						<br />
						<?php } ?>
					</div>
				</p>				

				<p>
					<label><?php esc_html_e('Cruise tag (leave blank to ignore)', 'bookyourtravel') ?></label>
					<div>
						<?php for ($j=0;$j<count($cruise_tags);$j++) {
							$tag = $cruise_tags[$j];
							$checked = false;
							if (isset($instance['cruise_tag_ids'])) {
								if (in_array($tag->term_id, $instance['cruise_tag_ids']))
									$checked = true;
							}
						?>
						<input <?php echo ($checked ? 'checked="checked"' : ''); ?> type="checkbox" id="<?php echo esc_attr ( $this->get_field_name( 'cruise_tag_ids' ) ); ?>_<?php echo esc_attr ($tag->term_id); ?>" name="<?php echo esc_attr ( $this->get_field_name( 'cruise_tag_ids' ) ); ?>[]" value="<?php echo esc_attr ($tag->term_id); ?>">
						<label for="<?php echo esc_attr ( $this->get_field_name( 'cruise_tag_ids' ) ); ?>_<?php echo esc_attr ($tag->term_id); ?>"><?php echo esc_html($tag->name); ?></label>
						<br />
						<?php } ?>
					</div>
				</p>
			</div>

			<div class="w-group">
				<p class="section-title"><?php esc_html_e('Item display settings', 'bookyourtravel') ?></p>
				<p>
					<input type="checkbox"  <?php echo ( $instance['hide_title'] == '1' ? 'checked="checked"' : '' ); ?> class="checkbox" id="<?php echo esc_attr ( $this->get_field_id( 'hide_title' ) ); ?>" name="<?php echo esc_attr ( $this->get_field_name( 'hide_title' ) ); ?>" value="1" />
					<label for="<?php echo esc_attr( $this->get_field_id( 'hide_title') ); ?>"><?php esc_html_e('Hide item titles?', 'bookyourtravel') ?></label>
				</p>

				<p>
					<input type="checkbox"  <?php echo ( $instance['hide_image'] == '1' ? 'checked="checked"' : '' ); ?> class="checkbox" id="<?php echo esc_attr ( $this->get_field_id( 'hide_image' ) ); ?>" name="<?php echo esc_attr ( $this->get_field_name( 'hide_image' ) ); ?>" value="1" />
					<label for="<?php echo esc_attr( $this->get_field_id( 'hide_image') ); ?>"><?php esc_html_e('Hide item images?', 'bookyourtravel') ?></label>
				</p>

				<p>
					<input type="checkbox"  <?php echo ( $instance['hide_description'] == '1' ? 'checked="checked"' : '' ); ?> class="checkbox" id="<?php echo esc_attr ( $this->get_field_id( 'hide_description' ) ); ?>" name="<?php echo esc_attr ( $this->get_field_name( 'hide_description' ) ); ?>" value="1" />
					<label for="<?php echo esc_attr( $this->get_field_id( 'hide_description') ); ?>"><?php esc_html_e('Hide item descriptions?', 'bookyourtravel') ?></label>
				</p>

				<p>
					<input type="checkbox"  <?php echo ( $instance['hide_actions'] == '1' ? 'checked="checked"' : '' ); ?> class="checkbox" id="<?php echo esc_attr ( $this->get_field_id( 'hide_actions' ) ); ?>" name="<?php echo esc_attr ( $this->get_field_name( 'hide_actions' ) ); ?>" value="1" />
					<label for="<?php echo esc_attr( $this->get_field_id( 'hide_actions') ); ?>"><?php esc_html_e('Hide buttons?', 'bookyourtravel') ?></label>
				</p>

				<p>
					<input type="checkbox"  <?php echo ( $instance['hide_rating'] == '1' ? 'checked="checked"' : '' ); ?> class="checkbox" id="<?php echo esc_attr ( $this->get_field_id( 'hide_rating' ) ); ?>" name="<?php echo esc_attr ( $this->get_field_name( 'hide_rating' ) ); ?>" value="1" />
					<label for="<?php echo esc_attr( $this->get_field_id( 'hide_rating') ); ?>"><?php esc_html_e('Hide item ratings?', 'bookyourtravel') ?></label>
				</p>

				<p>
					<input type="checkbox"  <?php echo ( $instance['hide_address'] == '1' ? 'checked="checked"' : '' ); ?> class="checkbox" id="<?php echo esc_attr ( $this->get_field_id( 'hide_address' ) ); ?>" name="<?php echo esc_attr ( $this->get_field_name( 'hide_address' ) ); ?>" value="1" />
					<label for="<?php echo esc_attr( $this->get_field_id( 'hide_address') ); ?>"><?php esc_html_e('Hide address?', 'bookyourtravel') ?></label>
				</p>

				<p>
					<input type="checkbox"  <?php echo ( $instance['hide_price'] == '1' ? 'checked="checked"' : '' ); ?> class="checkbox" id="<?php echo esc_attr ( $this->get_field_id( 'hide_price' ) ); ?>" name="<?php echo esc_attr ( $this->get_field_name( 'hide_price' ) ); ?>" value="1" />
					<label for="<?php echo esc_attr( $this->get_field_id( 'hide_price') ); ?>"><?php esc_html_e('Hide item prices?', 'bookyourtravel') ?></label>
				</p>
			</div>
		</div>
	<?php
	}
}

add_shortcode( 'byt_widget_cruise_list', 'byt_widget_cruise_list' );
function byt_widget_cruise_list( $atts ) {

	if (isset($atts['cruise_type_ids']) && $atts['cruise_type_ids'] == '') {
		unset($atts['cruise_type_ids']);
	}

	if (isset($atts['cruise_duration_ids']) && $atts['cruise_duration_ids'] == '') {
		unset($atts['cruise_duration_ids']);
	}	

	if (isset($atts['cruise_tag_ids']) && $atts['cruise_tag_ids'] == '') {
		unset($atts['cruise_tag_ids']);
	}

	extract( shortcode_atts(
		array(
			'title' => esc_html__('Explore our latest cruises', 'bookyourtravel'),
			'number_of_posts' => '4',
			'sort_by' => 'title',
			'sort_descending' => '1',
			'display_mode' => 'card',
			'posts_per_row' => 4,
			'cruise_tag_ids' => array(),
			'show_featured_only' => '0',
			'cruise_type_ids' => array(),
			'cruise_duration_ids' => array(),
			'show_fields' => 'title,image,actions',
			'css' => ''
		),
		$atts
	));

	if (isset($atts["cruise_tag_ids"]) && !is_array($atts["cruise_tag_ids"]) && !empty($atts["cruise_tag_ids"])) {
		$atts["cruise_tag_ids"] = explode(',', $atts["cruise_tag_ids"]);
	}

	if (isset($atts["cruise_duration_ids"]) && !is_array($atts["cruise_duration_ids"]) && !empty($atts["cruise_duration_ids"])) {
		$atts["cruise_duration_ids"] = explode(',', $atts["cruise_duration_ids"]);
	}
	
	if (isset($atts["cruise_type_ids"]) && !is_array($atts["cruise_type_ids"]) && !empty($atts["cruise_type_ids"])) {
		$atts["cruise_type_ids"] = explode(',', $atts["cruise_type_ids"]);
	}

	$show_fields = explode(',', $show_fields);

	$atts['hide_title'] = !in_array('title', $show_fields);
	$atts['hide_image'] = !in_array('image', $show_fields);
	$atts['hide_actions'] = !in_array('actions', $show_fields);
	$atts['hide_description'] = !in_array('description', $show_fields);
	$atts['hide_rating'] = !in_array('rating', $show_fields);
	$atts['hide_price'] = !in_array('price', $show_fields);
	$atts['hide_address'] = !in_array('address', $show_fields);

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
	the_widget( 'bookyourtravel_cruise_list_widget', $atts, $widget_args );
	$output = ob_get_clean();

	return $output;
}
