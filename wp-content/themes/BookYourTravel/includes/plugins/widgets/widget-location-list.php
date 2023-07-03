<?php
/**
 * The template for displaying Location list widget
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */

// Add function to widgets_init that'll load our widget.
add_action('widgets_init', 'bookyourtravel_location_lists_widgets');

// Register widget.
function bookyourtravel_location_lists_widgets() {
    register_widget('bookyourtravel_location_list_widget');
}

// Widget class.
class bookyourtravel_location_list_widget extends WP_Widget {

    /*-----------------------------------------------------------------------------------*/
    /*    Widget Setup
    /*-----------------------------------------------------------------------------------*/

    function __construct() {

        /* Widget settings. */
        $widget_ops = array('classname' => 'bookyourtravel_location_list_widget', 'description' => esc_html__('BookYourTravel: Location List', 'bookyourtravel'));

        /* Widget control settings. */
        $control_ops = array('width' => 260, 'height' => 400, 'id_base' => 'bookyourtravel_location_list_widget');

        /* Create the widget. */
        parent::__construct('bookyourtravel_location_list_widget', esc_html__('BookYourTravel: Location List', 'bookyourtravel'), $widget_ops, $control_ops);
    }

/*-----------------------------------------------------------------------------------*/
/*    Display Widget
/*-----------------------------------------------------------------------------------*/

    function widget($args, $instance) {

        global $sc_theme_globals, $bookyourtravel_location_helper;

        $card_layout_classes = array(
            'full-width',
            'one-half',
            'one-third',
            'one-fourth',
            'one-fifth',
        );

        extract($args);

        /* Our variables from the widget settings. */
        $title = apply_filters('widget_title', isset($instance['title']) ? $instance['title'] : esc_html__('Top destinations around the world', 'bookyourtravel'));

        global $bookyourtravel_theme_globals, $location_list_args, $location_item_args;

        $location_list_args = array();

        $location_list_args['posts_per_page'] = isset($instance['number_of_posts']) ? (int) $instance['number_of_posts'] : 4;
        $location_list_args['sort_by'] = isset($instance['sort_by']) ? $instance['sort_by'] : 'title';
        $sort_descending = isset($instance['sort_descending']) && $instance['sort_descending'] == '1';
        $location_list_args['sort_order'] = $sort_descending ? 'DESC' : 'ASC';
        $location_list_args['posts_per_row'] = isset($instance['posts_per_row']) ? (int) $instance['posts_per_row'] : 4;
        $location_list_args['show_featured_only'] = isset($instance['show_featured_only']) && $instance['show_featured_only'] == '1';
        $location_list_args['location_type_ids'] = isset($instance['location_type_ids']) ? (array) $instance['location_type_ids'] : array();
        $location_list_args['location_tag_ids'] = isset($instance['location_tag_ids']) ? (array) $instance['location_tag_ids'] : array();
        $location_list_args['display_mode'] = isset($instance['display_mode']) ? $instance['display_mode'] : 'card';
        $location_list_args['exclude_descendant_locations'] = isset($instance['exclude_descendant_locations']) && $instance['exclude_descendant_locations'] == '1';
        $location_list_args['paged'] = 1;

        $display_mode = $location_list_args['display_mode'];

        $location_item_args = array();
        $location_item_args['hide_title'] = isset($instance['hide_title']) && $instance['hide_title'] == '1';
        $location_item_args['hide_image'] = (isset($instance['hide_image']) && $instance['hide_image'] == '1');
        $location_item_args['hide_description'] = (isset($instance['hide_description']) && $instance['hide_description'] == '1');
        $location_item_args['hide_actions'] = (isset($instance['hide_actions']) && $instance['hide_actions'] == '1');
        $location_item_args['hide_counts'] = (isset($instance['hide_counts']) && $instance['hide_counts'] == '1');
        $location_item_args['hide_ribbon'] = (isset($instance['hide_ribbon']) && $instance['hide_ribbon'] == '1');

        echo $before_widget;

        if ($display_mode == 'card') {
            echo '<div class="s-title">' . $before_title . $title . $after_title . '</div>';
        } else {
            echo $before_title . $title . $after_title;
        }

        do_action('bookyourtravel_widget_location_list_before');
        get_template_part('includes/parts/location/location', 'list');
        do_action('bookyourtravel_widget_location_list_after');

        /* After widget (defined by themes). */
        echo $after_widget;

        // set back to default since this is a global variable
        $location_list_args['display_mode'] = 'card';

        wp_reset_postdata();
        wp_reset_query();
    }

/*-----------------------------------------------------------------------------------*/
/*    Update Widget
/*-----------------------------------------------------------------------------------*/

