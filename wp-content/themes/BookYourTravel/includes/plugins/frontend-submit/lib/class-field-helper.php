<?php

require_once BookYourTravel_Theme_Utils::get_file_path('/includes/plugins/frontend-submit/lib/class-html-helper.php');

class Frontend_Submit_Field_Helper {

	protected $enable_accommodations = false;
	protected $enable_car_rentals = false;
	protected $enable_cruises = false;
	protected $enable_tours = false;
	protected $html_helper;

	protected $user_has_correct_role = false;
	protected $has_admin_role = false;
	protected $current_user_id = 0;
	protected $date_format;

	function __construct($current_user_id, $user_has_correct_role = false, $has_admin_role = false) {
		global $bookyourtravel_theme_globals;

		$this->html_helper = new Html_Helper();
		$this->date_format = get_option('date_format');
		$this->enable_accommodations = $bookyourtravel_theme_globals->enable_accommodations();
		$this->enable_tours = $bookyourtravel_theme_globals->enable_tours();
		$this->enable_cruises = $bookyourtravel_theme_globals->enable_cruises();
		$this->enable_car_rentals = $bookyourtravel_theme_globals->enable_car_rentals();

		$this->has_admin_role = $has_admin_role;
        $this->user_has_correct_role = $user_has_correct_role;
        $this->current_user_id = $current_user_id;
	}

	function init() {
		add_action('bookyourtravel_render_entry_generic_fields', array($this, 'render_entry_generic_fields'), 10, 3);
		add_action('bookyourtravel_render_entry_extra_fields', array($this, 'render_entry_extra_fields'), 10, 3);
        add_action('bookyourtravel_render_entry_field_scripts', array($this, 'render_entry_field_scripts'), 10, 1);
        add_action('bookyourtravel_frontend_save_extra_fields', array($this, 'save_extra_fields'), 10, 3);
	}

	function render_entry_generic_fields($entry_id, $entry, $content_type) {

		$output = "";

		ob_start();

		$atts = array( 'type' => 'hidden', 'role' => 'internal', 'name' => 'post_author', 'id' => 'fes_post_author', 'value' =>  $this->current_user_id );
		echo $this->render_input($entry, $content_type, $atts);

		$atts = array( 'type' => 'hidden', 'role' => 'internal', 'name' => 'content_type', 'id' => 'fes_content_type', 'value' =>  $content_type );
		echo $this->render_input($entry, $content_type, $atts);

		if ($entry_id > 0) {
			$atts = array( 'type' => 'hidden', 'role' => 'internal', 'name' => 'entry_id', 'value' => $entry_id, 'id' => 'fes_entry_id' );
			echo $this->render_input($entry, $content_type, $atts);
		}

		if ( ($this->enable_accommodations && $content_type == 'accommodation') ||
			 ($this->enable_accommodations && $content_type == 'room_type') ||
			 ($this->enable_tours && $content_type == 'tour') ||
			 ($this->enable_cruises && $content_type == 'cruise') ||
			 ($this->enable_cruises && $content_type == 'cabin_type') ||
			 ($this->enable_car_rentals && $content_type == 'car_rental') ||
			  $content_type == 'location') {

			$atts = array( 'type' => 'text', 'role' => 'title', 'name' => 'post_title',	'id' => 'fes_post_title', 'class' => 'required', 'label' => esc_html__( 'Title', 'bookyourtravel' ) );
			echo $this->render_input($entry, $content_type, $atts);

			if ($content_type == 'room_type' || $content_type == 'cabin_type') {
				$atts = array( 'role' => 'content', 'name' => 'post_content', 'id' => 'fes_post_content', 'class' => '', 'label' => esc_html__( 'Default WP content', 'bookyourtravel' ), 'wysiwyg_enabled' => true );
				echo $this->render_textarea($entry, $content_type, $atts);
			}

			if ($content_type == 'location') {

				$args = array(
					'posts_per_page'   => -1,
					'orderby'          => 'title',
					'order'            => 'ASC',
					'post_type'        => 'location',
					'post_status'      => array('publish', 'private', 'draft'),
					'suppress_filters' => true
				);

				if ($entry_id > 0) {
					$args['exclude'] = $entry_id;
				}

				// if (!$this->has_admin_role) {
				// 	$args['author'] = $this->current_user_id;
				// }

				$posts_array = get_posts( $args );

				if (count($posts_array) > 0) {
					$post_values_str = "::" . __("Select one", "bookyourtravel") . ",";
					foreach ($posts_array as $a_post) {
						$post_title = str_replace(",", "", $a_post->post_title);
						$post_values_str .= "{$a_post->ID}::{$post_title},";
					}
					$post_values_str = rtrim($post_values_str, ',');

					$atts = array( 'type' => 'select', 'role' => 'content', 'name' => 'post_parent', 'id' => 'fes_post_parent', 'class' => '', 'label' => __('Parent location', 'bookyourtravel'), 'description' => '', 'values' => $post_values_str );
					echo $this->render_select($entry, $content_type, $atts);
				}
			}

			$feature_image_uri = "";
			$featured_image_id = "";

			if ($entry != null && $entry_id > 0) {
				$feature_image_uri = $entry->get_main_image('medium');
				$featured_image_id = get_post_thumbnail_id( $entry_id );
			}

			if ($featured_image_id > 0) {
				echo '<script>';
				echo 'window.featuredImageUri = ' . json_encode($feature_image_uri) . ';';
				echo 'window.featuredImageId = ' . json_encode($featured_image_id) . ';';
				echo '</script>';
			}

			echo '<div class="fes-input-wrapper">';
			echo '<label>' . esc_html__( 'Featured image', 'bookyourtravel' ) . '</label>';
			echo '<div id="featured-image-uploader" class="dropzone"></div><input type="hidden" id="featured-image-id" name="featured-image-id" value="' . $featured_image_id . '">';
			echo '</div>';

		} else if (	($this->enable_accommodations && $content_type == 'accommodation_vacancy') ||
			($this->enable_accommodations && $content_type == 'accommodation_booking') ||
			($this->enable_car_rentals && $content_type == 'car_rental_availability') ||
			($this->enable_car_rentals && $content_type == 'car_rental_booking') ||
			($this->enable_cruises && $content_type == 'cruise_schedule') ||
			($this->enable_cruises && $content_type == 'cruise_booking') ||
			($this->enable_tours && $content_type == 'tour_schedule') ||
			($this->enable_tours && $content_type == 'tour_booking')) {

			// no real generic fields to render. Might refactor to some later;
		}

		$output = ob_get_clean();

		echo $output;
	}

	function render_entry_field_scripts($content_type) {

		$output = '';

		switch ($content_type) {
			case "accommodation":
				if ($this->enable_accommodations) {
					$output .= $this->render_accommodation_field_scripts();
				}
				break;
			case "room_type":
				if ($this->enable_accommodations) {
					$output .= $this->render_room_type_field_scripts();
				}
				break;
			case "accommodation_vacancy":
				if ($this->enable_accommodations) {
					$output .= $this->render_accommodation_vacancy_field_scripts();
				}
				break;
			case "accommodation_booking":
				if ($this->enable_accommodations) {
					$output .= $this->render_accommodation_booking_field_scripts();
				}
				break;
			case "car_rental":
				if ($this->enable_car_rentals) {
					$output .= $this->render_car_rental_field_scripts();
				}
				break;
			case "car_rental_availability":
				if ($this->enable_car_rentals) {
					$output .= $this->render_car_rental_availability_field_scripts();
				}
				break;
			case "car_rental_booking":
				if ($this->enable_car_rentals) {
					$output .= $this->render_car_rental_booking_field_scripts();
				}
				break;
			case "cruise":
				if ($this->enable_cruises) {
					$output .= $this->render_cruise_field_scripts();
				}
				break;
			case "cabin_type":
				if ($this->enable_cruises) {
					$output .= $this->render_cabin_type_field_scripts();
				}
				break;
			case "cruise_schedule":
				if ($this->enable_cruises) {
					$output .= $this->render_cruise_schedule_field_scripts();
				}
				break;
			case "cruise_booking":
				if ($this->enable_cruises) {
					$output .= $this->render_cruise_booking_field_scripts();
				}
				break;
			case "tour":
				if ($this->enable_tours) {
					$output .= $this->render_tour_field_scripts();
				}
				break;
			case "tour_schedule":
				if ($this->enable_tours) {
					$output .= $this->render_tour_schedule_field_scripts();
				}
				break;
			case "tour_booking":
				if ($this->enable_tours) {
					$output .= $this->render_tour_booking_field_scripts();
				}
				break;
			case "location":
				$output .= $this->render_location_field_scripts();
				break;
			default:
				break;
		}

		echo $output;
	}

