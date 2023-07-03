<?php
/**
 * Post single image template part
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */

global $post, $bookyourtravel_theme_globals, $bookyourtravel_post_helper;
?>
<?php if ( has_post_thumbnail() ) { ?>
<div class="page-featured-image">
	<?php $featured_img_url = get_the_post_thumbnail_url(get_the_ID(), "byt-featured"); ?>
	<div class="keyvisual" style="background-image:url(<?php echo esc_url($featured_img_url); ?>)"></div>
	<div class="wrap"><h1><?php the_title(); ?></h1></div>
</div>
<?php } ?>
