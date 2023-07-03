<?php
/**
 * The sidebar containing the above the footer widget area.
 *
 * If no active widgets in sidebar, let's hide it completely.
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */
if ( is_active_sidebar( 'above-footer' ) ) : ?>
<div class="above-footer-sidebar">
	<div id="above-footer-sidebar" class="widget-area wrap">
		<ul>
		<?php dynamic_sidebar( 'above-footer' ); ?>
		</ul>
	</div>
</div>
<?php endif;