	function save_car_rental_booking($entry_id, $entry) {

        global $bookyourtravel_car_rental_helper;

        if (isset($_POST['first_name']) &&
            isset($_POST['last_name']) &&
            isset($_POST['start_date']) &&
            isset($_POST['end_date']) &&
            isset($_POST['email']) &&
            isset($_POST['car_rental_id'])) {

            $booking_object = new stdClass();

            $booking_object->Id = $entry_id > 0 ? intval($entry_id) : 0;
            $booking_object->user_id = $this->current_user_id;
            $booking_object->total_price = 0;
            $booking_object->total_car_rental_price = 0;
            $booking_object->total_extra_items_price = 0;

            $booking_object->first_name = isset( $_POST['first_name'] ) ? sanitize_text_field( $_POST['first_name'] ) : '';
            $booking_object->last_name = isset( $_POST['last_name'] ) ? sanitize_text_field( $_POST['last_name'] ) : '';
            $booking_object->company = isset( $_POST['company'] ) ? sanitize_text_field( $_POST['company'] ) : '';
            $booking_object->email = isset( $_POST['email'] ) ? sanitize_text_field( $_POST['email'] ) : '';
            $booking_object->phone = isset( $_POST['phone'] ) ? sanitize_text_field( $_POST['phone'] ) : '';
            $booking_object->address = isset( $_POST['address'] ) ? sanitize_text_field( $_POST['address'] ) : '';
            $booking_object->address_2 = isset( $_POST['address_2'] ) ? sanitize_text_field( $_POST['address_2'] ) : '';
            $booking_object->town = isset( $_POST['town'] ) ? sanitize_text_field( $_POST['town'] ) : '';
            $booking_object->zip = isset( $_POST['zip'] ) ? sanitize_text_field( $_POST['zip'] ) : '';
            $booking_object->state = isset( $_POST['state'] ) ? sanitize_text_field( $_POST['state'] ) : '';
            $booking_object->country = isset( $_POST['country'] ) ? sanitize_text_field( $_POST['country'] ) : '';
            $booking_object->special_requirements = isset( $_POST['special_requirements'] ) ? sanitize_text_field( $_POST['special_requirements'] ) : '';

            $start_date = isset( $_POST['start_date'] ) ? sanitize_text_field( $_POST['start_date'] ) : '';
            $booking_object->date_from = date('Y-m-d', strtotime($start_date));
            $booking_object->start_date = date('Y-m-d', strtotime($start_date));

            $end_date = isset( $_POST['end_date'] ) ? sanitize_text_field( $_POST['end_date'] ) : '';
            $booking_object->date_to = date('Y-m-d', strtotime($end_date));
            $booking_object->end_date = date('Y-m-d', strtotime($end_date));

            $booking_object->car_rental_id = isset( $_POST['car_rental_id'] ) ? intval( $_POST['car_rental_id'] ) : 0;

            $booking_object->total_car_rental_price = isset( $_POST['total_car_rental_price'] ) ? intval( $_POST['total_car_rental_price'] ) : 0;
            $booking_object->total_extra_items_price = isset( $_POST['total_extra_items_price'] ) ? intval( $_POST['total_extra_items_price'] ) : 0;
            $booking_object->total_price = isset( $_POST['total_price'] ) ? intval( $_POST['total_price'] ) : 0;
			$booking_object->cart_price = isset( $_POST['cart_price'] ) ? intval( $_POST['cart_price'] ) : 0;
            if (!isset($_POST['cart_price'])) {
                $booking_object->cart_price = $booking_object->total_price;
            }

            if ($entry_id > 0) {
                $bookyourtravel_car_rental_helper->update_car_rental_booking ($entry_id, $booking_object);
            } else {
                $booking_object->Id = $bookyourtravel_car_rental_helper->create_car_rental_booking($this->current_user_id, $booking_object);
                $entry_id = $booking_object->Id;

				global $bookyourtravel_theme_globals, $bookyourtravel_theme_woocommerce;

				$address_array = array(
					'first_name' => isset($booking_object->first_name) ? $booking_object->first_name : '',
					'last_name'  => isset($booking_object->last_name) ? $booking_object->last_name : '',
					'company'    => isset($booking_object->company) ? $booking_object->company : '',
					'email'      => isset($booking_object->email) ? $booking_object->email : '',
					'phone'      => isset($booking_object->phone) ? $booking_object->phone : '',
					'address_1'  => isset($booking_object->address) ? $booking_object->address : '',
					'address_2'  => isset($booking_object->address_2) ? $booking_object->address_2 : '',
					'city'       => isset($booking_object->city) ? $booking_object->city : '',
					'state'      => isset($booking_object->state) ? $booking_object->state : '',
					'postcode'   => isset($booking_object->postcode) ? $booking_object->postcode : '',
					'country'    => isset($booking_object->country) ? $booking_object->country : '',
				);

				$car_rental_obj = new BookYourTravel_Car_Rental($booking_object->car_rental_id);				

				$car_rental_is_reservation_only = $car_rental_obj->get_is_reservation_only();
				$use_woocommerce_for_checkout = $bookyourtravel_theme_globals->use_woocommerce_for_checkout();

				if (BookYourTravel_Theme_Utils::is_woocommerce_active() && $use_woocommerce_for_checkout && !$car_rental_is_reservation_only) {
					$bookyourtravel_theme_woocommerce->dynamically_create_car_rental_woo_order($booking_object->Id, $booking_object->cart_price, $address_array, $booking_object->car_rental_id);
				}	
            }
        }

        return $entry_id;
    }

	function save_car_rental_availability($entry_id, $entry) {

        global $bookyourtravel_car_rental_helper;

		$start_date = isset( $_POST['start_date'] ) ? sanitize_text_field( $_POST['start_date'] ) : '';
		$end_date = isset( $_POST['end_date'] ) ? sanitize_text_field( $_POST['end_date'] ) : '';

		$start_date = date('Y-m-d', strtotime($start_date));
		$end_date = date('Y-m-d', strtotime($end_date));

        $car_rental_id = isset( $_POST['car_rental_id'] ) ? intval( $_POST['car_rental_id'] ) : 0;

        $price_per_day = isset( $_POST['price_per_day'] ) ?  sanitize_text_field ( $_POST['price_per_day'] ) : 0;

        $number_of_cars = isset( $_POST['number_of_cars'] ) ?  sanitize_text_field ( $_POST['number_of_cars'] ) : 1;

		$season_name = isset( $_POST['season_name'] ) ?  sanitize_text_field ( $_POST['season_name'] ) : '';

		if ($entry_id > 0 && $entry != null ) {
            $bookyourtravel_car_rental_helper->update_car_rental_availability($entry_id, $car_rental_id, $season_name, $start_date, $end_date, $number_of_cars, $price_per_day);
        } else {
            $entry_id = $bookyourtravel_car_rental_helper->create_car_rental_availability($season_name, $car_rental_id, $start_date, $end_date, $number_of_cars, $price_per_day);
        }

        return $entry_id;
    }

	function save_tour_schedule($entry_id, $entry) {

        global $bookyourtravel_tour_helper;

		$start_date = isset( $_POST['start_date'] ) ? sanitize_text_field( $_POST['start_date'] ) : '';
		$end_date = isset( $_POST['end_date'] ) ? sanitize_text_field( $_POST['end_date'] ) : '';

		$start_date = date('Y-m-d', strtotime($start_date));
		$end_date = date('Y-m-d', strtotime($end_date));

        $tour_id = isset( $_POST['tour_id'] ) ? intval( $_POST['tour_id'] ) : 0;

        $price = isset( $_POST['price'] ) ?  sanitize_text_field ( $_POST['price'] ) : 0;
        $price_child = isset( $_POST['price_child'] ) ? sanitize_text_field( $_POST['price_child'] ) : null;

        $max_people = isset( $_POST['max_people'] ) ?  sanitize_text_field ( $_POST['max_people'] ) : 1;

		$season_name = isset( $_POST['season_name'] ) ?  sanitize_text_field ( $_POST['season_name'] ) : '';

		if ($entry_id > 0 && $entry != null ) {
            $bookyourtravel_tour_helper->update_tour_schedule($entry_id, $season_name, $start_date, $tour_id, $price, $price_child, $max_people, $end_date);
        } else {
            $entry_id = $bookyourtravel_tour_helper->create_tour_schedule($season_name, $tour_id, $start_date, $price, $price_child, $max_people, $end_date);
        }

        return $entry_id;
    }

	function save_tour_booking($entry_id, $entry) {

        global $bookyourtravel_tour_helper;

        if (isset($_POST['first_name']) &&
            isset($_POST['last_name']) &&
            isset($_POST['tour_date']) &&
            isset($_POST['email']) &&
            isset($_POST['tour_id'])) {

            $booking_object = new stdClass();
            $booking_object->user_id = $this->current_user_id;
            $booking_object->Id = $entry_id > 0 ? intval($entry_id) : 0;

            $booking_object->total_price = 0;
            $booking_object->total_tour_price = 0;
            $booking_object->total_extra_items_price = 0;

            $booking_object->first_name = isset( $_POST['first_name'] ) ? sanitize_text_field( $_POST['first_name'] ) : '';
            $booking_object->last_name = isset( $_POST['last_name'] ) ? sanitize_text_field( $_POST['last_name'] ) : '';
            $booking_object->company = isset( $_POST['company'] ) ? sanitize_text_field( $_POST['company'] ) : '';
            $booking_object->email = isset( $_POST['email'] ) ? sanitize_text_field( $_POST['email'] ) : '';
            $booking_object->phone = isset( $_POST['phone'] ) ? sanitize_text_field( $_POST['phone'] ) : '';
            $booking_object->address = isset( $_POST['address'] ) ? sanitize_text_field( $_POST['address'] ) : '';
            $booking_object->address_2 = isset( $_POST['address_2'] ) ? sanitize_text_field( $_POST['address_2'] ) : '';
            $booking_object->town = isset( $_POST['town'] ) ? sanitize_text_field( $_POST['town'] ) : '';
            $booking_object->zip = isset( $_POST['zip'] ) ? sanitize_text_field( $_POST['zip'] ) : '';
            $booking_object->state = isset( $_POST['state'] ) ? sanitize_text_field( $_POST['state'] ) : '';
            $booking_object->country = isset( $_POST['country'] ) ? sanitize_text_field( $_POST['country'] ) : '';
            $booking_object->special_requirements = isset( $_POST['special_requirements'] ) ? sanitize_text_field( $_POST['special_requirements'] ) : '';

            $tour_date = isset( $_POST['tour_date'] ) ? sanitize_text_field( $_POST['tour_date'] ) : '';

            $booking_object->tour_date = date('Y-m-d', strtotime($tour_date));

            $booking_object->tour_id = isset( $_POST['tour_id'] ) ? intval( $_POST['tour_id'] ) : 0;

            $adults = isset( $_POST['adults'] ) ? intval( $_POST['adults'] ) : 0;
            $booking_object->adults = $adults > 0 ? $adults : 1;

            $children = isset( $_POST['children'] ) ? intval( $_POST['children'] ) : 0;
            $booking_object->children = $children > 0 ? $children : 1;

            $booking_object->total_tour_price = isset( $_POST['total_tour_price'] ) ? intval( $_POST['total_tour_price'] ) : 0;
            $booking_object->total_extra_items_price = isset( $_POST['total_extra_items_price'] ) ? intval( $_POST['total_extra_items_price'] ) : 0;
            $booking_object->total_price = isset( $_POST['total_price'] ) ? intval( $_POST['total_price'] ) : 0;
			$booking_object->cart_price = isset( $_POST['cart_price'] ) ? intval( $_POST['cart_price'] ) : 0;
            if (!isset($_POST['cart_price'])) {
                $booking_object->cart_price = $booking_object->total_price;
            }

            if ($entry_id > 0) {
                $bookyourtravel_tour_helper->update_tour_booking ($entry_id, $booking_object);
            } else {
                $booking_object->Id = $bookyourtravel_tour_helper->create_tour_booking($this->current_user_id, $booking_object);
                $entry_id = $booking_object->Id;

				global $bookyourtravel_theme_globals, $bookyourtravel_theme_woocommerce;

				$address_array = array(
					'first_name' => isset($booking_object->first_name) ? $booking_object->first_name : '',
					'last_name'  => isset($booking_object->last_name) ? $booking_object->last_name : '',
					'company'    => isset($booking_object->company) ? $booking_object->company : '',
					'email'      => isset($booking_object->email) ? $booking_object->email : '',
					'phone'      => isset($booking_object->phone) ? $booking_object->phone : '',
					'address_1'  => isset($booking_object->address) ? $booking_object->address : '',
					'address_2'  => isset($booking_object->address_2) ? $booking_object->address_2 : '',
					'city'       => isset($booking_object->city) ? $booking_object->city : '',
					'state'      => isset($booking_object->state) ? $booking_object->state : '',
					'postcode'   => isset($booking_object->postcode) ? $booking_object->postcode : '',
					'country'    => isset($booking_object->country) ? $booking_object->country : '',
				);

				$tour_obj = new BookYourTravel_Tour($booking_object->tour_id);				

				$tour_is_reservation_only = $tour_obj->get_is_reservation_only();
				$use_woocommerce_for_checkout = $bookyourtravel_theme_globals->use_woocommerce_for_checkout();

				if (BookYourTravel_Theme_Utils::is_woocommerce_active() && $use_woocommerce_for_checkout && !$tour_is_reservation_only) {
					$bookyourtravel_theme_woocommerce->dynamically_create_tour_woo_order($booking_object->Id, $booking_object->cart_price, $address_array, $booking_object->tour_id);
				}	
            }
        }

        return $entry_id;
    }

