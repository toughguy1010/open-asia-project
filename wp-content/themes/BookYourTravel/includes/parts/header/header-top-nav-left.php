<?php 
ob_start();
?>
<!--primary navigation-->
<?php
if ( function_exists('max_mega_menu_is_enabled') && max_mega_menu_is_enabled('top-nav-left') ) {
	wp_nav_menu( array( 'theme_location' => 'top-nav-left') );
} else if ( has_nav_menu( 'top-nav-left-menu' ) ) {
	wp_nav_menu( array( 'theme_location' => 'top-nav-left-menu', 'container' => 'nav', 'container_class' => 'top-nav-left', 'container_id' => 'nav', 'menu_class' => '') );
} else { ?>
<nav class="top-nav-left">
	<ul>
		<li class="menu-item"><a href="<?php echo esc_url ( home_url('/') ); ?>"><?php esc_html_e('Home', "bookyourtravel"); ?></a></li>
		<li class="menu-item"><a href="<?php echo esc_url ( admin_url('nav-menus.php') ); ?>"><?php esc_html_e('Configure', "bookyourtravel"); ?></a></li>
	</ul>
</nav>
<?php } ?>
<!--//primary navigation-->
<?php
$nav_html = ob_get_contents();
ob_end_clean();
echo apply_filters('bookyourtravel_top_header_nav', $nav_html);