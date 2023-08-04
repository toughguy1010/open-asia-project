<?php


// Get menu items for a specific menu location
$menu_contact = 'contact-info'; // Replace with your desired menu location slug
$contact_items = wp_get_nav_menu_items($menu_contact);
?>

<section id="footer-wrap">
    <div class="footer-wrap-item footer-info">
        <?php //get_template_part('includes/parts/header/header', 'logo'); ?>
        <h2> Open Asia Travel </h2>
        <ul class="footer-info-list">
            <?php
            // wp_nav_menu(array(
            //     'theme_location' => 'contact-info',
            // ));
            ?>
            <div class="menu-contact-infor-container">
                <ul id="menu-contact-infor" class="menu">
                    <!-- <li id="menu-item-603" class="menu-item menu-item-type-custom menu-item-object-custom current-menu-item current_page_item menu-item-home menu-item-603">
                        <a  href="http://openasiatravel.nanoweb.vn/" aria-current="page">Open Asia Travel</a>
                    </li> -->
                    <li id="menu-item-604" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-604 have-icon">
                        <span class="footer-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><!--! Font Awesome Pro 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
                                <path d="M215.7 499.2C267 435 384 279.4 384 192C384 86 298 0 192 0S0 86 0 192c0 87.4 117 243 168.3 307.2c12.3 15.3 35.1 15.3 47.4 0zM192 128a64 64 0 1 1 0 128 64 64 0 1 1 0-128z" />
                            </svg>
                        </span>

                        <a href="https://www.google.com/maps/place/173%2F75+%C4%90.+Ho%C3%A0ng+Hoa+Th%C3%A1m,+Ng%E1%BB%8Dc+H%E1%BB%93,+Ba+%C4%90%C3%ACnh,+H%C3%A0+N%E1%BB%99i,+Vi%E1%BB%87t+Nam/@21.0411381,105.8208083,17z/data=!3m1!4b1!4m6!3m5!1s0x3135ab0bdc9e6e9b:0xb8ad8c2c49cafa20!8m2!3d21.0411331!4d105.8233832!16s%2Fg%2F11g9gk3yc9?hl=vi-VN&amp;entry=ttu">173/75 Hoang Hoa Tham, Ba Dinh, Hanoi, Vietnam</a>
                    </li>
                    <li id="menu-item-605" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-605 have-icon">
                        <span class="footer-icon" >
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--! Font Awesome Pro 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
                                <path d="M164.9 24.6c-7.7-18.6-28-28.5-47.4-23.2l-88 24C12.1 30.2 0 46 0 64C0 311.4 200.6 512 448 512c18 0 33.8-12.1 38.6-29.5l24-88c5.3-19.4-4.6-39.7-23.2-47.4l-96-40c-16.3-6.8-35.2-2.1-46.3 11.6L304.7 368C234.3 334.7 177.3 277.7 144 207.3L193.3 167c13.7-11.2 18.4-30 11.6-46.3l-40-96z" />
                            </svg>
                        </span>
                        <a href="tel:+84%20982%20996%20967">+84 982 996 967</a>
                    </li>
                    <li id="menu-item-606" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-606 have-icon">
                        <span class="footer-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--! Font Awesome Pro 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
                                <path d="M48 64C21.5 64 0 85.5 0 112c0 15.1 7.1 29.3 19.2 38.4L236.8 313.6c11.4 8.5 27 8.5 38.4 0L492.8 150.4c12.1-9.1 19.2-23.3 19.2-38.4c0-26.5-21.5-48-48-48H48zM0 176V384c0 35.3 28.7 64 64 64H448c35.3 0 64-28.7 64-64V176L294.4 339.2c-22.8 17.1-54 17.1-76.8 0L0 176z" />
                            </svg>
                        </span>
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