	function save_cruise_schedule($entry_id, $entry) {

        global $bookyourtravel_cruise_helper;

		$start_date = isset( $_POST['start_date'] ) ? sanitize_text_field( $_POST['start_date'] ) : '';
		$end_date = isset( $_POST['end_date'] ) ? sanitize_text_field( $_POST['end_date'] ) : '';

		$start_date = date('Y-m-d', strtotime($start_date));
		$end_date = date('Y-m-d', strtotime($end_date));

        $cruise_id = isset( $_POST['cruise_id'] ) ? intval( $_POST['cruise_id'] ) : 0;
        $cabin_type_id = isset( $_POST['cabin_type_id'] ) ? intval( $_POST['cabin_type_id'] ) : 0;
        $cabin_count = isset( $_POST['cabin_count'] ) ? intval( $_POST['cabin_count'] ) : '';
        $cabin_count = $cabin_count > 0 ? $cabin_count : 1;

        $price = isset( $_POST['price'] ) ?  sanitize_text_field ( $_POST['price'] ) : 0;
        $price_child = isset( $_POST['price_child'] ) ? sanitize_text_field( $_POST['price_child'] ) : null;

		$season_name = isset( $_POST['season_name'] ) ?  sanitize_text_field ( $_POST['season_name'] ) : '';

		if ($entry_id > 0 && $entry != null ) {
            $bookyourtravel_cruise_helper->update_cruise_schedule($entry_id, $season_name, $cruise_id, $cabin_type_id, $cabin_count, $start_date, $price, $price_child, $end_date);
        } else {
            $entry_id = $bookyourtravel_cruise_helper->create_cruise_schedule($season_name, $cruise_id, $cabin_type_id, $cabin_count, $start_date, $price, $price_child, $end_date);
        }

		return $entry_id;
    }

	function save_cruise_booking($entry_id, $entry) {

        global $bookyourtravel_cruise_helper;

        if (isset($_POST['first_name']) &&
            isset($_POST['last_name']) &&
            isset($_POST['cruise_date']) &&
            isset($_POST['email']) &&
            isset($_POST['cruise_id']) &&
            isset($_POST['cabin_type_id'])) {

            $booking_object = new stdClass();
            $booking_object->user_id = $this->current_user_id;
            $booking_object->Id = $entry_id > 0 ? intval($entry_id) : 0;

            $booking_object->total_price = 0;
            $booking_object->total_cruise_price = 0;
            $booking_object->total_extra_items_price = 0;

            $booking_object->first_name = isset( $_POST['first_name'] ) ? sanitize_text_field( $_POST['first_name'] ) : '';
            $booking_object->last_name = isset( $_POST['last_name'] ) ? sanitize_text_field( $_POST['last_name'] ) : '';
            $booking_object->company = isset( $_POST['company'] ) ? sanitize_text_field( $_POST['company'] ) : '';
            $booking_object->email = isset( $_POST['email'] ) ? sanitize_text_field( $_POST['email'] ) : '';
            $booking_object->phone = isset( $_POST['phone'] ) ? sanitize_text_field( $_POST['phone'] ) : '';
            $booking_object->address = isset( $_POST['address'] ) ? sanitize_text_field( $_POST['address'] ) : '';
            $booking_object->address_2 = isset( $_POST['address_2'] ) ? sanitize_text_field( $_POST['address_2'] ) : '';
            $booking_object->town = isset( $_POST['town'] ) ? sanitize_text_field( $_POST['town'] ) : '';
            $booking_object->zip = isset( $_POST['zip'] ) ? sanitize_text_field( $_POST['zip'] ) : '';
            $booking_object->state = isset( $_POST['state'] ) ? sanitize_text_field( $_POST['state'] ) : '';
            $booking_object->country = isset( $_POST['country'] ) ? sanitize_text_field( $_POST['country'] ) : '';
            $booking_object->special_requirements = isset( $_POST['special_requirements'] ) ? sanitize_text_field( $_POST['special_requirements'] ) : '';

            $cruise_date = isset( $_POST['cruise_date'] ) ? sanitize_text_field( $_POST['cruise_date'] ) : '';
            $booking_object->cruise_date = date('Y-m-d', strtotime($cruise_date));

            $booking_object->cruise_id = isset( $_POST['cruise_id'] ) ? intval( $_POST['cruise_id'] ) : 0;
            $booking_object->cabin_type_id = isset( $_POST['cabin_type_id'] ) ? intval( $_POST['cabin_type_id'] ) : 0;

            $adults = isset( $_POST['adults'] ) ? intval( $_POST['adults'] ) : 0;
            $booking_object->adults = $adults > 0 ? $adults : 1;

            $children = isset( $_POST['children'] ) ? intval( $_POST['children'] ) : 0;
            $booking_object->children = $children > 0 ? $children : 1;

            $booking_object->total_cruise_price = isset( $_POST['total_cruise_price'] ) ? intval( $_POST['total_cruise_price'] ) : 0;
            $booking_object->total_extra_items_price = isset( $_POST['total_extra_items_price'] ) ? intval( $_POST['total_extra_items_price'] ) : 0;
            $booking_object->total_price = isset( $_POST['total_price'] ) ? intval( $_POST['total_price'] ) : 0;
			$booking_object->cart_price = isset( $_POST['cart_price'] ) ? intval( $_POST['cart_price'] ) : 0;
            if (!isset($_POST['cart_price'])) {
                $booking_object->cart_price = $booking_object->total_price;
            }

            if ($entry_id > 0) {
                $bookyourtravel_cruise_helper->update_cruise_booking ($entry_id, $booking_object);
            } else {
                $booking_object->Id = $bookyourtravel_cruise_helper->create_cruise_booking($this->current_user_id, $booking_object);
                $entry_id = $booking_object->Id;

				global $bookyourtravel_theme_globals, $bookyourtravel_theme_woocommerce;

				$address_array = array(
					'first_name' => isset($booking_object->first_name) ? $booking_object->first_name : '',
					'last_name'  => isset($booking_object->last_name) ? $booking_object->last_name : '',
					'company'    => isset($booking_object->company) ? $booking_object->company : '',
					'email'      => isset($booking_object->email) ? $booking_object->email : '',
					'phone'      => isset($booking_object->phone) ? $booking_object->phone : '',
					'address_1'  => isset($booking_object->address) ? $booking_object->address : '',
					'address_2'  => isset($booking_object->address_2) ? $booking_object->address_2 : '',
					'city'       => isset($booking_object->city) ? $booking_object->city : '',
					'state'      => isset($booking_object->state) ? $booking_object->state : '',
					'postcode'   => isset($booking_object->postcode) ? $booking_object->postcode : '',
					'country'    => isset($booking_object->country) ? $booking_object->country : '',
				);

				$cruise_obj = new BookYourTravel_Cruise($booking_object->cruise_id);				

				$cruise_is_reservation_only = $cruise_obj->get_is_reservation_only();
				$use_woocommerce_for_checkout = $bookyourtravel_theme_globals->use_woocommerce_for_checkout();

				if (BookYourTravel_Theme_Utils::is_woocommerce_active() && $use_woocommerce_for_checkout && !$cruise_is_reservation_only) {
					$bookyourtravel_theme_woocommerce->dynamically_create_cruise_woo_order($booking_object->Id, $booking_object->cart_price, $address_array, $booking_object->cruise_id, $booking_object->cabin_type_id);
				}	
            }
        }

        return $entry_id;
    }

