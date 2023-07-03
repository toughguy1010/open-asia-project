<?php
/**
 * Post single gallery template part
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */
global $entity_obj;

$images = $entity_obj->get_images();

if ($images && count($images) > 0) { ?>
	<?php
	$image_sources = array();
	for ( $i = 0; $i < count($images); $i++ ) {
		$image = $images[$i];
		$image_id = $image['image'];
		$image_src = wp_get_attachment_image_src($image_id, 'full');
		$image_alt = get_post_meta( $image_id, '_wp_attachment_image_alt', true);
		$image_src = $image_src && is_array($image_src) && count($image_src) > 0 ? $image_src[0] : '';
		if (!empty($image_src)) {
			$image_sources[$image_src] = $image_alt;
		}
	}

	if (count($image_sources) > 1) { ?>
	<!--gallery-->
	<ul id="post-gallery" class="cS-hidden post-gallery">
	<?php foreach ($image_sources as $image_src => $image_alt) {?>
		<li data-thumb="<?php echo esc_attr($image_src) ?>"><img src="<?php echo esc_attr($image_src) ?>" alt="<?php echo esc_attr($image_alt) ?>" /></li>
	<?php } ?>
	</ul>
	<!--//gallery-->
	<?php
	} else if (count($image_sources) > 0) {
        $featured_img_alt = reset($image_sources);
        $featured_img_url = key($image_sources);
    ?>
	<!--featured image-->
	<ul class="featured-image">
		<li><img src="<?php echo esc_attr($featured_img_url) ?>" alt="<?php echo esc_attr($featured_img_alt) ?>" /></li>
	</ul>
	<!--//gallery-->
    <?php
    }
}