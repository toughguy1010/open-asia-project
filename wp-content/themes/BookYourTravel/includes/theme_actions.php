<?php
/**
 * BookYourTravel_Theme_Actions class
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class BookYourTravel_Theme_Actions extends BookYourTravel_BaseSingleton {

    protected function __construct() {

        // our parent class might contain shared code in its constructor
        parent::__construct();
    }

    public function init() {

        global $bookyourtravel_theme_globals;

        add_action('after_setup_theme', array($this, 'theme_setup'));
        add_action('wp_head', array($this, 'pingback_header'));
        add_action('wp_head', array($this, 'wp_head'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts_styles'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts_styles'), 1);
        add_action('customize_register', array($this, 'customize_register'), 10, 1);
        add_action('customize_preview_init', array($this, 'customize_preview_init'));
        add_action('customize_controls_enqueue_scripts', array($this, 'customize_controls_enqueue_scripts'));

        $login_page_url = $bookyourtravel_theme_globals->get_login_page_url();
	    $partner_login_page_url = $bookyourtravel_theme_globals->get_partner_login_page_url();
		
        add_action('tgmpa_register', array($this, 'register_required_plugins'));
        add_action('admin_notices', array($this, 'admin_notices'));
        add_action('pre_get_posts', array($this, 'custom_archive_posts_per_page'), 1);
        add_action('pre_get_posts', array($this, 'exclude_products_from_search'), 1);

        add_action('enqueue_block_editor_assets', array($this, 'gutenberg_styles'));

        add_action('admin_init', array($this, 'add_editor_styles'));

        add_action('pre_get_posts', array($this, 'single_page_published_and_draft_posts'));

        add_action ('wp_head', array($this, 'bookyourtravel_head'));

        add_action('login_form_login', array($this, 'override_wp_login'));
        add_action('login_form_register', array($this, 'override_wp_login'));        

        do_action('bookyourtravel_init');
    }

	function bookyourtravel_head() {

		global $default_currency_symbol, $price_decimal_places, $show_currency_symbol_after, $date_picker_date_format, $site_url, $cart_page_url, $current_user, $bookyourtravel_theme_globals, $body_class, $post, $bookyourtravel_theme_post_types;

		$show_currency_symbol_after = $bookyourtravel_theme_globals->show_currency_symbol_after();
		$price_decimal_places = $bookyourtravel_theme_globals->get_price_decimal_places();
		$default_currency_symbol = $bookyourtravel_theme_globals->get_default_currency_symbol();
		$date_picker_date_format = BookYourTravel_Theme_Utils::dateformat_PHP_to_jQueryUI( get_option( 'date_format' ) );
		$cart_page_url = $bookyourtravel_theme_globals->get_cart_page_url();
		$site_url = $bookyourtravel_theme_globals->get_site_url();
		$use_woocommerce_for_checkout = $bookyourtravel_theme_globals->use_woocommerce_for_checkout();
		$website_layout = $bookyourtravel_theme_globals->get_website_layout();

		if ($website_layout) {
			$body_class .= ' ' . $website_layout;
        }

        if (is_category()) {
            $term_id = get_query_var('cat');
            $taxonomy_featured_image_id = $bookyourtravel_theme_post_types->get_taxonomy_image_id($term_id);
            if ($taxonomy_featured_image_id > 0) {
                $body_class .= ' has-featured-image';
            }
        } elseif (is_tag()) {
            $term_id = get_query_var('tag_id');
            $taxonomy_featured_image_id = $bookyourtravel_theme_post_types->get_taxonomy_image_id($term_id);
            if ($taxonomy_featured_image_id > 0) {
                $body_class .= ' has-featured-image';
            }
        } elseif (is_tax()) {
            $current_term = get_term_by('slug', get_query_var('term'), get_query_var('taxonomy'));
            $term_id = $current_term->term_id;
            $taxonomy_featured_image_id = $bookyourtravel_theme_post_types->get_taxonomy_image_id($term_id);
            if ($taxonomy_featured_image_id > 0) {
                $body_class .= ' has-featured-image';
			}
		} elseif (is_home()) {
			$img = wp_get_attachment_image_src(get_post_thumbnail_id(get_option('page_for_posts')),'full');
			$featured_image = is_array($img) && count($img) > 0 ? $img[0] : '';
			if (!empty($featured_image)) {
				$body_class .= ' has-featured-image';
			}
		} elseif (is_search()) {

        } else if ($post) {
			if ($post->post_type == 'accommodation' ||
				$post->post_type == 'tour' ||
				$post->post_type == 'cruise' ||
                $post->post_type == 'car_rental' ||
				$post->post_type == 'location') {

				$displayed_featured_element = get_post_meta($post->ID, $post->post_type . '_displayed_featured_element', true);
				if ($displayed_featured_element == 'image' && has_post_thumbnail($post->ID)) {
					$body_class .= ' has-featured-image';
				} else if ($displayed_featured_element == 'gallery') {
					$images = get_post_meta($post->ID, $post->post_type . '_images', true);

					$has_gallery = false;
					if ($images && count($images) > 0) {
						for ( $i = 0; $i < count($images); $i++ ) {
                            $image_meta_id = $images[$i]['image'];
                            $image_array = wp_get_attachment_image_src($image_meta_id, 'full');
							$image_src = $image_array && is_array($image_array) && count($image_array) > 0 ? $image_array[0] : '';
							if (!empty($image_src)) {
								$has_gallery = true;
								break;
							}
						}
					}

					if ($has_gallery) {
						$body_class .= ' has-featured-gallery';
					}
				}
			} else if ($post->post_type == 'post' || $post->post_type == 'page') {
                if (has_post_thumbnail($post->ID)) {
                    $body_class .= ' has-featured-image';
                }
            }
		}

		$header_sticky = $bookyourtravel_theme_globals->get_header_sticky();
		if ($header_sticky) {
			$body_class .= ' sticky-header';
		}

		$header_overlay = $bookyourtravel_theme_globals->get_header_overlay();
		if ($header_overlay) {
			$body_class .= ' overlay-header';
		}
		
		$header_minicart = $bookyourtravel_theme_globals->get_header_minicart();
		if ($header_minicart) {
			$body_class .= ' show-minicart';
		}

		if (is_front_page()) {
			if (class_exists ('RevSlider') && function_exists('putRevSlider')) {
				if (!$bookyourtravel_theme_globals->frontpage_show_slider() || $bookyourtravel_theme_globals->get_homepage_slider() == -1) {
					$body_class .= ' noslider';
				}
			} else {
				$body_class .= ' noslider';
			}

			$home_header_transparent = $bookyourtravel_theme_globals->get_home_header_transparent();
			if ($home_header_transparent) {
				$body_class .= ' transparent-header';
			}
		}

		if (!isset($current_user)) {
			$current_user = wp_get_current_user();
        }

        $current_url = BookYourTravel_Theme_Utils::get_current_page_url();
	?>
<script>
    window.currentUrl = <?php echo json_encode($current_url); ?>;
	window.themePath = <?php echo json_encode( get_template_directory_uri() ); ?>;
<?php if ($current_user->ID > 0) {	?>
	window.currentUserId = <?php echo json_encode( $current_user->ID ); ?>;
	<?php } else { ?>
	window.currentUserId = 0;
	<?php } ?>
	window.datepickerDateFormat = <?php echo json_encode( $date_picker_date_format ); ?>;
	window.datepickerAltFormat = <?php echo json_encode( BOOKYOURTRAVEL_ALT_DATE_FORMAT ) ?>;
	window.siteUrl = <?php echo json_encode( $site_url ); ?>;
	window.wooCartPageUri = <?php echo json_encode( $cart_page_url ); ?>;
	window.useWoocommerceForCheckout = <?php echo json_encode($use_woocommerce_for_checkout); ?>;
	window.enableRtl = <?php echo json_encode($bookyourtravel_theme_globals->enable_rtl() || (defined('BYT_DEMO') && isset($_REQUEST['rtl']))); ?>;
	window.currencySymbol = <?php echo json_encode($default_currency_symbol) ?>;
	window.currencySymbolShowAfter = <?php echo json_encode($show_currency_symbol_after) ?>;
    window.priceDecimalPlaces = <?php echo json_encode($price_decimal_places); ?>;
    window.currentLocale = <?php echo json_encode(get_locale()); ?>;
	window.gdprError = '<?php esc_html_e('Agreeing with gdpr terms and conditions is required!', 'bookyourtravel'); ?>';
</script>

	<?php
		$recaptcha_key = $bookyourtravel_theme_globals->get_google_recaptcha_key();
		if (!empty($recaptcha_key)) { ?>
<script src='https://www.google.com/recaptcha/api.js'></script>
		<?php
		}
	}    

    function single_page_published_and_draft_posts( $query ) {
        if( is_single() ) {
            global $post;

            global $current_user;
            $user_id = 0;
            if (!isset($current_user)) {
                $current_user = wp_get_current_user();
            }
            $user_id = $current_user ? $current_user->ID : 0;

            if ($user_id > 0) {
                if (isset($query->query["p"])) {
                    $p = get_post($query->query["p"]);
                    if (isset($p)) {
                        if (is_super_admin()) {
                            $query->set('post_status', array('publish', 'draft', 'inherit', 'future', 'auto-draft', 'pending', 'private'));
                        } else if ($p->post_author == $user_id && in_array($p->post_type, array('accommodation', 'cruise', 'car_rental', 'location', 'tour'))) {
                            $query->set('post_status', array('publish', 'draft', 'future', 'inherit', 'auto-draft', 'pending', 'private'));
                        }
                    }
                }
            }
        }
    }

    function add_editor_styles() {
        add_editor_style();
    }

    function exclude_products_from_search($query) {
        if (!$query->is_admin && $query->is_search && $query->is_main_query()) {

            global $bookyourtravel_theme_woocommerce;
            $product_ids = array();

            $accommodation_product_id = $bookyourtravel_theme_woocommerce->get_product_id('accommodation');
            if ($accommodation_product_id > 0) {
                $product_ids[] = $accommodation_product_id;
            }
            $tour_product_id = $bookyourtravel_theme_woocommerce->get_product_id('tour');
            if ($tour_product_id > 0) {
                $product_ids[] = $tour_product_id;
            }
            $cruise_product_id = $bookyourtravel_theme_woocommerce->get_product_id('cruise');
            if ($cruise_product_id > 0) {
                $product_ids[] = $cruise_product_id;
            }
            $car_rental_product_id = $bookyourtravel_theme_woocommerce->get_product_id('car_rental');
            if ($car_rental_product_id > 0) {
                $product_ids[] = $car_rental_product_id;
            }

            if (count($product_ids) > 0) {
                $query->set('post__not_in', $product_ids);
            }
        }
    }

    function custom_archive_posts_per_page($query) {

        if (!is_tax() || !$query->is_main_query() || is_admin()) {
            return;
        }

        global $bookyourtravel_theme_globals;
        $sort_by_field = $bookyourtravel_theme_globals->get_taxonomy_pages_sort_by_field();
        $sort_descending = $bookyourtravel_theme_globals->taxonomy_pages_sort_descending();
        $posts_per_page = $bookyourtravel_theme_globals->get_taxonomy_pages_items_per_page();

        $query->set('orderby', $sort_by_field);
        $query->set('order', $sort_descending ? 'DESC' : 'ASC');
        $query->set('posts_per_page', $posts_per_page);
    }

    function admin_notices() {
        remove_action('after_plugin_row_revslider/revslider.php', array('RevSliderAdmin', 'show_purchase_notice'));
    }

    /**
     * Register the required plugins for this theme.
     *
     *
     * The variable passed to tgmpa_register_plugins() should be an array of plugin
     * arrays.
     *
     * This function is hooked into tgmpa_init, which is fired within the
     * TGM_Plugin_Activation class constructor.
     */
    function register_required_plugins() {

        /**
         * Array of plugin arrays. Required keys are name and slug.
         * If the source is NOT from the .org repo, then source is also required.
         */
        $plugins = array(
            array(
                'name' => 'WooCommerce',
                'slug' => 'woocommerce',
                'required' => false,
            ),
            array(
                'name' => 'Contact Form 7',
                'slug' => 'contact-form-7',
                'required' => false,
            ),
            array(
                'name' => 'One Click Demo Import',
                'slug' => 'one-click-demo-import',
                'required' => false,
            ),
            array(
                'name' => 'Max Mega Menu',
                'slug' => 'megamenu',
                'required' => false,
            ),
            array(
                'name' => 'Envato market', // The plugin name.
                'slug' => 'envato-market', // The plugin slug (typically the folder name).
                'source' => BookYourTravel_Theme_Utils::get_file_path('/includes/plugins/envato-market/envato-market.zip'), // The plugin source.
                'required' => false, // If false, the plugin is only 'recommended' instead of required.
                'version' => '2.0.1', // E.g. 1.0.0. If set, the active plugin must be this version or higher.
                'force_activation' => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
                'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
                'external_url' => '', // If set, overrides default API URL and points to an external URL.
            ),
            array(
                'name' => 'Revolution Slider', // The plugin name.
                'slug' => 'revslider', // The plugin slug (typically the folder name).
                'source' => BookYourTravel_Theme_Utils::get_file_path('/includes/plugins/revslider/revslider.zip'), // The plugin source.
                'required' => false, // If false, the plugin is only 'recommended' instead of required.
                'version' => '6.6.13', // E.g. 1.0.0. If set, the active plugin must be this version or higher.
                'force_activation' => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
                'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
                'external_url' => '', // If set, overrides default API URL and points to an external URL.
            ),
            array(
                'name' => 'WPBakery Page Builder', // The plugin name.
                'slug' => 'js_composer', // The plugin slug (typically the folder name).
                'source' => BookYourTravel_Theme_Utils::get_file_path('/includes/plugins/visual-composer/js_composer.zip'), // The plugin source.
                'required' => false, // If false, the plugin is only 'recommended' instead of required.
                'version' => '6.11.0', // E.g. 1.0.0. If set, the active plugin must be this version or higher.
                'force_activation' => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
                'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
                'external_url' => '', // If set, overrides default API URL and points to an external URL.
            ),

        );

        /**
         * Array of configuration settings. Amend each line as needed.
         * If you want the default strings to be available under your own theme domain,
         * leave the strings uncommented.
         * Some of the strings are added into a sprintf, so see the comments at the
         * end of each line for what each argument will be.
         */

        global $bookyourtravel_theme_globals;
        $hide_notices = $bookyourtravel_theme_globals->hide_required_plugins_notice();

        $config = array(
            'id' => 'tgmpa', // Unique ID for hashing notices for multiple instances of TGMPA.
            'default_path' => '', // Default absolute path to pre-packaged plugins.
            'menu' => 'tgmpa-install-plugins', // Menu slug.
            'has_notices' => !$hide_notices, // Show admin notices or not.
            'dismissable' => true, // If false, a user cannot dismiss the nag message.
            'dismiss_msg' => '', // If 'dismissable' is false, this message will be output at top of nag.
            'is_automatic' => false, // Automatically activate plugins after installation or not.
            'message' => '', // Message to output right before the plugins table.
            'strings' => array(
                'page_title' => esc_html__('Install Required Plugins', 'bookyourtravel'),
                'menu_title' => esc_html__('Install Plugins', 'bookyourtravel'),
                'installing' => esc_html__('Installing Plugin: %s', 'bookyourtravel'), // %s = plugin name.
                'oops' => esc_html__('Something went wrong with the plugin API.', 'bookyourtravel'),
                'notice_can_install_required' => _n_noop('This theme requires the following plugin: %1$s.', 'This theme requires the following plugins: %1$s.', 'bookyourtravel'), // %1$s = plugin name(s).
                'notice_can_install_recommended' => _n_noop('This theme recommends the following plugin: %1$s.', 'This theme recommends the following plugins: %1$s.', 'bookyourtravel'), // %1$s = plugin name(s).
                'notice_cannot_install' => _n_noop('Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.', 'bookyourtravel'), // %1$s = plugin name(s).
                'notice_can_activate_required' => _n_noop('The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.', 'bookyourtravel'), // %1$s = plugin name(s).
                'notice_can_activate_recommended' => _n_noop('The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.', 'bookyourtravel'), // %1$s = plugin name(s).
                'notice_cannot_activate' => _n_noop('Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.', 'bookyourtravel'), // %1$s = plugin name(s).
                'notice_ask_to_update' => _n_noop('The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.', 'bookyourtravel'), // %1$s = plugin name(s).
                'notice_cannot_update' => _n_noop('Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.', 'bookyourtravel'), // %1$s = plugin name(s).
                'install_link' => _n_noop('Begin installing plugin', 'Begin installing plugins', 'bookyourtravel'),
                'activate_link' => _n_noop('Begin activating plugin', 'Begin activating plugins', 'bookyourtravel'),
                'return' => esc_html__('Return to Required Plugins Installer', 'bookyourtravel'),
                'plugin_activated' => esc_html__('Plugin activated successfully.', 'bookyourtravel'),
                'complete' => esc_html__('All plugins installed and activated successfully. %s', 'bookyourtravel'), // %s = dashboard link.
                'nag_type' => 'updated', // Determines admin notice type - can only be 'updated', 'update-nag' or 'error'.
            ),
        );

        tgmpa($plugins, $config);
    }

    function override_wp_login() {
        if (isset($_GET['loggedout'])) {
            wp_redirect(home_url('/'));
        }
    }

    /**
     * Fire customizer specific action hooks
     */
    function customize_preview_init() {
        do_action('bookyourtravel_customize_preview_init');
    }

    function customize_controls_enqueue_scripts() {
        do_action('bookyourtravel_customize_controls_enqueue_scripts');
    }

    function customize_register($wp_customize) {
        do_action('bookyourtravel_customize_register', $wp_customize);
    }

    function wp_head() {
        do_action('bookyourtravel_wp_head');
    }

    function gutenberg_styles() {
        // Load the theme styles within Gutenberg.
        wp_enqueue_style('bookyourtravel-gutenberg', BookYourTravel_Theme_Utils::get_file_uri('/css/admin/gutenberg.css'), BOOKYOURTRAVEL_VERSION, 'all');
    }

    /**
     * Enqueues scripts and styles for front-end.
     */
    function enqueue_scripts_styles() {

        global $bookyourtravel_theme_globals;

        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-ui-core');
        wp_enqueue_script('jquery-ui-slider');
        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_script('jquery-effects-core');

        wp_enqueue_script('bookyourtravel-preloader', BookYourTravel_Theme_Utils::get_file_uri('/js/preloader.js'), array('jquery'), BOOKYOURTRAVEL_VERSION, true);

        global $sitepress;
        $language_code = $bookyourtravel_theme_globals->get_current_language_code();

        $i18n = array();
        $i18n['pageLang'] = '';
        $i18n['langCode'] = $language_code;

        if ($sitepress) {
            $i18n['pageLang'] = $sitepress->get_current_language();
        }

        wp_localize_script('bookyourtravel-preloader', 'byt_i18n', $i18n);

		if (BookYourTravel_Theme_Utils::check_file_exists('/js/lib/i18n/datepicker-' . $language_code . '.js')) {
			wp_register_script(	'bookyourtravel-datepicker-' . $language_code, BookYourTravel_Theme_Utils::get_file_uri('/js/lib/i18n/datepicker-' . $language_code . '.js'), array('jquery', 'jquery-ui-datepicker'), BOOKYOURTRAVEL_VERSION, true);
			wp_enqueue_script( 'bookyourtravel-datepicker-' . $language_code );
			wp_register_script(	'bookyourtravel-datepicker-i18n-fix', BookYourTravel_Theme_Utils::get_file_uri('/js/lib/i18n/datepicker-i18n-fix.js'), array('bookyourtravel-datepicker-' . $language_code), BOOKYOURTRAVEL_VERSION, true);
			wp_enqueue_script( 'bookyourtravel-datepicker-i18n-fix' );
        }

        wp_enqueue_script('bookyourtravel-jquery-validate', BookYourTravel_Theme_Utils::get_file_uri('/js/lib/jquery.validate.min.js'), array('jquery', 'jquery-ui-datepicker'), BOOKYOURTRAVEL_VERSION, true);
        wp_enqueue_script('bookyourtravel-extras-jquery-validate', BookYourTravel_Theme_Utils::get_file_uri('/js/lib/extras.jquery.validate.min.js'), array('bookyourtravel-jquery-validate'), BOOKYOURTRAVEL_VERSION, true);

        wp_enqueue_style('bookyourtravel-font-awesome', BookYourTravel_Theme_Utils::get_file_uri('/css/lib/font-awesome.min.css'), BOOKYOURTRAVEL_VERSION, "screen,print");


        $font_dependencies = array(
            'bookyourtravel-font-icon-style',
            'bookyourtravel-font-awesome'
        );

        $base_font = $bookyourtravel_theme_globals->get_base_font();
        $heading_font = $bookyourtravel_theme_globals->get_heading_font();

        if (!BookYourTravel_Theme_Utils::is_web_safe_font($base_font)) {
            $google_base_font_css_uri = $this->get_google_fonts_css_uri($base_font);
            if (!empty($google_base_font_css_uri)) {
                wp_enqueue_style('bookyourtravel-base-font-css-style', $google_base_font_css_uri);
                $font_dependencies[] = 'bookyourtravel-base-font-css-style';
            }
        }

        if (!BookYourTravel_Theme_Utils::is_web_safe_font($heading_font)) {
            $google_heading_font_css_uri = $this->get_google_fonts_css_uri($heading_font);
            if (!empty($google_heading_font_css_uri)) {
                wp_enqueue_style('bookyourtravel-heading-font-css-style', $google_heading_font_css_uri);
                $font_dependencies[] = 'bookyourtravel-heading-font-css-style';
            }
        }

        $google_fonts_icon_uri = $this->get_google_fonts_icon_uri();
        if (!empty($google_fonts_icon_uri)) {
            wp_enqueue_style('bookyourtravel-font-icon-style', $google_fonts_icon_uri);
        }

        if ($bookyourtravel_theme_globals->enable_rtl() || (defined('BYT_DEMO') && isset($_REQUEST['rtl']))) {
            wp_enqueue_style('bookyourtravel-style-rtl', BookYourTravel_Theme_Utils::get_file_uri('/css/style-rtl.css'), $font_dependencies, BOOKYOURTRAVEL_VERSION, "screen,print");
        } else {
            wp_enqueue_style('bookyourtravel-style-main', BookYourTravel_Theme_Utils::get_file_uri('/css/style.css'), $font_dependencies, BOOKYOURTRAVEL_VERSION, "screen,print");
            wp_enqueue_style('bookyourtravel-style-open-asia', BookYourTravel_Theme_Utils::get_file_uri('/css/open-asia.css'), $font_dependencies, BOOKYOURTRAVEL_VERSION, "screen,print");
            wp_enqueue_style('bookyourtravel-style-responsive', BookYourTravel_Theme_Utils::get_file_uri('/css/open-asia-responsive.css'), $font_dependencies, BOOKYOURTRAVEL_VERSION, "screen,print");
        }

        wp_enqueue_style('bookyourtravel-style', get_stylesheet_uri());
        wp_enqueue_style('bookyourtravel-style-pp', BookYourTravel_Theme_Utils::get_file_uri('/css/lib/prettyPhoto.min.css'), array(), BOOKYOURTRAVEL_VERSION, "screen");
        wp_enqueue_script('bookyourtravel-jquery-raty', BookYourTravel_Theme_Utils::get_file_uri('/js/lib/jquery.raty.min.js'), array('jquery'), BOOKYOURTRAVEL_VERSION, true);
        wp_enqueue_script('bookyourtravel-jquery-uniform', BookYourTravel_Theme_Utils::get_file_uri('/js/lib/jquery.uniform.min.js'), array('jquery'), BOOKYOURTRAVEL_VERSION, true);

        wp_enqueue_script('bookyourtravel-ajaxqueue', BookYourTravel_Theme_Utils::get_file_uri('/js/lib/jquery.ajaxqueue.js'), array('jquery'), BOOKYOURTRAVEL_VERSION, true);        
        wp_enqueue_script('bookyourtravel-scripts', BookYourTravel_Theme_Utils::get_file_uri('/js/scripts.js'), array('jquery', 'bookyourtravel-jquery-uniform', 'bookyourtravel-ajaxqueue'), BOOKYOURTRAVEL_VERSION, true);
        wp_enqueue_script('bookyourtravel-header-ribbon', BookYourTravel_Theme_Utils::get_file_uri('/js/header-ribbon.js'), array('jquery', 'bookyourtravel-jquery-uniform'), BOOKYOURTRAVEL_VERSION, true);
        wp_enqueue_script('bookyourtravel-main', BookYourTravel_Theme_Utils::get_file_uri('/js/main.js'), array('jquery', 'bookyourtravel-jquery-uniform'), BOOKYOURTRAVEL_VERSION, true);

        $ajaxurl = admin_url('admin-ajax.php');

        global $sitepress;
        if ($sitepress) {
            $lang = $sitepress->get_current_language();
            $ajaxurl = admin_url('admin-ajax.php?lang=' . $lang);
        }

        global $current_user;
        if (!isset($current_user)) {
            $current_user = wp_get_current_user();
        }

        wp_localize_script('bookyourtravel-scripts', 'BYTAjax', array(
            'ajaxurl' => $ajaxurl,
            'slimajaxurl' => $bookyourtravel_theme_globals->use_custom_ajax_handler() ? BookYourTravel_Theme_Utils::get_file_uri('/includes/theme_custom_ajax_handler.php') : $ajaxurl,
            'nonce' => wp_create_nonce('bookyourtravel_nonce'),
            'current_user_id' => $current_user ? $current_user->ID : 0,
        ));

        $google_maps_key = trim($bookyourtravel_theme_globals->get_google_maps_key());

        if (is_page()) {
            $page_id = get_queried_object_id();
            $template_file = get_post_meta($page_id, '_wp_page_template', true);

            if ($template_file == 'page-user-account.php') {
                wp_enqueue_script('bookyourtravel-user-account', BookYourTravel_Theme_Utils::get_file_uri('/js/account.js'), array('jquery', 'bookyourtravel-jquery-validate'), BOOKYOURTRAVEL_VERSION, true);
            } else if ($template_file == 'page-contact.php' || $template_file == 'page-contact-form-7.php') {

                wp_enqueue_script('bookyourtravel-contact', BookYourTravel_Theme_Utils::get_file_uri('/js/contact.js'), 'jquery', BOOKYOURTRAVEL_VERSION, true);

                if (!empty($google_maps_key)) {
                    wp_enqueue_script('bookyourtravel-google-maps', '//maps.google.com/maps/api/js?key=' . $google_maps_key, 'jquery', BOOKYOURTRAVEL_VERSION, true);
                    wp_enqueue_script('bookyourtravel-infobox', BookYourTravel_Theme_Utils::get_file_uri('/js/lib/infobox.min.js'), 'jquery', BOOKYOURTRAVEL_VERSION, true);

                    $business_address_longitude = $bookyourtravel_theme_globals->get_business_address_longitude();
                    $business_address_latitude = $bookyourtravel_theme_globals->get_business_address_latitude();

                    $contact_phone_number = $bookyourtravel_theme_globals->get_contact_phone_number();
                    $contact_company_name = $bookyourtravel_theme_globals->get_contact_company_name();
                    $contact_address_street = $bookyourtravel_theme_globals->get_contact_address_street();
                    $contact_address_city = $bookyourtravel_theme_globals->get_contact_address_city();
                    $contact_address_country = $bookyourtravel_theme_globals->get_contact_address_country();

                    $company_address = '<strong>' . $contact_company_name . '</strong>';
                    $company_address .= (!empty($contact_address_street) ? $contact_address_street : '') . ', ';
                    $company_address .= (!empty($contact_address_city) ? $contact_address_city : '') . ', ';
                    $company_address .= (!empty($contact_address_country) ? $contact_address_country : '');
                    $company_address = rtrim(trim($company_address), ',');

                    wp_localize_script('bookyourtravel-contact', 'BYTContact', array(
                        'business_address_latitude' => $business_address_latitude,
                        'business_address_longitude' => $business_address_longitude,
                        'company_address' => $company_address,
                    ));
                }
            } else if ($template_file == 'byt_home.php' || $template_file == 'page-custom-search-results.php' || $template_file == 'page-location-list.php') {
                wp_enqueue_script('bookyourtravel-accommodations', BookYourTravel_Theme_Utils::get_file_uri(BOOKYOURTRAVEL_ACCOMMODATIONS_JS_PATH), array('bookyourtravel-scripts'), BOOKYOURTRAVEL_VERSION, true);
                wp_enqueue_script('bookyourtravel-car_rentals', BookYourTravel_Theme_Utils::get_file_uri(BOOKYOURTRAVEL_CAR_RENTALS_JS_PATH), array('bookyourtravel-scripts'), BOOKYOURTRAVEL_VERSION, true);
                wp_enqueue_script('bookyourtravel-cruises', BookYourTravel_Theme_Utils::get_file_uri(BOOKYOURTRAVEL_CRUISES_JS_PATH), array('bookyourtravel-scripts'), BOOKYOURTRAVEL_VERSION, true);
                wp_enqueue_script('bookyourtravel-tours', BookYourTravel_Theme_Utils::get_file_uri(BOOKYOURTRAVEL_TOURS_JS_PATH), array('bookyourtravel-scripts'), BOOKYOURTRAVEL_VERSION, true);
            } else if ($template_file == 'page-accommodation-list.php') {
                wp_enqueue_script('bookyourtravel-accommodations', BookYourTravel_Theme_Utils::get_file_uri(BOOKYOURTRAVEL_ACCOMMODATIONS_JS_PATH), array('bookyourtravel-scripts'), BOOKYOURTRAVEL_VERSION, true);
            } else if ($template_file == 'page-car_rental-list.php') {
                wp_enqueue_script('bookyourtravel-car_rentals', BookYourTravel_Theme_Utils::get_file_uri(BOOKYOURTRAVEL_CAR_RENTALS_JS_PATH), array('bookyourtravel-scripts'), BOOKYOURTRAVEL_VERSION, true);
            } else if ($template_file == 'page-cruise-list.php') {
                wp_enqueue_script('bookyourtravel-cruises', BookYourTravel_Theme_Utils::get_file_uri(BOOKYOURTRAVEL_CRUISES_JS_PATH), array('bookyourtravel-scripts'), BOOKYOURTRAVEL_VERSION, true);
            } else if ($template_file == 'page-tour-list.php') {
                wp_enqueue_script('bookyourtravel-tours', BookYourTravel_Theme_Utils::get_file_uri(BOOKYOURTRAVEL_TOURS_JS_PATH), array('bookyourtravel-scripts'), BOOKYOURTRAVEL_VERSION, true);
            } else if ($template_file == 'page-user-submit-content.php' || $template_file == 'page-user-content-list.php') {

                wp_enqueue_script('bookyourtravel-reviews', BookYourTravel_Theme_Utils::get_file_uri('/js/reviews.js'), 'jquery', BOOKYOURTRAVEL_VERSION, true);

                wp_enqueue_style('bookyourtravel-dropzone-style', BookYourTravel_Theme_Utils::get_file_uri('/css/lib/dropzone.min.css'));
                wp_enqueue_script('bookyourtravel-dropzone', BookYourTravel_Theme_Utils::get_file_uri('/js/lib/dropzone.min.js'), array('jquery', 'bookyourtravel-jquery-validate'), '1.0', true);
                wp_enqueue_script('bookyourtravel-url-polyfill', BookYourTravel_Theme_Utils::get_file_uri('/includes/plugins/frontend-submit/lib/js/url.polyfill.js'), array('jquery', 'jquery-ui-slider', 'bookyourtravel-dropzone', 'bookyourtravel-jquery-validate'), BOOKYOURTRAVEL_VERSION, true);
                wp_enqueue_script('bookyourtravel-frontend-submit', BookYourTravel_Theme_Utils::get_file_uri('/includes/plugins/frontend-submit/lib/js/frontend-submit.js'), array('jquery', 'jquery-ui-slider', 'bookyourtravel-dropzone', 'bookyourtravel-jquery-validate', 'bookyourtravel-url-polyfill'), BOOKYOURTRAVEL_VERSION, true);

                $enable_accommodations = $bookyourtravel_theme_globals->enable_accommodations();
                $enable_tours = $bookyourtravel_theme_globals->enable_tours();
                $enable_cruises = $bookyourtravel_theme_globals->enable_cruises();
                $enable_car_rentals = $bookyourtravel_theme_globals->enable_car_rentals();

                if ($enable_accommodations) {
                    wp_enqueue_script('bookyourtravel-frontend-submit-accommodations', BookYourTravel_Theme_Utils::get_file_uri('/includes/plugins/frontend-submit/lib/js/frontend-submit-accommodations.js'), array('bookyourtravel-frontend-submit'), BOOKYOURTRAVEL_VERSION, true);
                }
                if ($enable_tours) {
                    wp_enqueue_script('bookyourtravel-frontend-submit-tours', BookYourTravel_Theme_Utils::get_file_uri('/includes/plugins/frontend-submit/lib/js/frontend-submit-tours.js'), array('bookyourtravel-frontend-submit'), BOOKYOURTRAVEL_VERSION, true);
                }
                if ($enable_cruises) {
                    wp_enqueue_script('bookyourtravel-frontend-submit-cruises', BookYourTravel_Theme_Utils::get_file_uri('/includes/plugins/frontend-submit/lib/js/frontend-submit-cruises.js'), array('bookyourtravel-frontend-submit'), BOOKYOURTRAVEL_VERSION, true);
                }
                if ($enable_car_rentals) {
                    wp_enqueue_script('bookyourtravel-frontend-submit-car-rentals', BookYourTravel_Theme_Utils::get_file_uri('/includes/plugins/frontend-submit/lib/js/frontend-submit-car-rentals.js'), array('bookyourtravel-frontend-submit'), BOOKYOURTRAVEL_VERSION, true);
                }
            }
        }

        if (is_single()) {
            wp_enqueue_script('bookyourtravel-jquery-lightSlider', BookYourTravel_Theme_Utils::get_file_uri('/includes/plugins/lightSlider/js/jquery.lightSlider.js'), 'jquery', BOOKYOURTRAVEL_VERSION, true);
            wp_enqueue_style('bookyourtravel-lightSlider-style', BookYourTravel_Theme_Utils::get_file_uri('/includes/plugins/lightSlider/css/lightSlider.css'));

            wp_enqueue_script('bookyourtravel-gallery', BookYourTravel_Theme_Utils::get_file_uri('/js/gallery.js'), array('jquery', 'bookyourtravel-jquery-lightSlider'), BOOKYOURTRAVEL_VERSION, true);
            wp_enqueue_script('bookyourtravel-tabs', BookYourTravel_Theme_Utils::get_file_uri('/js/tabs.js'), 'jquery', BOOKYOURTRAVEL_VERSION, true);
            wp_enqueue_script('bookyourtravel-reviews', BookYourTravel_Theme_Utils::get_file_uri('/js/reviews.js'), 'jquery', BOOKYOURTRAVEL_VERSION, true);
            wp_enqueue_script('bookyourtravel-inquiry', BookYourTravel_Theme_Utils::get_file_uri('/js/inquiry.js'), 'jquery', BOOKYOURTRAVEL_VERSION, true);

            if (!empty($google_maps_key)) {
                wp_enqueue_script('bookyourtravel-google-maps', '//maps.google.com/maps/api/js?key=' . $google_maps_key, 'jquery', BOOKYOURTRAVEL_VERSION, true);
                wp_enqueue_script('bookyourtravel-infobox', BookYourTravel_Theme_Utils::get_file_uri('/js/lib/infobox.min.js'), 'jquery', BOOKYOURTRAVEL_VERSION, true);
                wp_enqueue_script('bookyourtravel-maps', BookYourTravel_Theme_Utils::get_file_uri('/js/maps.js'), array('jquery', 'bookyourtravel-infobox'), BOOKYOURTRAVEL_VERSION, true);
            }

            $post_type = get_post_type();
            if ($post_type == 'accommodation' || $post_type == 'car_rental' || $post_type == 'cruise' || $post_type == 'tour') {
                wp_enqueue_script('bookyourtravel-tablesorter', BookYourTravel_Theme_Utils::get_file_uri('/js/lib/jquery.tablesorter.min.js'), 'jquery', '1.0', true);
            }

            if ($post_type == 'accommodation') {
                wp_enqueue_script('bookyourtravel-extra-items', BookYourTravel_Theme_Utils::get_file_uri(BOOKYOURTRAVEL_EXTRA_ITEMS_JS_PATH), array('bookyourtravel-scripts'), BOOKYOURTRAVEL_VERSION, true);
                wp_enqueue_script('bookyourtravel-prettyPhoto', BookYourTravel_Theme_Utils::get_file_uri('/js/lib/jquery.prettyPhoto.min.js'), array('bookyourtravel-scripts'), BOOKYOURTRAVEL_VERSION, true);
                wp_enqueue_script('bookyourtravel-accommodations', BookYourTravel_Theme_Utils::get_file_uri(BOOKYOURTRAVEL_ACCOMMODATIONS_JS_PATH), array('bookyourtravel-scripts', 'bookyourtravel-prettyPhoto', 'bookyourtravel-extra-items'), BOOKYOURTRAVEL_VERSION, true);
            } else if ($post_type == 'car_rental') {
                wp_enqueue_script('bookyourtravel-extra-items', BookYourTravel_Theme_Utils::get_file_uri(BOOKYOURTRAVEL_EXTRA_ITEMS_JS_PATH), array('bookyourtravel-scripts'), BOOKYOURTRAVEL_VERSION, true);
                wp_enqueue_script('bookyourtravel-prettyPhoto', BookYourTravel_Theme_Utils::get_file_uri('/js/lib/jquery.prettyPhoto.min.js'), array('bookyourtravel-scripts'), BOOKYOURTRAVEL_VERSION, true);
                wp_enqueue_script('bookyourtravel-car_rentals', BookYourTravel_Theme_Utils::get_file_uri(BOOKYOURTRAVEL_CAR_RENTALS_JS_PATH), array('bookyourtravel-scripts', 'bookyourtravel-prettyPhoto', 'bookyourtravel-extra-items'), BOOKYOURTRAVEL_VERSION, true);
            } else if ($post_type == 'cruise') {
                wp_enqueue_script('bookyourtravel-extra-items', BookYourTravel_Theme_Utils::get_file_uri(BOOKYOURTRAVEL_EXTRA_ITEMS_JS_PATH), array('bookyourtravel-scripts'), BOOKYOURTRAVEL_VERSION, true);
                wp_enqueue_script('bookyourtravel-prettyPhoto', BookYourTravel_Theme_Utils::get_file_uri('/js/lib/jquery.prettyPhoto.min.js'), array('bookyourtravel-scripts'), BOOKYOURTRAVEL_VERSION, true);
                wp_enqueue_script('bookyourtravel-cruises', BookYourTravel_Theme_Utils::get_file_uri(BOOKYOURTRAVEL_CRUISES_JS_PATH), array('bookyourtravel-scripts', 'bookyourtravel-prettyPhoto', 'bookyourtravel-extra-items'), BOOKYOURTRAVEL_VERSION, true);
            } else if ($post_type == 'tour') {
                wp_enqueue_script('bookyourtravel-extra-items', BookYourTravel_Theme_Utils::get_file_uri(BOOKYOURTRAVEL_EXTRA_ITEMS_JS_PATH), array('bookyourtravel-scripts'), BOOKYOURTRAVEL_VERSION, true);
                wp_enqueue_script('bookyourtravel-prettyPhoto', BookYourTravel_Theme_Utils::get_file_uri('/js/lib/jquery.prettyPhoto.min.js'), array('bookyourtravel-scripts'), BOOKYOURTRAVEL_VERSION, true);
                wp_enqueue_script('bookyourtravel-tours', BookYourTravel_Theme_Utils::get_file_uri(BOOKYOURTRAVEL_TOURS_JS_PATH), array('bookyourtravel-scripts', 'bookyourtravel-prettyPhoto', 'bookyourtravel-extra-items'), BOOKYOURTRAVEL_VERSION, true);
            } else if ($post_type == "location") {

                $enable_accommodations = $bookyourtravel_theme_globals->enable_accommodations();
                $enable_tours = $bookyourtravel_theme_globals->enable_tours();
                $enable_cruises = $bookyourtravel_theme_globals->enable_cruises();
                $enable_car_rentals = $bookyourtravel_theme_globals->enable_car_rentals();

                wp_enqueue_script('bookyourtravel-extra-items', BookYourTravel_Theme_Utils::get_file_uri(BOOKYOURTRAVEL_EXTRA_ITEMS_JS_PATH), array('bookyourtravel-scripts'), BOOKYOURTRAVEL_VERSION, true);
                wp_enqueue_script('bookyourtravel-prettyPhoto', BookYourTravel_Theme_Utils::get_file_uri('/js/lib/jquery.prettyPhoto.min.js'), array('bookyourtravel-scripts'), BOOKYOURTRAVEL_VERSION, true);

                if ($enable_accommodations) {
                    wp_enqueue_script('bookyourtravel-accommodations', BookYourTravel_Theme_Utils::get_file_uri(BOOKYOURTRAVEL_ACCOMMODATIONS_JS_PATH), array('bookyourtravel-scripts', 'bookyourtravel-prettyPhoto', 'bookyourtravel-extra-items'), BOOKYOURTRAVEL_VERSION, true);
                }
                if ($enable_tours) {
                    wp_enqueue_script('bookyourtravel-tours', BookYourTravel_Theme_Utils::get_file_uri(BOOKYOURTRAVEL_TOURS_JS_PATH), array('bookyourtravel-scripts', 'bookyourtravel-prettyPhoto', 'bookyourtravel-extra-items'), BOOKYOURTRAVEL_VERSION, true);
                }
                if ($enable_cruises) {
                    wp_enqueue_script('bookyourtravel-cruises', BookYourTravel_Theme_Utils::get_file_uri(BOOKYOURTRAVEL_CRUISES_JS_PATH), array('bookyourtravel-scripts', 'bookyourtravel-prettyPhoto', 'bookyourtravel-extra-items'), BOOKYOURTRAVEL_VERSION, true);
                }
                if ($enable_car_rentals) {
                    wp_enqueue_script('bookyourtravel-car_rentals', BookYourTravel_Theme_Utils::get_file_uri(BOOKYOURTRAVEL_CAR_RENTALS_JS_PATH), array('bookyourtravel-scripts', 'bookyourtravel-prettyPhoto', 'bookyourtravel-extra-items'), BOOKYOURTRAVEL_VERSION, true);
                }
            }
        }

        if (is_singular() && comments_open() && get_option('thread_comments')) {
            wp_enqueue_script('comment-reply');
        }

        do_action('bookyourtravel_enqueue_scripts_styles');
    }

    /**
     * Enqueues scripts and styles for admin.
     *
     * @since Book Your Travel 1.0
     */
    function enqueue_admin_scripts_styles() {

        $ajaxurl = admin_url('admin-ajax.php');

        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-effects-core');
        wp_enqueue_script('jquery-ui-core');
        wp_enqueue_script('jquery-ui-mouse');
        wp_enqueue_script('jquery-ui-sortable');
        wp_enqueue_script('jquery-ui-slider');
        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_script('jquery-ui-droppable');
        wp_enqueue_script('jquery-ui-draggable');
        wp_enqueue_script('jquery-ui-selectable');
        wp_enqueue_script('jquery-ui-autocomplete');
        wp_enqueue_script('jquery-ui-tabs');
        wp_enqueue_script('jquery-ui-dialog');
        wp_enqueue_script('jquery-ui-spinner');

        add_thickbox();

        wp_enqueue_media();

        $google_fonts_icon_uri = $this->get_google_fonts_icon_uri();
        if (!empty($google_fonts_icon_uri)) {
            wp_enqueue_style('bookyourtravel-font-icon-style', $google_fonts_icon_uri);
        }

        wp_enqueue_style('bookyourtravel-admin-icons-css', BookYourTravel_Theme_Utils::get_file_uri('/css/admin/admin_icons.css'), array());

        global $pagenow;
        if ((isset($_REQUEST['page']) && (
            ($_REQUEST['page'] == 'theme_accommodation_booking_admin.php') ||
            ($_REQUEST['page'] == 'theme_tour_booking_admin.php') ||
            ($_REQUEST['page'] == 'theme_cruise_booking_admin.php') ||
            ($_REQUEST['page'] == 'theme_car_rental_booking_admin.php') ||
            ($_REQUEST['page'] == 'theme_accommodation_vacancy_admin.php') ||
            ($_REQUEST['page'] == 'theme_car_rental_availability_admin.php') ||
            ($_REQUEST['page'] == 'theme_cruise_schedule_admin.php') ||
            ($_REQUEST['page'] == 'theme_tour_schedule_admin.php'))) ||
            ($pagenow == 'widgets.php') ||
            ($pagenow == 'options-general.php') ||
            ($pagenow == 'themes.php') ||
            ($pagenow == 'edit.php') ||
            ($pagenow == 'post.php') ||
            ($pagenow == 'post-new.php') ||
            ($pagenow == 'customize.php') ||
            ($pagenow == 'nav-menus.php')) {
            wp_enqueue_style('bookyourtravel-admin-css', BookYourTravel_Theme_Utils::get_file_uri('/css/admin/admin_custom.css'), array());
        }

        wp_enqueue_script('bookyourtravel-admin-script', BookYourTravel_Theme_Utils::get_file_uri('/js/admin/admin.js'), array('jquery'), BOOKYOURTRAVEL_VERSION, true);
        wp_localize_script('bookyourtravel-admin-script', 'BYTAdmin', array(
            'ajaxurl' => $ajaxurl,
            'datepickerDateFormat' => BookYourTravel_Theme_Utils::dateformat_PHP_to_jQueryUI(get_option('date_format')),
            'datepickerAltFormat' => BOOKYOURTRAVEL_ALT_DATE_FORMAT,
            'currentDay' => date_i18n('j'),
            'currentMonth' => date_i18n('n'),
            'currentYear' => date_i18n('Y'),
        ));

        if (isset($_REQUEST['page']) && (($_REQUEST['page'] == 'theme_accommodation_booking_admin.php') || ($_REQUEST['page'] == 'theme_accommodation_vacancy_admin.php'))) {
            wp_enqueue_script('bookyourtravel-admin-accommodations', BookYourTravel_Theme_Utils::get_file_uri('/js/admin/admin_accommodations.js'), array('jquery', 'bookyourtravel-admin-script'), BOOKYOURTRAVEL_VERSION, true);
        } else if (isset($_REQUEST['page']) && (($_REQUEST['page'] == 'theme_tour_booking_admin.php') || ($_REQUEST['page'] == 'theme_tour_schedule_admin.php'))) {
            wp_enqueue_script('bookyourtravel-admin-tours', BookYourTravel_Theme_Utils::get_file_uri('/js/admin/admin_tours.js'), array('jquery', 'bookyourtravel-admin-script'), BOOKYOURTRAVEL_VERSION, true);
        } else if (isset($_REQUEST['page']) && (($_REQUEST['page'] == 'theme_cruise_booking_admin.php') || ($_REQUEST['page'] == 'theme_cruise_schedule_admin.php'))) {
            wp_enqueue_script('bookyourtravel-admin-cruises', BookYourTravel_Theme_Utils::get_file_uri('/js/admin/admin_cruises.js'), array('jquery', 'bookyourtravel-admin-script'), BOOKYOURTRAVEL_VERSION, true);
        } else if (isset($_REQUEST['page']) && ($_REQUEST['page'] == 'theme_car_rental_booking_admin.php' || $_REQUEST['page'] == 'theme_car_rental_availability_admin.php')) {
            wp_enqueue_script('bookyourtravel-admin-car-rentals', BookYourTravel_Theme_Utils::get_file_uri('/js/admin/admin_car_rentals.js'), array('jquery', 'bookyourtravel-admin-script'), BOOKYOURTRAVEL_VERSION, true);
        }

        do_action('bookyourtravel_enqueue_admin_scripts_styles');
    }

    /**
     * Add a pingback url auto-discovery header for singularly identifiable articles.
     */
    function pingback_header() {
        if (is_singular() && pings_open()) {
            printf('<link rel="pingback" href="%s">' . "\n", get_bloginfo('pingback_url'));
        }

        do_action('bookyourtravel_pingback_header');
    }

    /**
     * Sets up theme defaults and registers support for various WordPress features.
     *
     * Note that this function is hooked into the after_setup_theme hook, which
     * runs before the init hook. The init hook is too late for some features, such
     * as indicating support for post thumbnails.
     */
    function theme_setup() {

        global $content_width;

        // create new frontend submit role custom to BYT if it's not already created
        $frontend_submit_role = get_role(BOOKYOURTRAVEL_FRONTEND_SUBMIT_ROLE);
        if ($frontend_submit_role == null) {
            $frontend_submit_role = add_role(
                BOOKYOURTRAVEL_FRONTEND_SUBMIT_ROLE,
                esc_html__('BYT Frontend Submit Role', 'bookyourtravel'),
                array(
                    'read' => true, // true allows this capability
                )
            );
        }

        /*
         * Book Your Travel available for translation.
         *
         * Translations can be added to the /languages/ directory.
         * If you're building a theme based on Book Your Travel, use a find and replace
         * to change 'bookyourtravel' to the name of your theme in all the template files.
         */

        if (is_child_theme()) {
            load_child_theme_textdomain('bookyourtravel', get_stylesheet_directory() . '/languages');
        } else {
            load_theme_textdomain('bookyourtravel', get_template_directory() . '/languages');
        }

        if (defined('DOING_AJAX') && DOING_AJAX) {
            global $sitepress;
            if ($sitepress && isset($_GET['page_lang'])) {
                $sitepress->switch_lang($_GET['page_lang'], true);
            }
        }

        remove_theme_support( 'widgets-block-editor' );

        add_theme_support('automatic-feed-links');
        add_theme_support('post-thumbnails');
        add_theme_support('woocommerce');

        // Add theme support for selective refresh for widgets.
        add_theme_support('customize-selective-refresh-widgets');

        /*
         * Let WordPress manage the document title.
         * By adding theme support, we declare that this theme does not use a
         * hard-coded <title> tag in the document head, and expect WordPress to
         * provide it for us.
         */
        add_theme_support('title-tag');

        add_theme_support('custom-background', array(
            'default-color' => 'E9E6E0',
        ));

        add_theme_support('custom-logo', array(
            'height' => 248,
            'width' => 248,
            'flex-height' => true,
            'flex-width' => true,
        ));

        /*
         * Switch default core markup for search form, comment form, and comments
         * to output valid HTML5.
         */
        add_theme_support('html5', array(
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
        ));

        if (!isset($content_width)) {
            $content_width = 850;
        }

        $image_sizes = BookYourTravel_Theme_Utils::get_all_image_sizes();

        if (!isset($image_sizes["thumbnail"])
            || (isset($image_sizes["thumbnail"]["width"]) && $image_sizes["thumbnail"]["width"] == 0)
            || (isset($image_sizes["thumbnail"]["height"]) && $image_sizes["thumbnail"]["height"] == 0)
            || (isset($image_sizes["thumbnail"]["height"]) && $image_sizes["thumbnail"]["height"] == 150 && isset($image_sizes["thumbnail"]["width"]) && $image_sizes["thumbnail"]["width"] == 150)
            ) {
            update_option('thumbnail_size_w', 400);
            update_option('thumbnail_size_h', 300);
            update_option('thumbnail_crop', 1);
        }

        if (!isset($image_sizes["medium"])
            || (isset($image_sizes["medium"]["width"]) && $image_sizes["medium"]["width"] == 0)
            || (isset($image_sizes["medium"]["height"]) && $image_sizes["medium"]["height"] == 0)
            || (isset($image_sizes["thumbnail"]["height"]) && $image_sizes["thumbnail"]["height"] == 300 && isset($image_sizes["thumbnail"]["width"]) && $image_sizes["thumbnail"]["width"] == 300)
            ) {
            update_option('medium_size_w', 600);
            update_option('medium_size_h', 400);
            update_option('medium_crop', 1);
        }

        if (!isset($image_sizes["large"])
            || (isset($image_sizes["large"]["width"]) && $image_sizes["large"]["width"] == 0)
            || (isset($image_sizes["large"]["height"]) && $image_sizes["large"]["height"] == 0)
            || (isset($image_sizes["thumbnail"]["height"]) && $image_sizes["thumbnail"]["height"] == 1024 && isset($image_sizes["thumbnail"]["width"]) && $image_sizes["thumbnail"]["width"] == 1024)
            ) {
            update_option('large_size_w', 900);
            update_option('large_size_h', 600);
            update_option('large_crop', 1);
        }

        add_image_size('byt-featured', 1600, 600, true);

        register_nav_menus(array(
            'primary-menu' => esc_html__('Primary Menu', 'bookyourtravel'),
            'top-nav-menu' => esc_html__('Top Header Menu', 'bookyourtravel'),
            'top-nav-left-menu' => esc_html__('Top Left Header Menu', 'bookyourtravel'),
            'header-ribbon-menu' => esc_html__('Header Ribbon Menu', 'bookyourtravel'),
            'footer-menu' => esc_html__('Footer Menu', 'bookyourtravel'),
            'user-account-menu' => esc_html__('User Account Menu', 'bookyourtravel'),
            'partner-account-menu' => esc_html__('Partner Account Menu', 'bookyourtravel'),
        ));

        $this->register_sidebars();

        do_action('bookyourtravel_after_setup_theme');
    }

    function register_sidebars() {

        //Left Sidebar Widget area
        $layout_class = BookYourTravel_Theme_Utils::get_sidebar_widget_layout_class('left');
        register_sidebar(array(
            'name' => esc_html__('Left Sidebar', 'bookyourtravel'),
            'id' => 'left',
            'description' => esc_html__('This Widget area is used for the left sidebar', 'bookyourtravel'),
            'before_widget' => '<li class="widget widget-sidebar ' . $layout_class . '">',
            'after_widget' => '</li>',
            'before_title' => '<h4>',
            'after_title' => '</h4>',
        ));

        // Right Sidebar Widget area
        $layout_class = BookYourTravel_Theme_Utils::get_sidebar_widget_layout_class('right');
        register_sidebar(array(
            'name' => esc_html__('Right Sidebar', 'bookyourtravel'),
            'id' => 'right',
            'description' => esc_html__('This Widget area is used for the right sidebar', 'bookyourtravel'),
            'before_widget' => '<li class="widget widget-sidebar ' . $layout_class . '">',
            'after_widget' => '</li>',
            'before_title' => '<h4>',
            'after_title' => '</h4>',
        ));

        // Right Sidebar Widget area
        $layout_class = BookYourTravel_Theme_Utils::get_sidebar_widget_layout_class('right-accommodation');
        register_sidebar(array(
            'name' => esc_html__('Right Accommodation Sidebar', 'bookyourtravel'),
            'id' => 'right-accommodation',
            'description' => esc_html__('This Widget area is used for the right sidebar for single accommodations', 'bookyourtravel'),
            'before_widget' => '<li class="widget widget-sidebar ' . $layout_class . '">',
            'after_widget' => '</li>',
            'before_title' => '<h4>',
            'after_title' => '</h4>',
        ));

        // Right Sidebar Widget area
        $layout_class = BookYourTravel_Theme_Utils::get_sidebar_widget_layout_class('right-tour');
        register_sidebar(array(
            'name' => esc_html__('Right Tour Sidebar', 'bookyourtravel'),
            'id' => 'right-tour',
            'description' => esc_html__('This Widget area is used for the right sidebar for single tours', 'bookyourtravel'),
            'before_widget' => '<li class="widget widget-sidebar ' . $layout_class . '">',
            'after_widget' => '</li>',
            'before_title' => '<h4>',
            'after_title' => '</h4>',
        ));

        // Right Sidebar Widget area
        $layout_class = BookYourTravel_Theme_Utils::get_sidebar_widget_layout_class('right-cruise');
        register_sidebar(array(
            'name' => esc_html__('Right Cruise Sidebar', 'bookyourtravel'),
            'id' => 'right-cruise',
            'description' => esc_html__('This Widget area is used for the right sidebar for single cruises', 'bookyourtravel'),
            'before_widget' => '<li class="widget widget-sidebar ' . $layout_class . '">',
            'after_widget' => '</li>',
            'before_title' => '<h4>',
            'after_title' => '</h4>',
        ));

        // Right Sidebar Widget area
        $layout_class = BookYourTravel_Theme_Utils::get_sidebar_widget_layout_class('right-car_rental');
        register_sidebar(array(
            'name' => esc_html__('Right Car Rental Sidebar', 'bookyourtravel'),
            'id' => 'right-car_rental',
            'description' => esc_html__('This Widget area is used for the right sidebar for single car rentals', 'bookyourtravel'),
            'before_widget' => '<li class="widget widget-sidebar ' . $layout_class . '">',
            'after_widget' => '</li>',
            'before_title' => '<h4>',
            'after_title' => '</h4>',
        ));

        // Header Sidebar Widget area
        register_sidebar(array(
            'name' => esc_html__('Header Sidebar', 'bookyourtravel'),
            'id' => 'header',
            'description' => esc_html__('This Widget area is used for the header area (usually for purposes of displaying WPML language switcher widget)', 'bookyourtravel'),
            'before_widget' => '',
            'after_widget' => '',
            'class' => 'lang-nav',
            'before_title' => '<h4>',
            'after_title' => '</h4>',
        ));

        // Under Header Sidebar Widget area
        $layout_class = BookYourTravel_Theme_Utils::get_sidebar_widget_layout_class('under-header');
        register_sidebar(array(
            'name' => esc_html__('Under Header Sidebar', 'bookyourtravel'),
            'id' => 'under-header',
            'description' => esc_html__('This Widget area is placed under the website header', 'bookyourtravel'),
            'before_widget' => '<li class="widget widget-sidebar ' . $layout_class . '">',
            'after_widget' => '</li>',
            'before_title' => '<h4>',
            'after_title' => '</h4>',
        ));

        // Hero Sidebar Widget area
        $layout_class = BookYourTravel_Theme_Utils::get_sidebar_widget_layout_class('hero');
        register_sidebar(array(
            'name' => esc_html__('Hero Sidebar', 'bookyourtravel'),
            'id' => 'hero',
            'description' => esc_html__('This Widget area is placed above the website content', 'bookyourtravel'),
            'before_widget' => '<li class="widget widget-sidebar ' . $layout_class . '">',
            'after_widget' => '</li>',
            'before_title' => '<h4>',
            'after_title' => '</h4>',
        ));

        // Under Header Sidebar Widget area
        $layout_class = BookYourTravel_Theme_Utils::get_sidebar_widget_layout_class('above-footer');
        register_sidebar(array(
            'name' => esc_html__('Above Footer Sidebar', 'bookyourtravel'),
            'id' => 'above-footer',
            'description' => esc_html__('This Widget area is placed above the website footer', 'bookyourtravel'),
            'before_widget' => '<li class="widget widget-sidebar ' . $layout_class . '"><div>',
            'after_widget' => '</div></li>',
            'before_title' => '<h4>',
            'after_title' => '</h4>',
        ));

        // Footer Sidebar Widget area
        $layout_class = BookYourTravel_Theme_Utils::get_sidebar_widget_layout_class('footer');
        register_sidebar(array(
            'name' => esc_html__('Footer Sidebar', 'bookyourtravel'),
            'id' => 'footer',
            'description' => esc_html__('This Widget area is used for the footer area', 'bookyourtravel'),
            'before_widget' => '<li class="widget widget-sidebar ' . $layout_class . '">',
            'after_widget' => '</li>',
            'before_title' => '<h6>',
            'after_title' => '</h6>',
        ));

        // Home Footer Sidebar Widget area
        $layout_class = BookYourTravel_Theme_Utils::get_sidebar_widget_layout_class('home-footer');
        register_sidebar(array(
            'name' => esc_html__('Home Footer Widget Area', 'bookyourtravel'),
            'id' => 'home-footer',
            'description' => esc_html__('This Widget area is used for the home page footer area above the regular footer', 'bookyourtravel'),
            'before_widget' => '<li class="widget widget-sidebar ' . $layout_class . '"><div>',
            'after_widget' => '</div></li>',
            'before_title' => '<h4>',
            'after_title' => '</h4>',
        ));

        $layout_class = BookYourTravel_Theme_Utils::get_sidebar_widget_layout_class('home-content');
        register_sidebar(array(
            'name' => esc_html__('Home Content Widget Area', 'bookyourtravel'),
            'id' => 'home-content',
            'description' => esc_html__('This Widget area is used for the home page main content area', 'bookyourtravel'),
            'before_widget' => '<li class="widget widget-sidebar ' . $layout_class . '">',
            'after_widget' => '</li>',
            'before_title' => '<h2>',
            'after_title' => '</h2>',
        ));

        do_action('bookyourtravel_register_sidebars');
    }

    function get_google_fonts_icon_uri() {

        $fonts_url = '';

        $font_families = array();

        $font_families[] = 'Material+Icons';

        $query_args = array(
            'family' => implode('|', $font_families),
        );

        $fonts_url = add_query_arg($query_args, '//fonts.googleapis.com/icon');

        return esc_url_raw($fonts_url);
    }

    function get_google_fonts_css_uri($font_family) {

        global $bookyourtravel_theme_globals;

        $fonts_url = '';

        $font_families = array();

        $font_families[] = $font_family . ':400,500,600,700';

        $query_args = array(
            'family' => implode('|', $font_families),
            'subset' => 'latin,cyrillic,latin-ext,vietnamese,greek,greek-ext,cyrillic-ext',
        );

        $fonts_url = add_query_arg($query_args, '//fonts.googleapis.com/css');

        return esc_url_raw($fonts_url);
    }
}

// store the instance in a variable to be retrieved later and call init
$bookyourtravel_theme_actions = BookYourTravel_Theme_Actions::get_instance();
