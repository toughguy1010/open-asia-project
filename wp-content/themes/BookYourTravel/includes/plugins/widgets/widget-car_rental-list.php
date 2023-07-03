<?php
/**
 * The template for displaying Car Rental list widget
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */

// Add function to widgets_init that'll load our widget.
add_action('widgets_init', 'bookyourtravel_car_rental_lists_widgets');

// Register widget.
function bookyourtravel_car_rental_lists_widgets() {
    global $bookyourtravel_theme_globals;
    if ($bookyourtravel_theme_globals->enable_car_rentals()) {
        register_widget('bookyourtravel_car_rental_list_widget');
    }
}

// Widget class.
class bookyourtravel_car_rental_list_widget extends WP_Widget {

    /*-----------------------------------------------------------------------------------*/
    /*    Widget Setup
    /*-----------------------------------------------------------------------------------*/

    function __construct() {

        /* Widget settings. */
        $widget_ops = array('classname' => 'bookyourtravel_car_rental_list_widget', 'description' => esc_html__('BookYourTravel: Car Rental List', 'bookyourtravel'));

        /* Widget control settings. */
        $control_ops = array('width' => 260, 'height' => 400, 'id_base' => 'bookyourtravel_car_rental_list_widget');

        /* Create the widget. */
        parent::__construct('bookyourtravel_car_rental_list_widget', esc_html__('BookYourTravel: Car Rental List', 'bookyourtravel'), $widget_ops, $control_ops);
    }

    /*-----------------------------------------------------------------------------------*/
    /*    Display Widget
    /*-----------------------------------------------------------------------------------*/

