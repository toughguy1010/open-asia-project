<?php
/*
Plugin Name: Weather Management Plugin
Description: A plugin for managing weather form options.
Version: 1.0
Author: Nanoweb
*/

function create_custom_post_type_weather() {
    register_post_type('weather', array(
        'labels' => array(
            'name' => __('Weather', 'your-text-domain'),
            'singular_name' => __('Weather', 'your-text-domain'),
        ),
        'public' => true,
        'has_archive' => true,
        'rewrite' => array('slug' => 'weather'),
        'menu_icon' => 'dashicons-cloud'
    ));
}
add_action('init', 'create_custom_post_type_weather');

// Đăng ký taxonomy "region" cho post type "weather"
function create_region_taxonomy() {
    register_taxonomy(
        'region',        // Slug của taxonomy
        'weather',       // Post type liên quan
        array(
            'label' => __('Region', 'your-text-domain'),
            'rewrite' => array('slug' => 'region'),
            'hierarchical' => true,
        )
    );
}
add_action('init', 'create_region_taxonomy');
// 
// function add_custom_admin_menu() {
//     add_menu_page(
//         'Weather Menu', // Tiêu đề hiển thị trên menu
//         'Weather',      // Tên menu trong menu
//         'manage_options', // Quyền truy cập cần thiết để xem menu
//         'weather-menu',   // Slug của menu
//         'weather_menu_callback', // Hàm callback để hiển thị nội dung menu
//         'dashicons-cloud' // Biểu tượng menu (tùy chọn)
//     );
// }
// add_action('admin_menu', 'add_custom_admin_menu');

function weather_menu_callback() {
    echo '<div class="wrap">';
    echo '<h2>Weather Menu Page</h2>';
    // Hiển thị nội dung bạn muốn trong menu
    echo '</div>';
}