<?php
/**
 * Javascript navs for single location template
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */

global $bookyourtravel_theme_globals, $post;
 ?>
<script>
	window.postId = <?php echo json_encode($post->ID); ?>;
	window.postType = 'location';
	window.pauseBetweenSlides = <?php echo json_encode($bookyourtravel_theme_globals->get_light_slider_pause_between_slides() * 1000); ?>;
</script>
