<?php
/**
 * BookYourTravel_Theme_Filters class
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */

if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class BookYourTravel_Theme_Filters extends BookYourTravel_BaseSingleton
{
    protected function __construct()
    {

        // our parent class might contain shared code in its constructor
        parent::__construct();
    }

    public function init()
    {
        global $bookyourtravel_theme_globals;
        
        // add_filter( 'style_loader_tag', array($this, 'remove_type_attributes'), 10, 2);
        // add_filter( 'script_loader_tag', array($this, 'remove_type_attributes'), 10, 2);
        add_filter('revslider_dashboard_elements', array($this, 'revslider_dashboard_elements_remove_activation_widget'));
        add_filter('post_thumbnail_html', array($this, 'filter_post_thumbnail_html'), 10, 5);
        add_filter('get_the_archive_title', array($this, 'get_the_archive_title'));
        add_filter('vc_gitem_template_attribute_post_excerpt', array($this, 'customize_excerpt'), 10, 2);
        add_filter('image_size_names_choose', array($this, 'my_custom_sizes'));

        add_filter('pt-ocdi/import_files', array($this, 'demo_import_files'));
        add_action('pt-ocdi/after_import', array($this, 'demo_after_import'));
        add_filter('wp_dropdown_users_args', array($this, 'add_roles_to_dropdown'), 10, 2);

        add_filter( 'body_class', array($this, 'body_class'));
    }

    function body_class( $classes ) {
        global $body_class;
        $body_classes = explode(" ",$body_class);

        return array_merge( $classes, $body_classes);
    }
        
    public function add_roles_to_dropdown($query_args, $r)
    {
        $query_args['who'] = '';
        return $query_args;
    }

    public function demo_after_import($selected_import)
    {
        if ('Default BookYourTravel' === $selected_import['import_file_name'] ||
            'Agritourism' === $selected_import['import_file_name'] ||
            'Beach Resort' === $selected_import['import_file_name'] ||
            'Bed &amp; Breakfast' === $selected_import['import_file_name'] ||
            'Chalet' === $selected_import['import_file_name'] ||
            'Christmas &amp; New Year' === $selected_import['import_file_name'] ||
            'Cruise Operator' === $selected_import['import_file_name'] ||
            'Eco Lodge' === $selected_import['import_file_name'] ||
            'Event Festival' === $selected_import['import_file_name'] ||
            'Exotic Tours' === $selected_import['import_file_name'] ||
            'Hostel' === $selected_import['import_file_name'] ||
            'Hotel' === $selected_import['import_file_name'] ||
            'Luxury Resort' === $selected_import['import_file_name'] ||
            'Motorhomes &amp; Campervans' === $selected_import['import_file_name'] ||
            'Rent a Car' === $selected_import['import_file_name'] ||
            'Rent a Car Premium' === $selected_import['import_file_name'] ||
            'Ski Resort' === $selected_import['import_file_name'] ||
            'Sustainable Travel' === $selected_import['import_file_name'] ||
            'Tour Landing Page' === $selected_import['import_file_name'] ||
            'Tour Operator' === $selected_import['import_file_name'] ||
            'Travel Agency' === $selected_import['import_file_name'] ||
            'Travel Guide' === $selected_import['import_file_name'] ||
            'Vacation Rentals' === $selected_import['import_file_name'] ||
            'Villa' === $selected_import['import_file_name'] ||
            'Wedding Venue' === $selected_import['import_file_name'] ||
            'Travel &amp; Tours' === $selected_import['import_file_name']) {
            //Set Menu

            $top_menu = get_term_by('name', esc_html__('Primary Menu', 'bookyourtravel'), 'nav_menu');
            $top_nav_menu = get_term_by('name', esc_html__('Top Header Menu', 'bookyourtravel'), 'nav_menu');
            $top_nav_left_menu = get_term_by('name', esc_html__('Top Left Header Menu', 'bookyourtravel'), 'nav_menu');
            $header_ribbon_menu = get_term_by('name', esc_html__('Header Ribbon Menu', 'bookyourtravel'), 'nav_menu');
            $footer_menu = get_term_by('name', esc_html__('Footer Menu', 'bookyourtravel'), 'nav_menu');
            $user_account_menu = get_term_by('name', esc_html__('User Account Menu', 'bookyourtravel'), 'nav_menu');
            $partner_account_menu = get_term_by('name', esc_html__('Partner Account Menu', 'bookyourtravel'), 'nav_menu');

            $menus = array();
            if ($top_menu) {
                $menus['primary-menu'] = $top_menu->term_id;
            }
            if ($top_nav_menu) {
                $menus['top-nav-menu'] = $top_nav_menu->term_id;
            }
            if ($top_nav_left_menu) {
                $menus['top-nav-left-menu'] = $top_nav_left_menu->term_id;
            }
            if ($header_ribbon_menu) {
                $menus['header-ribbon-menu'] = $header_ribbon_menu->term_id;
            }
            if ($footer_menu) {
                $menus['footer-menu'] = $footer_menu->term_id;
            }
            if ($user_account_menu) {
                $menus['user-account-menu'] = $user_account_menu->term_id;
            }
            if ($partner_account_menu) {
                $menus['partner-account-menu'] = $partner_account_menu->term_id;
            }

            set_theme_mod('nav_menu_locations', $menus);
        }

        $page = null;
        $slider_array = array();

        if ('Default BookYourTravel' === $selected_import['import_file_name']) {
            $page = get_page_by_title('Book Your Travel Home');
            $slider_array = array(
                get_template_directory()."/includes/imports/_sliders/Default.zip",
                get_template_directory()."/includes/imports/_sliders/Default.zip",
            );
        } elseif ('Agritourism' === $selected_import['import_file_name']) {
            $page = get_page_by_title('Agritourism Home');
            $slider_array = array(
                get_template_directory()."/includes/imports/_sliders/Agritourism.zip",
                get_template_directory()."/includes/imports/_sliders/Agritourism-inner.zip",
            );            
        } elseif ('Beach Resort' === $selected_import['import_file_name']) {
            $page = get_page_by_title('Home Beach Resort');
            $slider_array = array(
                get_template_directory()."/includes/imports/_sliders/BeachResort.zip",
                get_template_directory()."/includes/imports/_sliders/BeachResort-Accommodation.zip",
                get_template_directory()."/includes/imports/_sliders/BeachResort-Nightlife.zip",
            );
        } elseif ('Bed &amp; Breakfast' === $selected_import['import_file_name']) {
            $page = get_page_by_title('Home Bed & Breakfast');
            $slider_array = array(
                get_template_directory()."/includes/imports/_sliders/Bed&Breakfast.zip",
            );
        } elseif ('Chalet' === $selected_import['import_file_name']) {
            $page = get_page_by_title('Home Chalet');
            $slider_array = array(
                get_template_directory()."/includes/imports/_sliders/Chalet.zip",
            );
        } elseif ('Christmas &amp; New Year' === $selected_import['import_file_name']) {
            $page = get_page_by_title('Christmas & New Year Home');
            $slider_array = array(
                get_template_directory()."/includes/imports/_sliders/Christmas-NewYear.zip",
                get_template_directory()."/includes/imports/_sliders/Christmas-Search.zip",
            );
        } elseif ('Cruise Operator' === $selected_import['import_file_name']) {
            $page = get_page_by_title('Home Cruise Operator');
            $slider_array = array(
                get_template_directory()."/includes/imports/_sliders/CruiseOperator.zip",
            );
        } elseif ('Eco Lodge' === $selected_import['import_file_name']) {
            $page = get_page_by_title('Eco Lodge Home');
            $slider_array = array(

            );            
        } elseif ('Event Festival' === $selected_import['import_file_name']) {
            $page = get_page_by_title('Event Festival Home');
            $slider_array = array(
                get_template_directory()."/includes/imports/_sliders/EventFestival.zip",
            );
        } elseif ('Exotic Tours' === $selected_import['import_file_name']) {
            $page = get_page_by_title('Home Exotic Tours');
        } elseif ('Hostel' === $selected_import['import_file_name']) {
            $page = get_page_by_title('Home hostel');
            $slider_array = array(
                get_template_directory()."/includes/imports/_sliders/Hostel.zip",
            );
        } elseif ('Hotel' === $selected_import['import_file_name']) {
            $page = get_page_by_title('Home Hotel');
            $slider_array = array(
                get_template_directory()."/includes/imports/_sliders/Hotel.zip",
            );
        } elseif ('Luxury Resort' === $selected_import['import_file_name']) {
            $page = get_page_by_title('Home resort');
            $slider_array = array(
                get_template_directory()."/includes/imports/_sliders/LuxuryResortTestimonials.zip",
                get_template_directory()."/includes/imports/_sliders/LuxuryResort.zip"
            );
        } elseif ('Motorhomes &amp; Campervans' === $selected_import['import_file_name']) {
            $page = get_page_by_title('Home Motor homes');
            $slider_array = array(
                get_template_directory()."/includes/imports/_sliders/MotorHomes&CamperVans.zip",
            );
        } elseif ('Rent a Car' === $selected_import['import_file_name']) {
            $page = get_page_by_title('Car rental home');
            $slider_array = array(
                get_template_directory()."/includes/imports/_sliders/RentACar.zip",
                get_template_directory()."/includes/imports/_sliders/RentACarCarousel.zip",
            );
        } elseif ('Rent a Car Premium' === $selected_import['import_file_name']) {
            $page = get_page_by_title('Rent A Car Premium Home');
            $slider_array = array(
                get_template_directory()."/includes/imports/_sliders/RentACarPremium.zip",
                get_template_directory()."/includes/imports/_sliders/RentACarCarousel.zip",
            );
        } elseif ('Ski Resort' === $selected_import['import_file_name']) {
            $page = get_page_by_title('Home Ski Resort');
            $slider_array = array(
                get_template_directory()."/includes/imports/_sliders/SkiResort.zip",
            );
        } elseif ('Sustainable Travel' === $selected_import['import_file_name']) {
            $page = get_page_by_title('Sustainable Travel Home');
            $slider_array = array(
                get_template_directory()."/includes/imports/_sliders/SustainableTravel.zip",
            );
        } elseif ('Tour Landing Page' === $selected_import['import_file_name']) {
            $page = get_page_by_title('Tour Splash Home');
            $slider_array = array(
                get_template_directory()."/includes/imports/_sliders/TourOperator.zip",
            );
        } elseif ('Tour Operator' === $selected_import['import_file_name']) {
            $page = get_page_by_title('Home Tour Operator');
            $slider_array = array(
                get_template_directory()."/includes/imports/_sliders/TourOperator.zip",
            );
        } elseif ('Travel Agency' === $selected_import['import_file_name']) {
            $page = get_page_by_title('Home Travel Agency');
            $slider_array = array(
                get_template_directory()."/includes/imports/_sliders/TravelAgency.zip",
            );
        } elseif ('Travel &amp; Tours' === $selected_import['import_file_name']) {
            $page = get_page_by_title('Home Travel Tours');
            $slider_array = array(
                get_template_directory()."/includes/imports/_sliders/Travel&Tours.zip",
            );
        } elseif ('Travel Guide' === $selected_import['import_file_name']) {
            $page = get_page_by_title('Travel Guide Home');
            $slider_array = array(
                get_template_directory()."/includes/imports/_sliders/TravelGuide.zip",
            );
        } elseif ('Vacation Rentals' === $selected_import['import_file_name']) {
            $page = get_page_by_title('Home Vacation Rentals');
            $slider_array = array(
                get_template_directory()."/includes/imports/_sliders/VacationRentals.zip",
            );
        } elseif ('Villa' === $selected_import['import_file_name']) {
            $page = get_page_by_title('Home Villa');
            $slider_array = array(
                get_template_directory()."/includes/imports/_sliders/Villa.zip",
                get_template_directory()."/includes/imports/_sliders/VillaTestimonials.zip",
            );
        } elseif ('Wedding Venue' === $selected_import['import_file_name']) {
            $page = get_page_by_title('Home wedding');
            $slider_array = array(
                get_template_directory()."/includes/imports/_sliders/Wedding.zip",
                get_template_directory()."/includes/imports/_sliders/WeddingTestimonials.zip",
            );
        }

        if (isset($page) && $page->ID > 0) {
            update_option('page_on_front', $page->ID);
            update_option('show_on_front', 'page');
        }

        if (class_exists('RevSlider')) {
            if (count($slider_array) > 0) {
                $slider = new RevSlider();
            
                foreach ($slider_array as $filepath) {
                    $slider->importSliderFromPost(true, true, $filepath);
                }
            }
        }
    }

    public function demo_import_files()
    {
        return array(
            array(
                'import_file_name'             => 'Default BookYourTravel',
                'local_import_file'            => trailingslashit(get_template_directory()) . 'includes/imports/default/bookyourtravel.WordPress.xml',
                'local_import_widget_file'     => trailingslashit(get_template_directory()) . 'includes/imports/default/bookyourtravel.Widgets.wie',
                'local_import_customizer_file' => trailingslashit(get_template_directory()) . 'includes/imports/default/bookyourtravel.Customizer.dat',
                'import_preview_image_url'     => trailingslashit(get_template_directory_uri()) . 'includes/imports/default/bookyourtravel.jpg',
                'import_notice'                => __('After you import this demo, you will have to setup the slider and menus separately.', 'bookyourtravel'),
                'preview_url'                  => 'https://themes.themeenergy.com/bookyourtravel/',
            ),
            array(
              'import_file_name'             => 'Agritourism',
              'local_import_file'            => trailingslashit(get_template_directory()) . 'includes/imports/agritourism/agritourism.WordPress.xml',
              'local_import_widget_file'     => trailingslashit(get_template_directory()) . 'includes/imports/agritourism/agritourism.Widgets.wie',
              'local_import_customizer_file' => trailingslashit(get_template_directory()) . 'includes/imports/agritourism/agritourism.Customizer.dat',
              'import_preview_image_url'     => trailingslashit(get_template_directory_uri()) . 'includes/imports/agritourism/agritourism.jpg',
              'import_notice'                => __('After you import this demo, you will have to setup the slider and menus separately.', 'bookyourtravel'),
              'preview_url'                  => 'https://themes.themeenergy.com/bookyourtravel/agritourism/',
            ),
            array(
              'import_file_name'             => 'Beach Resort',
              'local_import_file'            => trailingslashit(get_template_directory()) . 'includes/imports/beachresort/beachresort.WordPress.xml',
              'local_import_widget_file'     => trailingslashit(get_template_directory()) . 'includes/imports/beachresort/beachresort.Widgets.wie',
              'local_import_customizer_file' => trailingslashit(get_template_directory()) . 'includes/imports/beachresort/beachresort.Customizer.dat',
              'import_preview_image_url'     => trailingslashit(get_template_directory_uri()) . 'includes/imports/beachresort/beachresort.jpg',
              'import_notice'                => __('After you import this demo, you will have to setup the slider and menus separately.', 'bookyourtravel'),
              'preview_url'                  => 'https://themes.themeenergy.com/bookyourtravel/beach-resort/',
            ),
            array(
              'import_file_name'             => 'Bed &amp; Breakfast',
              'local_import_file'            => trailingslashit(get_template_directory()) . 'includes/imports/bedandbreakfast/bedandbreakfast.WordPress.xml',
              'local_import_widget_file'     => trailingslashit(get_template_directory()) . 'includes/imports/bedandbreakfast/bedandbreakfast.Widgets.wie',
              'local_import_customizer_file' => trailingslashit(get_template_directory()) . 'includes/imports/bedandbreakfast/bedandbreakfast.Customizer.dat',
              'import_preview_image_url'     => trailingslashit(get_template_directory_uri()) . 'includes/imports/bedandbreakfast/bedandbreakfast.jpg',
              'import_notice'                => __('After you import this demo, you will have to setup the slider and menus separately.', 'bookyourtravel'),
              'preview_url'                  => 'https://themes.themeenergy.com/bookyourtravel/bed-and-breakfast/',
            ),
            array(
              'import_file_name'             => 'Chalet',
              'local_import_file'            => trailingslashit(get_template_directory()) . 'includes/imports/chalet/chalet.WordPress.xml',
              'local_import_widget_file'     => trailingslashit(get_template_directory()) . 'includes/imports/chalet/chalet.Widgets.wie',
              'local_import_customizer_file' => trailingslashit(get_template_directory()) . 'includes/imports/chalet/chalet.Customizer.dat',
              'import_preview_image_url'     => trailingslashit(get_template_directory_uri()) . 'includes/imports/chalet/chalet.jpg',
              'import_notice'                => __('After you import this demo, you will have to setup the slider and menus separately.', 'bookyourtravel'),
              'preview_url'                  => 'https://themes.themeenergy.com/bookyourtravel/chalet/',
            ),
            array(
                'import_file_name'             => 'Christmas &amp; New Year',
                'local_import_file'            => trailingslashit(get_template_directory()) . 'includes/imports/christmasnewyear/christmasnewyear.WordPress.xml',
                'local_import_widget_file'     => trailingslashit(get_template_directory()) . 'includes/imports/christmasnewyear/christmasnewyear.Widgets.wie',
                'local_import_customizer_file' => trailingslashit(get_template_directory()) . 'includes/imports/christmasnewyear/christmasnewyear.Customizer.dat',
                'import_preview_image_url'     => trailingslashit(get_template_directory_uri()) . 'includes/imports/christmasnewyear/christmasnewyear.jpg',
                'import_notice'                => __('After you import this demo, you will have to setup the slider and menus separately.', 'bookyourtravel'),
                'preview_url'                  => 'https://themes.themeenergy.com/bookyourtravel/christmas-new-year/',
            ),
            array(
              'import_file_name'             => 'Cruise Operator',
              'local_import_file'            => trailingslashit(get_template_directory()) . 'includes/imports/cruiseoperator/cruiseoperator.WordPress.xml',
              'local_import_widget_file'     => trailingslashit(get_template_directory()) . 'includes/imports/cruiseoperator/cruiseoperator.Widgets.wie',
              'local_import_customizer_file' => trailingslashit(get_template_directory()) . 'includes/imports/cruiseoperator/cruiseoperator.Customizer.dat',
              'import_preview_image_url'     => trailingslashit(get_template_directory_uri()) . 'includes/imports/cruiseoperator/cruiseoperator.jpg',
              'import_notice'                => __('After you import this demo, you will have to setup the slider and menus separately.', 'bookyourtravel'),
              'preview_url'                  => 'https://themes.themeenergy.com/bookyourtravel/cruise-operator/',
			),
            array(
				'import_file_name'             => 'Eco Lodge',
				'local_import_file'            => trailingslashit(get_template_directory()) . 'includes/imports/ecolodge/ecolodge.WordPress.xml',
				'local_import_widget_file'     => trailingslashit(get_template_directory()) . 'includes/imports/ecolodge/ecolodge.Widgets.wie',
				'local_import_customizer_file' => trailingslashit(get_template_directory()) . 'includes/imports/ecolodge/ecolodge.Customizer.dat',
				'import_preview_image_url'     => trailingslashit(get_template_directory_uri()) . 'includes/imports/ecolodge/ecolodge.jpg',
				'import_notice'                => __('After you import this demo, you will have to setup the slider and menus separately.', 'bookyourtravel'),
				'preview_url'                  => 'https://themes.themeenergy.com/bookyourtravel/eco-lodge/',
			  ),			
            array(
              'import_file_name'             => 'Event Festival',
              'local_import_file'            => trailingslashit(get_template_directory()) . 'includes/imports/eventfestival/eventfestival.WordPress.xml',
              'local_import_widget_file'     => trailingslashit(get_template_directory()) . 'includes/imports/eventfestival/eventfestival.Widgets.wie',
              'local_import_customizer_file' => trailingslashit(get_template_directory()) . 'includes/imports/eventfestival/eventfestival.Customizer.dat',
              'import_preview_image_url'     => trailingslashit(get_template_directory_uri()) . 'includes/imports/eventfestival/eventfestival.jpg',
              'import_notice'                => __('After you import this demo, you will have to setup the slider and menus separately.', 'bookyourtravel'),
              'preview_url'                  => 'https://themes.themeenergy.com/bookyourtravel/event-festival/',
            ),
            array(
              'import_file_name'             => 'Exotic Tours',
              'local_import_file'            => trailingslashit(get_template_directory()) . 'includes/imports/exotictours/exotictours.WordPress.xml',
              'local_import_widget_file'     => trailingslashit(get_template_directory()) . 'includes/imports/exotictours/exotictours.Widgets.wie',
              'local_import_customizer_file' => trailingslashit(get_template_directory()) . 'includes/imports/exotictours/exotictours.Customizer.dat',
              'import_preview_image_url'     => trailingslashit(get_template_directory_uri()) . 'includes/imports/exotictours/exotictours.jpg',
              'import_notice'                => __('After you import this demo, you will have to setup the slider and menus separately.', 'bookyourtravel'),
              'preview_url'                  => 'https://themes.themeenergy.com/bookyourtravel/exotic-tours/',
            ),
            array(
              'import_file_name'             => 'Hostel',
              'local_import_file'            => trailingslashit(get_template_directory()) . 'includes/imports/hostel/hostel.WordPress.xml',
              'local_import_widget_file'     => trailingslashit(get_template_directory()) . 'includes/imports/hostel/hostel.Widgets.wie',
              'local_import_customizer_file' => trailingslashit(get_template_directory()) . 'includes/imports/hostel/hostel.Customizer.dat',
              'import_preview_image_url'     => trailingslashit(get_template_directory_uri()) . 'includes/imports/hostel/hostel.jpg',
              'import_notice'                => __('After you import this demo, you will have to setup the slider and menus separately.', 'bookyourtravel'),
              'preview_url'                  => 'https://themes.themeenergy.com/bookyourtravel/hostel/',
            ),
            array(
              'import_file_name'             => 'Hotel',
              'local_import_file'            => trailingslashit(get_template_directory()) . 'includes/imports/hotel/hotel.WordPress.xml',
              'local_import_widget_file'     => trailingslashit(get_template_directory()) . 'includes/imports/hotel/hotel.Widgets.wie',
              'local_import_customizer_file' => trailingslashit(get_template_directory()) . 'includes/imports/hotel/hotel.Customizer.dat',
              'import_preview_image_url'     => trailingslashit(get_template_directory_uri()) . 'includes/imports/hotel/hotel.jpg',
              'import_notice'                => __('After you import this demo, you will have to setup the slider and menus separately.', 'bookyourtravel'),
              'preview_url'                  => 'https://themes.themeenergy.com/bookyourtravel/hotel/',
            ),
            array(
              'import_file_name'             => 'Luxury Resort',
              'local_import_file'            => trailingslashit(get_template_directory()) . 'includes/imports/luxuryresort/luxuryresort.WordPress.xml',
              'local_import_widget_file'     => trailingslashit(get_template_directory()) . 'includes/imports/luxuryresort/luxuryresort.Widgets.wie',
              'local_import_customizer_file' => trailingslashit(get_template_directory()) . 'includes/imports/luxuryresort/luxuryresort.Customizer.dat',
              'import_preview_image_url'     => trailingslashit(get_template_directory_uri()) . 'includes/imports/luxuryresort/luxuryresort.jpg',
              'import_notice'                => __('After you import this demo, you will have to setup the slider and menus separately.', 'bookyourtravel'),
              'preview_url'                  => 'https://themes.themeenergy.com/bookyourtravel/resort/',
            ),
            array(
              'import_file_name'             => 'Motorhomes &amp; Campervans',
              'local_import_file'            => trailingslashit(get_template_directory()) . 'includes/imports/motorhomescampervans/motorhomescampervans.WordPress.xml',
              'local_import_widget_file'     => trailingslashit(get_template_directory()) . 'includes/imports/motorhomescampervans/motorhomescampervans.Widgets.wie',
              'local_import_customizer_file' => trailingslashit(get_template_directory()) . 'includes/imports/motorhomescampervans/motorhomescampervans.Customizer.dat',
              'import_preview_image_url'     => trailingslashit(get_template_directory_uri()) . 'includes/imports/motorhomescampervans/motorhomescampervans.jpg',
              'import_notice'                => __('After you import this demo, you will have to setup the slider and menus separately.', 'bookyourtravel'),
              'preview_url'                  => 'https://themes.themeenergy.com/bookyourtravel/motorhomes-campervans/',
            ),
            array(
              'import_file_name'             => 'Rent a Car',
              'local_import_file'            => trailingslashit(get_template_directory()) . 'includes/imports/rentacar/rentacar.WordPress.xml',
              'local_import_widget_file'     => trailingslashit(get_template_directory()) . 'includes/imports/rentacar/rentacar.Widgets.wie',
              'local_import_customizer_file' => trailingslashit(get_template_directory()) . 'includes/imports/rentacar/rentacar.Customizer.dat',
              'import_preview_image_url'     => trailingslashit(get_template_directory_uri()) . 'includes/imports/rentacar/rentacar.jpg',
              'import_notice'                => __('After you import this demo, you will have to setup the slider and menus separately.', 'bookyourtravel'),
              'preview_url'                  => 'https://themes.themeenergy.com/bookyourtravel/rent-a-car/',
            ),
            array(
              'import_file_name'             => 'Rent a Car Premium',
              'local_import_file'            => trailingslashit(get_template_directory()) . 'includes/imports/rentacarpremium/rentacarpremium.WordPress.xml',
              'local_import_widget_file'     => trailingslashit(get_template_directory()) . 'includes/imports/rentacarpremium/rentacarpremium.Widgets.wie',
              'local_import_customizer_file' => trailingslashit(get_template_directory()) . 'includes/imports/rentacarpremium/rentacarpremium.Customizer.dat',
              'import_preview_image_url'     => trailingslashit(get_template_directory_uri()) . 'includes/imports/rentacarpremium/rentacarpremium.jpg',
              'import_notice'                => __('After you import this demo, you will have to setup the slider and menus separately.', 'bookyourtravel'),
              'preview_url'                  => 'https://themes.themeenergy.com/bookyourtravel/rent-a-car-premium/',
            ),
            array(
              'import_file_name'             => 'Ski Resort',
              'local_import_file'            => trailingslashit(get_template_directory()) . 'includes/imports/skiresort/skiresort.WordPress.xml',
              'local_import_widget_file'     => trailingslashit(get_template_directory()) . 'includes/imports/skiresort/skiresort.Widgets.wie',
              'local_import_customizer_file' => trailingslashit(get_template_directory()) . 'includes/imports/skiresort/skiresort.Customizer.dat',
              'import_preview_image_url'     => trailingslashit(get_template_directory_uri()) . 'includes/imports/skiresort/skiresort.jpg',
              'import_notice'                => __('After you import this demo, you will have to setup the slider and menus separately.', 'bookyourtravel'),
              'preview_url'                  => 'https://themes.themeenergy.com/bookyourtravel/ski-resort/',
            ),
            array(
                'import_file_name'             => 'Sustainable Travel',
                'local_import_file'            => trailingslashit(get_template_directory()) . 'includes/imports/sustainabletravel/sustainabletravel.WordPress.xml',
                'local_import_widget_file'     => trailingslashit(get_template_directory()) . 'includes/imports/sustainabletravel/sustainabletravel.Widgets.wie',
                'local_import_customizer_file' => trailingslashit(get_template_directory()) . 'includes/imports/sustainabletravel/sustainabletravel.Customizer.dat',
                'import_preview_image_url'     => trailingslashit(get_template_directory_uri()) . 'includes/imports/sustainabletravel/sustainabletravel.jpg',
                'import_notice'                => __('After you import this demo, you will have to setup the slider and menus separately.', 'bookyourtravel'),
                'preview_url'                  => 'https://themes.themeenergy.com/bookyourtravel/sustainable-travel/',
              ),
            array(
              'import_file_name'             => 'Tour Landing Page',
              'local_import_file'            => trailingslashit(get_template_directory()) . 'includes/imports/tourlandingpage/tourlandingpage.WordPress.xml',
              'local_import_widget_file'     => trailingslashit(get_template_directory()) . 'includes/imports/tourlandingpage/tourlandingpage.Widgets.wie',
              'local_import_customizer_file' => trailingslashit(get_template_directory()) . 'includes/imports/tourlandingpage/tourlandingpage.Customizer.dat',
              'import_preview_image_url'     => trailingslashit(get_template_directory_uri()) . 'includes/imports/tourlandingpage/tourlandingpage.jpg',
              'import_notice'                => __('After you import this demo, you will have to setup the slider and menus separately.', 'bookyourtravel'),
              'preview_url'                  => 'https://themes.themeenergy.com/bookyourtravel/tour-landing-page/',
            ),
            array(
              'import_file_name'             => 'Tour Operator',
              'local_import_file'            => trailingslashit(get_template_directory()) . 'includes/imports/touroperator/touroperator.WordPress.xml',
              'local_import_widget_file'     => trailingslashit(get_template_directory()) . 'includes/imports/touroperator/touroperator.Widgets.wie',
              'local_import_customizer_file' => trailingslashit(get_template_directory()) . 'includes/imports/touroperator/touroperator.Customizer.dat',
              'import_preview_image_url'     => trailingslashit(get_template_directory_uri()) . 'includes/imports/touroperator/touroperator.jpg',
              'import_notice'                => __('After you import this demo, you will have to setup the slider and menus separately.', 'bookyourtravel'),
              'preview_url'                  => 'https://themes.themeenergy.com/bookyourtravel/tour-operator/',
            ),
            array(
              'import_file_name'             => 'Travel Agency',
              'local_import_file'            => trailingslashit(get_template_directory()) . 'includes/imports/travelagency/travelagency.WordPress.xml',
              'local_import_widget_file'     => trailingslashit(get_template_directory()) . 'includes/imports/travelagency/travelagency.Widgets.wie',
              'local_import_customizer_file' => trailingslashit(get_template_directory()) . 'includes/imports/travelagency/travelagency.Customizer.dat',
              'import_preview_image_url'     => trailingslashit(get_template_directory_uri()) . 'includes/imports/travelagency/travelagency.jpg',
              'import_notice'                => __('After you import this demo, you will have to setup the slider and menus separately.', 'bookyourtravel'),
              'preview_url'                  => 'https://themes.themeenergy.com/bookyourtravel/travel-agency/',
            ),
            array(
              'import_file_name'             => 'Travel &amp; Tours',
              'local_import_file'            => trailingslashit(get_template_directory()) . 'includes/imports/travelandtours/travelandtours.WordPress.xml',
              'local_import_widget_file'     => trailingslashit(get_template_directory()) . 'includes/imports/travelandtours/travelandtours.Widgets.wie',
              'local_import_customizer_file' => trailingslashit(get_template_directory()) . 'includes/imports/travelandtours/travelandtours.Customizer.dat',
              'import_preview_image_url'     => trailingslashit(get_template_directory_uri()) . 'includes/imports/travelandtours/travelandtours.jpg',
              'import_notice'                => __('After you import this demo, you will have to setup the slider and menus separately.', 'bookyourtravel'),
              'preview_url'                  => 'https://themes.themeenergy.com/bookyourtravel/travel-tours/',
            ),
            array(
              'import_file_name'             => 'Travel Guide',
              'local_import_file'            => trailingslashit(get_template_directory()) . 'includes/imports/travelguide/travelguide.WordPress.xml',
              'local_import_widget_file'     => trailingslashit(get_template_directory()) . 'includes/imports/travelguide/travelguide.Widgets.wie',
              'local_import_customizer_file' => trailingslashit(get_template_directory()) . 'includes/imports/travelguide/travelguide.Customizer.dat',
              'import_preview_image_url'     => trailingslashit(get_template_directory_uri()) . 'includes/imports/travelguide/travelguide.jpg',
              'import_notice'                => __('After you import this demo, you will have to setup the slider and menus separately.', 'bookyourtravel'),
              'preview_url'                  => 'https://themes.themeenergy.com/bookyourtravel/travel-guide/',
            ),
            array(
              'import_file_name'             => 'Vacation Rentals',
              'local_import_file'            => trailingslashit(get_template_directory()) . 'includes/imports/vacationrentals/vacationrentals.WordPress.xml',
              'local_import_widget_file'     => trailingslashit(get_template_directory()) . 'includes/imports/vacationrentals/vacationrentals.Widgets.wie',
              'local_import_customizer_file' => trailingslashit(get_template_directory()) . 'includes/imports/vacationrentals/vacationrentals.Customizer.dat',
              'import_preview_image_url'     => trailingslashit(get_template_directory_uri()) . 'includes/imports/vacationrentals/vacationrentals.jpg',
              'import_notice'                => __('After you import this demo, you will have to setup the slider and menus separately.', 'bookyourtravel'),
              'preview_url'                  => 'https://themes.themeenergy.com/bookyourtravel/vacation-rentals/',
            ),
            array(
              'import_file_name'             => 'Villa',
              'local_import_file'            => trailingslashit(get_template_directory()) . 'includes/imports/villa/villa.WordPress.xml',
              'local_import_widget_file'     => trailingslashit(get_template_directory()) . 'includes/imports/villa/villa.Widgets.wie',
              'local_import_customizer_file' => trailingslashit(get_template_directory()) . 'includes/imports/villa/villa.Customizer.dat',
              'import_preview_image_url'     => trailingslashit(get_template_directory_uri()) . 'includes/imports/villa/villa.jpg',
              'import_notice'                => __('After you import this demo, you will have to setup the slider and menus separately.', 'bookyourtravel'),
              'preview_url'                  => 'https://themes.themeenergy.com/bookyourtravel/villa/',
            ),
            array(
              'import_file_name'             => 'Wedding Venue',
              'local_import_file'            => trailingslashit(get_template_directory()) . 'includes/imports/weddingvenue/weddingvenue.WordPress.xml',
              'local_import_widget_file'     => trailingslashit(get_template_directory()) . 'includes/imports/weddingvenue/weddingvenue.Widgets.wie',
              'local_import_customizer_file' => trailingslashit(get_template_directory()) . 'includes/imports/weddingvenue/weddingvenue.Customizer.dat',
              'import_preview_image_url'     => trailingslashit(get_template_directory_uri()) . 'includes/imports/weddingvenue/weddingvenue.jpg',
              'import_notice'                => __('After you import this demo, you will have to setup the slider and menus separately.', 'bookyourtravel'),
              'preview_url'                  => 'https://themes.themeenergy.com/bookyourtravel/wedding/',
            ),
        );
    }

    public function my_custom_sizes($sizes)
    {
        return array_merge($sizes, array(
            'byt-featured' => __('Featured Image', 'bookyourtravel'),
        ));
    }

    public function customize_excerpt($excerpt, $arr)
    {
        $short_description = '';
        if (count($arr) > 0) {
            $post = $arr["post"];
            $post_type = $post->post_type;
            if ($post_type == "accommodation" ||
                $post_type == "cruise" ||
                $post_type == "car_rental" ||
                $post_type == "location" ||
                $post_type == "tour") {
                $short_description = get_post_meta($post->ID, $post->post_type . "_short_description", true);
            }
        }

        return !empty($short_description) ? $short_description : $excerpt;
    }

    public function get_the_archive_title($title)
    {
        $taxonomy = get_query_var('taxonomy');
        if (is_category()) {
            $title = single_cat_title('', false);
        } elseif (is_tag()) {
            $title = single_tag_title('', false);
        } elseif (is_author()) {
            $title = '<span class="vcard">' . get_the_author() . '</span>' ;
        } elseif ($taxonomy == 'acc_tag' || $taxonomy == 'accommodation_type' ||
            $taxonomy == 'tour_tag' || $taxonomy == 'tour_type' ||
            $taxonomy == 'cruise_tag' || $taxonomy == 'cruise_type' ||
            $taxonomy == 'cruise_duration' || $taxonomy == 'tour_duration' ||
            $taxonomy == 'car_rental_tag' || $taxonomy == 'car_type' ||
            $taxonomy == 'location_type' || $taxonomy == 'location_tag' ||
            $taxonomy == 'facility') {
            $title = single_cat_title('', false);
        }

        return $title;
    }

    public function remove_type_attributes($tag, $handle)
    {
        return preg_replace("/type=['\"]text\/(javascript|css)['\"]/", '', $tag);
    }

    public function revslider_dashboard_elements_remove_activation_widget($dashboard_array)
    {
        if (isset($dashboard_array['rs-validation'])) {
            unset($dashboard_array['rs-validation']);
        }
        return $dashboard_array;
    }

    public function filter_post_thumbnail_html($html, $post_id, $post_thumbnail_id, $size, $attr)
    {
        // If there is no post thumbnail,
        // Return a default image
        require_once(ABSPATH . WPINC . '/class-simplepie.php');
        $simplePie = new SimplePie();

        $width = 0;
        $height = 0;

        if ($size == 'thumbnail' || $size == 'thumb') {
            $width = get_option('thumbnail_size_w');
            $height = get_option('thumbnail_size_h');
        } elseif ($size == 'medium') {
            $width = get_option('medium_size_w');
            $height = get_option('medium_size_h');
        } elseif ($size == 'large') {
            $width = get_option('large_size_w');
            $height = get_option('large_size_h');
        } elseif ($size == 'byt-featured') {
            $width = $simplePie->get_image_width("byt-featured");
            $height = $simplePie->get_image_height("byt-featured");
        } else {
            $width = $simplePie->get_image_width("large");
            $height = $simplePie->get_image_height("large");
        }

        if ('' == $html) {
            return sprintf(
                '<img src="%s" height="%s" width="%s" title="%s" />',
                BookYourTravel_Theme_Utils::get_file_uri('/images/uploads/img.jpg'),
                $width,
                $height,
                get_the_title()
            );
        }
        // Else, return the post thumbnail
        return $html;
    }
}

// store the instance in a variable to be retrieved later and call init
$bookyourtravel_theme_filters = BookYourTravel_Theme_Filters::get_instance();