    function save_accommodation_vacancy($entry_id, $entry) {
		global $bookyourtravel_accommodation_helper;

		$start_date = isset( $_POST['start_date'] ) ? sanitize_text_field( $_POST['start_date'] ) : '';
		$end_date = isset( $_POST['end_date'] ) ? sanitize_text_field( $_POST['end_date'] ) : '';

		$start_date = date('Y-m-d', strtotime($start_date));
		$end_date = date('Y-m-d', strtotime($end_date));

		$accommodation_id = isset( $_POST['accommodation_id'] ) ? intval( $_POST['accommodation_id'] ) : 0;
		$room_type_id = isset( $_POST['room_type_id'] ) ? intval( $_POST['room_type_id'] ) : 0;
		$room_count = isset( $_POST['room_count'] ) ? intval( $_POST['room_count'] ) : 0;
		$room_count = $room_count > 0 ? $room_count : 1;

		$price_per_day = isset( $_POST['price_per_day'] ) ? sanitize_text_field ( $_POST['price_per_day'] ) : 0;
		$price_per_day_child = isset( $_POST['price_per_day_child'] ) ? sanitize_text_field( $_POST['price_per_day_child'] ) : null;
		$weekend_price_per_day = isset( $_POST['weekend_price_per_day'] ) ?  sanitize_text_field ( $_POST['weekend_price_per_day'] ) : 0;
		$weekend_price_per_day_child = isset( $_POST['weekend_price_per_day_child'] ) ? sanitize_text_field( $_POST['weekend_price_per_day_child'] ) : null;

		$season_name = isset( $_POST['season_name'] ) ?  sanitize_text_field ( $_POST['season_name'] ) : '';

		if ($entry_id > 0 && $entry != null ) {
			$bookyourtravel_accommodation_helper->update_accommodation_vacancy($entry_id, $season_name, $start_date, $end_date, $accommodation_id, $room_type_id, $room_count, $price_per_day, $price_per_day_child, $weekend_price_per_day, $weekend_price_per_day_child);
		} else {
			$entry_id = $bookyourtravel_accommodation_helper->create_accommodation_vacancy($season_name, $start_date, $end_date, $accommodation_id, $room_type_id, $room_count, $price_per_day, $price_per_day_child, $weekend_price_per_day, $weekend_price_per_day_child);
		}

		return $entry_id;
    }

	function save_accommodation_booking($entry_id, $entry) {
        global $bookyourtravel_accommodation_helper;

        if (isset($_POST['first_name']) &&
            isset($_POST['last_name']) &&
            isset($_POST['date_from']) &&
            isset($_POST['date_to']) &&
            isset($_POST['email']) &&
            isset($_POST['accommodation_id'])) {

            $booking_object = new stdClass();

            $booking_object->user_id = $this->current_user_id;
            $booking_object->Id = $entry_id > 0 ? intval($entry_id) : 0;

            $booking_object->total_price = 0;
            $booking_object->total_accommodation_price = 0;
            $booking_object->total_extra_items_price = 0;

            $booking_object->first_name = isset( $_POST['first_name'] ) ? sanitize_text_field( $_POST['first_name'] ) : '';
            $booking_object->last_name = isset( $_POST['last_name'] ) ? sanitize_text_field( $_POST['last_name'] ) : '';
            $booking_object->company = isset( $_POST['company'] ) ? sanitize_text_field( $_POST['company'] ) : '';
            $booking_object->email = isset( $_POST['email'] ) ? sanitize_text_field( $_POST['email'] ) : '';
            $booking_object->phone = isset( $_POST['phone'] ) ? sanitize_text_field( $_POST['phone'] ) : '';
            $booking_object->address = isset( $_POST['address'] ) ? sanitize_text_field( $_POST['address'] ) : '';
            $booking_object->address_2 = isset( $_POST['address_2'] ) ? sanitize_text_field( $_POST['address_2'] ) : '';
            $booking_object->town = isset( $_POST['town'] ) ? sanitize_text_field( $_POST['town'] ) : '';
            $booking_object->zip = isset( $_POST['zip'] ) ? sanitize_text_field( $_POST['zip'] ) : '';
            $booking_object->state = isset( $_POST['state'] ) ? sanitize_text_field( $_POST['state'] ) : '';
            $booking_object->country = isset( $_POST['country'] ) ? sanitize_text_field( $_POST['country'] ) : '';
            $booking_object->special_requirements = isset( $_POST['special_requirements'] ) ? sanitize_text_field( $_POST['special_requirements'] ) : '';

            $date_from = isset( $_POST['date_from'] ) ? sanitize_text_field( $_POST['date_from'] ) : '';
            $date_to = isset( $_POST['date_to'] ) ? sanitize_text_field( $_POST['date_to'] ) : '';

            $booking_object->date_from = date('Y-m-d', strtotime($date_from));
            $booking_object->date_to = date('Y-m-d', strtotime($date_to));

            $booking_object->accommodation_id = isset( $_POST['accommodation_id'] ) ? intval( $_POST['accommodation_id'] ) : 0;
            $booking_object->room_type_id = isset( $_POST['room_type_id'] ) ? intval( $_POST['room_type_id'] ) : 0;
            $booking_object->room_count = isset( $_POST['room_count'] ) ? intval( $_POST['room_count'] ) : 0;
            $booking_object->room_count = $room_count > 0 ? $room_count : 1;

            $adults = isset( $_POST['adults'] ) ? intval( $_POST['adults'] ) : 0;
            $booking_object->adults = $adults > 0 ? $adults : 1;

            $children = isset( $_POST['children'] ) ? intval( $_POST['children'] ) : 0;
            $booking_object->children = $children > 0 ? $children : 1;

            $booking_object->total_accommodation_price = isset( $_POST['total_accommodation_price'] ) ? intval( $_POST['total_accommodation_price'] ) : 0;
            $booking_object->total_extra_items_price = isset( $_POST['total_extra_items_price'] ) ? intval( $_POST['total_extra_items_price'] ) : 0;
            $booking_object->total_price = isset( $_POST['total_price'] ) ? intval( $_POST['total_price'] ) : 0;
			$booking_object->cart_price = isset( $_POST['cart_price'] ) ? intval( $_POST['cart_price'] ) : 0;
            if (!isset($_POST['cart_price'])) {
                $booking_object->cart_price = $booking_object->total_price;
            }

            if ($entry_id > 0) {
                $bookyourtravel_accommodation_helper->update_accommodation_booking ($entry_id, $booking_object);
            } else {
                $booking_object->Id = $bookyourtravel_accommodation_helper->create_accommodation_booking($this->current_user_id, $booking_object);
                $entry_id = $booking_object->Id;

				global $bookyourtravel_theme_globals, $bookyourtravel_theme_woocommerce;

				$address_array = array(
					'first_name' => isset($booking_object->first_name) ? $booking_object->first_name : '',
					'last_name'  => isset($booking_object->last_name) ? $booking_object->last_name : '',
					'company'    => isset($booking_object->company) ? $booking_object->company : '',
					'email'      => isset($booking_object->email) ? $booking_object->email : '',
					'phone'      => isset($booking_object->phone) ? $booking_object->phone : '',
					'address_1'  => isset($booking_object->address) ? $booking_object->address : '',
					'address_2'  => isset($booking_object->address_2) ? $booking_object->address_2 : '',
					'city'       => isset($booking_object->city) ? $booking_object->city : '',
					'state'      => isset($booking_object->state) ? $booking_object->state : '',
					'postcode'   => isset($booking_object->postcode) ? $booking_object->postcode : '',
					'country'    => isset($booking_object->country) ? $booking_object->country : '',
				);

				$accommodation_obj = new BookYourTravel_Accommodation($booking_object->accommodation_id);				

				$accommodation_is_reservation_only = $accommodation_obj->get_is_reservation_only();
				$use_woocommerce_for_checkout = $bookyourtravel_theme_globals->use_woocommerce_for_checkout();

				if (BookYourTravel_Theme_Utils::is_woocommerce_active() && $use_woocommerce_for_checkout && !$accommodation_is_reservation_only) {
					$bookyourtravel_theme_woocommerce->dynamically_create_accommodation_woo_order($booking_object->Id, $booking_object->cart_price, $address_array, $booking_object->accommodation_id, $booking_object->room_type_id);
				}				
            }
        }

		return $entry_id;
	}

    function save_extra_fields($entry_id, $entry, $content_type) {
		$output = '';

		switch ($content_type) {
			case "accommodation":
				if ($this->enable_accommodations) {
					$this->save_accommodation_fields($entry_id, $entry, $content_type);
				}
				break;
			case "room_type":
				if ($this->enable_accommodations) {
					$this->save_room_type_fields($entry_id, $entry, $content_type);
				}
				break;
			case "accommodation_vacancy":
				if ($this->enable_accommodations) {
					$this->save_accommodation_vacancy_fields($entry_id, $entry, $content_type);
				}
				break;
			case "accommodation_booking":
				if ($this->enable_accommodations) {
					$this->save_accommodation_booking_fields($entry_id, $entry, $content_type);
				}
				break;
			case "car_rental":
				if ($this->enable_car_rentals) {
					$this->save_car_rental_fields($entry_id, $entry, $content_type);
				}
				break;
			case "car_rental_availability":
				if ($this->enable_car_rentals) {
					$this->save_car_rental_availability_fields($entry_id, $entry, $content_type);
				}
				break;
			case "car_rental_booking":
				if ($this->enable_car_rentals) {
					$this->save_car_rental_booking_fields($entry_id, $entry, $content_type);
				}
				break;
			case "cruise":
				if ($this->enable_cruises) {
					$this->save_cruise_fields($entry_id, $entry, $content_type);
				}
				break;
			case "cabin_type":
				if ($this->enable_cruises) {
					$this->save_cabin_type_fields($entry_id, $entry, $content_type);
				}
				break;
			case "cruise_schedule":
				if ($this->enable_cruises) {
					$this->save_cruise_schedule_fields($entry_id, $entry, $content_type);
				}
				break;
			case "cruise_booking":
				if ($this->enable_cruises) {
					$this->save_cruise_booking_fields($entry_id, $entry, $content_type);
				}
				break;
			case "tour":
				if ($this->enable_tours) {
					$this->save_tour_fields($entry_id, $entry, $content_type);
				}
				break;
			case "tour_schedule":
				if ($this->enable_tours) {
					$this->save_tour_schedule_fields($entry_id, $entry, $content_type);
				}
				break;
			case "tour_booking":
				if ($this->enable_tours) {
					$this->save_tour_booking_fields($entry_id, $entry, $content_type);
				}
				break;
			case "location":
				$this->save_location_fields($entry_id, $entry, $content_type);
				break;
			default:
				break;
		}
    }

