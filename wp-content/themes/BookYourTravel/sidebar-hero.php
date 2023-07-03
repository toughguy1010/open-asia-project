<?php
/**
 * The sidebar containing the above content/hero widget area.
 *
 * If no active widgets in sidebar, let's hide it completely.
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since Book Your Travel 1.0
 */
if ( is_active_sidebar( 'hero' ) ) {
	ob_start();
	dynamic_sidebar( 'hero' );
	$hero_content = ob_get_clean();
	$hero_content = trim($hero_content);
	$hero_content = str_replace(array('<li class="widget widget-sidebar full-width"></li>'), '', $hero_content);
	if (!empty($hero_content) && $hero_content != '<li class="widget widget-sidebar full-width"></li>') { ?>
		<div id="hero-sidebar" class="hero-sidebar widget-area">
			<ul>
			<?php echo $hero_content; ?>
			</ul>
		</div><!-- #hero -->
	<?php
	} 
}