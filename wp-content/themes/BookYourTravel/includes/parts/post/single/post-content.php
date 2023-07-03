<?php
/**
 * Accommodation below tab content template part
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */
global $tab, $post, $found_post_content, $first_display_tab, $layout_class, $bookyourtravel_theme_globals, $entity_obj, $default_location_extra_fields, $echo_post_title;

if (isset($entity_obj)) {
	$content = $entity_obj->get_content();
	if (!empty($content)) {
		$found_post_content = true;
?>
<section class="post-general-content">
	<?php if ($echo_post_title) { ?>
		<h2><?php echo esc_html($entity_obj->get_title()); ?></h2>
	<?php } ?>
	<?php the_content(); ?>
</section>
<?php }
}