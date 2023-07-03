<?php
if (!function_exists('home_banner')) {

    function home_banner($attr, $content, $span)
    {
        $attr = shortcode_atts([
            'search_title' => '',
            'search_span' => $content,
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