	function render_entry_extra_fields($entry_id, $entry, $content_type) {
		$output = '';

		switch ($content_type) {
			case "accommodation":
				if ($this->enable_accommodations) {
					$output .= $this->render_accommodation_fields($entry_id, $entry, $content_type);
				}
				break;
			case "room_type":
				if ($this->enable_accommodations) {
					$output .= $this->render_room_type_fields($entry_id, $entry, $content_type);
				}
				break;
			case "accommodation_vacancy":
				if ($this->enable_accommodations) {
					$output .= $this->render_accommodation_vacancy_fields($entry_id, $entry, $content_type);
				}
				break;
			case "accommodation_booking":
				if ($this->enable_accommodations) {
					$output .= $this->render_accommodation_booking_fields($entry_id, $entry, $content_type);
				}
				break;
			case "car_rental":
				if ($this->enable_car_rentals) {
					$output .= $this->render_car_rental_fields($entry_id, $entry, $content_type);
				}
				break;
			case "car_rental_availability":
				if ($this->enable_car_rentals) {
					$output .= $this->render_car_rental_availability_fields($entry_id, $entry, $content_type);
				}
				break;
			case "car_rental_booking":
				if ($this->enable_car_rentals) {
					$output .= $this->render_car_rental_booking_fields($entry_id, $entry, $content_type);
				}
				break;
			case "cruise":
				if ($this->enable_cruises) {
					$output .= $this->render_cruise_fields($entry_id, $entry, $content_type);
				}
				break;
			case "cabin_type":
				if ($this->enable_cruises) {
					$output .= $this->render_cabin_type_fields($entry_id, $entry, $content_type);
				}
				break;
			case "cruise_schedule":
				if ($this->enable_cruises) {
					$output .= $this->render_cruise_schedule_fields($entry_id, $entry, $content_type);
				}
				break;
			case "cruise_booking":
				if ($this->enable_cruises) {
					$output .= $this->render_cruise_booking_fields($entry_id, $entry, $content_type);
				}
				break;
			case "tour":
				if ($this->enable_tours) {
					$output .= $this->render_tour_fields($entry_id, $entry, $content_type);
				}
				break;
			case "tour_schedule":
				if ($this->enable_tours) {
					$output .= $this->render_tour_schedule_fields($entry_id, $entry, $content_type);
				}
				break;
			case "tour_booking":
				if ($this->enable_tours) {
					$output .= $this->render_tour_booking_fields($entry_id, $entry, $content_type);
				}
				break;
			case "location":
				$output .= $this->render_location_fields($entry_id, $entry, $content_type);
				break;
			default:
				break;
		}

		echo $output;
	}

	/*
	Field scripts rendering
	*/

	function render_accommodation_field_scripts() {
		return '';
	}

	function render_room_type_field_scripts() {
		return '';
	}

	function render_accommodation_vacancy_field_scripts() {

		ob_start();
		?>
		<script>
			window.pricePerDayLabel = <?php echo json_encode(__('Price per day', 'bookyourtravel')); ?>;
			window.pricePerWeekLabel = <?php echo json_encode(__('Price per week', 'bookyourtravel')); ?>;
			window.pricePerMonthLabel = <?php echo json_encode(__('Price per month', 'bookyourtravel')); ?>;
			window.pricePerDayChildLabel = <?php echo json_encode(__('Price per day (child)', 'bookyourtravel')); ?>;
			window.pricePerWeekChildLabel = <?php echo json_encode(__('Price per week (child)', 'bookyourtravel')); ?>;
			window.pricePerMonthChildLabel = <?php echo json_encode(__('Price per month (child)', 'bookyourtravel')); ?>;
		</script>
		<?php
		$output = ob_get_clean();

		return $output;
	}

	function render_accommodation_booking_field_scripts() {
		return '';
	}

	function render_location_field_scripts() {
		return '';
	}

	function render_car_rental_field_scripts() {
		return '';
	}

	function render_car_rental_availability_field_scripts() {
		return '';
	}

	function render_car_rental_booking_field_scripts() {
		return '';
	}

	function render_cruise_field_scripts() {
		return '';
	}

	function render_cabin_type_field_scripts() {
		return '';
	}

	function render_cruise_schedule_field_scripts() {
		return '';
	}

	function render_cruise_booking_field_scripts() {
		return '';
	}

	function render_tour_field_scripts() {
		return '';
	}

	function render_tour_schedule_field_scripts() {
		return '';
	}

	function render_tour_booking_field_scripts() {
		return '';
	}


	/*
	Field saving/updating
	*/

	function save_accommodation_fields($entry_id, $entry, $content_type) {
		global $bookyourtravel_accommodation_helper;
        $fields = $bookyourtravel_accommodation_helper->get_custom_meta_fields();

        $this->_save_extra_fields($entry_id, $entry, $content_type, $fields);
	}

	function save_room_type_fields($entry_id, $entry, $content_type) {
		global $bookyourtravel_room_type_helper;
        $fields = $bookyourtravel_room_type_helper->get_custom_meta_fields();

        $this->_save_extra_fields($entry_id, $entry, $content_type, $fields);
	}

	function save_accommodation_vacancy_fields($entry_id, $entry, $content_type) {

    }

	function save_accommodation_booking_fields($entry_id, $entry, $content_type) {

	}

	function save_location_fields($entry_id, $entry, $content_type) {
		global $bookyourtravel_location_helper;
        $fields = $bookyourtravel_location_helper->get_custom_meta_fields();

        $this->_save_extra_fields($entry_id, $entry, $content_type, $fields);
	}

	function save_car_rental_fields($entry_id, $entry, $content_type) {
		global $bookyourtravel_car_rental_helper;
        $fields = $bookyourtravel_car_rental_helper->get_custom_meta_fields();

        $this->_save_extra_fields($entry_id, $entry, $content_type, $fields);
	}

	function save_car_rental_availability_fields($entry_id, $entry, $content_type) {

    }

	function save_car_rental_booking_fields($entry_id, $entry, $content_type) {

	}

	function save_cruise_fields($entry_id, $entry, $content_type) {
		global $bookyourtravel_cruise_helper;
        $fields = $bookyourtravel_cruise_helper->get_custom_meta_fields();

        $this->_save_extra_fields($entry_id, $entry, $content_type, $fields);
	}

	function save_cabin_type_fields($entry_id, $entry, $content_type) {
		global $bookyourtravel_cabin_type_helper;
        $fields = $bookyourtravel_cabin_type_helper->get_custom_meta_fields();

        $this->_save_extra_fields($entry_id, $entry, $content_type, $fields);
	}

	function save_cruise_schedule_fields($entry_id, $entry, $content_type) {

    }

	function save_cruise_booking_fields($entry_id, $entry, $content_type) {

	}

	function save_tour_fields($entry_id, $entry, $content_type) {
		global $bookyourtravel_tour_helper;
        $fields = $bookyourtravel_tour_helper->get_custom_meta_fields();

        $this->_save_extra_fields($entry_id, $entry, $content_type, $fields);
	}

	function save_tour_schedule_fields($entry_id, $entry, $content_type) {
	}

	function save_tour_booking_fields($entry_id, $entry, $content_type) {

    }

    function _save_extra_fields($entry_id, $entry, $content_type, $fields) {
        if ($fields != null) {
            foreach ($fields as $field) {
                $this->_save_extra_field($entry_id, $entry, $content_type, $field);
            }
        }
    }

    function _save_extra_field($entry_id, $entry, $content_type, $field) {
        if ($field != null) {
            $field_id = isset($field['id']) ? $field["id"] : "";
            $field_type = isset($field['type']) ? $field["type"] : "";

            switch ($field_type) {
                case 'tax_checkboxes': {

                    $value = isset($_POST[$field_id]) ? $_POST[$field_id] : array();
                    $value = array_map( array( $this, 'sanitize_array_element_callback' ), $value );

					$term_ids = array();
					foreach ($value as $term_id) {
						$term_ids[] = intval($term_id);
                    }

					wp_set_post_terms( $entry_id, $term_ids, $field_id);

                    break;
                }
                case 'tax_select': {

                    $value = isset($_POST[$field_id]) ? intval($_POST[$field_id]) : 0;

					wp_set_post_terms( $entry_id, array($value), $field_id);

                    break;
                }
                case 'post_select': {

                    $value = isset($_POST[$field_id]) ? intval($_POST[$field_id]) : 0;

                    update_post_meta( $entry_id, $field_id, $value );

                    break;
                }
                case 'post_checkboxes': {

					$value = isset($_POST[$field_id]) ? $_POST[$field_id] : '';
					if (is_array($value)) {
						$value = array_map( array( $this, 'sanitize_array_element_callback' ), $value );

						$post_ids = array();
						foreach ($value as $post_id) {
							$post_ids[] = "$post_id";
						}
	
						update_post_meta( $entry_id, $field_id, $post_ids );
					}

                    break;
                }
                case 'repeatable': {

                    break;
                }
                case "checkboxes": {
                    $value = isset($_POST[$field_id]) ? $_POST[$field_id] : '';
                    $value = array_map( array( $this, 'sanitize_array_element_callback' ), $value );

                    update_post_meta( $entry_id, $field_id, $value );
                }
				case "image": {
					if (isset($_POST[$field_id])) {
						$image_array = json_decode($_POST[$field_id]);
						if (is_array($image_array) && count($image_array) > 0) {
							$image_id = $image_array[0];
							update_post_meta($entry_id, $field_id, $image_id);
						}
					}

					break;
				}
                default: {

					global $allowedtags;
				
					$allowedtags['iframe'] = array(
						'src' => array(),
						'width' => array(),
						'height' => array(),
						'frameborder' => array(),
						'style' => array(),
						'allowfullscreen' => array(),
						'aria-hidden' => array(),
						'tabindex' => array(),
						'referrerpolicy' => array(),
						'loading' => array(),
					);

                    $value = isset($_POST[$field_id]) ? $_POST[$field_id] : '';
                    $value = wp_kses( $value, $allowedtags );

                    update_post_meta( $entry_id, $field_id, $value );

                    break;
                }
            }
        }
    }

