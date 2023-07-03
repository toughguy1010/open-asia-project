<?php
/**
 * The template for displaying Post list widget
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */

// Add function to widgets_init that'll load our widget.
add_action('widgets_init', 'bookyourtravel_post_lists_widgets');

// Register widget.
function bookyourtravel_post_lists_widgets() {
    register_widget('bookyourtravel_post_list_widget');
}

// Widget class.
class bookyourtravel_post_list_widget extends WP_Widget {

    /*-----------------------------------------------------------------------------------*/
    /*    Widget Setup
    /*-----------------------------------------------------------------------------------*/

    function __construct() {

        /* Widget settings. */
        $widget_ops = array('classname' => 'bookyourtravel_post_list_widget', 'description' => esc_html__('BookYourTravel: Post List', 'bookyourtravel'));

        /* Widget control settings. */
        $control_ops = array('width' => 260, 'height' => 400, 'id_base' => 'bookyourtravel_post_list_widget');

        /* Create the widget. */
        parent::__construct('bookyourtravel_post_list_widget', esc_html__('BookYourTravel: Post List', 'bookyourtravel'), $widget_ops, $control_ops);
    }

    /*-----------------------------------------------------------------------------------*/
    /*    Display Widget
    /*-----------------------------------------------------------------------------------*/

    function widget($args, $instance) {

        global $bookyourtravel_theme_globals, $bookyourtravel_post_helper, $display_mode;

        $card_layout_classes = array(
            'full-width',
            'one-half',
            'one-third',
            'one-fourth',
            'one-fifth',
        );

        extract($args);

        /* Our variables from the widget settings. */
        $title = apply_filters('widget_title', isset($instance['title']) ? $instance['title'] : esc_html__('Explore our latest offers', 'bookyourtravel'));
        $number_of_posts = isset($instance['number_of_posts']) && !empty($instance['number_of_posts']) ? (int) $instance['number_of_posts'] : 4;
        $sort_by = isset($instance['sort_by']) && !empty($instance['sort_by']) ? $instance['sort_by'] : 'title';
        $sort_descending = isset($instance['sort_by']) && isset($instance['sort_descending']) && $instance['sort_descending'] == '1';
        $order = $sort_descending ? 'DESC' : 'ASC';
        $posts_per_row = isset($instance['posts_per_row']) && !empty($instance['posts_per_row']) ? (int) $instance['posts_per_row'] : 4;
        $display_mode = isset($instance['display_mode']) && !empty($instance['display_mode']) ? $instance['display_mode'] : 'card';
        $category_ids = isset($instance['category_ids']) ? (array) $instance['category_ids'] : array();

        global $post_item_args;

        $post_item_args = array();
        $post_item_args['hide_title'] = isset($instance['hide_title']) && $instance['hide_title'] == '1';
        $post_item_args['hide_image'] = (isset($instance['hide_image']) && $instance['hide_image'] == '1');
        $post_item_args['hide_description'] = (isset($instance['hide_description']) && $instance['hide_description'] == '1');
        $post_item_args['hide_actions'] = (isset($instance['hide_actions']) && $instance['hide_actions'] == '1');

        /* Display Widget */

        echo $before_widget;

        if ($display_mode == 'card') {?>
			<div class="s-title">
			<?php echo $before_title . $title . $after_title; ?>
			</div> <?php
} else {
            echo $before_title . $title . $after_title;
        }

        $post_results = $bookyourtravel_post_helper->list_posts(0, $number_of_posts, $sort_by, $order, $category_ids);

        if (count($post_results) > 0 && $post_results['total'] > 0) {

            if ($display_mode == 'card') {
                echo '<div class="offers">';
                echo '<div class="row wrap-post-list">';
            } else {
                echo '<ul class="small-list offers">';
            }

            foreach ($post_results['results'] as $post_result) {
                global $post;
                $post = $post_result;
                setup_postdata($post);

                if (isset($post)) {
                    $post_item_args['post_id'] = $post->ID;
                    $post_item_args['post'] = $post;
                    $post_item_args['item_class'] = BookYourTravel_Theme_Utils::get_item_class_by_row_posts($posts_per_row);
                    $post_item_args['display_mode'] = $display_mode;
                    get_template_part('includes/parts/post/post', 'item');
                }

            }

            wp_reset_postdata();

            if ($display_mode == 'card') {
                echo '</div><!--row-->';
                echo '</div><!--offers-->';
            } else {
                echo '</ul>';
            }
        }

        /* After widget (defined by themes). */
        echo $after_widget;

        // set back to default since this is a global variable
        $display_mode = 'card';
    }

/*-----------------------------------------------------------------------------------*/
/*    Update Widget
/*-----------------------------------------------------------------------------------*/

