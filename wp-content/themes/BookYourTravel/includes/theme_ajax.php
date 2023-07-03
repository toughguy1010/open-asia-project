<?php
/**
 * BookYourTravel_Theme_Ajax class
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class BookYourTravel_Theme_Ajax extends BookYourTravel_BaseSingleton {

	protected function __construct() {

        // our parent class might contain shared code in its constructor
        parent::__construct();
    }

    public function init() {

		do_action( 'bookyourtravel_initialize_ajax' );

		add_action( 'wp_ajax_inquiry_ajax_request', array( $this, 'inquiry_ajax_request' ) );
		add_action( 'wp_ajax_nopriv_inquiry_ajax_request', array( $this, 'inquiry_ajax_request' ) );

		add_action( 'wp_ajax_settings_ajax_save_password', array( $this, 'settings_ajax_save_password' ) );
		add_action( 'wp_ajax_settings_ajax_save_email', array( $this, 'settings_ajax_save_email' ) );
		add_action( 'wp_ajax_settings_ajax_save_last_name', array( $this, 'settings_ajax_save_last_name' ) );
        add_action( 'wp_ajax_settings_ajax_save_first_name', array( $this, 'settings_ajax_save_first_name' ) );
        add_action( 'wp_ajax_settings_ajax_request_account_export', array( $this, 'settings_ajax_request_account_export' ) );
        add_action( 'wp_ajax_settings_ajax_request_account_delete', array( $this, 'settings_ajax_request_account_delete' ) );
		add_action( 'wp_ajax_generate_unique_dynamic_element_id', array( $this, 'generate_unique_dynamic_element_id' ) );

		add_action( 'wp_ajax_upgrade_bookyourtravel_db_ajax_request', array( $this, 'upgrade_bookyourtravel_db' ) );

        add_action( 'wp_ajax_admin_get_sidebar_id_ajax_request', array($this, 'admin_get_sidebar_id' ) );

		add_action( 'wp_ajax_frontend_submit_delete_entity', array($this, 'frontend_submit_delete_entity'));
	}

    function frontend_submit_delete_entity() {

        $return = 0;

        if ( isset($_REQUEST) ) {

            $nonce = wp_kses($_REQUEST['nonce'], array());

            if (wp_verify_nonce($nonce, 'bookyourtravel_nonce')) {

                $current_user = wp_get_current_user();
                $entity_id = isset($_REQUEST['entityId']) ? intval(wp_kses($_REQUEST['entityId'], array())) : 0;

                if ($entity_id > 0) {
                    $entity_obj = new BookYourTravel_Post($entity_id);

                    if ($entity_obj) {
                        if (is_super_admin() || $entity_obj->get_post_author() == $current_user->ID) {
                            wp_trash_post($entity_id);
                            $return = 1;
                        }
                    }
                }
            }
        }

        echo json_encode($return);

        die();
    }

	function admin_get_sidebar_id() {

        if ( isset($_REQUEST) ) {
			$nonce = wp_kses($_REQUEST['nonce'], array());
			$widget_id = wp_kses($_REQUEST['widget_id'], array());

			if ( wp_verify_nonce( $nonce, 'bookyourtravel_nonce' ) ) {
				$sidebars = wp_get_sidebars_widgets();
				foreach( (array) $sidebars as $sidebar_id => $sidebar )
				{
					if( in_array( $widget_id, (array) $sidebar, true ) ) {
						echo json_encode($sidebar_id);
					}
				}
			}
		}

		// Always die in functions echoing ajax content
		die();
	}

    function settings_ajax_request_account_export() {

        if ( isset($_REQUEST) ) {

			$user_id = intval(wp_kses($_REQUEST['userId'], array()));

			$nonce = wp_kses($_REQUEST['nonce'], array());
			if ( wp_verify_nonce( $nonce, 'bookyourtravel_nonce' ) ) {
                $user = get_user_by( 'id', $user_id );

                if ($user) {
                    $request_id = wp_create_user_request($user->user_email, 'export_personal_data' );
                    wp_send_user_request( $request_id );

                    add_user_meta($user_id, "export_personal_data_request_id", $request_id, true);
                }
            }
        }

		// Always die in functions echoing ajax content
		die();
    }

    function settings_ajax_request_account_delete() {

		if ( isset($_REQUEST) ) {

			$user_id = intval(wp_kses($_REQUEST['userId'], array()));

			$nonce = wp_kses($_REQUEST['nonce'], array());
			if ( wp_verify_nonce( $nonce, 'bookyourtravel_nonce' ) ) {
                $user = get_user_by( 'id', $user_id );

                if ($user) {
                    $request_id = wp_create_user_request($user->user_email, 'remove_personal_data' );
                    wp_send_user_request( $request_id );

                    add_user_meta($user_id, "remove_personal_data_request_id", $request_id, true);
                }
            }
        }

		// Always die in functions echoing ajax content
		die();
    }

	function upgrade_bookyourtravel_db() {

		if ( isset($_REQUEST) ) {

			$nonce = wp_kses($_REQUEST['nonce'], array());

			if ( wp_verify_nonce( $nonce, 'optionsframework-options' ) ) {

				$this->force_upgrade_bookyourtravel_db();

				update_option( '_byt_needs_update', 0 );
				update_option( '_byt_version_before_update', BOOKYOURTRAVEL_VERSION );

			} else {
				echo json_encode('oops!');
			}
		}

		// Always die in functions echoing ajax content
		die();
	}

	function force_upgrade_bookyourtravel_db() {

		global $wpdb, $force_recreate_tables, $bookyourtravel_accommodation_helper, $bookyourtravel_cruise_helper, $bookyourtravel_tour_helper, $bookyourtravel_theme_globals, $bookyourtravel_car_rental_helper;

		$force_recreate_tables = true;
		$bookyourtravel_installed_version = get_option('bookyourtravel_version', null);

		$bookyourtravel_tour_helper->create_tour_extra_tables();

		$bookyourtravel_cruise_helper->create_cruise_extra_tables();

		$bookyourtravel_car_rental_helper->create_car_rental_extra_tables();

		$bookyourtravel_accommodation_helper->create_accommodation_extra_tables();

		if ($bookyourtravel_installed_version < 6.07) {
			$sql = "UPDATE " . BOOKYOURTRAVEL_ACCOMMODATION_BOOKINGS_TABLE . " SET cart_price = total_price;";
			$wpdb->query($sql);
			$sql = "UPDATE " . BOOKYOURTRAVEL_CAR_RENTAL_BOOKINGS_TABLE . " SET cart_price = total_price;";
			$wpdb->query($sql);
			$sql = "UPDATE " . BOOKYOURTRAVEL_TOUR_BOOKING_TABLE . " SET cart_price = total_price;";
			$wpdb->query($sql);
			$sql = "UPDATE " . BOOKYOURTRAVEL_CRUISE_BOOKING_TABLE . " SET cart_price = total_price;";
			$wpdb->query($sql);
		}

		$force_recreate_tables = false;
	}

	function inquiry_ajax_request() {

		global $bookyourtravel_theme_globals;

		if ( isset($_REQUEST) ) {

			$use_recaptcha = $bookyourtravel_theme_globals->is_recaptcha_usable() && $bookyourtravel_theme_globals->enable_inquiry_recaptcha();
			$recaptcha_key = $bookyourtravel_theme_globals->get_google_recaptcha_key();
			$recaptcha_secret = $bookyourtravel_theme_globals->get_google_recaptcha_secret();

			$user_id = intval(wp_kses($_REQUEST['userId'], array()));

			$nonce = wp_kses($_REQUEST['nonce'], array());
			if ( wp_verify_nonce( $nonce, 'bookyourtravel_nonce' ) ) {

				$captcha_status = 1;
				if ($use_recaptcha) {
					if (isset($_REQUEST['g-recaptcha-response'])) {

						$captcha = $_REQUEST['g-recaptcha-response'];

						$json_uri = "https://www.google.com/recaptcha/api/siteverify?secret=" . $recaptcha_secret . "&response=" . $captcha . "&remoteip=" . $_SERVER['REMOTE_ADDR'];
						$request = wp_remote_get($json_uri);
						$response = wp_remote_retrieve_body( $request );
						$response = json_decode($response, true);

						if (!$response['success']) {
							$captcha_status	= -5;
						}
					} else {
						$captcha_status	= -4;
					}
				}

				if ($captcha_status == 1) {

					$postId = intval(wp_kses($_REQUEST['postId'], array()));
					$post = get_post($postId);

					if ($post) {

						$post_type = $post->post_type;

						$contact_form_heading = esc_html__('Use the form below to contact us directly.', 'bookyourtravel');
						$inquiry_form_fields = $bookyourtravel_theme_globals->get_inquiry_form_fields();

						$admin_email = get_bloginfo('admin_email');
						$contact_email = get_post_meta($postId, $post->post_type . '_contact_email', true );
						$contact_emails = explode(';', $contact_email);
						if (empty($contact_email)) {
							$contact_emails = array($admin_email);
						}

						$subject = esc_html__('New inquiry', 'bookyourtravel');

						$message = esc_html__('The following inquiry has just arrived for %s', 'bookyourtravel');
						$message .= "\n";
						$message = sprintf($message, $post->post_title);

						$customer_email = '';

						foreach ($inquiry_form_fields as $form_field) {
							if ($form_field['hide'] !== '1') {
								$field_id = $form_field['id'];
								$field_value = sanitize_text_field($_REQUEST[$field_id]);

								if ($field_id == 'your_email') {
									$customer_email = $field_value;
								}

								$message .= $form_field['label'] . ' ' . $field_value . "\n";
							}
						}

						$headers = "Content-Type: text/plain; charset=utf-8\r\n";
						$headers .= "From: " . $admin_email . " <" . $admin_email . ">\r\n";
						if (!empty($customer_email)) {
							$headers .= "Reply-To: " . $customer_email . " <" . $customer_email . ">\r\n";
						} else {
							$headers .= "Reply-To: " . $admin_email . " <" . $admin_email . ">\r\n";
						}

						foreach ($contact_emails as $email) {
							if (!empty($email)) {
								$ret = wp_mail(trim($email), $subject, $message, $headers, "");

								if (!$ret) {
									global $phpmailer;
									if (isset($phpmailer) && WP_DEBUG) {
										var_dump($phpmailer->ErrorInfo);
									}
								}
							}
						}
					} else {
						echo -1;
					}
				} else {
					echo 'captcha_error';
				}
			} else {
				echo -2;
			}
		} else {
			echo -3;
		}

		// Always die in functions echoing ajax content
		die();
	}

	function generate_unique_dynamic_element_id() {

		if ( isset($_REQUEST) ) {

			$nonce = wp_kses($_REQUEST['nonce'], array());

			if ( wp_verify_nonce( $nonce, 'optionsframework-options' ) ) {

				global $bookyourtravel_theme_globals;

				$element_type = sanitize_text_field($_REQUEST['element_type']);
				$parent = sanitize_text_field($_REQUEST['parent']);
				$element_id = trim(sanitize_text_field($_REQUEST['element_id']));

				if (empty($element_id) && $element_type == 'tab') {
					$element_id = 't';
				} else if (empty($element_id) && $element_type == 'field') {
					$element_id = 'f';
				} else if (empty($element_id) && $element_type == 'review_field') {
					$element_id = 'rf';
				} else if (empty($element_id) && $element_type == 'inquiry_form_field') {
					$element_id = 'iff';
				} else if (empty($element_id) && $element_type == 'booking_form_field') {
					$element_id = 'bff';
				}

				if ($element_type == 'review_field' && !BookYourTravel_Theme_Utils::string_starts_with($element_id, 'review_')) {
					$element_id = 'review_' . $element_id;
				}

				$elements = null;
				if ($parent == 'location_tabs') {
					$elements = $bookyourtravel_theme_globals->get_location_tabs();
				} else if ($parent == 'accommodation_tabs') {
					$elements = $bookyourtravel_theme_globals->get_accommodation_tabs();
				} else if ($parent == 'tour_tabs') {
					$elements = $bookyourtravel_theme_globals->get_tour_tabs();
				} else if ($parent == 'cruise_tabs') {
					$elements = $bookyourtravel_theme_globals->get_cruise_tabs();
				} else if ($parent == 'car_rental_tabs') {
					$elements = $bookyourtravel_theme_globals->get_car_rental_tabs();
				} else if ($parent == 'location_extra_fields') {
					$elements = $bookyourtravel_theme_globals->get_location_extra_fields();
				} else if ($parent == 'accommodation_extra_fields') {
					$elements = $bookyourtravel_theme_globals->get_accommodation_extra_fields();
				} else if ($parent == 'tour_extra_fields') {
					$elements = $bookyourtravel_theme_globals->get_tour_extra_fields();
				} else if ($parent == 'cruise_extra_fields') {
					$elements = $bookyourtravel_theme_globals->get_cruise_extra_fields();
				} else if ($parent == 'car_rental_extra_fields') {
					$elements = $bookyourtravel_theme_globals->get_car_rental_extra_fields();
				} else if ($parent == 'car_rental_review_fields') {
					$elements = $bookyourtravel_theme_globals->get_car_rental_review_fields();
				} else if ($parent == 'cruise_review_fields') {
					$elements = $bookyourtravel_theme_globals->get_cruise_review_fields();
				} else if ($parent == 'tour_review_fields') {
					$elements = $bookyourtravel_theme_globals->get_tour_review_fields();
				} else if ($parent == 'accommodation_review_fields') {
					$elements = $bookyourtravel_theme_globals->get_accommodation_review_fields();
				} else if ($parent == 'inquiry_form_fields') {
					$elements = $bookyourtravel_theme_globals->get_inquiry_form_fields();
				} else if ($parent == 'booking_form_fields') {
					$elements = $bookyourtravel_theme_globals->get_booking_form_fields();
				}

				$exists_count = 1;
				$new_element_id = $element_id;
				$exists = BookYourTravel_Theme_Of_Custom::of_element_exists($elements, $element_id);
				if ($exists) {
					while ($exists) {
						$new_element_id = $element_id . '_' . $exists_count;
						$exists = BookYourTravel_Theme_Of_Custom::of_element_exists($elements, $new_element_id);
						$exists_count++;
					}
				}

				echo json_encode($new_element_id);
			}
		}

		die();
	}


	function settings_ajax_save_password() {

		if ( isset($_REQUEST) ) {
			$nonce = sanitize_text_field($_REQUEST['nonce']);
			if ( wp_verify_nonce( $nonce, 'bookyourtravel_nonce' ) ) {
				$user_id = intval($_REQUEST['userId']);
				$oldPassword = sanitize_text_field($_REQUEST['oldPassword']);
				$password = sanitize_text_field($_REQUEST['password']);

				$user = get_user_by( 'id', $user_id );
				if ( $user && wp_check_password( $oldPassword, $user->data->user_pass, $user->ID) )
				{
					// ok
					echo wp_update_user( array ( 'ID' => $user_id, 'user_pass' => $password ) ) ;
				}
			}
		}

		// Always die in functions echoing ajax content
		die();
	}

	function settings_ajax_save_email() {
		if ( isset($_REQUEST) ) {
			$nonce = sanitize_text_field($_REQUEST['nonce']);
			if ( wp_verify_nonce( $nonce, 'bookyourtravel_nonce' ) ) {
				$email = sanitize_text_field($_REQUEST['email']);
				$user_id = intval($_REQUEST['userId']);
				echo wp_update_user( array ( 'ID' => $user_id, 'user_email' => $email ) ) ;
			}
		}

		// Always die in functions echoing ajax content
		die();
	}

	function settings_ajax_save_last_name() {
		if ( isset($_REQUEST) ) {
			$nonce = sanitize_text_field($_REQUEST['nonce']);
			if ( wp_verify_nonce( $nonce, 'bookyourtravel_nonce' ) ) {
				$lastName = sanitize_text_field($_REQUEST['lastName']);
				$user_id = intval($_REQUEST['userId']);
				echo wp_update_user( array ( 'ID' => $user_id, 'last_name' => $lastName ) ) ;
			}
		}

		// Always die in functions echoing ajax content
		die();
	}

	function settings_ajax_save_first_name() {
		if ( isset($_REQUEST) ) {
			$nonce = sanitize_text_field($_REQUEST['nonce']);
			if ( wp_verify_nonce( $nonce, 'bookyourtravel_nonce' ) ) {
				$firstName = sanitize_text_field($_REQUEST['firstName']);
				$user_id = intval($_REQUEST['userId']);
				echo wp_update_user( array ( 'ID' => $user_id, 'first_name' => $firstName ) ) ;
			}
		}

		// Always die in functions echoing ajax content
		die();
	}
}

// store the instance in a variable to be retrieved later and call init
$bookyourtravel_theme_ajax = BookYourTravel_Theme_Ajax::get_instance();
