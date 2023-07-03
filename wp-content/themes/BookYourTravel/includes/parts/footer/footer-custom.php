<?php


// Get menu items for a specific menu location
$menu_contact = 'contact-info'; // Replace with your desired menu location slug
$contact_items = wp_get_nav_menu_items($menu_contact);
?>

<section id="footer-wrap">
    <div class="footer-wrap-item footer-info">
        <?php get_template_part('includes/parts/header/header', 'logo'); ?>
        <ul class="footer-info-list">
            <?php
            wp_nav_menu(array(
                'theme_location' => 'contact-info',
            ));
            ?>
        </ul>
    </div>
    <div class="footer-wrap-item footer-overview">
        <div class="footer-title">
            Overview
        </div>
        <ul class="footer-overview-list">
            <?php
            wp_nav_menu(array(
                'theme_location' => 'about-menu',
            ));
            ?>
        </ul>
    </div>
    <div class="footer-wrap-item footer-destination">
        <div class="footer-title">
            Our Destinations
        </div>
        <ul class="footer-destinations-list">
            <?php
            wp_nav_menu(array(
                'theme_location' => 'destinations-menu',
            ));
            ?>
        </ul>
    </div>
    <div class="footer-wrap-item footer-user">
        <div class="footer-title">
            Useful Info
        </div>
        <ul class="footer-user-list">
            <?php
            wp_nav_menu(array(
                'theme_location' => 'user-menu',
            ));
            ?>
        </ul>
    </div>


</section>