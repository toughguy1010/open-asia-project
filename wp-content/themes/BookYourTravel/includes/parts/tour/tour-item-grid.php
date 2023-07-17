<?php

/**
 * Tour item template part
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */

global $current_url, $post, $date_from, $date_to, $bookyourtravel_theme_globals, $bookyourtravel_tour_helper, $tour_list_args, $tour_item_args, $bookyourtravel_theme_post_types, $entity_obj;
$tour_id = $tour_item_args['tour_id'];
$tour_permalink = '';
$tour_description = '';
$tour_title = '';
$tour_thumbnail_html = '';
$tour_ribbon_text = '';
if ($tour_id > 0) {
    $tour_post = $tour_item_args['post'];
    global $post;
    if (!$post || $post->ID != $tour_id) {
        $post = $tour_post;
        setup_postdata($post);
    }

    $tour_obj = new BookYourTravel_Tour($post);
    $entity_obj = $tour_obj;

    $tour_description = wpautop($tour_obj->get_short_description());
    if ($post) {
        $thumbnail_id = get_post_thumbnail_id($post->ID);
        $attachment = get_post($thumbnail_id);
        if ($attachment) {
            $image_title = $attachment->post_title; //The Title
            $tour_thumbnail_html = get_the_post_thumbnail($post->ID, "thumbnail", array('title' => $image_title));
        }
    }
    $tour_permalink = get_the_permalink();
    $tour_title = get_the_title();
    // custom
    $tour_duration = $tour_obj->get_tour_durations();
    $tour_tag = $tour_obj->get_tags();
    if ($tour_price = get_field('tour_price', get_the_ID())) {
		$have_price = $tour_price['have_price'];
		
		if (isset($have_price) && is_array($have_price) && count($have_price) > 0) {
			$value_have_price = $have_price[0];
		} else {
			// Xử lý khi mảng $have_price không tồn tại hoặc không có phần tử
			$value_have_price = '';
		}
	}
}
if (get_field('order_location', get_the_ID())) {
    $order_location = get_field('order_location', get_the_ID());
    $total_location = count($order_location);
}

?>
<article data-tour-id="<?= $tour_id ?>" class="tour_grid_item">
    <div class="tour_grid-top">
        <a href="<?= $tour_permalink ?>" class="tour_grid-thumbnail">
            <?php
            $featured_element = $tour_obj->get_displayed_featured_element();
            $images = $entity_obj->get_images();
            $img =  $images[0];
            $thumbnail_id = $img['image'];
            $thumbnail_url =  wp_get_attachment_image_src($thumbnail_id, 'full');
            ?>
            <img src="<?= $thumbnail_url[0] ?>" alt="">
        </a>
        <div class="tour_grid-top-content">
            <?php
            if ($tour_duration) {
            ?>
                <div class="tour_item-duraiton tour_item-text">
                    <?php
                    foreach ($tour_duration as $duration) {
                    ?>
                        <div class="item-duraiton">
                            <?= $duration->name  ?>
                        </div>
                    <?php
                    }
                    ?>
                </div>
            <?php
            }
            ?>
            <div class="tour_location-sequence tour_item-text">
                <?= $total_location ?> Cities
            </div>
        </div>
    </div>
    <div class="tour_grid-bottom">
        <div class="tour_grid-bottom-content">
            <div class="tour_grid-title">
                <?= $tour_title ?>
            </div>
            <div class="tour_grid-description">
                <?= $tour_description ?>
            </div>

        </div>

        <div class="tour_item-action">
            <div class="tour_item-btn">
                <a href="<?php echo home_url() ?>/tailor-made-your-tour/" class="gray-btn">Request a Quote</a>
            </div>
            <div class="tour_item-btn">
                <a href="<?php echo $tour_permalink ?>">More Details</a>
            </div>
        </div>
    </div>
</article>
<?php