    function update($new_instance, $old_instance) {

        $instance = $old_instance;

        /* Strip tags to remove HTML (important for text inputs). */
        $instance['title'] = isset($new_instance['title']) ? strip_tags($new_instance['title']) : '';
        $instance['number_of_posts'] = isset($new_instance['number_of_posts']) ? strip_tags($new_instance['number_of_posts']) : 12;
        $instance['sort_by'] = isset($new_instance['sort_by']) ? strip_tags($new_instance['sort_by']) : '';
        $instance['sort_descending'] = isset($new_instance['sort_descending']) ? strip_tags($new_instance['sort_descending']) : false;
        $instance['display_mode'] = isset($new_instance['display_mode']) ? strip_tags($new_instance['display_mode']) : '';
        $instance['posts_per_row'] = isset($new_instance['posts_per_row']) ? strip_tags($new_instance['posts_per_row']) : 1;
        $instance['show_featured_only'] = isset($new_instance['show_featured_only']) ? strip_tags($new_instance['show_featured_only']) : false;
        $instance['exclude_descendant_locations'] = isset($new_instance['exclude_descendant_locations']) ? strip_tags($new_instance['exclude_descendant_locations']) : false;
        $instance['location_type_ids'] = isset($new_instance['location_type_ids']) ? $new_instance['location_type_ids'] : [];
        $instance['location_tag_ids'] = isset($new_instance['location_tag_ids']) ? $new_instance['location_tag_ids'] : [];
        $instance['hide_title'] = isset($new_instance['hide_title']) ? $new_instance['hide_title'] : false;
        $instance['hide_image'] = isset($new_instance['hide_image']) ? $new_instance['hide_image'] : false;
        $instance['hide_description'] = isset($new_instance['hide_description']) ? $new_instance['hide_description'] : false;
        $instance['hide_actions'] = isset($new_instance['hide_actions']) ? $new_instance['hide_actions'] : false;
        $instance['hide_counts'] = isset($new_instance['hide_counts']) ? $new_instance['hide_counts'] : false;
        $instance['hide_ribbon'] = isset($new_instance['hide_ribbon']) ? $new_instance['hide_ribbon'] : false;

        return $instance;
    }

/*-----------------------------------------------------------------------------------*/
/*    Widget Settings
/*-----------------------------------------------------------------------------------*/

