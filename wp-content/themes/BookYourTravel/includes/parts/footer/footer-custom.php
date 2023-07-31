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
            // wp_nav_menu(array(
            //     'theme_location' => 'contact-info',
            // ));
            ?>
            <div class="menu-contact-infor-container">
                <ul id="menu-contact-infor" class="menu">
                    <li id="menu-item-603" class="menu-item menu-item-type-custom menu-item-object-custom current-menu-item current_page_item menu-item-home menu-item-603">
                        <a href="http://openasiatravel.nanoweb.vn/" aria-current="page">Open Asia Travel</a>
                    </li>
                    <li id="menu-item-604" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-604">
                        <a href="https://www.google.com/maps/place/173%2F75+%C4%90.+Ho%C3%A0ng+Hoa+Th%C3%A1m,+Ng%E1%BB%8Dc+H%E1%BB%93,+Ba+%C4%90%C3%ACnh,+H%C3%A0+N%E1%BB%99i,+Vi%E1%BB%87t+Nam/@21.0411381,105.8208083,17z/data=!3m1!4b1!4m6!3m5!1s0x3135ab0bdc9e6e9b:0xb8ad8c2c49cafa20!8m2!3d21.0411331!4d105.8233832!16s%2Fg%2F11g9gk3yc9?hl=vi-VN&amp;entry=ttu">173/75 Hoang Hoa Tham, Ba Dinh, Hanoi, Vietnam</a>
                    </li>
                    <li id="menu-item-605" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-605">
                        <a href="tel:+84%20982%20996%20967">+84 982 996 967</a>
                    </li>
                    <li id="menu-item-606" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-606">
                        <a href="mailto:info@openasiatravel.com">info@openasiatravel.com</a>
                    </li>
                </ul>
            </div>
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
    <div class="footer-wrap-item footer-connect">
        <div class="footer-title">
            Connect us
        </div>
        <ul class="footer-connect-list">
            <?php
            $post_id = 815; // ID 
            $post = get_post($post_id);
            $contact_socials = get_field('social', $post);
            $contact_text = get_field('connect_text', $post);
            $contact_btn_link = get_field('connect_button_link', $post);
            ?>
            <div class="footer-connect-item">
                <?= $contact_text ?>
            </div>
            <div class="footer-connect-item">
                <div class="social-list">
                    <?php
                    foreach ($contact_socials as $social) {
                    ?>
                        <a href="<?= $social['link_social'] ?>" class="social-item">
                            <img src="<?= $social['icon_image'] ?>" alt="">
                        </a>
                    <?php
                    }
                    ?>
                </div>
            </div>
            <div class="footer-connect-item connect-btn">
                <p>Share your ideas with us:</p>
                <a href="<?= $contact_btn_link  ?>"> Send us a message</a>
            </div>
        </ul>
    </div>


</section>

<div id="sticky-request" class="vc_row wpb_row vc_row-fluid">
    <div class="wpb_column vc_column_container vc_col-sm-12">
        <div class="vc_column-inner">
            <div class="wpb_wrapper">
                <div class="widget widget-sidebar "> <!-- Call to action -->
                    <div class="cta">
                        <div class="wrap">
                            <p>
                                Request a quote </p>
                            <a href="<?php echo home_url() ?>/contact-us" class="gradient-button">Request a quote</a>
                        </div>
                    </div>
                    <!-- //Call to action -->
                </div>
            </div>
        </div>
    </div>
</div>