    function widget($args, $instance) {

        global $bookyourtravel_theme_globals, $bookyourtravel_car_rental_helper;

        extract($args);

        /* Our variables from the widget settings. */
        $title = apply_filters('widget_title', isset($instance['title']) ? $instance['title'] : esc_html__('Explore our latest car rentals', 'bookyourtravel'));

        global $bookyourtravel_theme_globals, $car_rental_list_args, $car_rental_item_args;

        $car_rental_list_args = array();

        $car_rental_list_args['posts_per_page'] = isset($instance['number_of_posts']) ? (int) $instance['number_of_posts'] : 4;
        $car_rental_list_args['sort_by'] = isset($instance['sort_by']) ? $instance['sort_by'] : 'title';
        $sort_descending = isset($instance['sort_by']) && $instance['sort_descending'] == '1';
        $car_rental_list_args['sort_order'] = $sort_descending ? 'DESC' : 'ASC';
        $car_rental_list_args['posts_per_row'] = isset($instance['posts_per_row']) ? (int) $instance['posts_per_row'] : 4;
        $car_rental_list_args['show_featured_only'] = isset($instance['show_featured_only']) && $instance['show_featured_only'] == '1';
        $car_rental_list_args['car_rental_type_ids'] = isset($instance['car_rental_type_ids']) ? (array) $instance['car_rental_type_ids'] : array();
        $car_rental_list_args['car_rental_tag_ids'] = isset($instance['car_rental_tag_ids']) ? (array) $instance['car_rental_tag_ids'] : array();
        $car_rental_list_args['display_mode'] = isset($instance['display_mode']) ? $instance['display_mode'] : 'card';
        $car_rental_list_args['paged'] = 1;

        $display_mode = $car_rental_list_args['display_mode'];

        $car_rental_item_args = array();
        $car_rental_item_args['hide_title'] = isset($instance['hide_title']) && $instance['hide_title'] == '1';
        $car_rental_item_args['hide_image'] = isset($instance['hide_image']) && $instance['hide_image'] == '1';
        $car_rental_item_args['hide_description'] = isset($instance['hide_description']) && $instance['hide_description'] == '1';
        $car_rental_item_args['hide_actions'] = isset($instance['hide_actions']) && $instance['hide_actions'] == '1';
        $car_rental_item_args['hide_rating'] = isset($instance['hide_rating']) && $instance['hide_rating'] == '1';
        $car_rental_item_args['hide_price'] = isset($instance['hide_price']) && $instance['hide_price'] == '1';
        $car_rental_item_args['hide_address'] = (isset($instance['hide_address']) && $instance['hide_address'] == '1');

        echo $before_widget;

        if ($display_mode == 'card') {
            echo '<div class="s-title">' . $before_title . $title . $after_title . '</div>';
        } else {
            echo $before_title . $title . $after_title;
        }

        do_action('bookyourtravel_widget_car_rental_list_before');
        get_template_part('includes/parts/car_rental/car_rental', 'list');
        do_action('bookyourtravel_widget_car_rental_list_after');

        /* After widget (defined by themes). */
        echo $after_widget;

        // set back to default since this is a global variable
        $car_rental_list_args['display_mode'] = 'card';

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
        $instance['number_of_posts'] = isset($new_instance['number_of_posts']) ? strip_tags($new_instance['number_of_posts']) : 9;
        $instance['sort_by'] = isset($new_instance['sort_by']) ? strip_tags($new_instance['sort_by']) : '';
        $instance['sort_descending'] = isset($new_instance['sort_descending']) ? strip_tags($new_instance['sort_descending']) : false;
        $instance['display_mode'] = isset($new_instance['display_mode']) ? strip_tags($new_instance['display_mode']) : '';
        $instance['posts_per_row'] = isset($new_instance['posts_per_row']) ? strip_tags($new_instance['posts_per_row']) : 3;
        $instance['show_featured_only'] = isset($new_instance['show_featured_only']) ? strip_tags($new_instance['show_featured_only']) : false;
        $instance['car_rental_type_ids'] = isset($new_instance['car_rental_type_ids']) ? $new_instance['car_rental_type_ids'] : array();
        $instance['car_rental_tag_ids'] = isset($new_instance['car_rental_tag_ids']) ? $new_instance['car_rental_tag_ids'] : array();

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
    /*    Widget Settings
    /*-----------------------------------------------------------------------------------*/

    function form($instance) {

        $cat_args = array(
            'taxonomy' => 'car_type',
            'hide_empty' => '0',
        );
        $car_rental_types = get_categories($cat_args);

        $cat_args = array(
            'taxonomy' => 'car_rental_tag',
            'hide_empty' => '0',
        );
        $car_rental_tags = get_categories($cat_args);

        /* Set up some default widget settings. */
        $defaults = array(
            'title' => esc_html__('Explore our latest car rentals', 'bookyourtravel'),
            'number_of_posts' => '4',
            'sort_by' => 'title',
            'sort_descending' => '1',
            'display_mode' => 'card',
            'posts_per_row' => 4,
            'show_featured_only' => '0',
            'car_rental_type_ids' => array(),
            'car_rental_tag_ids' => array(),
            'hide_title' => '0',
            'hide_image' => '0',
            'hide_description' => '0',
            'hide_actions' => '0',
            'hide_rating' => '0',
            'hide_price' => '0',
            'hide_address' => '0',
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
					<label for="<?php echo esc_attr($this->get_field_id('number_of_posts')); ?>"><?php esc_html_e('How many car rentals do you want to display?', 'bookyourtravel');?></label>
					<select id="<?php echo esc_attr($this->get_field_id('number_of_posts')); ?>" name="<?php echo esc_attr($this->get_field_name('number_of_posts')); ?>">
						<?php for ($i = 1; $i < 13; $i++) {?>
						<option <?php echo ($i == $instance['number_of_posts'] ? 'selected="selected"' : ''); ?> value="<?php echo esc_attr($i); ?>"><?php echo esc_html($i); ?></option>
						<?php }?>
					</select>
				</p>

				<p class="cards" <?php echo ($instance['display_mode'] != 'card' ? 'style="display:none"' : ''); ?>>
					<label for="<?php echo esc_attr($this->get_field_id('posts_per_row')); ?>"><?php esc_html_e('How many car rentals do you want to display per row?', 'bookyourtravel');?></label>
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
					<label for="<?php echo esc_attr($this->get_field_id('sort_by')); ?>"><?php esc_html_e('What do you want to sort the car rentals by?', 'bookyourtravel');?></label>
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
					<label for="<?php echo esc_attr($this->get_field_id('sort_descending')); ?>"><?php esc_html_e('Sort car rentals in descending order?', 'bookyourtravel');?></label>
					<input type="checkbox"  <?php echo ($instance['sort_descending'] == '1' ? 'checked="checked"' : ''); ?> class="checkbox" id="<?php echo esc_attr($this->get_field_id('sort_descending')); ?>" name="<?php echo esc_attr($this->get_field_name('sort_descending')); ?>" value="1" />
				</p>

				<p>
					<input type="checkbox"  <?php echo ($instance['show_featured_only'] == '1' ? 'checked="checked"' : ''); ?> class="checkbox" id="<?php echo esc_attr($this->get_field_id('show_featured_only')); ?>" name="<?php echo esc_attr($this->get_field_name('show_featured_only')); ?>" value="1" />
					<label for="<?php echo esc_attr($this->get_field_id('show_featured_only')); ?>"><?php esc_html_e('Show only featured car rentals?', 'bookyourtravel');?></label>
				</p>

				<p>
					<label><?php esc_html_e('Car rental type (leave blank to ignore)', 'bookyourtravel');?></label>
					<div>
						<?php for ($j = 0; $j < count($car_rental_types); $j++) {
            $type = $car_rental_types[$j];
            $checked = false;
            if (isset($instance['car_rental_type_ids'])) {
                if (in_array($type->term_id, $instance['car_rental_type_ids'])) {
                    $checked = true;
                }
            }
            ?>
						<input <?php echo ($checked ? 'checked="checked"' : ''); ?> type="checkbox" id="<?php echo esc_attr($this->get_field_name('car_rental_type_ids')); ?>_<?php echo esc_attr($type->term_id); ?>" name="<?php echo esc_attr($this->get_field_name('car_rental_type_ids')); ?>[]" value="<?php echo esc_attr($type->term_id); ?>">
						<label for="<?php echo esc_attr($this->get_field_name('car_rental_type_ids')); ?>_<?php echo esc_attr($type->term_id); ?>"><?php echo esc_html($type->name); ?></label>
						<br />
						<?php }?>
					</div>
				</p>

				<p>
					<label><?php esc_html_e('Car rental tag (leave blank to ignore)', 'bookyourtravel');?></label>
					<div>
						<?php for ($j = 0; $j < count($car_rental_tags); $j++) {
            $tag = $car_rental_tags[$j];
            $checked = false;
            if (isset($instance['car_rental_tag_ids'])) {
                if (in_array($tag->term_id, $instance['car_rental_tag_ids'])) {
                    $checked = true;
                }

            }
            ?>
						<input <?php echo ($checked ? 'checked="checked"' : ''); ?> type="checkbox" id="<?php echo esc_attr($this->get_field_name('car_rental_tag_ids')); ?>_<?php echo esc_attr($tag->term_id); ?>" name="<?php echo esc_attr($this->get_field_name('car_rental_tag_ids')); ?>[]" value="<?php echo esc_attr($tag->term_id); ?>">
						<label for="<?php echo esc_attr($this->get_field_name('car_rental_tag_ids')); ?>_<?php echo esc_attr($tag->term_id); ?>"><?php echo esc_html($tag->name); ?></label>
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
					<input type="checkbox"  <?php echo ($instance['hide_rating'] == '1' ? 'checked="checked"' : ''); ?> class="checkbox" id="<?php echo esc_attr($this->get_field_id('hide_rating')); ?>" name="<?php echo esc_attr($this->get_field_name('hide_rating')); ?>" value="1" />
					<label for="<?php echo esc_attr($this->get_field_id('hide_rating')); ?>"><?php esc_html_e('Hide item ratings?', 'bookyourtravel');?></label>
				</p>

				<p>
					<input type="checkbox"  <?php echo ($instance['hide_address'] == '1' ? 'checked="checked"' : ''); ?> class="checkbox" id="<?php echo esc_attr($this->get_field_id('hide_address')); ?>" name="<?php echo esc_attr($this->get_field_name('hide_address')); ?>" value="1" />
					<label for="<?php echo esc_attr($this->get_field_id('hide_address')); ?>"><?php esc_html_e('Hide address?', 'bookyourtravel');?></label>
				</p>

				<p>
					<input type="checkbox"  <?php echo ($instance['hide_price'] == '1' ? 'checked="checked"' : ''); ?> class="checkbox" id="<?php echo esc_attr($this->get_field_id('hide_price')); ?>" name="<?php echo esc_attr($this->get_field_name('hide_price')); ?>" value="1" />
					<label for="<?php echo esc_attr($this->get_field_id('hide_price')); ?>"><?php esc_html_e('Hide item prices?', 'bookyourtravel');?></label>
				</p>
			</div>
		</div>
	<?php
}
}

add_shortcode('byt_widget_car_rental_list', 'byt_widget_car_rental_list');
function byt_widget_car_rental_list($atts) {

    if (isset($atts['car_rental_tag_ids']) && $atts['car_rental_tag_ids'] == '') {
        unset($atts['car_rental_tag_ids']);
    }

    if (isset($atts['car_rental_type_ids']) && $atts['car_rental_type_ids'] == '') {
        unset($atts['car_rental_type_ids']);
    }

    extract(shortcode_atts(
        array(
            'title' => esc_html__('Explore our latest car rentals', 'bookyourtravel'),
            'number_of_posts' => '4',
            'sort_by' => 'title',
            'sort_descending' => '1',
            'display_mode' => 'card',
            'posts_per_row' => 4,
            'car_rental_tag_ids' => array(),
            'show_featured_only' => '0',
            'car_rental_type_ids' => array(),
            'show_fields' => 'title,image,actions',
            'css' => '',
        ),
        $atts
    ));

    if (isset($atts["car_rental_tag_ids"]) && !is_array($atts["car_rental_tag_ids"]) && !empty($atts["car_rental_tag_ids"])) {
        $atts["car_rental_tag_ids"] = explode(',', $atts["car_rental_tag_ids"]);
    }

    if (isset($atts["car_rental_type_ids"]) && !is_array($atts["car_rental_type_ids"]) && !empty($atts["car_rental_type_ids"])) {
        $atts["car_rental_type_ids"] = explode(',', $atts["car_rental_type_ids"]);
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
        'before_widget' => sprintf('<div class="widget widget-sidebar %s">', $css_class),
        'after_widget' => '</div>',
        'before_title' => '<h2>',
        'after_title' => '</h2>',
    );

    ob_start();
    the_widget('bookyourtravel_car_rental_list_widget', $atts, $widget_args);
    $output = ob_get_clean();

    return $output;
}