    function form($instance) {

        $cat_args = array(
            'taxonomy' => 'location_type',
            'hide_empty' => '0',
        );
        $location_types = get_categories($cat_args);

        $cat_args = array(
            'taxonomy' => 'location_tag',
            'hide_empty' => '0',
        );
        $location_tags = get_categories($cat_args);

        /* Set up some default widget settings. */
        $defaults = array(
            'title' => esc_html__('Top destinations around the world', 'bookyourtravel'),
            'number_of_posts' => '4',
            'sort_by' => 'title',
            'sort_descending' => '1',
            'display_mode' => 'card',
            'posts_per_row' => 4,
            'show_featured_only' => '0',
            'exclude_descendant_locations' => '0',
            'location_type_ids' => array(),
            'location_tag_ids' => array(),
            'hide_title' => '0',
            'hide_image' => '0',
            'hide_description' => '0',
            'hide_actions' => '0',
            'hide_counts' => '0',
            'hide_ribbon' => '0',
        );
        $instance = wp_parse_args((array) $instance, $defaults);?>

		<!-- Widget Title: Text Input -->
		<div class="wp-widget-styling">
			<p>
				<label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Title:', 'bookyourtravel');?></label>
				<input type="text" class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" value="<?php echo esc_attr($instance['title']); ?>" />
			</p>

			<div class="w-group">
				<p class="section-title"><?php esc_html_e('Widget layout', 'bookyourtravel');?></p>
				<p>
					<label for="<?php echo esc_attr($this->get_field_id('number_of_posts')); ?>"><?php esc_html_e('How many locations do you want to display?', 'bookyourtravel');?></label>
					<select id="<?php echo esc_attr($this->get_field_id('number_of_posts')); ?>" name="<?php echo esc_attr($this->get_field_name('number_of_posts')); ?>">
						<?php for ($i = 1; $i < 13; $i++) {?>
						<option <?php echo ($i == $instance['number_of_posts'] ? 'selected="selected"' : ''); ?> value="<?php echo esc_attr($i); ?>"><?php echo esc_html($i); ?></option>
						<?php }?>
					</select>
				</p>
				<p class="cards" <?php echo ($instance['display_mode'] != 'card' ? 'style="display:none"' : ''); ?>>
					<label for="<?php echo esc_attr($this->get_field_id('posts_per_row')); ?>"><?php esc_html_e('How many locations do you want to display per row?', 'bookyourtravel');?></label>
					<select id="<?php echo esc_attr($this->get_field_id('posts_per_row')); ?>" name="<?php echo esc_attr($this->get_field_name('posts_per_row')); ?>">
						<?php for ($i = 1; $i < 6; $i++) {?>
						<option <?php echo ($i == $instance['posts_per_row'] ? 'selected="selected"' : ''); ?> value="<?php echo esc_attr($i); ?>"><?php echo esc_html($i); ?></option>
						<?php }?>
					</select>
				</p>
				<p>
					<label for="<?php echo esc_attr($this->get_field_id('display_mode')); ?>"><?php esc_html_e('Display mode?', 'bookyourtravel');?></label>
					<select class="posts_widget_display_mode" id="<?php echo esc_attr($this->get_field_id('display_mode')); ?>" name="<?php echo esc_attr($this->get_field_name('display_mode')); ?>">
						<option <?php echo 'small' == $instance['display_mode'] ? 'selected="selected"' : ''; ?> value="small"><?php esc_html_e('List', 'bookyourtravel');?></option>
						<option <?php echo 'card' == $instance['display_mode'] ? 'selected="selected"' : ''; ?> value="card"><?php esc_html_e('Grid', 'bookyourtravel');?></option>
					</select>
				</p>
			</div>
			<div class="w-group">
				<p class="section-title"><?php esc_html_e('Widget content', 'bookyourtravel');?></p>
				<p>
					<label for="<?php echo esc_attr($this->get_field_id('sort_by')); ?>"><?php esc_html_e('What do you want to sort the locations by?', 'bookyourtravel');?></label>
					<select id="<?php echo esc_attr($this->get_field_id('sort_by')); ?>" name="<?php echo esc_attr($this->get_field_name('sort_by')); ?>">
						<option <?php echo 'title' == $instance['sort_by'] ? 'selected="selected"' : ''; ?> value="title"><?php esc_html_e('Post Title', 'bookyourtravel');?></option>
						<option <?php echo 'ID' == $instance['sort_by'] ? 'selected="selected"' : ''; ?> value="ID"><?php esc_html_e('Post ID', 'bookyourtravel');?></option>
						<option <?php echo 'rand' == $instance['sort_by'] ? 'selected="selected"' : ''; ?> value="rand"><?php esc_html_e('Random', 'bookyourtravel');?></option>
						<option <?php echo 'date' == $instance['sort_by'] ? 'selected="selected"' : ''; ?> value="date"><?php esc_html_e('Publish Date', 'bookyourtravel');?></option>
						<option <?php echo 'comment_count' == $instance['sort_by'] ? 'selected="selected"' : ''; ?> value="comment_count"><?php esc_html_e('Comment Count', 'bookyourtravel');?></option>
                        <option <?php echo 'menu_order' == $instance['sort_by'] ? 'selected="selected"' : ''; ?> value="menu_order"><?php esc_html_e('Order attribute', 'bookyourtravel');?></option>
					</select>
				</p>
				<p>
					<input type="checkbox"  <?php echo ($instance['sort_descending'] == '1' ? 'checked="checked"' : ''); ?> class="checkbox" id="<?php echo esc_attr($this->get_field_id('sort_descending')); ?>" name="<?php echo esc_attr($this->get_field_name('sort_descending')); ?>" value="1" />
					<label for="<?php echo esc_attr($this->get_field_id('sort_descending')); ?>"><?php esc_html_e('Sort locations in descending order?', 'bookyourtravel');?></label>
				</p>
				<p>
					<input type="checkbox"  <?php echo ($instance['show_featured_only'] == '1' ? 'checked="checked"' : ''); ?> class="checkbox" id="<?php echo esc_attr($this->get_field_id('show_featured_only')); ?>" name="<?php echo esc_attr($this->get_field_name('show_featured_only')); ?>" value="1" />
					<label for="<?php echo esc_attr($this->get_field_id('show_featured_only')); ?>"><?php esc_html_e('Show only featured locations?', 'bookyourtravel');?></label>
				</p>
				<p>
					<input type="checkbox"  <?php echo ($instance['exclude_descendant_locations'] == '1' ? 'checked="checked"' : ''); ?> class="checkbox" id="<?php echo esc_attr($this->get_field_id('exclude_descendant_locations')); ?>" name="<?php echo esc_attr($this->get_field_name('exclude_descendant_locations')); ?>" value="1" />
					<label for="<?php echo esc_attr($this->get_field_id('exclude_descendant_locations')); ?>"><?php esc_html_e('Exclude descendant locations?', 'bookyourtravel');?></label>
				</p>
				<p>
					<label><?php esc_html_e('Location types (leave blank to ignore)', 'bookyourtravel');?></label>
					<div>
						<?php for ($j = 0; $j < count($location_types); $j++) {
            $type = $location_types[$j];
            $checked = false;
            if (isset($instance['location_type_ids'])) {
                if (in_array($type->term_id, $instance['location_type_ids'])) {
                    $checked = true;
                }
            }
            ?>
						<input <?php echo ($checked ? 'checked="checked"' : ''); ?> type="checkbox" id="<?php echo esc_attr($this->get_field_name('location_type_ids')); ?>_<?php echo esc_attr($type->term_id); ?>" name="<?php echo esc_attr($this->get_field_name('location_type_ids')); ?>[]" value="<?php echo esc_attr($type->term_id); ?>">
						<label for="<?php echo esc_attr($this->get_field_name('location_type_ids')); ?>_<?php echo esc_attr($type->term_id); ?>"><?php echo esc_html($type->name); ?></label>
						<br />
						<?php }?>
					</div>
				</p>
				<p>
					<label><?php esc_html_e('Location tags (leave blank to ignore)', 'bookyourtravel');?></label>
					<div>
						<?php for ($j = 0; $j < count($location_tags); $j++) {
            $tag = $location_tags[$j];
            $checked = false;
            if (isset($instance['location_tag_ids'])) {
                if (in_array($tag->term_id, $instance['location_tag_ids'])) {
                    $checked = true;
                }

            }
            ?>
						<input <?php echo ($checked ? 'checked="checked"' : ''); ?> type="checkbox" id="<?php echo esc_attr($this->get_field_name('location_tag_ids')); ?>_<?php echo esc_attr($tag->term_id); ?>" name="<?php echo esc_attr($this->get_field_name('location_tag_ids')); ?>[]" value="<?php echo esc_attr($tag->term_id); ?>">
						<label for="<?php echo esc_attr($this->get_field_name('location_tag_ids')); ?>_<?php echo esc_attr($tag->term_id); ?>"><?php echo esc_html($tag->name); ?></label>
						<br />
						<?php }?>
					</div>
				</p>
			</div>
			<div class="w-group">
				<p class="section-title"><?php esc_html_e('Item display settings', 'bookyourtravel');?></p>
				<p>
					<input type="checkbox"  <?php echo ($instance['hide_title'] == '1' ? 'checked="checked"' : ''); ?> class="checkbox" id="<?php echo esc_attr($this->get_field_id('hide_title')); ?>" name="<?php echo esc_attr($this->get_field_name('hide_title')); ?>" value="1" />
					<label for="<?php echo esc_attr($this->get_field_id('hide_title')); ?>"><?php esc_html_e('Hide item titles?', 'bookyourtravel');?></label>
				</p>
				<p>
					<input type="checkbox"  <?php echo ($instance['hide_image'] == '1' ? 'checked="checked"' : ''); ?> class="checkbox" id="<?php echo esc_attr($this->get_field_id('hide_image')); ?>" name="<?php echo esc_attr($this->get_field_name('hide_image')); ?>" value="1" />
					<label for="<?php echo esc_attr($this->get_field_id('hide_image')); ?>"><?php esc_html_e('Hide item images?', 'bookyourtravel');?></label>
				</p>
				<p>
					<input type="checkbox"  <?php echo ($instance['hide_description'] == '1' ? 'checked="checked"' : ''); ?> class="checkbox" id="<?php echo esc_attr($this->get_field_id('hide_description')); ?>" name="<?php echo esc_attr($this->get_field_name('hide_description')); ?>" value="1" />
					<label for="<?php echo esc_attr($this->get_field_id('hide_description')); ?>"><?php esc_html_e('Hide item descriptions?', 'bookyourtravel');?></label>
				</p>
				<p>
					<input type="checkbox"  <?php echo ($instance['hide_actions'] == '1' ? 'checked="checked"' : ''); ?> class="checkbox" id="<?php echo esc_attr($this->get_field_id('hide_actions')); ?>" name="<?php echo esc_attr($this->get_field_name('hide_actions')); ?>" value="1" />
					<label for="<?php echo esc_attr($this->get_field_id('hide_actions')); ?>"><?php esc_html_e('Hide buttons?', 'bookyourtravel');?></label>
				</p>
				<p>
					<input type="checkbox"  <?php echo ($instance['hide_counts'] == '1' ? 'checked="checked"' : ''); ?> class="checkbox" id="<?php echo esc_attr($this->get_field_id('hide_counts')); ?>" name="<?php echo esc_attr($this->get_field_name('hide_counts')); ?>" value="1" />
					<label for="<?php echo esc_attr($this->get_field_id('hide_counts')); ?>"><?php esc_html_e('Hide counts?', 'bookyourtravel');?></label>
				</p>
				<p>
					<input type="checkbox"  <?php echo ($instance['hide_ribbon'] == '1' ? 'checked="checked"' : ''); ?> class="checkbox" id="<?php echo esc_attr($this->get_field_id('hide_ribbon')); ?>" name="<?php echo esc_attr($this->get_field_name('hide_ribbon')); ?>" value="1" />
					<label for="<?php echo esc_attr($this->get_field_id('hide_ribbon')); ?>"><?php esc_html_e('Hide ribbon?', 'bookyourtravel');?></label>
				</p>
			</div>
		</div>
	<?php
}
}

