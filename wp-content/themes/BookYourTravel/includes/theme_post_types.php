<?php
/**
 * BookYourTravel_Theme_Post_Types class
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class BookYourTravel_Theme_Post_Types extends BookYourTravel_BaseSingleton {

	protected function __construct() {

        // our parent class might
        // contain shared code in its constructor
        parent::__construct();
    }

    public function init() {
        add_action( 'init', array($this, 'initialize_post_types' ), 1 );
    }

	function save_post($post_id, $post, $update) {

		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		if ($post->post_type != 'location' && $post->post_type != 'accommodation' && $post->post_type != 'tour' && $post->post_type != 'cruise' && $post->post_type != 'car_rental') {
			return;
		}

		do_action('bookyourtravel_save_' . $post->post_type, $post_id);
	}

	function after_delete_post($post_id) {

		global $post_type;
		if ($post_type != 'location' && $post_type != 'accommodation' && $post_type != 'tour' && $post_type != 'cruise' && $post_type != 'car_rental') {
			return;
		}

		do_action('bookyourtravel_after_delete_' . $post_type, $post_id);
	}

	function initialize_post_types() {

        do_action('bookyourtravel_initialize_post_types');

        $taxonomy_list = apply_filters( 'bookyourtravel_custom_taxonomy_list', array('category', 'tag'));

        foreach ($taxonomy_list as $index => $taxonomy) {
            add_action($taxonomy . '_add_form_fields', array($this, 'add_taxonomy_field'), 10, 1);
            add_action($taxonomy . '_edit_form_fields',  array($this, 'edit_taxonomy_field'), 10, 2);
        }

        add_action('edit_term', array($this, 'save_taxonomy_image'), 10, 1);
        add_action('create_term', array($this, 'save_taxonomy_image'), 10, 1);

		add_action( 'after_delete_post', array($this, 'after_delete_post'), 10, 1 );
		add_action( 'save_post', array($this, 'save_post'), 10, 3 );
    }

    function save_taxonomy_image($term_id) {
        if (isset($_POST['taxonomy_featured_image'])) {
            update_option('bookyourtravel_taxonomy_image'.$term_id, $_POST['taxonomy_featured_image'], NULL);
        }
    }

    function add_taxonomy_field($taxonomy) {
        ?>
        <script>
            window.currentTaxonomy = <?php echo json_encode($taxonomy); ?>;
        </script>
		<tr class="form-field">
			<th scope="row" valign="top"><label for="taxonomy_featured_image]"><?php esc_html_e( 'Featured image?', 'bookyourtravel' ); ?></label></th>
            <td>
                <input type="text" name="taxonomy_featured_image" id="taxonomy_featured_image" value="" />
                <p class="description"><?php _e("Select an image to be shown as the featured image of this taxonomy's archive", "bookyourtravel"); ?></p>
                <button class="upload_image_button button"><?php echo __('Upload image', 'bookyourtravel'); ?></button>
            </td>
        </tr>
        <?php
        echo $this->render_taxonomy_featured_image_script();
    }

    function edit_taxonomy_field($term, $taxonomy) {
        $image_url = $this->get_taxonomy_image_url( $term->term_id, NULL, TRUE );

        ?>
        <script>
            window.currentTaxonomy = <?php echo json_encode($taxonomy); ?>;
        </script>
		<tr class="form-field">
			<th scope="row" valign="top"><label for="taxonomy_featured_image]"><?php esc_html_e( 'Featured image?', 'bookyourtravel' ); ?></label></th>
            <td>
                <input type="text" name="taxonomy_featured_image" id="taxonomy_featured_image" value="<?php echo esc_attr($image_url); ?>" />
                <p class="description"><?php _e("Select an image to be shown as the featured image of this taxonomy's archive", "bookyourtravel"); ?></p>
                <button class="upload_image_button button"><?php echo __('Upload image', 'bookyourtravel'); ?></button>
            </td>
        </tr>
        <?php
        echo $this->render_taxonomy_featured_image_script();
    }

    function get_taxonomy_image_id($term_id = NULL, $size = 'full') {
        if (!$term_id) {
            if (is_category())
                $term_id = get_query_var('cat');
            elseif (is_tag())
                $term_id = get_query_var('tag_id');
            elseif (is_tax()) {
                $current_term = get_term_by('slug', get_query_var('term'), get_query_var('taxonomy'));
                $term_id = $current_term->term_id;
            }
        }

        $attachment_id = 0;
        $taxonomy_image_url = get_option('bookyourtravel_taxonomy_image'.$term_id);
        if(!empty($taxonomy_image_url)) {
            $attachment_id = BookYourTravel_Theme_Utils::get_image_id_from_url($taxonomy_image_url);
        }

        return $attachment_id;
    }

    function get_taxonomy_image_url($term_id = NULL, $size = 'full') {
        if (!$term_id) {
            if (is_category())
                $term_id = get_query_var('cat');
            elseif (is_tag())
                $term_id = get_query_var('tag_id');
            elseif (is_tax()) {
                $current_term = get_term_by('slug', get_query_var('term'), get_query_var('taxonomy'));
                $term_id = $current_term->term_id;
            }
        }

        $taxonomy_image_url = get_option('bookyourtravel_taxonomy_image'.$term_id);
        if(!empty($taxonomy_image_url)) {
            $attachment_id = BookYourTravel_Theme_Utils::get_image_id_from_url($taxonomy_image_url);
            if(!empty($attachment_id)) {
                $taxonomy_image_url = wp_get_attachment_image_src($attachment_id, $size);
                $taxonomy_image_url = is_array($taxonomy_image_url) && count($taxonomy_image_url) > 0 ? $taxonomy_image_url[0] : '';
            }
        }

        return $taxonomy_image_url;
    }

    // upload using wordpress upload
    function render_taxonomy_featured_image_script() {
        return '<script type="text/javascript">
            jQuery(document).ready(function($) {
                if (window.currentTaxonomy) {
                    var upload_button;
                    $(".upload_image_button").on("click", function(event) {
                        upload_button = $(this);
                        var frame;
                        event.preventDefault();

                        if (frame) {
                            frame.open();
                            return;
                        }
                        frame = wp.media();
                        frame.on( "select", function() {
                            // Grab the selected attachment.
                            var attachment = frame.state().get("selection").first();
                            frame.close();
                            $("#taxonomy_featured_image").val(attachment.attributes.url);
                        });
                        frame.open();
                    });

                    $(".remove_image_button").on("click", function() {
                        $(".taxonomy-image").attr("src", "");
                        $("#taxonomy_featured_image").val("");
                        return false;
                    });
                }
            });
        </script>';
    }
}

// store the instance in a variable to be retrieved later and call init
$bookyourtravel_theme_post_types = BookYourTravel_Theme_Post_Types::get_instance();