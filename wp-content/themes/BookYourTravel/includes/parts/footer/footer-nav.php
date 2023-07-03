<?php
ob_start();
?>
<!--footer navigation-->
<?php if ( has_nav_menu( 'footer-menu' ) ) {
	wp_nav_menu( array( 
		'theme_location' => 'footer-menu', 
		'container' => 'nav', 
	) ); 
} else { ?>
<nav class="menu-main-menu-container">
	<ul class="menu">
		<li class="menu-item"><a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Home', "bookyourtravel"); ?></a></li>
		<li class="menu-item"><a href="<?php echo esc_url( admin_url('nav-menus.php') ); ?>"><?php esc_html_e('Configure', "bookyourtravel"); ?></a></li>
	</ul>
</nav>
<?php } 
$nav_html = ob_get_contents();
ob_end_clean();

echo apply_filters('bookyourtravel_footer_nav', $nav_html);
?>
<!--//footer navigation-->