add_shortcode('byt_widget_location_list', 'byt_widget_location_list');
function byt_widget_location_list($atts) {

    if (isset($atts['location_type_ids']) && $atts['location_type_ids'] == '') {
        unset($atts['location_type_ids']);
    }
    if (isset($atts['location_tag_ids']) && $atts['location_tag_ids'] == '') {
        unset($atts['location_tag_ids']);
    }

    extract(shortcode_atts(
        array(
            'title' => esc_html__('Top destinations around the world', 'bookyourtravel'),
            'number_of_posts' => '4',
            'sort_by' => 'title',
            'sort_descending' => '1',
            'display_mode' => 'card',
            'posts_per_row' => 4,
            'location_type_ids' => array(),
            'location_tag_ids' => array(),
            'exclude_descendant_locations' => '0',
            'show_featured_only' => '0',
            'show_fields' => 'title,image,actions',
            'css' => '',
        ),
        $atts
    ));

    if (isset($atts["location_tag_ids"]) && !is_array($atts["location_tag_ids"]) && !empty($atts["location_tag_ids"])) {
      $atts["location_tag_ids"] = explode(',', $atts["location_tag_ids"]);
    }

    if (isset($atts["location_type_ids"]) && !is_array($atts["location_type_ids"]) && !empty($atts["location_type_ids"])) {
      $atts["location_type_ids"] = explode(',', $atts["location_type_ids"]);
    }

    $show_fields = explode(',', $show_fields);

    $atts['hide_title'] = !in_array('title', $show_fields);
    $atts['hide_image'] = !in_array('image', $show_fields);
    $atts['hide_actions'] = !in_array('actions', $show_fields);
    $atts['hide_description'] = !in_array('description', $show_fields);
    $atts['hide_counts'] = !in_array('counts', $show_fields);
    $atts['hide_ribbon'] = !in_array('ribbon', $show_fields);

	$css_class = $css;
	if (function_exists('vc_shortcode_custom_css_class')) {
		$css_class = vc_shortcode_custom_css_class( $css, ' ' );
	}

    $widget_args = array(
        'before_widget' => sprintf('<div class="widget widget-sidebar %s">', $css_class),
        'after_widget' => '</div>',
        'before_title' => '<h2>',
        'after_title' => '</h2>',
    );

    ob_start();
    the_widget('bookyourtravel_location_list_widget', $atts, $widget_args);
    $output = ob_get_clean();

    return $output;
}
