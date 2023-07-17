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

?>

<article data-tour-id="<?= $tour_id ?>" class="tour_item">
	<div class="tour_item-top">
		<div class="tour_item-content">
			<div class="tour_item-content-wrap">
				<a href="<?= $tour_permalink ?>" class="tour_item-title">
					<?= $tour_title ?>
				</a>
				<?php
				if ($tour_duration) {
				?>
					<div class="tour_item-duraiton tour_item-text">
						<?php
						foreach ($tour_duration as $duration) {
						?>
							<div class="item-duraiton">
								<img src="<?php echo get_template_directory_uri() ?>/css/images/ico__clock.png" alt="">
								<?= $duration->name  ?>
							</div>
						<?php
						}
						?>
					</div>
				<?php
				}
				if ($value_have_price == 'Yes') {
					$static_price = $tour_price['static_price'];
					$market_price = $tour_price['market_price'];
					$save_price = $market_price - $static_price;
				?>
					<div class="tour_item-price">
						<div class="static_price">
							Deal: <strong>US$<?= $static_price ?></strong>
						</div>
						<div class="market_price">
							Typically: <strong>US$<?= $market_price ?></strong>
						</div>
						<div class="save_price">
							You save: <strong>US$<?= $save_price ?></strong>
						</div>
					</div>
				<?php
				}
				if ($tour_tag) {
				?>
					<div class="tour_item-tags">
						<?php
						foreach ($tour_tag as $tag) {
							$thumbnail_id = $bookyourtravel_theme_post_types->get_taxonomy_image_id($tag->term_id);
							$thumbnail_url = wp_get_attachment_image_src($thumbnail_id, "byt-featured")[0];
						?>
							<div class="item-tags">
								<div class="tags-icon">
									<img src="<?= $thumbnail_url ?>" alt="">
								</div>
								<div class="tag-name tour_item-text">
									<?= $tag->name  ?>
								</div>
							</div>
						<?php
						}
						?>
					</div>
				<?php
				}
				?>
				<div class="tour_item-action">
					<div class="tour_item-btn">
						<a href="<?php echo home_url() ?>/tailor-made-your-tour/" class="gray-btn">Request a Quote</a>
					</div>
					<div class="tour_item-btn">
						<a href="<?php echo $tour_permalink ?>">More Details</a>
					</div>
				</div>
			</div>
		</div>
		<!-- check xem tour có gallery không -->
		<div class="tour_item-gallery">
			<button id="slider-prev" class="slider-prev "></button>
			<button id="slider-next" class="slider-next "></button>
			<?php
			$featured_element = $tour_obj->get_displayed_featured_element();

			$images = $entity_obj->get_images();

			if ($images && count($images) > 0) { ?>
				<div id="slider-dots" class="slider-dots">
					<?php foreach ($images as $index => $image) {
						$image_id = $image['image'];
						$image_src = wp_get_attachment_image_src($image_id, 'full');
						$image_alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);
						$image_src = $image_src && is_array($image_src) && count($image_src) > 0 ? $image_src[0] : '';
						if (!empty($image_src)) {
					?>
							<span class="dot" data-index="<?php echo $index ?>"></span>
					<?php
						}
					} ?>
				</div>
				<!--gallery-->
				<ul id="post-gallery" class="cS-hidden post-gallery">
					<?php foreach ($images as $image) {
						$image_id = $image['image'];
						$image_src = wp_get_attachment_image_src($image_id, 'full');
						$image_alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);
						$image_src = $image_src && is_array($image_src) && count($image_src) > 0 ? $image_src[0] : '';
						if (!empty($image_src)) {
					?>
							<li class="gallery-item" data-thumb="<?php echo esc_attr($image_src) ?>">
								<img src="<?php echo esc_attr($image_src) ?>" alt="<?php echo esc_attr($image_alt) ?>" />
							</li>
					<?php
						}
					} ?>
				</ul>
				<!--//gallery-->
			<?php } else {
				echo "khong co gallery";
			} ?>

		</div>
	</div>
	<div class="tour_item-bottom">
		<div class="tour_item-location">

			<?php
			if (get_field('tour_itinerary', get_the_ID())) {
				$tour_itinerary = get_field('tour_itinerary', get_the_ID());
				if ($tour_itinerary) {
			?>
					<div class="tour_itinerary">
						<div class="tour_itinerary-img">
							<img src="<?php echo $tour_itinerary ?>" alt="">
							<img src="<?php echo get_template_directory_uri() ?>/css/images/xmark-solid.svg" class="map-icon" alt="">
						</div>
					</div>
			<?php
				}
			}
			?>

			<div class="tour_item-location-icon">
				<img src="<?php echo get_template_directory_uri() ?>/css/images/ico__location1.png" alt="">
			</div>
			<div class="tour_item-location-sequence">
				<?php
				if (get_field('order_location', get_the_ID())) {
					$order_location = get_field('order_location', get_the_ID());
					$last_index = count($order_location) - 1; // Chỉ số cuối cùng của mảng

					foreach ($order_location as $index => $locations) {
						echo $locations['sequence_of_the_locations'];

						if ($index != $last_index) {
							echo " → ";
						}
					}
				}
				?>
			</div>
		</div>
		<div class="tour_item-description">
			<?= $tour_description ?>
		</div>
	</div>
</article>
<?php
