<?php
global $bookyourtravel_theme_globals;
$logo_title = get_bloginfo('name') . ' | ' . (is_home() || is_front_page() ? get_bloginfo('description') : wp_title('', false));
ob_start();
?>
<!--logo-->
<div class="logo">
	<a href="<?php echo esc_url(home_url('/')); ?>" title="<?php echo esc_attr($logo_title); ?>">
		<div class="logo-container">
			<h2 class="text-logo">open</h2>
			<h2 class="text-logo">asia travel</h2>
		</div>
	</a>
</div>
<!--//logo-->
<?php
$logo_html = ob_get_contents();
ob_end_clean();
echo apply_filters('bookyourtravel_header_logo', $logo_html);