	/*
	Field rendering
	*/

	function render_accommodation_fields($entry_id, $entry, $content_type) {

		global $bookyourtravel_accommodation_helper;
		$output = "";

		$tabs = $bookyourtravel_accommodation_helper->get_custom_meta_tabs();
		$fields = $bookyourtravel_accommodation_helper->get_custom_meta_fields();

		ob_start();
		$this->_render_tabs_and_fields($entry_id, $entry, $content_type, $tabs, $fields);
		$output = ob_get_clean();

		return $output;
	}

	function render_room_type_fields($entry_id, $entry, $content_type) {
		global $bookyourtravel_room_type_helper;
		$output = "";

		$tabs = $bookyourtravel_room_type_helper->get_custom_meta_tabs();
		$fields = $bookyourtravel_room_type_helper->get_custom_meta_fields();

		ob_start();
		$this->_render_tabs_and_fields($entry_id, $entry, $content_type, $tabs, $fields);
		$output = ob_get_clean();

		return $output;
	}

	function render_accommodation_vacancy_fields($entry_id, $entry, $content_type) {
		global $bookyourtravel_accommodation_helper;

		$output = "";
		$fields = $bookyourtravel_accommodation_helper->get_accommodation_vacancy_fields();

		ob_start();
		$this->_render_extra_fields($entry_id, $entry, $content_type, '', $fields);
		$output = ob_get_clean();

		return $output;
	}

	function render_accommodation_booking_fields($entry_id, $entry, $content_type) {
		global $bookyourtravel_accommodation_helper;

		$output = "";
		$fields = $bookyourtravel_accommodation_helper->get_accommodation_booking_fields();

		ob_start();
		$this->_render_extra_fields($entry_id, $entry, $content_type, '', $fields);
		$output = ob_get_clean();

		return $output;
	}

	function render_location_fields($entry_id, $entry, $content_type) {
		global $bookyourtravel_location_helper;
		$output = "";

		$tabs = $bookyourtravel_location_helper->get_custom_meta_tabs();
		$fields = $bookyourtravel_location_helper->get_custom_meta_fields();

		ob_start();
		$this->_render_tabs_and_fields($entry_id, $entry, $content_type, $tabs, $fields);
		$output = ob_get_clean();

		return $output;
	}

	function render_car_rental_fields($entry_id, $entry, $content_type) {
		global $bookyourtravel_car_rental_helper;
		$output = "";

		$tabs = $bookyourtravel_car_rental_helper->get_custom_meta_tabs();
		$fields = $bookyourtravel_car_rental_helper->get_custom_meta_fields();

		ob_start();
		$this->_render_tabs_and_fields($entry_id, $entry, $content_type, $tabs, $fields);
		$output = ob_get_clean();

		return $output;
	}

	function render_car_rental_availability_fields($entry_id, $entry, $content_type) {
		global $bookyourtravel_car_rental_helper;

		$output = "";
		$fields = $bookyourtravel_car_rental_helper->get_car_rental_availability_fields();

		ob_start();
		$this->_render_extra_fields($entry_id, $entry, $content_type, '', $fields);
		$output = ob_get_clean();

		return $output;
	}

	function render_car_rental_booking_fields($entry_id, $entry, $content_type) {
		global $bookyourtravel_car_rental_helper;

		$output = "";
		$fields = $bookyourtravel_car_rental_helper->get_car_rental_booking_fields();

		ob_start();
		$this->_render_extra_fields($entry_id, $entry, $content_type, '', $fields);
		$output = ob_get_clean();

		return $output;
	}

	function render_cruise_fields($entry_id, $entry, $content_type) {
		global $bookyourtravel_cruise_helper;
		$output = "";

		$tabs = $bookyourtravel_cruise_helper->get_custom_meta_tabs();
		$fields = $bookyourtravel_cruise_helper->get_custom_meta_fields();

		ob_start();
		$this->_render_tabs_and_fields($entry_id, $entry, $content_type, $tabs, $fields);
		$output = ob_get_clean();

		return $output;
	}

	function render_cabin_type_fields($entry_id, $entry, $content_type) {
		global $bookyourtravel_cabin_type_helper;
		$output = "";

		$tabs = $bookyourtravel_cabin_type_helper->get_custom_meta_tabs();
		$fields = $bookyourtravel_cabin_type_helper->get_custom_meta_fields();

		ob_start();
		$this->_render_tabs_and_fields($entry_id, $entry, $content_type, $tabs, $fields);
		$output = ob_get_clean();

		return $output;
	}

	function render_cruise_schedule_fields($entry_id, $entry, $content_type) {
		global $bookyourtravel_cruise_helper;

		$output = "";
		$fields = $bookyourtravel_cruise_helper->get_cruise_schedule_fields();

		ob_start();
		$this->_render_extra_fields($entry_id, $entry, $content_type, '', $fields);
		$output = ob_get_clean();

		return $output;
	}

	function render_cruise_booking_fields($entry_id, $entry, $content_type) {
		global $bookyourtravel_cruise_helper;

		$output = "";
		$fields = $bookyourtravel_cruise_helper->get_cruise_booking_fields();

		ob_start();
		$this->_render_extra_fields($entry_id, $entry, $content_type, '', $fields);
		$output = ob_get_clean();

		return $output;
	}

	function render_tour_fields($entry_id, $entry, $content_type) {
		global $bookyourtravel_tour_helper;
		$output = "";

		$tabs = $bookyourtravel_tour_helper->get_custom_meta_tabs();
		$fields = $bookyourtravel_tour_helper->get_custom_meta_fields();

		ob_start();
		$this->_render_tabs_and_fields($entry_id, $entry, $content_type, $tabs, $fields);
		$output = ob_get_clean();

		return $output;
	}

	function render_tour_schedule_fields($entry_id, $entry, $content_type) {
		global $bookyourtravel_tour_helper;

		$output = "";
		$fields = $bookyourtravel_tour_helper->get_tour_schedule_fields();

		ob_start();
		$this->_render_extra_fields($entry_id, $entry, $content_type, '', $fields);
		$output = ob_get_clean();

		return $output;
	}

	function render_tour_booking_fields($entry_id, $entry, $content_type) {
		global $bookyourtravel_tour_helper;

		$output = "";
		$fields = $bookyourtravel_tour_helper->get_tour_booking_fields();

		ob_start();
		$this->_render_extra_fields($entry_id, $entry, $content_type, '', $fields);
		$output = ob_get_clean();

		return $output;
	}

	/*
	Render helpers
	*/
	function _render_tabs_and_fields($entry_id, $entry, $content_type, $tabs, $fields) {

		echo "<div class='extra_fields_wrap'>";

		if (isset($tabs) && count($tabs) > 0) {
			echo sprintf("<ul class='%s-field-tabs field-tabs'>", $content_type);
			foreach ($tabs as $tab) {
				echo "<li><a href='#" . $tab["class"] . "'>" . $tab["label"] . "</a></li>";
			}
			echo "</ul>";
		}

		echo "<div class='extra_fields'>";

		if (isset($tabs) && count($tabs) > 0) {
			foreach ($tabs as $tab) {
				$tab_id = $tab["class"];
				echo sprintf('<div style="display:none" class="field-tab-content %s">', $tab_id); // display:none
				$this->_render_extra_fields($entry_id, $entry, $content_type, $tab_id, $fields);
				echo "</div><!--field-tab-content-->";
			}
		} else {
			echo sprintf('<div class="field-tab-content %s">', $tab_id);
			$this->_render_extra_fields($entry_id, $entry, $content_type, '', $fields);
			echo "</div><!--field-tab-content-->";
		}

		echo "</div><!--extra_fields-->";
		echo "</div><!--extra_fields_wrap-->";
	}

	function _render_extra_fields($entry_id, $entry, $content_type, $tab_id, $fields) {

        if ($fields) {
            foreach ($fields as $field) {
                $field_tab_id = isset($field['admin_tab_id']) ? $field["admin_tab_id"] : "";

                if ($field_tab_id == $tab_id || empty($tab_id)) {
                    $field_container_class = isset($field['field_container_class']) ? $field["field_container_class"] : "";

                    echo sprintf('<div class="field %s %s">', $field_container_class, $field["id"]);
                    echo $this->_render_extra_field($entry_id, $entry, $content_type, $field);
                    echo "</div>";
                }
            }
        }
	}

