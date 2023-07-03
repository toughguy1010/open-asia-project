<?php 

global $header_nav_class, $header_menu_class;

if (empty($header_nav_class))
	$header_nav_class = 'main-nav';
if (empty($header_menu_class))
	$header_menu_class = '';
	
ob_start();

?>
<!--primary navigation-->
<?php

$menu_walker = new BookYourTravel_Menu_With_Description();
if ( function_exists('max_mega_menu_is_enabled') && max_mega_menu_is_enabled('primary') ) {
	wp_nav_menu( array( 'theme_location' => 'primary', 'walker' => $menu_walker) );
} else if ( has_nav_menu( 'primary-menu' ) ) {
	
	wp_nav_menu( array( 'theme_location' => 'primary-menu', 'walker' => $menu_walker, 'container' => 'nav', 'container_class' => $header_nav_class, 'container_id' => 'nav', 'menu_class' => $header_menu_class) );
} else { ?>

<nav class="<?php echo esc_attr($header_nav_class); ?>">
	<ul class="<?php echo esc_attr($header_menu_class); ?>">
		<li class="menu-item"><a href="<?php echo esc_url ( home_url('/') ); ?>"><?php esc_html_e('Home', "bookyourtravel"); ?></a></li>
		<li class="menu-item"><a href="<?php echo esc_url ( admin_url('nav-menus.php') ); ?>"><?php esc_html_e('Configure', "bookyourtravel"); ?></a></li>
	</ul>
</nav>
<?php } ?>
<!--//primary navigation-->
<?php
$nav_html = ob_get_contents();

ob_end_clean();
echo apply_filters('bookyourtravel_header_nav', $nav_html);