    function update($new_instance, $old_instance) {

        $instance = $old_instance;

        /* Strip tags to remove HTML (important for text inputs). */
        $instance['title'] = isset($new_instance['title']) ? $new_instance['title'] : '';
        $instance['number_of_posts'] = isset($new_instance['number_of_posts']) ? $new_instance['number_of_posts'] : 12;
        $instance['sort_by'] = isset($new_instance['sort_by']) ? $new_instance['sort_by'] : '';
        $instance['sort_descending'] = isset($new_instance['sort_descending']) ? $new_instance['sort_descending'] : false;
        $instance['display_mode'] = isset($new_instance['display_mode']) ? $new_instance['display_mode'] : '';
        $instance['posts_per_row'] = isset($new_instance['posts_per_row']) ? $new_instance['posts_per_row'] : 3;
        $instance['category_ids'] = isset($new_instance['category_ids']) ? $new_instance['category_ids'] : array();
        $instance['hide_title'] = isset($new_instance['hide_title']) ? $new_instance['hide_title'] : false;
        $instance['hide_image'] = isset($new_instance['hide_image']) ? $new_instance['hide_image'] : false;
        $instance['hide_description'] = isset($new_instance['hide_description']) ? $new_instance['hide_description'] : false;
        $instance['hide_actions'] = isset($new_instance['hide_actions']) ? $new_instance['hide_actions'] : false;

        return $instance;
    }

/*-----------------------------------------------------------------------------------*/
/*    Widget Settings
/*-----------------------------------------------------------------------------------*/

    function form($instance) {

        $cat_args = array(
            'taxonomy' => 'category',
            'hide_empty' => '0',
        );
        $categories = get_categories($cat_args);

        /* Set up some default widget settings. */
        $defaults = array(
            'title' => esc_html__('Explore our latest offers', 'bookyourtravel'),
            'number_of_posts' => '4',
            'sort_by' => 'title',
            'sort_descending' => '1',
            'display_mode' => 'card',
            'posts_per_row' => 4,
            'category_ids' => array(),
            'hide_title' => '0',
            'hide_image' => '0',
            'hide_description' => '0',
            'hide_actions' => '0',
        );
        $instance = wp_parse_args((array) $instance, $defaults);
        ?>
		<!-- Widget Title: Text Input -->

		<div class="wp-widget-styling">
			<p>
				<label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Title:', 'bookyourtravel');?></label>
				<input type="text" class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" value="<?php echo esc_attr($instance['title']); ?>" />
			</p>

			<div class="w-group">
				<p class="section-title"><?php esc_html_e('Widget layout', 'bookyourtravel');?></p>
				<p>
					<label for="<?php echo esc_attr($this->get_field_id('number_of_posts')); ?>"><?php esc_html_e('How many posts do you want to display?', 'bookyourtravel');?></label>
					<select id="<?php echo esc_attr($this->get_field_id('number_of_posts')); ?>" name="<?php echo esc_attr($this->get_field_name('number_of_posts')); ?>">
						<?php for ($i = 1; $i < 13; $i++) {?>
						<option <?php echo ($i == $instance['number_of_posts'] ? 'selected="selected"' : ''); ?> value="<?php echo esc_attr($i); ?>"><?php echo esc_html($i); ?></option>
						<?php }?>
					</select>
				</p>

				<p class="cards" <?php echo ($instance['display_mode'] != 'card' ? 'style="display:none"' : ''); ?>>
					<label for="<?php echo esc_attr($this->get_field_id('posts_per_row')); ?>"><?php esc_html_e('How many posts do you want to display per row?', 'bookyourtravel');?></label>
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
					<label><?php esc_html_e('Categories', 'bookyourtravel');?></label>
					<div>
						<?php for ($j = 0; $j < count($categories); $j++) {
            $type = $categories[$j];
            $checked = false;
            if (isset($instance['category_ids'])) {
                if (in_array($type->term_id, $instance['category_ids'])) {
                    $checked = true;
                }

            }
            ?>
						<input <?php echo ($checked ? 'checked="checked"' : ''); ?> type="checkbox" id="<?php echo esc_attr($this->get_field_name('category_ids')); ?>_<?php echo esc_attr($type->term_id); ?>" name="<?php echo esc_attr($this->get_field_name('category_ids')); ?>[]" value="<?php echo esc_attr($type->term_id); ?>">
						<label for="<?php echo esc_attr($this->get_field_name('category_ids')); ?>_<?php echo esc_attr($type->term_id); ?>"><?php echo esc_html($type->name); ?></label>
						<br />
						<?php }?>
					</div>
				</p>

				<p>
					<label for="<?php echo esc_attr($this->get_field_id('sort_by')); ?>"><?php esc_html_e('What do you want to sort the posts by?', 'bookyourtravel');?></label>
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
					<label for="<?php echo esc_attr($this->get_field_id('sort_descending')); ?>"><?php esc_html_e('Sort posts in descending order?', 'bookyourtravel');?></label>
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
			</div>
		</div>

	<?php
}
}

add_shortcode('byt_widget_post_list', 'byt_widget_post_list');
function byt_widget_post_list($atts) {

    if (isset($atts['category_ids']) && $atts['category_ids'] == '') {
        unset($atts['category_ids']);
    }

    extract(shortcode_atts(
        array(
            'title' => esc_html__('Explore our latest offers', 'bookyourtravel'),
            'number_of_posts' => '4',
            'sort_by' => 'title',
            'sort_descending' => '1',
            'display_mode' => 'card',
            'posts_per_row' => 4,
            'category_ids' => array(),
            'css' => '',
        ),
        $atts
    ));

    if (isset($atts["category_ids"]) && !is_array($atts["category_ids"]) && !empty($atts["category_ids"])) {
      $atts["category_ids"] = explode(',', $atts["category_ids"]);
    }

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
    the_widget('bookyourtravel_post_list_widget', $atts, $widget_args);
    $output = ob_get_clean();

    return $output;
}
