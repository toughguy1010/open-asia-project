<?php
/**
 * BookYourTravel_Theme_Schemes class
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class BookYourTravel_Theme_Schemes
{
    public static function get_default_scheme()
    {
		global $bookyourtravel_theme_globals;
	
        $bc_copyright_default = of_get_option('copyright_footer', __('&copy; 2013 - 2023 ThemeEnergy.com', 'bookyourtravel'));
        $bc_contact_number_default = of_get_option('contact_phone_number', __('1 - 555 - 555 - 555', 'bookyourtravel'));

        $scheme = array(
            'label' => 'Default',
            'id' => 'default',
            'sections' => array(
				'byt_custom_posts' => array(
					'title' => esc_html__('Custom Posts Styles', 'bookyourtravel'),
					'description' => esc_html__('Style settings for Book Your Travel custom post single views', 'bookyourtravel'),					
					'priority' => 146,
					'settings' => array(					
                        'byt_custom_posts_tab_background' => array(
                            'type' => 'color',
							'property' => 'background',
							'color' => '#C1B6AE',
							'selector' => '.single .inner-nav li',
							'direction' => 'top',
                            'label' => __('Tab background color', 'bookyourtravel'),
                            'description' => __('Select tab background color', 'bookyourtravel'),
                        ),
                        'byt_custom_posts_tab_background_hover' => array(
                            'type' => 'color',
							'property' => 'background',
							'color' => '#5fc8c2',
							'selector' => '.single .inner-nav li:hover, .single .inner-nav li.active, .single .inner-nav li.active:hover, .single .inner-nav li.current-menu-item, .single .inner-nav li.current-menu-item:hover',
                            'label' => __('Tab hover/active background color', 'bookyourtravel'),
                            'description' => __('Select tab hover/active background color', 'bookyourtravel'),
                            'dependents' => array(
                                'select_nav_item_arrow_color' => array(
                                    'color' => '#5fc8c2',
                                    'selector' => '.single .inner-nav li.active a:after, .single .inner-nav li.current-menu-item a:after',
                                    'property' => 'border-left-color',
                                ),
                            ),
                        ),					
						'byt_custom_posts_tab_text_color' => array(
							'type' => 'color',
							'color' => '#fff',
							'force' => '1',
							'selector' => '.single .main .inner-nav li:not(.active):not(:hover) a',							
							'label' => __('Tab text color', 'bookyourtravel'),
							'description' => __('Select tab text color', 'bookyourtravel'),
						),
						'byt_custom_posts_active_tab_text_color' => array(
							'type' => 'color',
							'color' => '#fff',
							'force' => '1',	
							'selector' => '.single .main .inner-nav li a:hover,.single .main .inner-nav li:hover a,.single .main .inner-nav li.active a, .single .main .inner-nav li.active a:hover, .single .main .inner-nav li.current-menu-item a, .single .main .inner-nav li.current-menu-item a:hover,.single .main .inner-nav li a:focus, .single .main .inner-nav li:focus a, .single .main .inner-nav li.active a:hover, .single .main .inner-nav li.active a:focus, .single .main .inner-nav li.current-menu-item a:hover, .single .main .inner-nav li.current-menu-item a:focus',							
							'label' => __('Active tab text color', 'bookyourtravel'),
							'description' => __('Select active tab text color', 'bookyourtravel'),
						),
						'byt_custom_posts_tab_icon_color' => array(
							'type' => 'color',
							'color' => '#fff',
							'force' => '1',	
							'selector' => '.single .inner-nav li a span.material-icons',							
							'label' => __('Tab icon color', 'bookyourtravel'),
							'description' => __('Select tab icon color', 'bookyourtravel'),
						),
						'byt_custom_posts_tab_active_icon_color' => array(
							'type' => 'color',
							'color' => '#fff',
							'force' => '1',	
							'selector' => '.single .inner-nav li.active a span.material-icons,.single .inner-nav li a:hover span.material-icons',							
							'label' => __('Active tab icon color', 'bookyourtravel'),
							'description' => __('Select active tab icon color', 'bookyourtravel'),
						),
						'availability_calendar_unavailable_dates' => array(
                            'type' => 'color',
                            'color' => '#e8e8e8',
                            'selector' => '.ui-datepicker .ui-datepicker-unselectable,.f-item .unavailable span,.ui-datepicker-multi td',
                            'property' => 'background',
                            'label' => __('Calendar unavailable dates background', 'bookyourtravel'),
                        ),
						'availability_calendar_unavailable_dates_text_color' => array(
                            'type' => 'color',
                            'color' => '#454545',
                            'selector' => '.ui-datepicker td span, .ui-datepicker td a ',
                            'label' => __('Calendar unavailable dates text color', 'bookyourtravel'),
                        ),
						'availability_calendar_available_dates' => array(
                            'type' => 'color',
                            'color' => '#4CAF50',
                            'selector' => '.ui-datepicker .dp-highlight a.ui-state-default,.f-item .available span,.ui-datepicker .dp-highlight.dp-highlight-start-date:after,.ui-datepicker .dp-highlight.dp-highlight-end-date:after',
                            'property' => 'background-color',
                            'label' => __('Calendar available dates background', 'bookyourtravel'),
                        ),
						'availability_calendar_available_dates_text_color' => array(
                            'type' => 'color',
                            'color' => '#fff',
                            'selector' => '.ui-datepicker .dp-highlight a.ui-state-default,.ui-datepicker .dp-highlight-end-date span.ui-state-default',
                            'label' => __('Calendar available dates text color', 'bookyourtravel'),
                        ),
						'availability_calendar_selected_dates' => array(
                            'type' => 'color',
                            'color' => '#ffc107',
							'force' => '1',
                            'selector' => '.ui-datepicker .dp-highlight a.ui-state-hover, .ui-datepicker .dp-highlight-selected span,.f-item .selected span,.ui-datepicker .dp-highlight.dp-highlight-selected.dp-highlight-start-date:after,.ui-datepicker .dp-highlight.dp-highlight-selected.dp-highlight-end-date:after,.ui-datepicker td:not(.ui-datepicker-unselectable) a.ui-state-hover',
                            'property' => 'background-color',
                            'label' => __('Calendar selected dates background', 'bookyourtravel'),
                        ),
						'availability_calendar_selected_dates_text_color' => array(
                            'type' => 'color',
                            'color' => '#fff',
							'force' => '1',
                            'selector' => '.ui-datepicker .dp-highlight a.ui-state-hover, .ui-datepicker .dp-highlight-selected span,.ui-datepicker td:not(.ui-datepicker-unselectable) a.ui-state-hover',
                            'label' => __('Calendar selected dates text color', 'bookyourtravel'),
                        ),
						'location_ribbons_bg_color' => array(
                            'type' => 'color',
                            'color' => '#baaca3',
							'property' => 'background-color',
                            'selector' => '.location_item .ribbon',
                            'label' => __('Location ribbons background color', 'bookyourtravel'),
							'dependents' => array(
								'ribbon_left_triangle' => array(
									'color' => '#baaca3',
									'selector' => '.location_item .ribbon:before',
									'property' => 'border-right-color',
								),
								'ribbon_right_triangle' => array(
									'color' => '#baaca3',
									'selector' => '.location_item .ribbon:after',
									'property' => 'border-top-color',
								),
								'ribbon_fullwidth_triangle' => array(
									'color' => '#baaca3',
									'selector' => '.location_item.full-width .ribbon:before',
									'property' => 'border-left-color',
								),
							),
                        ),
						'location_ribbons_bg_hover_color' => array(
                            'type' => 'color',
                            'color' => '#5FC8C2',
							'property' => 'background-color',
                            'selector' => '.location_item .ribbon:focus,.location_item .ribbon:hover',
                            'label' => __('Location ribbons background hover color', 'bookyourtravel'),
							'dependents' => array(
								'ribbon_arrows' => array(
									'color' => '#5FC8C2',
									'selector' => '.location_item .ribbon:focus:before,.location_item .ribbon:focus:after,.location_item .ribbon:hover:before,.location_item .ribbon:hover:after',
									'property' => 'border-color',
								),
							),
                        ),
						'content_leftright_padding' => array(
							'type' => 'custom_slider',
							'force' => '1',
							'min' => 0,
							'max' => 100,
							'step' => 5,
							'default' => 20,
							'selector' => '.tab-content > article,.static-content,.blog .entry-header,.page-template-page-post-list .entry-header,.single .entry-header,.single .entry-content,.blog .entry-content,.page-template-page-post-list .post .post-content',
							'property' => 'padding-left,padding-right',
							'label' => __('Content left/right padding', 'bookyourtravel'),
							'description' => __('Select content left/right padding on single post type pages', 'bookyourtravel'),        
						),
						'content_topbottom_padding' => array(
							'type' => 'custom_slider',
							'force' => '1',
							'min' => 0,
							'max' => 100,
							'step' => 5,
							'default' => 20,
							'selector' => '.tab-content > article,.static-content',
							'property' => 'padding-top,padding-bottom',
							'label' => __('Content top/bottom padding', 'bookyourtravel'),
							'description' => __('Select content top/bottom padding on single post type pages', 'bookyourtravel'),        
						),
						'item_cards_border_radius' => array(
                            'type' => 'custom_slider',
							'min' => 0,
							'max' => 50,
							'step' => 1,
							'default' => 0,
							'property' => 'text-border-radius',
                            'selector' => '.deals > .row > article > div,.destinations > .row > article > div,.offers > .row > article > div,.deals > .row > article > div > a > figure img,.destinations > .row > article > div > a > figure img,.offers > .row > article > div > a > figure img, .single-card, .single-card img, .single-card > div, .single-card .details',
                            'label' => __('Item cards border radius', 'bookyourtravel'),
                        ),
						
						'byt_location_single_layout' => array(
                            'type' => 'single_post_layout_selector',
                            'label' => __('Location single tabs layout', 'bookyourtravel'),
                            'description' => __('Select location post type single view tabs layout', 'bookyourtravel'),
                        ),
                        'byt_location_single_sidebar_position' => array(
                            'type' => 'single_post_sidebar_selector',
                            'label' => __('Location single sidebar position', 'bookyourtravel'),
                            'description' => __('Select location post type single view sidebar position', 'bookyourtravel'),
                        )
					)
				),			
                'byt_widgets' => array(
					'title' => esc_html__('Widget Styles', 'bookyourtravel'),
					'description' => esc_html__('General style settings for Book Your Travel theme widgets', 'bookyourtravel'),					
					'priority' => 140,
					'settings' => array(
                        'byt_widget_background_color' => array(
                            'type' => 'color',
							'property' => 'background-color',
							'color' => '#fff',
							'selector' => '.main aside .widget,.tags li:before',
                            'label' => __('Widget background color', 'bookyourtravel'),
                            'description' => __('Select widgets background color', 'bookyourtravel'),
                        ),
						'byt_widget_text_color' => array(
                            'type' => 'color',
							'color' => '#454545',
							'force' => '1',	
							'selector' => 'aside .widget',							
                            'label' => __('Widget text color', 'bookyourtravel'),
                            'description' => __('Select widgets text color', 'bookyourtravel'),
                        ),
						'byt_widget_heading_text_color' => array(
                            'type' => 'color',
							'color' => '#858585',
							'selector' => 'aside .widget h4, aside .widget h2, aside .widget h5,.sort-by h3',							
                            'label' => __('Widget heading text color', 'bookyourtravel'),
                            'description' => __('Select widgets heading text color', 'bookyourtravel'),
                        ),
						'byt_widget_box_shadow_opacity' => array(
                            'type' => 'custom_slider',
							'min' => 0,
							'max' => 1,
							'step' => 0.1,
							'default' => 0.2,
							'property' => 'widget-box-shadow-opacity',
							'selector' => '.main aside .widget, .deals > .row > article > div,.destinations > .row > article > div,.offers > .row > article > div, .single-card,.testimonials,.home-footer-sidebar .widget > div,.hero-sidebar .byt-widget-search-inner',
                            'label' => __('Widget box shadow opacity', 'bookyourtravel'),
                            'description' => __('Select widget box shadow opacity value', 'bookyourtravel'),						
						),
						'above_footer_sidebar_background_color' => array(
                            'type' => 'color',
                            'color' => '#e8e8e8',
                            'selector' => '.above-footer-sidebar',
                            'property' => 'background-color',
                            'label' => __('Above footer sidebar background', 'bookyourtravel'),
                        ),
						'above_footer_sidebar_widget_background_color' => array(
                            'type' => 'color',
                            'color' => '#e8e8e8',
                            'selector' => '.above-footer-sidebar .widget > div',
                            'property' => 'background-color',
                            'label' => __('Above footer sidebar widget background', 'bookyourtravel'),
                        ),
						'above_footer_sidebar_widget_text_color' => array(
                            'type' => 'color',
                            'color' => '#555',
                            'selector' => '.above-footer-sidebar .widget > div',
                            'property' => 'color',
                            'label' => __('Above footer sidebar widget text color', 'bookyourtravel'),
                        ),
						'home_footer_sidebar_widget_background_color' => array(
                            'type' => 'color',
                            'color' => '#fff',
                            'selector' => '.home-footer-sidebar .widget > div',
                            'property' => 'background-color',
                            'label' => __('Home footer sidebar widget background', 'bookyourtravel'),
                        ),
						'home_footer_sidebar_widget_text_color' => array(
                            'type' => 'color',
                            'color' => '#555',
                            'selector' => '.home-footer-sidebar .widget > div',
                            'property' => 'color',
                            'label' => __('Home footer sidebar widget text color', 'bookyourtravel'),
                        )
					)
				),	
                'byt_general' => array(
					'title' => esc_html__('General', 'bookyourtravel'),
					'description' => esc_html__('General style settings for Book Your Travel theme elements', 'bookyourtravel'),					
					'priority' => 130,
					'settings' => array(
                        'website_layout' => array(
                            'type' => 'layout_selector',
                            'label' => __('Website layout', 'bookyourtravel'),
                            'description' => __('Select website layout', 'bookyourtravel'),
                        ),
						'hide_breadcrumbs' => array(
                            'type' => 'yes_no_checkbox',
                            'label' => __('Hide breadcrumbs?', 'bookyourtravel'),
                            'description' => __('Hide the breadcrumbs control on all site pages?', 'bookyourtravel'),
                        ),
                        'links_and_other_color' => array(
                            'type' => 'color',
							'color' => '#41AFAA',
							'selector' => 'a, .tab-content h4, .home-footer-sidebar .widget > div h4, .tab-content .room-types .meta h3, .error-type h1,aside .widget h5,.ico:before,blockquote:before,.req,.woocommerce form .form-row .required,p.number:before,.tab-content .destinations > .row > article.full-width > div .details .ribbon .half > a:before',
                            'label' => __('Links and other colored elements color', 'bookyourtravel'),
                            'description' => __('Select color of links and other colored text', 'bookyourtravel'),
                        ),	
                        'links_hover_color' => array(
                            'type' => 'color',
							'color' => '#41AFAA',
							'selector' => 'a:hover, h1 a:hover, h2 a:hover, h3 a:hover, h4 a:hover, h5 a:hover, h6 a:hover,.breadcrumbs a:hover,a:focus, h1 a:focus, h2 a:focus, h3 a:focus, h4 a:focus, h5 a:focus, h6 a:focus,.breadcrumbs a:focus',
                            'label' => __('Links hover color', 'bookyourtravel'),
                            'description' => __('Select color of links on hover', 'bookyourtravel'),
                        ),	
                        'colored_ui_elements' => array(
                            'type' => 'color',
							'color' => '#5fc8c2',
							'property' => 'background',
							'selector' => '.view-type .active,.chart dd span, .infoBox, .ui-slider-horizontal .ui-slider-handle, .pager .current, .pager a:hover,.testimonials,.image-overlay:before',
                            'label' => __('Colored ui elements', 'bookyourtravel'),
                            'description' => __('Select background color of colored ui elements', 'bookyourtravel'),
							'dependents' => array(
                                'infoBox' => array(
                                    'color' => '#5fc8c2',
                                    'selector' => '.infoBox:after',
                                    'property' => 'border-top-color',
                                ),
								'spinner' => array(
                                    'color' => '#5fc8c2',
                                    'selector' => '.page-spinner > div:after,.tab-content .spinner > div:after',
                                    'property' => 'border-color',
                                ),
                            ),		
                        ),
						'promo_ribbons' => array(
                            'type' => 'color',
							'color' => '#FFC107',
							'property' => 'background',
							'selector' => '.promo-ribbon span',
                            'label' => __('Promotional ribbons color', 'bookyourtravel'),
                            'description' => __('Select color of promotional ribbons', 'bookyourtravel'),
                        ),
						'iconic_features' => array(
                            'type' => 'color',
							'color' => '#FFC107',
							'property' => 'background',
							'selector' => '.iconic .circle',
                            'label' => __('Iconic features icon background', 'bookyourtravel'),
                            'description' => __('Select background of iconic features icon', 'bookyourtravel'),
                        ),
						'footer_widget_social_background_color' => array(
                            'type' => 'color',
                            'color' => '#b9aca4',
                            'selector' => '.social li a',
                            'property' => 'background-color',
                            'label' => __('Social widget icon background color', 'bookyourtravel'),
                        ),
                        'footer_widget_social_hover_background_color' => array(
                            'type' => 'color',
                            'color' => '#5FC8C2',
                            'selector' => '.social li a:hover, social li a:focus',
                            'property' => 'background-color',
                            'force' => '1',
                            'label' => __('Social widget icon hover background color', 'bookyourtravel'),
                        ),
                        'footer_widget_social_text_color' => array(
                            'type' => 'color',
                            'color' => '#fff',
                            'selector' => '.social li a',
                            'force' => '1',
                            'label' => __('Social widget icon color', 'bookyourtravel'),
                        ),
                        'content_wrapper_background_color' => array(
                            'type' => 'color',
                            'color' => '#e8e8e8',
                            'selector' => '.main',
                            'property' => 'background-color',
                            'label' => __('Page background (Boxed layout)', 'bookyourtravel'),
                        ),
                        'content_background_color' => array(
                            'type' => 'color',
                            'color' => '#fff',
							'force' => '1',
                            'selector' => '.static-content,.tab-content > article,.sort-by,.boxed .page-wrap,.modal,.map-wrap,.comment,.page-spinner,.tab-content .spinner,.page-template-page-user-account .inner-nav,.page-template-page-user-content-list .inner-nav,.page-template-page-user-submit-content .inner-nav,.select2-dropdown,.deals > .row > article > div,.destinations > .row > article > div,.offers > .row > article > div,.pager > span:not(.current),.lightbox,.error-type,.woocommerce ul.products li.product, .woocommerce-page ul.products li.product,.woocommerce div.product div.summary,.woocommerce div.product .woocommerce-tabs .panel,.woocommerce-error, .woocommerce-info, .woocommerce-message,.single-card',
                            'property' => 'background-color',
                            'label' => __('Content background', 'bookyourtravel'),
                        ),
						'content_background_shadow_opacity' => array(
                            'type' => 'custom_slider',
							'min' => 0,
							'max' => 1,
							'step' => 0.1,
							'default' => 0.2,
							'property' => 'content-background-shadow-opacity',
							'selector' => '.static-content,.tab-content > article,.sort-by,.pager > span,.map-wrap,.comment,.error-type,.woocommerce ul.products li.product, .woocommerce-page ul.products li.product,.woocommerce div.product div.summary,.woocommerce div.product .woocommerce-tabs .panel,.woocommerce-error, .woocommerce-info, .woocommerce-message',	
                            'label' => __('Content background box shadow opacity', 'bookyourtravel'),
                            'description' => __('Select content background shadow opacity value', 'bookyourtravel'),						
						)
					),
				),
				'byt_texts' => array(
					'title' => esc_html__('Form elements', 'bookyourtravel'),
					'description' => esc_html__('Style settings for Book Your Travel theme form elements', 'bookyourtravel'),
					'priority' => 144,
					'settings' => array(
                        'textfield_text_color' => array(
                            'type' => 'color',
                            'color' => '#454545',
							'force' => '1',
                            'selector' => 'input[type="search"], input[type="email"], input[type="text"], input[type="number"], input[type="password"], input[type="tel"], input[type="url"], input[type="date"], textarea, select,div.selector span',
                            'label' => __('Text input text color', 'bookyourtravel'),
                            'description' => __('Select text input text color', 'bookyourtravel'),
                        ),
                        'textfield_placeholder_color' => array(
                            'type' => 'color',
                            'color' => '#454545',
							'force' => '1',
							'property' => 'placeholder-color',
                            'selector' => 'input::placeholder,input::-moz-placeholder,input::-webkit-input-placeholder,textarea::placeholder,textarea::-moz-placeholder,textarea::-webkit-input-placeholder',
                            'label' => __('Text input placeholder color', 'bookyourtravel'),
                            'description' => __('Select text input placeholder color', 'bookyourtravel'),
                        ),						
                        'textfield_border_radius' => array(
                            'type' => 'custom_slider',
							'min' => 0,
							'max' => 50,
							'step' => 1,
							'default' => 18,
							'property' => 'text-border-radius',
							'selector' => 'input[type="search"], input[type="email"], input[type="text"], input[type="number"], input[type="password"], input[type="tel"], input[type="url"], input[type="date"], textarea, select,.select2-container--default .select2-selection--single,div.selector,div.selector span',
                            'label' => __('Text input border radius', 'bookyourtravel'),
                            'description' => __('Select text input border radius to be used within theme', 'bookyourtravel'),																										
							'force' => '1',
                        ),
                        'textfield_height' => array(
                           'type' => 'number_text',
							'default' => 42,
							'property' => 'height',
							'selector' => 'input[type="search"],input[type="email"],input[type="text"],input[type="number"],input[type="password"],input[type="tel"],input[type="url"],input[type="date"], select,div.selector,div.selector span,div.selector select,.select2-container .select2-selection--single,.select2-container--default .select2-selection--single .select2-selection__arrow,.select2-container--default .select2-selection--single .select2-selection__rendered',
                            'label' => __('Text input height', 'bookyourtravel'),
                            'description' => __('Select text input height to be used within theme', 'bookyourtravel'),
							'force' => '1',
                        )
					)
				),					
                'byt_buttons' => array(
					'title' => esc_html__('Buttons', 'bookyourtravel'),
					'description' => esc_html__('Style settings for Book Your Travel theme buttons', 'bookyourtravel'),					
					'priority' => 145,
					'settings' => array(
                        'button_background_color' => array(
                            'type' => 'color',
                            'color' => '#5fc8c2',							
                            'label' => __('Button background color', 'bookyourtravel'),
                            'description' => __('Select button background color', 'bookyourtravel'),
                            'selector' => '.gradient-button, input[type="reset"], input[type="submit"],.scroll-to-top,.button,.woocommerce #respond input#submit, .woocommerce a.button, .woocommerce button.button, .woocommerce input.button,.woocommerce #respond input#submit.alt, .woocommerce a.button.alt, .woocommerce button.button.alt, .woocommerce input.button.alt',
							'property' => 'background-color',			
                        ),
                        'button_background_hover_color' => array(
                            'type' => 'color',
                            'color' => '#5fc8c2',							
                            'label' => __('Button background hover color', 'bookyourtravel'),
                            'description' => __('Select button background color', 'bookyourtravel'),
                            'selector' => '.gradient-button:hover,.gradient-button:focus,.widget .gradient-button:hover,.widget .gradient-button:focus, input[type="reset"]:hover, input[type="reset"]:focus, input[type="submit"]:hover,input[type="submit"]:focus,.scroll-to-top:hover,.scroll-to-top:focus,.button:hover,.button:focus,.woocommerce #respond input#submit:hover, .woocommerce a.button:hover, .woocommerce button.button:hover, .woocommerce input.button:hover,.woocommerce #respond input#submit:focus, .woocommerce a.button:focus, .woocommerce button.button:focus, .woocommerce input.button:focus,.woocommerce #respond input#submit.alt:hover, .woocommerce a.button.alt:hover, .woocommerce button.button.alt:hover, .woocommerce input.button.alt:hover,.woocommerce #respond input#submit.alt:focus, .woocommerce a.button.alt:focus, .woocommerce button.button.alt:focus, .woocommerce input.button.alt:focus',
							'property' => 'background-color',
                        ),						
                        'button_text_color' => array(
                            'type' => 'color',
                            'color' => '#fff',
							//'force' => '1',
                            'selector' => '.gradient-button,.widget .gradient-button, input[type="reset"], input[type="submit"],.scroll-to-top,.button,.woocommerce #respond input#submit, .woocommerce a.button, .woocommerce button.button, .woocommerce input.button,.woocommerce #respond input#submit.alt, .woocommerce a.button.alt, .woocommerce button.button.alt, .woocommerce input.button.alt',
                            'label' => __('Button text color', 'bookyourtravel'),
                            'description' => __('Select button text color', 'bookyourtravel'),
                        ),
						'button_hover_text_color' => array(
                            'type' => 'color',
                            'color' => '#fff',
							'force' => '1',
                            'selector' => '.gradient-button:hover,.gradient-button:focus,.widget .gradient-button:hover,.widget .gradient-button:focus, input[type="reset"]:hover, input[type="reset"]:focus, input[type="submit"]:hover,input[type="submit"]:focus,.scroll-to-top:hover,.scroll-to-top:focus,.button:hover,.button:focus,.woocommerce #respond input#submit:hover, .woocommerce a.button:hover, .woocommerce button.button:hover, .woocommerce input.button:hover,.woocommerce #respond input#submit:focus, .woocommerce a.button:focus, .woocommerce button.button:focus, .woocommerce input.button:focus,.woocommerce #respond input#submit.alt:hover, .woocommerce a.button.alt:hover, .woocommerce button.button.alt:hover, .woocommerce input.button.alt:hover,.woocommerce #respond input#submit.alt:focus, .woocommerce a.button.alt:focus, .woocommerce button.button.alt:focus, .woocommerce input.button.alt:focus,.vc_general.vc_btn3:focus, .vc_general.vc_btn3:hover',
                            'label' => __('Button text hover color', 'bookyourtravel'),
                            'description' => __('Select button text hover  color', 'bookyourtravel'),
                        ),
                        'button_border_radius' => array(
                            'type' => 'custom_slider',
							'min' => 0,
							'max' => 50,
							'step' => 1,
							'default' => 17,
							'property' => 'button-border-radius',
							'selector' => '.scroll-to-top,.pager a,.pager > span, .gradient-button, input[type="reset"], input[type="submit"],.button,.woocommerce #respond input#submit, .woocommerce a.button, .woocommerce button.button, .woocommerce input.button,.woocommerce #respond input#submit.alt, .woocommerce a.button.alt, .woocommerce button.button.alt, .woocommerce input.button.alt',
                            'label' => __('Button border radius', 'bookyourtravel'),
                            'description' => __('Select button border radius to be used within theme', 'bookyourtravel'),																										
                        ),
                        'button_padding_leftright' => array(
                            'type' => 'custom_slider',
							'min' => 0,
							'max' => 50,
							'step' => 1,
							'default' => 18,
							'property' => 'padding-left,padding-right',
							'selector' => '.gradient-button, input[type="reset"], input[type="submit"],.button,.woocommerce #respond input#submit, .woocommerce a.button, .woocommerce button.button, .woocommerce input.button,.woocommerce #respond input#submit.alt, .woocommerce a.button.alt, .woocommerce button.button.alt, .woocommerce input.button.alt,#add_payment_method table.cart td.actions .coupon .input-text, .woocommerce-cart table.cart td.actions .coupon .input-text, .woocommerce-checkout table.cart td.actions .coupon .input-text',
                            'label' => __('Button padding left/right', 'bookyourtravel'),
                            'description' => __('Select button padding left/right to be used within theme', 'bookyourtravel'),
							'force' => '1',
                        ),
                         'button_padding_topbottom' => array(
                            'type' => 'custom_slider',
							'min' => 0,
							'max' => 50,
							'step' => 1,
							'default' => 12,
							'property' => 'padding-top,padding-bottom',
							'selector' => '.gradient-button, input[type="reset"], input[type="submit"],.button,.woocommerce #respond input#submit, .woocommerce a.button, .woocommerce button.button, .woocommerce input.button,.woocommerce #respond input#submit.alt, .woocommerce a.button.alt, .woocommerce button.button.alt, .woocommerce input.button.alt,#add_payment_method table.cart td.actions .coupon .input-text, .woocommerce-cart table.cart td.actions .coupon .input-text, .woocommerce-checkout table.cart td.actions .coupon .input-text',
                            'label' => __('Button padding top/bottom', 'bookyourtravel'),
                            'description' => __('Select button padding top/bottom to be used within theme', 'bookyourtravel'),
							'force' => '1',
                        )						
					)
				),				
                'byt_fonts' => array(
					'title' => esc_html__('Fonts', 'bookyourtravel'),
					'description' => esc_html__('Settings for fonts used within the theme', 'bookyourtravel'),
					'priority' => 150,
					'settings' => array(
						'base_font' => array(
							'type' => 'font_family_selector',
							'selector' => 'body, .tab-content h4,.infoBox > div',
							'default' => 'Open+Sans',
							'label' => __('Base font family', 'bookyourtravel'),
							'description' => __('Select base font family to be used within theme', 'bookyourtravel'),
						),
						'base_font_size' => array(
                            'type' => 'custom_slider',
							'min' => 1,
							'max' => 30,
							'step' => 1,
							'default' => 13,
							'property' => 'font-size',
							'selector' => 'body',							
                            'label' => __('Base font size', 'bookyourtravel'),
                            'description' => __('Select base font size to be used within theme', 'bookyourtravel'),												
						),
						'base_font_weight' => array(
                            'type' => 'custom_slider',
							'min' => 200,
							'max' => 700,
							'step' => 100,
							'default' => 400,
							'property' => 'font-weight',
							'selector' => 'body',							
                            'label' => __('Base font weight', 'bookyourtravel'),
                            'description' => __('Select base font weight to be used within theme', 'bookyourtravel'),																			
						),					
						'base_line_height' => array(
                            'type' => 'custom_slider',
							'min' => 1.0,
							'max' => 10.0,
							'step' => 0.1,
							'default' => 1.5,
							'property' => 'line-height',
							'selector' => 'body',							
                            'label' => __('Base font line height', 'bookyourtravel'),
                            'description' => __('Select base font line height to be used within theme', 'bookyourtravel'),																			
						),
						'base_font_color' => array(
							'type' => 'color',
							'selector' => 'body,.tab-content .destinations > .row > article.full-width > div .details .ribbon .small',
							'color' => '#454545',
							'label' => __('Base font color', 'bookyourtravel'),
							'description' => __('Select base font color to be used within theme', 'bookyourtravel'),
						),					
						'heading_font' => array(
							'type' => 'font_family_selector',
							'selector' => 'h1,h2,h3,h4,h5,h6,.price em',
							'default' => 'Roboto+Slab',
							'label' => __('Heading element font family', 'bookyourtravel'),
							'description' => __('Select heading element (e.g. h1, h2, h3 etc) font family to be used within theme', 'bookyourtravel'),
						),
						'heading_font_weight' => array(
                            'type' => 'custom_slider',
							'min' => 100,
							'max' => 900,
							'step' => 100,
							'default' => 600,
							'property' => 'font-weight',
							'selector' => 'h1,h2,h3,h4,h5,h6,.price em',							
                            'label' => __('Heading font weight', 'bookyourtravel'),
                            'description' => __('Select heading font weight to be used within theme', 'bookyourtravel'),																										
						),
						'heading_padding_bottom' => array(
                           'type' => 'custom_slider',
							'min' => 0,
							'max' => 60,
							'step' => 1,
							'default' => 20,
							'property' => 'margin-bottom',
							'selector' => 'h1,h2,h3,h4,h5,h6,p',
                            'label' => __('Heading padding bottom', 'bookyourtravel'),
                            'description' => __('Select heading padding bottom to be used within theme', 'bookyourtravel'),
                        ),
						'heading_font_color' => array(
							'type' => 'color',
							'selector' => 'h1,h2,h3,h4,h5,h6,.price em',
							'color' => '#454545',
							'label' => __('Heading font color', 'bookyourtravel'),
							'description' => __('Select heading font color to be used within theme', 'bookyourtravel'),
						),
						'h1_font_size' => array(
                            'type' => 'custom_slider',
							'min' => 1,
							'max' => 80,
							'step' => 1,
							'default' => 23,
							'property' => 'font-size',
							'selector' => 'h1,.main .widget-area .s-title h2',							
                            'label' => __('h1 font size', 'bookyourtravel'),
                            'description' => __('Select h1 font size to be used within theme', 'bookyourtravel'),												
						),						
						'h2_font_size' => array(
                            'type' => 'custom_slider',
							'min' => 1,
							'max' => 80,
							'step' => 1,
							'default' => 20,
							'property' => 'font-size',
							'selector' => 'h2',							
                            'label' => __('h2 font size', 'bookyourtravel'),
                            'description' => __('Select h2 font size to be used within theme', 'bookyourtravel'),																			
						),					
						'h3_font_size' => array(
                            'type' => 'custom_slider',
							'min' => 1,
							'max' => 80,
							'step' => 1,
							'default' => 18,
							'property' => 'font-size',
							'selector' => 'h3',							
                            'label' => __('h3 font size', 'bookyourtravel'),
                            'description' => __('Select h3 font size to be used within theme', 'bookyourtravel'),
						),
						'h4_font_size' => array(
                            'type' => 'custom_slider',
							'min' => 1,
							'max' => 50,
							'step' => 1,
							'default' => 16,
							'property' => 'font-size',
							'selector' => 'h4',							
                            'label' => __('h4 font size', 'bookyourtravel'),
                            'description' => __('Select h4 font size to be used within theme', 'bookyourtravel'),
						),					
						'h5_font_size' => array(
                            'type' => 'custom_slider',
							'min' => 1,
							'max' => 50,
							'step' => 1,
							'default' => 14,
							'property' => 'font-size',
							'selector' => 'h5',							
                            'label' => __('h5 font size', 'bookyourtravel'),
                            'description' => __('Select h5 font size to be used within theme', 'bookyourtravel'),
						),						
						'h6_font_size' => array(
                            'type' => 'custom_slider',
							'min' => 1,
							'max' => 50,
							'step' => 1,
							'default' => 13,
							'property' => 'font-size',
							'selector' => 'h6',							
                            'label' => __('h6 font size', 'bookyourtravel'),
                            'description' => __('Select h6 font size to be used within theme', 'bookyourtravel'),
						),
					)
				),
                'byt_header' => array(
                    'selector' => '.header',
                    'title' => esc_html__('Header', 'bookyourtravel'),
                    'description' => esc_html__('Settings for elements within the website header', 'bookyourtravel'),
                    'priority' => 160,
                    'settings' => array(
                        'header_disable_theme_header' => array(
                            'type' => 'yes_no_checkbox',
                            'label' => __('Disable theme header?', 'bookyourtravel'),
                            'description' => __('Use this when creating header with a page builder', 'bookyourtravel'),
                        ),
                        'header_layout' => array(
                            'type' => 'header_layout_selector',
                            'label' => __('Header layout', 'bookyourtravel'),
                            'description' => __('Change header layout', 'bookyourtravel'),
                        ),
						'header_logo_height' => array(
                            'type' => 'number_text',
							'default' => 50,
							'property' => 'height',
							'selector' => '.logo img',
                            'label' => __('Header logo height', 'bookyourtravel'),
                            'description' => __('Set header logo height to be used within theme', 'bookyourtravel'),
							'force' => '1',
                        ),	
						'header_bottom_margin' => array(
							'type' => 'custom_slider',
							'min' => 0,
							'max' => 100,
							'step' => 5,
							'default' => 20,
							'selector' => '.header,.woocommerce-page .header',
							'property' => 'margin-bottom',
							'label' => __('Header bottom margin', 'bookyourtravel'),
							'description' => __('Set bottom margin for the header', 'bookyourtravel'),        
						),
                        'header_sticky' => array(
                            'type' => 'yes_no_checkbox',
                            'label' => __('Header sticky?', 'bookyourtravel'),
                            'description' => __('Make header sticky?', 'bookyourtravel'),
                        ),
						'header_overlay' => array(
                            'type' => 'yes_no_checkbox',
                            'label' => __('Transparent header?', 'bookyourtravel'),
                            'description' => __('Make header appear over image?', 'bookyourtravel'),
                        ),
						 'home_header_transparent' => array(
                            'type' => 'yes_no_checkbox',
                            'label' => __('Transparent header on home page only?', 'bookyourtravel'),
                            'description' => __('Make header appear over image on home page?', 'bookyourtravel'),
                        ),						
                        'header_background_color' => array(
                            'type' => 'color',
                            'color' => '#fff',
                            'selector' => '.header',
                            'property' => 'background-color',
                            'label' => __('Background color', 'bookyourtravel'),
                        ),
                        'header_top_background_color' => array(
                            'type' => 'color',
                            'color' => '#fff',
                            'selector' => '.header .top-header',
                            'property' => 'background-color',
                            'label' => __('Top Background color', 'bookyourtravel'),
                        ),	
						'header_top_text_color' => array(
                            'type' => 'color',
                            'color' => '#aaa',
                            'selector' => '.top-nav li a,.top-nav-left li a',
                            'label' => __('Top Text color', 'bookyourtravel'),
                        ),		
						'header_top_text_active_color' => array(
                            'type' => 'color',
                            'color' => '#fff',
                            'selector' => '.top-nav li a:hover,.top-nav li a:focus,.top-nav-left li a:hover,.top-nav-left li a:focus',
                            'label' => __('Top Text active/hover color', 'bookyourtravel'),
                        ),		
                        'header_contact_icon_background_color' => array(
                            'type' => 'color',
                            'color' => '#5fc8c2',
                            'selector' => '.header .contact:before',
                            'property' => 'background-color',
                            'label' => __('Contact icon background color', 'bookyourtravel'),
                        ),
                        'header_contact_icon_color' => array(
                            'type' => 'color',
                            'color' => '#fff',
                            'selector' => '.header .contact:before',
                            'label' => __('Contact icon color', 'bookyourtravel'),
                        ),
                        'header_contact_text_color' => array(
                            'type' => 'color',
                            'color' => '#858585',
                            'selector' => '.header .contact span',
                            'label' => __('Contact text color', 'bookyourtravel'),
                        ),
                        'header_ribbon_link_color' => array(
                            'type' => 'color',
                            'color' => '#fff',
                            'selector' => '.header .ribbon li a,.header2 .ribbon:after,.header10 .ribbon:after',
                            'label' => __('Header ribbon link color', 'bookyourtravel'),
                        ),
                        'header_ribbon_color' => array(
                            'type' => 'color',
                            'color' => '#5fc8c2',
							'force' => '1',
                            'selector' => '.header .ribbon',
                            'property' => 'background-color',
                            'label' => __('Header ribbon color', 'bookyourtravel'),
                            'dependents' => array(
                                'border-1' => array(
                                    'color' => '#5fc8c2',
                                    'selector' => '.header .ribbon:before',
                                    'property' => 'border-top-color',
                                ),
                                'border-2' => array(
                                    'color' => '#5fc8c2',
                                    'selector' => '.header .ribbon:after',
                                    'property' => 'border-right-color',
                                ),
                            ),
                        ),
                        'header_contact_message' => array(
                            'type' => 'text',
                            'selector' => '.header .contact span.message',
                            'default' => __('24/7 Support number', 'bookyourtravel'),
                            'label' => esc_html__('Contact message', 'bookyourtravel'),
                            'description' => __('Default: 24/7 Support number', 'bookyourtravel'),
                            'render_callback' => function () {
                                return get_theme_mod('header_contact_message', '');
                            },
                        ),
                        'header_contact_number' => array(
                            'type' => 'text',
                            'selector' => '.header .contact span.number',
                            'default' => $bc_contact_number_default,
                            'label' => esc_html__('Contact number', 'bookyourtravel'),
                            'description' => __('Default: 1 - 555 - 555 - 555', 'bookyourtravel'),
                            'render_callback' => function () {
                                return get_theme_mod('header_contact_number', '');
                            },
                        ),
						'header_minicart' => array(
                            'type' => 'yes_no_checkbox',
                            'label' => __('Show cart icon?', 'bookyourtravel'),
                            'description' => __('Show WooCommerce cart?', 'bookyourtravel'),
                        ),
						'header_cart_background_color' => array(
                            'type' => 'color',
                            'color' => '#B1A398',
                            'selector' => '.minicart > a',
                            'property' => 'background-color',
                            'label' => __('Cart icon background', 'bookyourtravel'),
                        ),
						'header_cart_icon_color' => array(
                            'type' => 'color',
                            'color' => '#fff',
                            'selector' => '.minicart > a > i',
                            'label' => __('Cart icon color', 'bookyourtravel'),
                        ),
						'header_cart_count_background_color' => array(
                            'type' => 'color',
                            'color' => '#5fc8c2',
                            'selector' => '.minicart span',
                            'property' => 'background-color',
                            'label' => __('Cart count background', 'bookyourtravel'),
                        ),
						'header_cart_count_icon_color' => array(
                            'type' => 'color',
                            'color' => '#fff',
                            'selector' => '.minicart span',
                            'label' => __('Cart count color', 'bookyourtravel'),
                        ),
						'header_cart_icon_height' => array(
                            'type' => 'custom_slider',
							'min' => 37,
							'max' => 80,
							'step' => 1,
							'default' => 37,
							'property' => 'height',
							'selector' => '.minicart > a',
                            'label' => __('Cart icon height', 'bookyourtravel'),
							'force' => '1',
                        ),	
						'header_cart_icon_width' => array(
                            'type' => 'custom_slider',
							'min' => 37,
							'max' => 80,
							'step' => 1,
							'default' => 37,
							'property' => 'width',
							'selector' => '.minicart > a',
                            'label' => __('Cart icon width', 'bookyourtravel'),
							'force' => '1',
                        ),	
						'header_cart_border_radius' => array(
                            'type' => 'custom_slider',
							'min' => 0,
							'max' => 40,
							'step' => 1,
							'default' => 19,
							'property' => 'text-border-radius',
							'selector' => '.minicart > a',
                            'label' => __('Cart icon border radius', 'bookyourtravel'),																								
							'force' => '1',
                        ),
                    ),
                ),
                'byt_footer' => array(
                    'selector' => '.footer',
                    'title' => esc_html__('Footer', 'bookyourtravel'),
                    'description' => esc_html__('Settings for elements within the website footer', 'bookyourtravel'),
                    'priority' => 170,
                    'settings' => array(
                        'footer_disable_theme_footer' => array(
                            'type' => 'yes_no_checkbox',
                            'label' => __('Disable theme footer?', 'bookyourtravel'),
                            'description' => __('Use this when creating footer with a page builder', 'bookyourtravel'),
                        ),                        
                        'footer_background_color' => array(
                            'type' => 'color',
                            'color' => '#fff',
                            'selector' => '.footer',
                            'property' => 'background-color',
                            'label' => __('Background color', 'bookyourtravel'),
                        ),
                        'footer_widget_heading_color' => array(
                            'type' => 'color',
                            'color' => '#999',
                            'selector' => '.footer .widget h6, .footer .widget h5, .footer .widget h4, .footer .widget h3',
                            'label' => __('Widget heading color', 'bookyourtravel'),
                        ),
                        'footer_widget_text_color' => array(
                            'type' => 'color',
                            'color' => '#454545',
                            'selector' => '.footer .widget div, .footer .widget p',
                            'label' => __('Widget text color', 'bookyourtravel'),
                        ),
                        'footer_widget_link_color' => array(
                            'type' => 'color',
                            'color' => '#454545',
                            'selector' => '.footer .widget a',
                            'label' => __('Widget link color', 'bookyourtravel'),
                        ),
                        'footer_widget_link_hover_color' => array(
                            'type' => 'color',
                            'color' => '#5fc8c2',
                            'selector' => '.footer .widget a:hover',
                            'label' => __('Widget link hover color', 'bookyourtravel'),
                        ),
                        'footer_copyright_text_color' => array(
                            'type' => 'color',
                            'color' => '#858585',
                            'selector' => '.footer p.copy',
                            'label' => __('Copyright text color', 'bookyourtravel'),
                        ),
                        'footer_menu_link_color' => array(
                            'type' => 'color',
                            'color' => '#858585',
                            'selector' => '.footer div.full-width nav li a',
                            'label' => __('Footer menu link color', 'bookyourtravel'),
                        ),
                        'footer_menu_link_hover_color' => array(
                            'type' => 'color',
                            'color' => '#3f3f3f',
                            'selector' => '.footer div.full-width nav li a:hover, .footer div.full-width nav li a:focus',
                            'force' => '1',
                            'label' => __('Footer menu link hover color', 'bookyourtravel'),
                        ),
                        'footer_widget_emphasize_text_color' => array(
                            'type' => 'color',
                            'color' => '#5fc8c2',
                            'selector' => '.footer .widget div em, .footer .widget p em',
                            'label' => __('Widget emphasize text color', 'bookyourtravel'),
                        ),
                        'footer_copyright_text' => array(
                            'type' => 'text',
                            'selector' => '.footer p.copy',
                            'default' => $bc_copyright_default,
                            'label' => esc_html__('Copyright text', 'bookyourtravel'),
                            'description' => __('Default: &copy; 2013 - 2023 ThemeEnergy.com.', 'bookyourtravel'),
                            'render_callback' => function () {
                                return get_theme_mod('footer_copyright_text', '');
                            },
                        ),
                    ),
                ),
            ),
        );

		if ($bookyourtravel_theme_globals->enable_accommodations()) {
			$scheme['sections']['byt_custom_posts']['settings']['byt_accommodation_single_layout'] = array(
				'type' => 'single_post_layout_selector',
				'label' => __('Accommodation single tabs layout', 'bookyourtravel'),
				'description' => __('Select accommodation post type single view tabs layout', 'bookyourtravel'),
			);
			$scheme['sections']['byt_custom_posts']['settings']['byt_accommodation_single_sidebar_position'] = array(
				'type' => 'single_post_sidebar_selector',
				'label' => __('Accommodation single sidebar position', 'bookyourtravel'),
				'description' => __('Select accommodation post type single view sidebar position', 'bookyourtravel'),
			);			
		}
		
		if ($bookyourtravel_theme_globals->enable_tours()) {
			$scheme['sections']['byt_custom_posts']['settings']['byt_tour_single_layout'] = array(
				'type' => 'single_post_layout_selector',
				'label' => __('Tour single tabs layout', 'bookyourtravel'),
				'description' => __('Select tour post type single view tabs layout', 'bookyourtravel'),
			);
			$scheme['sections']['byt_custom_posts']['settings']['byt_tour_single_sidebar_position'] = array(
				'type' => 'single_post_sidebar_selector',
				'label' => __('Tour single sidebar position', 'bookyourtravel'),
				'description' => __('Select tour post type single view sidebar position', 'bookyourtravel'),			
			);			
		}

		if ($bookyourtravel_theme_globals->enable_cruises()) {
			$scheme['sections']['byt_custom_posts']['settings']['byt_cruise_single_layout'] = array(
				'type' => 'single_post_layout_selector',
				'label' => __('Cruise single tabs layout', 'bookyourtravel'),
				'description' => __('Select cruise post type single view tabs layout', 'bookyourtravel'),
			);
			$scheme['sections']['byt_custom_posts']['settings']['byt_cruise_single_sidebar_position'] = array(
				'type' => 'single_post_sidebar_selector',
				'label' => __('Cruise single sidebar position', 'bookyourtravel'),
				'description' => __('Select cruise post type single view sidebar position', 'bookyourtravel'),
			);			
		}

		if ($bookyourtravel_theme_globals->enable_car_rentals()) {
			$scheme['sections']['byt_custom_posts']['settings']['byt_car_rental_single_layout'] = array(
				'type' => 'single_post_layout_selector',
				'label' => __('Car rental single tabs layout', 'bookyourtravel'),
				'description' => __('Select car rental post type single view tabs layout', 'bookyourtravel'),			
			);
			
			$scheme['sections']['byt_custom_posts']['settings']['byt_car_rental_single_sidebar_position'] = array(
				'type' => 'single_post_sidebar_selector',
				'label' => __('Car rental single sidebar position', 'bookyourtravel'),
				'description' => __('Select car rental post type single view sidebar position', 'bookyourtravel'),			
			);
		}
		
        return apply_filters('bookyourtravel_theme_default_scheme', $scheme);
    }

    public static function get_schemes()
    {
        $schemes = array(
            'orange' => array(
                'label' => 'Orange',
                'id' => 'orange',
                'sections' => array(
                    'byt_header' => array(
                        'settings' => array(
                            'header_contact_icon_background_color' => array(
                                'color' => '#F26B09',
                            ),
                            'header_ribbon_color' => array(
                                'color' => '#F77515',
                                'dependents' => array(
                                    'border-1' => array(
                                        'color' => '#F77515',
                                    ),
                                    'border-2' => array(
                                        'color' => '#F77515',
                                    ),
                                ),
                            ),
                        ),
                    ),
                    'byt_footer' => array(
                        'settings' => array(
                            'footer_widget_link_hover_color' => array(
                                'color' => '#F26B09',
                            ),
                            'footer_widget_social_hover_background_color' => array(
                                'color' => '#F26B09',
                            ),
                            'footer_widget_list_icon_color' => array(
                                'color' => '#F26B09',
                            ),
                            'footer_widget_emphasize_text_color' => array(
                                'color' => '#F26B09',
                            ),
                        ),
                    ),
                ),
            ),
        );

        return apply_filters('bookyourtravel_theme_schemes', $schemes);
    }

    public static function get_scheme($scheme_id)
    {
        $defaults = self::get_default_scheme();

        $schemes = self::get_schemes();

        $scheme = isset($schemes[$scheme_id]) ? array_replace_recursive($defaults, $schemes[$scheme_id]) : $defaults;

        return apply_filters('bookyourtravel_theme_scheme_'. $scheme_id, $scheme);
    }
}