    function _render_extra_field($entry_id, $entry, $content_type, $field) {

        $field_id = isset($field['id']) ? $field["id"] : "";
        $field_desc = isset($field['desc']) ? $field["desc"] : "";
        $field_type = isset($field['type']) ? $field["type"] : "";
        $field_label = isset($field['label']) ? $field["label"] : "";
		$field_override_class = isset($field['field_override_class']) ? $field["field_override_class"] : "";

		switch ($field_type) {
			case "text":
				$atts = array( 'type' => 'text', 'role' => 'content', 'name' => $field_id, 'id' => 'fes_' . $field_id, 'class' => '' . $field_override_class, 'label' => $field_label, 'description' => $field_desc );
				echo $this->render_input($entry, $content_type, $atts);
				break;
			case "textarea":
				$atts = array( 'type' => 'text', 'role' => 'content', 'name' => $field_id, 'id' => 'fes_' . $field_id, 'class' => '' . $field_override_class, 'label' => $field_label, 'description' => $field_desc, 'wysiwyg_enabled' => false );
				echo $this->render_textarea($entry, $content_type, $atts);
				break;
			case "editor":
				$atts = array( 'role' => 'content', 'name' => $field_id, 'id' => 'fes_' . $field_id, 'class' => '' . $field_override_class, 'label' => $field_label, 'description' => $field_desc, 'wysiwyg_enabled' => true );
				echo $this->render_textarea($entry, $content_type, $atts);
				break;
			case "select":
				if (isset($field["options"])) {
					$values = "::" . __("Select one", "bookyourtravel") . ",";
					foreach ($field["options"] as $option) {
						$val = $option["value"];
						$label = str_replace(",", "", $option["label"]);
						$values .= sprintf("%s::%s,", $val, $label);
					}
					$values = rtrim($values, ',');
					$atts = array( 'role' => 'content', 'name' => $field_id, 'id' => 'fes_' . $field_id, 'class' => '' . $field_override_class, 'label' => $field_label, 'description' => $field_desc, 'values' => $values );
					echo $this->render_select( $entry, $content_type, $atts );
				}
				break;
			case "checkbox":
				$atts = array( 'role' => 'content', 'name' => $field_id, 'id' => 'fes_' . $field_id, 'class' => '' . $field_override_class, 'label' => $field_label, 'description' => $field_desc, 'value' => '1' );
				echo $this->render_checkbox_input( $entry, $content_type, $atts );
				break;
			case "tax_checkboxes":

				$taxonomies = array( $field_id );
				$args = array( 'hide_empty' => false, 'fields' => 'all' );
				$taxonomy_values = get_terms($taxonomies, $args);
				$taxonomy_values_str = '';
				foreach ($taxonomy_values as $taxonomy_value) {
					$taxonomy_title = str_replace(",", "", $taxonomy_value->name);
					$taxonomy_values_str .= "{$taxonomy_value->term_id}::{$taxonomy_title},";
				}
				$taxonomy_values_str = rtrim($taxonomy_values_str, ',');

				if (!empty($taxonomy_values_str)) {
					$atts = array( 'type' => 'checkbox', 'role' => 'content', 'name' => $field_id, 'id' => 'fes_' . $field_id, 'class' => 'checkboxes ' . $field_override_class, 'label' => $field_label, 'description' => $field_desc, 'values' => $taxonomy_values_str );
					echo $this->render_checkboxes($entry, $content_type, $atts);
				}

				break;
			case "tax_select":

				$taxonomies = array( $field_id );
				$args = array( 'hide_empty' => false, 'fields' => 'all' );
				$taxonomy_values = get_terms($taxonomies, $args);
                $taxonomy_values_str = "::" . __("Select one", "bookyourtravel") . ",";
				foreach ($taxonomy_values as $taxonomy_value) {
					$taxonomy_title = str_replace(",", "", $taxonomy_value->name);
					$taxonomy_values_str .= "{$taxonomy_value->term_id}::{$taxonomy_title},";
				}
				$taxonomy_values_str = rtrim($taxonomy_values_str, ',');

				$atts = array( 'type' => 'select', 'role' => 'content', 'name' => $field_id, 'id' => 'fes_' . $field_id, 'class' => '' . $field_override_class, 'label' => $field_label, 'description' => $field_desc, 'values' => $taxonomy_values_str );
				echo $this->render_select($entry, $content_type, $atts);

				break;

			case "post_select":
				$post_type = isset($field["post_type"]) ? $field["post_type"] : "post";

				$args = array(
					'posts_per_page'   => -1,
					'orderby'          => 'title',
					'order'            => 'ASC',
					'post_type'        => $post_type,
					'post_status'      => array('publish', 'private', 'draft'),
					'suppress_filters' => true
				);

				if (!$this->has_admin_role && $post_type != 'location' && $post_type != array('location')) {
					$args['author'] = $this->current_user_id;
				}

				$posts_array = get_posts( $args );

				$post_values_str = "::" . __("Select one", "bookyourtravel") . ",";
				foreach ($posts_array as $a_post) {
					$post_title = str_replace(",", "", $a_post->post_title);
					$post_values_str .= "{$a_post->ID}::{$post_title},";
				}
				$post_values_str = rtrim($post_values_str, ',');

				$atts = array( 'type' => 'select', 'role' => 'content', 'name' => $field_id, 'id' => 'fes_' . $field_id, 'class' => '' . $field_override_class, 'label' => $field_label, 'description' => $field_desc, 'values' => $post_values_str );
				echo $this->render_select($entry, $content_type, $atts);

				break;

			case "post_checkboxes":
				$post_type = isset($field["post_type"]) ? $field["post_type"] : "post";

				$args = array(
					'posts_per_page'   => -1,
					'orderby'          => 'title',
					'order'            => 'ASC',
					'post_type'        => $post_type,
					'post_status'      => array('publish', 'private', 'draft'),
					'suppress_filters' => true
				);

				if (!$this->has_admin_role && $post_type != 'location' && $post_type != array('location')) {
					$args['author'] = $this->current_user_id;
				}

				$posts_array = get_posts( $args );

				$post_values_str = "";
				foreach ($posts_array as $a_post) {
					$post_title = str_replace(",", "", $a_post->post_title);
					$post_values_str .= "{$a_post->ID}::{$post_title},";
				}
				$post_values_str = rtrim($post_values_str, ',');

				if (!empty($post_values_str)) {
					$atts = array( 'type' => 'checkbox', 'role' => 'content', 'name' => $field_id, 'id' => 'fes_' . $field_id, 'class' => 'checkboxes ' . $field_override_class, 'label' => $field_label, 'description' => $field_desc, 'values' => $post_values_str );
					echo $this->render_checkboxes($entry, $content_type, $atts);
				}

				break;
			case "slider":

				$min = isset($field["min"]) ? intval($field["min"]) : 0;
				$max = isset($field["max"]) ? intval($field["max"]) : 10;
				$step = isset($field["step"]) ? intval($field["step"]) : 1;

				$value = 0;
				if ($entry && $entry_id > 0) {
					$value = $this->get_entry_field_value($entry, $content_type, $field_id);
				}

				if ($value == 0 && $min > 0) {
					$value = $min;
				}
				if ($value > $max) {
					$value = $max;
				}

                echo '<div class="slider">';
				echo '<label for="' . esc_attr( $field_id ) . '-slider">' . $field_label . '</label>';
				echo '<div id="' . esc_attr( $field_id ) . '-slider" class="' . $field_override_class . '"></div>';
				echo '<input type="text" name="' . esc_attr( $field_id ) . '" id="fes_' . esc_attr( $field_id ) . '" value="' . $value . '" size="5" />';
				echo '<script>
						jQuery(function( $) {
							if ($.fn.slider) {
								$( "#' . $field_id . '-slider" ).slider({
									value: ' . $value . ',
									min: ' . $min . ',
									max: ' . $max . ',
									step: ' . $step . ',
									slide: function( event, ui ) {
										$( "#fes_' . $field_id . '" ).val( ui.value );
									}
								});
							}
						});
                    </script>';
                echo "</div>";
				echo '<span>' . $field_desc . '</span>';

				break;
			case "repeatable":

				if ($field_id == $content_type . "_images") {
					$gallery_images = array();

					if ($entry != null && $entry_id > 0) {
						$gallery_images = $entry->get_custom_field( 'images' );
					}

					echo '<script>';
					echo 'window.galleryImageUris = [];';

					$gallery_images_ids = [];
					if ($gallery_images && count($gallery_images) > 0) {
						for ( $i = 0; $i < count($gallery_images); $i++ ) {
							$image = $gallery_images[$i];
							$image_meta_id = $image['image'];
							if (isset($image_meta_id) && $image_meta_id != '') {
								$image_src = wp_get_attachment_image_src($image_meta_id, 'full');
								$image_src = is_array($image_src) && count($image_src) > 0 ? $image_src[0] : '';
								$gallery_images_ids[] = $image_meta_id;

								echo 'window.galleryImageUris.push({ image_id: ' . $image_meta_id . ', image_uri: ' . json_encode($image_src) . '});';
							}
						}
					}

					echo '</script>';
                    echo '<div class="fes-input-wrapper ' . $field_override_class . '">';
					echo '<label>' . esc_html__( 'Gallery images', 'bookyourtravel' ) . '</label>';
					echo '<div id="gallery-image-uploader" class="dropzone"></div><input type="hidden" id="gallery-image-ids" name="gallery-image-ids" value=' . json_encode('[' . implode(",", $gallery_images_ids) . ']') . '>';
					echo '</div>';
				}
				break;
			case "image":
				$atts = array( 'type' => 'file', 'role' => 'content', 'name' => $field_id, 'id' => 'fes_' . $field_id, 'class' => '' . $field_override_class, 'label' => $field_label, 'description' => $field_desc );
				$this->render_image_input($entry, $content_type, $atts);
				break;

			case "datepicker":

				$atts = array( 'type' => 'text', 'role' => 'internal', 'name' => 'fes_datepicker_control_' . $field_id, 'id' => 'datepicker_control_'. $field_id, 'label' => $field_label, 'description' => $field_desc, 'class' => 'fes-datepicker-control ' . $field_override_class);
				echo $this->render_input($entry, $content_type, $atts);
				$atts = array( 'type' => 'hidden', 'role' => 'internal', 'name' => $field_id, 'id' => 'fes_' . $field_id );
				echo $this->render_datepicker_hidden_input($entry, $content_type, $atts);

				break;

			case "number":

				$step = isset($field['step']) ? $field["step"] : "0.01";
				$min = isset($field['min']) ? $field["min"] : null;
				$max = isset($field['max']) ? $field["max"] : null;

				$atts = array('step' => $step, 'min' => $min, 'max' => $max, 'type' => 'number', 'role' => 'internal', 'class' => '' . $field_override_class, 'name' => $field_id, 'id' => 'fes_' . $field_id, 'label' => $field_label, 'description' => $field_desc);
				echo $this->render_input($entry, $content_type, $atts);

				break;

			default:
				echo "<br />field_type " . $field_type . "<br />";

				print_r($field);

				break;
		}
	}

	/*
	Control rendering
	*/

	function prepare_atts($atts) {

		$supported_atts = array(
			'id' => '',
			'name' => '',
			'description' => '',
			'label' => '',
			'value' => '',
			'type' => '',
			'class' => '',
			'multiple' => false,
			'values' => '',
			'wysiwyg_enabled' => false,
			'role' => 'meta',
			'container_class_override' => '',
			'step' => '',
			'min' => '',
			'max' => '',						
			'field_override_class' => ''
		);

		return shortcode_atts($supported_atts, $atts);
	}

