<?php
if (!function_exists('home_banner')) {

    function home_banner($attr, $content, $span)
    {
        $attr = shortcode_atts([
            'search_title' => '',
            'search_span' => $content,
            'disable_search' =>'',
            'background_image' => '',
        ], $attr,);
        require_once BookYourTravel_Theme_Utils::get_file_path('/templates/vc_elements/home_banner.php');
    }
    add_shortcode('home_banner', 'home_banner');
}
if (!function_exists('home_quote')) {

    function home_quote($attr, $content = false)
    {
        $attr = shortcode_atts([
            'list_member' => [],
            'quote_content' => '',
            'list_quote' => [],
        ], $attr);
        require_once BookYourTravel_Theme_Utils::get_file_path('/templates/vc_elements/home_quote.php');
    }
    add_shortcode('home_quote', 'home_quote');
}
if (!function_exists('easy_step')) {

    function easy_step($attr, $content = false)
    {
        $attr = shortcode_atts([
            'background_image' => '',
            'easy_title' => '',
            'step' => [],
        ], $attr);
        require_once BookYourTravel_Theme_Utils::get_file_path('/templates/vc_elements/easy_step.php');
    }
    add_shortcode('easy_step', 'easy_step');
}
if (!function_exists('location')) {

    function location($attr, $content = false)
    {
        $attr = shortcode_atts([
            'title' => '',
            'description' => '',
            'list_location' => [],
        ], $attr);
        require_once BookYourTravel_Theme_Utils::get_file_path('/templates/vc_elements/location.php');
    }
    add_shortcode('location', 'location');
}
if (!function_exists('reviews')) {

    function reviews($attr, $content = false)
    {
        $attr = shortcode_atts([
            'title' => '',
            'description' => '',
            'background_image' => '',
        ], $attr);
        require_once BookYourTravel_Theme_Utils::get_file_path('/templates/vc_elements/reviews.php');
    }
    add_shortcode('reviews', 'reviews');
}
if (!function_exists('introduction')) {

    function introduction($attr, $content, $span)
    {
        $attr = shortcode_atts([
            'title' => '',
            'avatar_image' => '',
            'description' => '',
        ], $attr,);
        require_once BookYourTravel_Theme_Utils::get_file_path('/templates/vc_elements/introduction.php');
    }
    add_shortcode('introduction', 'introduction');
}
if (!function_exists('certification')) {

    function certification($attr, $content, $span)
    {
        $attr = shortcode_atts([
            'logo' => '',
            'description' => '',
            'span' => '',
            'list_certification_image' => [],
            'brand_image' =>'',
            'rating_title'=>'',
            'rating_number'=>'',
            'ranking_title'=>'',
            'ranking'=>'',
            'list_traveler_review'=>'',
        ], $attr,);
        require_once BookYourTravel_Theme_Utils::get_file_path('/templates/vc_elements/certification.php');
    }
    add_shortcode('certification', 'certification');
}
if (!function_exists('hotline_information')) {

    function hotline_information($attr, $content, $span)
    {
        $attr = shortcode_atts([
            'name' => '',
            'phone_number' => '',
            'email' => '',
            'working_time' => '',
        ], $attr,);
        require_once BookYourTravel_Theme_Utils::get_file_path('/templates/vc_elements/hotline_information.php');
    }
    add_shortcode('hotline_information', 'hotline_information');
}
if (!function_exists('about_us')) {
    function about_us($attr, $content, $span)
    {
        
        $attr = shortcode_atts([
            'about_us_content' => '',
           
        ], $attr,);
        require_once BookYourTravel_Theme_Utils::get_file_path('/templates/vc_elements/about_us.php');
    }
    add_shortcode('about_us', 'about_us');
}
if (!function_exists('passionate_team')) {
    function passionate_team($attr, $content, $span)
    {
        
        $attr = shortcode_atts([
            'title' => '',
            'description' => '',
            'member' => '',
           
        ], $attr,);
        require_once BookYourTravel_Theme_Utils::get_file_path('/templates/vc_elements/passionate_team.php');
    }
    add_shortcode('passionate_team', 'passionate_team');
}


if (!function_exists('trip_option')) {
    function trip_option($attr, $content, $span)
    {
        
        $attr = shortcode_atts([
            'title' => '',
            'background' => '',
            'text_btn' => '',
            'link_btn' => '',
           
        ], $attr,);
        require_once BookYourTravel_Theme_Utils::get_file_path('/templates/vc_elements/trip_option.php');
    }
    add_shortcode('trip_option', 'trip_option');
}

if (!function_exists('page_banner')) {
    function page_banner($attr, $content, $span)
    {
        $attr = shortcode_atts([
            'title' => '',
            'background' => '',
            'disable_plan_field' => '',
            'title_plan' => '',
            'step_of_plan' => '',
            'text_btn' => '',
            'link_btn' => '',
           
        ], $attr,);
        require_once BookYourTravel_Theme_Utils::get_file_path('/templates/vc_elements/page_banner.php');
    }
    add_shortcode('page_banner', 'page_banner');
}

if (!function_exists('client_slider')) {
    function client_slider($attr, $content, $span)
    {
        $attr = shortcode_atts([
            'logo_element' => [],
           
        ], $attr,);
        require_once BookYourTravel_Theme_Utils::get_file_path('/templates/vc_elements/client_slider.php');
    }
    add_shortcode('client_slider', 'client_slider');
}

if (!function_exists('visa_content')) {
    function visa_content($attr, $content, $span)
    {
        $attr = shortcode_atts([
            'description' => '',
            'visa_location' => [],
           
        ], $attr,);
        require_once BookYourTravel_Theme_Utils::get_file_path('/templates/vc_elements/visa_content.php');
    }
    add_shortcode('visa_content', 'visa_content');
}
if (!function_exists('slider')) {
    function slider($attr, $content, $span)
    {
        $attr = shortcode_atts([
            'title' => '',
            'gallery' => [],
           
        ], $attr,);
        require_once BookYourTravel_Theme_Utils::get_file_path('/templates/vc_elements/slider.php');
    }
    add_shortcode('slider', 'slider');
}
if (!function_exists('contact_banner')) {
    function contact_banner($attr, $content, $span)
    {
        $attr = shortcode_atts([
            'title' => '',
            'phone' => '',
            'email' => '',
            'address' => '',
            'logo' => '',
            'behind_image' => '',
           
        ], $attr,);
        require_once BookYourTravel_Theme_Utils::get_file_path('/templates/vc_elements/contact_banner.php');
    }
    add_shortcode('contact_banner', 'contact_banner');
}
if (!function_exists('rotate_banner')) {
    function rotate_banner($attr, $content, $span)
    {
        $attr = shortcode_atts([
            'title_image' => '',
            'background' => '',
            'title_text_fields' => '',
            'content_text_fields' => '',
           
        ], $attr,);
        require_once BookYourTravel_Theme_Utils::get_file_path('/templates/vc_elements/rotate_banner.php');
    }
    add_shortcode('rotate_banner', 'rotate_banner');
}