<div class="hamburger-lines">
    <span class="line line1"></span>
    <span class="line line2"></span>
    <span class="line line3"></span>
</div>


<div class="mobile-nav">
    <div class="nav-mobile-wrap">
        <div class="mobile-nav-title back-btn">
            Menu
        </div>
            <?php
            global $header_nav_class, $header_menu_class;

            if (empty($header_nav_class))
                $header_nav_class = 'main-nav';
            if (empty($header_menu_class))
                $header_menu_class = '';

            ob_start();
            wp_nav_menu(array('theme_location' => 'primary-menu', 'container' => 'nav', 'container_class' => $header_nav_class, 'container_id' => 'nav', 'menu_class' => $header_menu_class));

            ?>
    </div>
</div>