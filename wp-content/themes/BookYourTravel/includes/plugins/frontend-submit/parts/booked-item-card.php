<?php

global $booked_item_args, $fs_table_list_args;
global $bookyourtravel_theme_globals, $bookyourtravel_review_helper, $current_user;
global $bookyourtravel_accommodation_helper, $bookyourtravel_car_rental_helper, $bookyourtravel_cruise_helper, $bookyourtravel_tour_helper;

$posts_per_row = isset($fs_table_list_args['posts_per_row']) ? $fs_table_list_args['posts_per_row'] : 4;
$item_class = BookYourTravel_Theme_Utils::get_item_class_by_row_posts($posts_per_row);

$post_id = $booked_item_args['post_id'];
$result = $booked_item_args['result'];
$entity_title = get_the_title($post_id);

$entity_thumbnail_html = '';
$entity_thumbnail_id = get_post_thumbnail_id($post_id);
$entity_attachment = get_post($entity_thumbnail_id);
if ($entity_attachment) {
    $entity_image_title = $entity_attachment->post_title;
    $entity_thumbnail_html = get_the_post_thumbnail($post_id, "thumbnail", array('title' => $entity_image_title));
}

$entity_obj = null;

if ($result->entry_type == 'accommodation_booking') {
    $entity_obj = new BookYourTravel_Accommodation($post_id);
    $entity_type = 'accommodation';
} else if ($result->entry_type == 'car_rental_booking') {
    $entity_obj = new BookYourTravel_Car_Rental($post_id);
    $entity_type = 'car_rental';
} else if ($result->entry_type == 'cruise_booking') {
    $entity_obj = new BookYourTravel_Cruise($post_id);
    $entity_type = 'cruise';
} else if ($result->entry_type == 'tour_booking') {
    $entity_obj = new BookYourTravel_Tour($post_id);
    $entity_type = 'tour';
}

$woo_status_name = '';
if (!empty($result->woo_status)) {
    if (function_exists('wc_get_order_status_name')) {
        $woo_status_name = wc_get_order_status_name($result->woo_status);
    }
}

$entity_base_id = $entity_obj->get_base_id();

$entity_ribbon_text = $entity_obj->get_ribbon_text();
$entity_description = wpautop($entity_obj->get_short_description());
$entity_permalink = get_the_permalink($post_id);
$entity_review_score = $entity_obj->get_custom_field('review_score', false, true);
$entity_address = $entity_obj->get_custom_field('address');
$entity_status = $entity_obj->get_status();
$entity_booking_id = (isset($result->Id) ? $result->Id : '');

$current_url = BookYourTravel_Theme_Utils::get_current_page_url_no_query();

$view_booking_url = esc_url( add_query_arg( 'bid', $entity_booking_id, $current_url ));

$entity_total_price = $booked_item_args['total_price'];
?>

<article data-post-id="<?php echo esc_attr($entity_base_id); ?>" data-post-type="<?php echo esc_attr($entity_type); ?>" class="booked_item <?php echo esc_attr($item_class); ?>">
    <div>
        <?php
            if (!empty($entity_thumbnail_html)) {
                BookYourTravel_Theme_Controls::the_entity_figure_start($entity_title, $entity_permalink, false);
                BookYourTravel_Theme_Controls::the_entity_reviews_score($entity_base_id, $entity_review_score);
                BookYourTravel_Theme_Controls::the_entity_figure_middle($entity_thumbnail_html, $entity_ribbon_text);

                if (!empty($woo_status_name)) { ?>
                    <span class="item_status"><?php echo esc_html($woo_status_name); ?></span>
                <?php
                }

                BookYourTravel_Theme_Controls::the_entity_figure_end($entity_permalink);
            }
        ?>
        <div class="details">
            <div class="item-header">
                <?php BookYourTravel_Theme_Controls::the_entity_title($entity_title, $entity_permalink, $entity_status, false); ?>
                <?php BookYourTravel_Theme_Controls::the_entity_address($entity_address); ?>
            </div>
            <?php BookYourTravel_Theme_Controls::the_entity_price($entity_total_price, esc_html__('Total', 'bookyourtravel'), ''); ?>

            <div class='actions'>
                <?php
                    BookYourTravel_Theme_Controls::the_link_button($view_booking_url, "gradient-button view-booking", "", __("View", "bookyourtravel"), true);

                    if ($bookyourtravel_theme_globals->enable_reviews()) {
                        $reviews_by_current_user_query = $bookyourtravel_review_helper->list_reviews($entity_base_id, $current_user->ID);	
                        if ((!$reviews_by_current_user_query->have_posts() && is_user_logged_in()) || is_super_admin()) {
    
                            $user_bookings = null;
                            if ($result->entry_type == 'accommodation_booking') {
                                $user_bookings = $bookyourtravel_accommodation_helper->list_accommodation_bookings(null, 0, 'Id', 'ASC', null, $current_user->ID);
                            } else if ($result->entry_type == 'car_rental_booking') {
                                $user_bookings = $bookyourtravel_car_rental_helper->list_car_rental_bookings(null, 'Id', 'ASC', null, 0, $current_user->ID);
                            } else if ($result->entry_type == 'cruise_booking') {
                                $user_bookings = $bookyourtravel_cruise_helper->list_cruise_bookings(null, 0, 'Id', 'ASC', null, $current_user->ID);
                            } else if ($result->entry_type == 'tour_booking') {
                                $user_bookings =  $bookyourtravel_tour_helper->list_tour_bookings(null, 0, 'Id', 'ASC', null, $current_user->ID);
                            }

                            if ($user_bookings) {
                                $item_is_booked = false;
                                $results = $user_bookings['results'];
                                foreach ($results as $result) {
                                    if ($result->entry_type == 'accommodation_booking') {
                                        if ($result->accommodation_id == $entity_base_id) {
                                            $item_is_booked = true;
                                            break;
                                        }
                                    } else if ($result->entry_type == 'car_rental_booking') {
                                        if ($result->car_rental_id == $entity_base_id) {
                                            $item_is_booked = true;
                                            break;
                                        }
                                    } else if ($result->entry_type == 'cruise_booking') {
                                        if ($result->cruise_id == $entity_base_id) {
                                            $item_is_booked = true;
                                            break;
                                        }
                                    } else if ($result->entry_type == 'tour_booking') {
                                        if ($result->tour_id == $entity_base_id) {
                                            $item_is_booked = true;
                                            break;
                                        }
                                    }                                    
                                }
        
                                if ($item_is_booked || is_super_admin()) {
                                    BookYourTravel_Theme_Controls::the_link_button('#', "gradient-button review-booking", "", __("Review", "bookyourtravel"), true);
                                }
                            }
                        }
                    }
                ?>
            </div>
        </div>
    </div>
</article>