	function render_select( $entry, $content_type, $atts ) {

        $atts = $this->prepare_atts($atts);

		extract( $atts );
		$atts = array( 'values' => $values );
		$values = explode( ',', $values );
        $options = '';

		$selected_value = $this->get_entry_field_value($entry, $content_type, $name);

		//Build options for the list
		foreach ( $values as $option ) {
			$kv = explode( "::", $option );
			$caption = isset( $kv[1] ) ? $kv[1] : $kv[0];
			$option_atts = array( 'value' => $kv[0] );
			if ( isset($selected_value) && $selected_value == $kv[0] )
				$option_atts['selected'] = 'selected';

			$options .= $this->html_helper->element( 'option', $caption, $option_atts, false );
		}

		$description_span = "";
		if (!empty($description)) {
			$description_span = sprintf("<span>%s</span>", $description);
		}

		//Render select field
		$label_element = $this->html_helper->element( 'label', $label, array( 'for' => $id ), false );
		$select_element = $this->html_helper->element( 'select', $options,
			array(
			'name' => $name,
			'id' => $id,
			'class' => $class
		), false );

		$container_class = 'fes-input-wrapper';
		if (isset($container_class_override) && !empty($container_class_override))
			$container_class .= ' ' . $container_class_override;
		return $this->html_helper->element( 'div', $label_element . $select_element . $description_span, array( 'class' => $container_class ), false );
	}

	function render_checkboxes( $entry, $content_type, $atts ) {

		$atts = $this->prepare_atts($atts);
		extract( $atts );

		$atts = array( 'values' => $values );
		$values = explode( ',', $values );
		$options = '';

		$selected_values = $this->get_entry_field_value($entry, $content_type, $name);

		// Making sure we're having array of values for checkboxes
		if ( false === stristr( '[]', $name ) )
			$name = $name . '[]';

		//Build options for the list
		foreach ( $values as $option ) {
			$kv = explode( "::", $option );
			if (is_array($selected_values) && in_array($kv[0], $selected_values)) {
				$atts['checked'] = 'checked';
			} else {
				unset($atts['checked']);
			}
			$options .= $this->html_helper->_checkbox( $name, isset( $kv[1] ) ? $kv[1] : $kv[0], $kv[0], $atts );
		}

		$description_span = "";
		if (!empty($description)) {
			$description_span = sprintf("<span>%s</span>", $description);
		}

		$label_element = $this->html_helper->element( 'label', $label, array(), false );

		// Render select field
		$element = $label_element . $this->html_helper->element( 'div', $options, array( 'class' => 'checkbox-wrapper' ), false ) . $description_span;

		$container_class = 'fes-input-wrapper';
		if (isset($container_class_override) && !empty($container_class_override))
			$container_class .= ' ' . $container_class_override;
		return $this->html_helper->element( 'div', $element, array( 'class' => $container_class ), false );
	}

	function render_checkbox_input($entry, $content_type, $atts) {

		$atts = $this->prepare_atts($atts);

		extract( $atts );

		$type = 'checkbox';

		$atts = array($type => 'checkbox', 'id' => $id, 'class' => $class, 'multiple' => $multiple );

		// Workaround for HTML5 multiple attribute
		if ( (bool) $multiple === false )
			unset( $atts['multiple'] );

		$selected_value = $this->get_entry_field_value($entry, $content_type, $name);
		if ($entry != null && isset($selected_value) ) {
			if ($type == 'checkbox' && $selected_value == '1')
				$atts['checked'] = 'checked';
		}

		$input = $this->html_helper->input( $type, $name, $value, $atts );

		$description_span = "";
		if (!empty($description)) {
			$description_span = sprintf("<span>%s</span>", $description);
		}

		$element = $this->html_helper->element( 'label',  $label . $input, array( 'for' => $id ), false ) . $description_span;

		$container_class = 'fes-input-wrapper';
		if (isset($container_class_override) && !empty($container_class_override))
			$container_class .= ' ' . $container_class_override;
		return $this->html_helper->element( 'div', $element, array( 'class' => $container_class ), false );
	}

	function render_textarea( $entry, $content_type, $atts ) {

		$atts = $this->prepare_atts($atts);

		extract( $atts );

		$selected_value = $this->get_entry_field_value($entry, $content_type, $name);
		if ( $entry != null && isset($selected_value) && !empty($selected_value) ) {
			$value = $selected_value;
		}

		$description_span = "";
		if (!empty($description)) {
			$description_span = sprintf("<span>%s</span>", $description);
		}

		// Render WYSIWYG textarea
		if ( $wysiwyg_enabled ) {
			ob_start();
			wp_editor( $value, $id, array(
					'textarea_name' => $name,
					'media_buttons' => false,
					'teeny' => true,
					"quicktags" => array(
						"buttons" => "em,strong,link"
                    ),
                    'editor_class' => $class
				) );
			$tiny = ob_get_clean();
			$label_element =  $this->html_helper->element( 'label', $label , array( 'for' => $id ), false );
			return $this->html_helper->element( 'div', $label_element . $tiny . $description_span, array( 'class' => 'fes-input-wrapper' ), false ) ;
		}
		// Render plain textarea
		$element = $this->html_helper->element( 'textarea', $value, array( 'name' => $name, 'id' => $id, 'class' => $class ) );
		$label_element = $this->html_helper->element( 'label', $label, array( 'for' => $id ), false );

		$container_class = 'fes-input-wrapper';
		if (isset($container_class_override) && !empty($container_class_override))
			$container_class .= ' ' . $container_class_override;
		return $this->html_helper->element( 'div', $label_element . $element . $description_span, array( 'class' => $container_class ), false );
	}

	function render_image_input( $entry, $content_type, $atts) {

		$atts = $this->prepare_atts($atts);

		extract( $atts );

		$image_id = 0;
		$selected_value = $this->get_entry_field_value($entry, $content_type, $name);
		if ($entry != null && isset($selected_value) && !empty($selected_value) ) {
			$image_id = intval($selected_value);
		}

		$description_span = "";
		if (!empty($description)) {
			$description_span = sprintf("<span>%s</span>", $description);
		}

		echo '<div class="fes-input-wrapper">';
		if ($image_id > 0) {
			$image_src = wp_get_attachment_image_src($image_id, 'full');
			$image_src = is_array($image_src) && count($image_src) > 0 ? $image_src[0] : '';

			echo '<script>';
			echo 'window.extraField_' . $id . '_Uri = ' . json_encode($image_src) . ';';
			echo 'window.extraField_' . $id . '_Id = ' . json_encode($image_id) . ';';
			echo '</script>';
		}
		echo '<label>' . $label . '</label>';
		echo '<div id="' . $id . '-uploader" class="dropzone extra-field-image-uploader"></div>';
		echo '<input type="hidden" id="' . $id . '" name="' . $name . '" value="' . $image_id . '">';
		echo $description_span;
		echo '</div>';
	}

	function render_datepicker_hidden_input( $entry, $content_type, $atts) {

		$atts = $this->prepare_atts($atts);

		extract( $atts );

		$atts = array( 'id' => $id, 'class' => $class, 'type' => 'hidden' );

		$selected_value = $this->get_entry_field_value($entry, $content_type, $name);

		if (!empty($selected_value)) {

			echo '<script>';
			echo 'window.datePicker_fes_' . $name . ' = ' . json_encode(date_i18n( $this->date_format, strtotime( $selected_value ))) . ';';
			echo '</script>';
		}

		$input = $this->html_helper->input( $type, $name, $value, $atts );

		return $input;
	}

	function render_input( $entry, $content_type, $atts ) {

		$atts = $this->prepare_atts($atts);

		extract( $atts );

		$atts = array( 'id' => $id, 'class' => $class, 'multiple' => $multiple );

		// Workaround for HTML5 multiple attribute
		if ( (bool) $multiple === false )
			unset( $atts['multiple'] );

		$selected_value = $this->get_entry_field_value($entry, $content_type, $name);
		if ($entry != null && isset($selected_value) && !empty($selected_value) ) {
			if ($type == 'checkbox' && $selected_value == '1')
				$atts['checked'] = 'checked';
			else if ($type == 'text')
				$value = $selected_value;
			else if ($type == 'number')
				$value = $selected_value;
		}

		// Allow multiple file upload by default.
		// To do so, we need to add array notation to name field: []
		if ( !strpos( $name, '[]' ) && $type == 'file' )
			$name = $name . '[]';

		if ( $type == 'number' ) {
			if (isset($step)) {
				$atts['step'] = $step;
			}
			if (isset($min)) {
				$atts['min'] = $min;
			}
			if (isset($max)) {
				$atts['max'] = $max;
			}
		}

		$input = $this->html_helper->input( $type, $name, $value, $atts );

		// No need for wrappers or labels for hidden input
		if ( $type == 'hidden' || $type == 'submit' )
			return $input;

		$description_span = "";
		if (!empty($description)) {
			$description_span = sprintf("<span>%s</span>", $description);
		}
		$element = $this->html_helper->element( 'label', $label , array( 'for' => $id ), false ) . $input . $description_span;

		$container_class = 'fes-input-wrapper';
		if (isset($container_class_override) && !empty($container_class_override))
			$container_class .= ' ' . $container_class_override;
		return $this->html_helper->element( 'div', $element, array( 'class' => $container_class ), false );
	}

	/*
	Value retrieval
	*/
	function get_entry_field_value($entry, $content_type, $field_id) {

		if ($entry != null) {

			if ( $content_type == 'accommodation' || $content_type == 'room_type' || $content_type == 'tour' ||
				$content_type == 'cruise' || $content_type == 'cabin_type' || $content_type == 'car_rental' || $content_type == 'location') {

				if ($content_type == 'location' && $field_id == 'post_parent') {
					return $entry->post->post_parent;
				} else {
					return $entry->get_field_value($field_id, false);
				}

			} else if ( $content_type == 'accommodation_vacancy' ||
				$content_type == 'accommodation_booking' ||
				$content_type == 'tour_schedule' ||
				$content_type == 'tour_booking' ||
				$content_type == 'cruise_schedule' ||
				$content_type == 'cruise_booking' ||
				$content_type == 'car_rental_availability' ||
				$content_type == 'car_rental_booking') {

				if (property_exists($entry, $field_id) && isset($entry->$field_id)) {
					return $entry->$field_id;
				}
			}
		}

		return null;
    }

	function sanitize_array_element_callback( $el ) {
		return sanitize_text_field( $el );
	}
}
