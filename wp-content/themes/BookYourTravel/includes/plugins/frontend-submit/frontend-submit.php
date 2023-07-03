<?php
/*
Class Name: Frontend Submit based on Frontend Uploader plugin
Description: Allow your visitors to upload content and moderate it.
Author: Rinat Khaziev, Daniel Bachhuber, ThemeEnergy.com
Version of Frontend Uploader: 0.8.1
Author of original plugin class URI: http://digitallyconscious.com
Author of modification: http://www.themeenergy.com

GNU General Public License, Free Software Foundation <http://creativecommons.org/licenses/GPL/2.0/>

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

*/

define( 'FES_ROOT' , dirname( __FILE__ ) );
define( 'FES_FILE_PATH' , FES_ROOT . '/' . basename( __FILE__ ) );
define( 'FES_URL' , plugins_url( '/', __FILE__ ) );

require_once BookYourTravel_Theme_Utils::get_file_path('/includes/plugins/frontend-submit/lib/class-field-helper.php');
require_once ABSPATH . 'wp-admin/includes/image.php';

class Frontend_Submit {

	protected $allowed_mime_types;
	protected $has_frontend_role;
	protected $has_admin_role;
	protected $date_format;
	protected $form_fields;
	protected $entry = null;
	protected $entry_author_id = '';
	protected $entry_id = 0;
	protected $content_type = '';
	protected $enable_accommodations = false;
	protected $enable_car_rentals = false;
	protected $enable_cruises = false;
	protected $enable_tours = false;
	protected $frontend_submit_field_helper;

	function __construct() {
		global $bookyourtravel_theme_globals;

		$this->enable_accommodations = $bookyourtravel_theme_globals->enable_accommodations();
		$this->enable_tours = $bookyourtravel_theme_globals->enable_tours();
		$this->enable_cruises = $bookyourtravel_theme_globals->enable_cruises();
		$this->enable_car_rentals = $bookyourtravel_theme_globals->enable_car_rentals();
		$this->allowed_mime_types = wp_get_mime_types();
		$this->has_frontend_role = BookYourTravel_Theme_Utils::check_user_role(BOOKYOURTRAVEL_FRONTEND_SUBMIT_ROLE, $this->get_current_user_id());
		$this->has_admin_role = current_user_can('editor') || current_user_can('administrator');
		$this->date_format = get_option('date_format');
		$this->content_type = 'accommodation'; // default
		$this->frontend_submit_field_helper = new Frontend_Submit_Field_Helper($this->get_current_user_id(), $this->user_has_correct_role(), $this->has_admin_role);
	}

	function init() {

		$this->frontend_submit_field_helper->init();

		add_action( 'wp_ajax_frontend_submit', array( $this, 'frontend_submit_ajax_request' ) );
		add_action( 'wp_ajax_nopriv_frontend_submit', array( $this, 'frontend_submit_ajax_request' ) );
		add_action( 'wp_ajax_frontend_delete_extra_field_image', array( $this, 'delete_extra_field_image' ) );
		add_action( 'wp_ajax_nopriv_frontend_delete_extra_field_image', array( $this, 'delete_extra_field_image' ) );
		add_action( 'wp_ajax_frontend_extra_field_image_upload', array( $this, 'frontend_extra_field_image_upload' ) );
		add_action( 'wp_ajax_nopriv_frontend_extra_field_image_upload', array( $this, 'frontend_extra_field_image_upload' ) );
		add_action( 'wp_ajax_frontend_featured_upload', array( $this, 'upload_featured_image' ) );
		add_action( 'wp_ajax_nopriv_frontend_featured_upload', array( $this, 'upload_featured_image' ) );
		add_action( 'wp_ajax_frontend_gallery_upload', array( $this, 'upload_gallery_images' ) );
		add_action( 'wp_ajax_nopriv_frontend_gallery_upload', array( $this, 'upload_gallery_images' ) );
		add_action( 'wp_ajax_frontend_delete_featured_image', array( $this, 'delete_featured_image' ) );
		add_action( 'wp_ajax_nopriv_frontend_delete_featured_image', array( $this, 'delete_featured_image' ) );
		add_action( 'wp_ajax_frontend_delete_gallery_image', array( $this, 'delete_gallery_image' ) );
		add_action( 'wp_ajax_nopriv_frontend_delete_gallery_image', array( $this, 'delete_gallery_image' ) );
    }

	function publish_immediately() {
		global $bookyourtravel_theme_globals;
		return $bookyourtravel_theme_globals->publish_frontend_submissions_immediately() && $this->user_has_correct_role();
    }

	function is_demo() {
		return defined('BookYourTravel_DEMO');
	}

	function get_current_user_id() {
		global $current_user;
		if (!isset($current_user)) {
			$current_user = wp_get_current_user();
		}
		return $current_user->ID;
	}

	public function user_has_correct_role() {
		return $this->has_frontend_role || $this->has_admin_role;
	}

	function initialize_entry($override_entry_id = 0, $override_content_type = '') {

		global $bookyourtravel_cruise_helper, $bookyourtravel_car_rental_helper, $bookyourtravel_accommodation_helper, $bookyourtravel_tour_helper, $bookyourtravel_room_type_helper;

        if (!empty($override_content_type)) {
            $this->content_type = $override_content_type;
        } else if (isset($_REQUEST['content_type'])) {
            $this->content_type = sanitize_text_field($_REQUEST['content_type']);
        }

        if ($override_entry_id > 0) {
            $this->entry_id = $override_entry_id;
        } else if (isset($_GET['fesid'])) {
            $this->entry_id = intval($_GET['fesid']);
		} else if (isset($_POST['entry_id'])) {
			$this->entry_id = intval($_POST['entry_id']);
        }

		if ($this->entry_id > 0 && !empty($this->content_type)) {
			switch ($this->content_type) {
				case "accommodation":
					if ($this->enable_accommodations) {
						$this->entry = new BookYourTravel_Accommodation($this->entry_id);
                        $this->entry_author_id = $this->entry->get_post_author();
					}
					break;
				case "room_type":
					if ($this->enable_accommodations) {
						$this->entry = new BookYourTravel_Room_Type($this->entry_id);
						$this->entry_author_id = $this->entry->get_post_author();
					}
					break;
				case "accommodation_vacancy":
					if ($this->enable_accommodations) {
						$this->entry = $bookyourtravel_accommodation_helper->get_accommodation_vacancy($this->entry_id);
						$accommodation = new BookYourTravel_Accommodation($this->entry->accommodation_id);
						$this->entry_author_id = $accommodation->get_post_author();
					}
					break;
				case "accommodation_booking":
					if ($this->enable_accommodations) {
						$this->entry = $bookyourtravel_accommodation_helper->get_accommodation_booking($this->entry_id);
						$accommodation = new BookYourTravel_Accommodation($this->entry->accommodation_id);
						$this->entry_author_id = $accommodation->get_post_author();
					}
					break;
				case "cruise":
					if ($this->enable_cruises) {
						$this->entry = new BookYourTravel_Cruise($this->entry_id);
						$this->entry_author_id = $this->entry->get_post_author();
					}
					break;
				case "cabin_type":
					if ($this->enable_cruises) {
						$this->entry = new BookYourTravel_Cabin_Type($this->entry_id);
						$this->entry_author_id = $this->entry->get_post_author();
					}
					break;
				case "cruise_schedule":
					if ($this->enable_cruises) {
						$this->entry = $bookyourtravel_cruise_helper->get_cruise_schedule($this->entry_id);
						$cruise = new BookYourTravel_Cruise($this->entry->cruise_id);
						$this->entry_author_id = $cruise->get_post_author();
					}
					break;
				case "cruise_booking":
					if ($this->enable_cruises) {
						$this->entry = $bookyourtravel_cruise_helper->get_cruise_booking($this->entry_id);
						$cruise = new BookYourTravel_Cruise($this->entry->cruise_id);
						$this->entry_author_id = $cruise->get_post_author();
					}
					break;
				case "car_rental":
					if ($this->enable_car_rentals) {
						$this->entry = new BookYourTravel_Car_Rental($this->entry_id);
						$this->entry_author_id = $this->entry->get_post_author();
					}
					break;
				case "car_rental_availability":
					if ($this->enable_car_rentals) {
						$this->entry = $bookyourtravel_car_rental_helper->get_car_rental_availability($this->entry_id);
						$car_rental = new BookYourTravel_Car_Rental($this->entry->car_rental_id);
						$this->entry_author_id = $car_rental->get_post_author();
					}
					break;
				case "car_rental_booking":
					if ($this->enable_car_rentals) {
						$this->entry = $bookyourtravel_car_rental_helper->get_car_rental_booking($this->entry_id);
						$car_rental = new BookYourTravel_Car_Rental($this->entry->car_rental_id);
						$this->entry_author_id = $car_rental->get_post_author();
					}
					break;
				case "location":
					$this->entry = new BookYourTravel_Location($this->entry_id);
					$this->entry_author_id = $this->entry->get_post_author();
					break;
				case "tour":
					if ($this->enable_tours) {
						$this->entry = new BookYourTravel_Tour($this->entry_id);
						$this->entry_author_id = $this->entry->get_post_author();
					}
					break;
				case "tour_schedule":
					if ($this->enable_tours) {
						$this->entry = $bookyourtravel_tour_helper->get_tour_schedule($this->entry_id);
						$tour = new BookYourTravel_Tour($this->entry->tour_id);
						$this->entry_author_id = $tour->get_post_author();
					}
					break;
				case "tour_booking":
					if ($this->enable_tours) {
						$this->entry = $bookyourtravel_tour_helper->get_tour_booking($this->entry_id);
						$tour = new BookYourTravel_Tour($this->entry->tour_id);
						$this->entry_author_id = $tour->get_post_author();
					}
					break;
				default:
					$this->entry = null;
					$this->entry_id = 0;
					$this->entry_author_id = '';
					break;
			}
		}

		if (!$this->user_has_correct_role() ||
			$this->entry == null ||
			$this->entry->entry_type != $this->content_type ||
			($this->entry_author_id != $this->get_current_user_id() && !$this->has_admin_role)){
			$this->entry_id = 0;
			$this->entry = null;
			$this->entry_author_id = '';
		}
	}

	function render_upload_form($content_type = 'accommodation') {

        $this->initialize_entry(0, $content_type);

		$output = "";

		ob_start();
		do_action("bookyourtravel_render_entry_field_scripts", $this->content_type);
		$output .= ob_get_clean();

		// Reset postdata in case it got polluted somewhere
		wp_reset_postdata();

		$page_post_id = (int)get_the_id();

		ob_start();
		?>
		<form action="<?php echo esc_url(admin_url( 'admin-ajax.php' )) ?>" method="post" id="fes-upload-form-<?php echo esc_attr($this->content_type); ?>" name="fes-upload-form-<?php echo esc_attr($this->content_type); ?>" class="fes-upload-form fes-form-<?php echo esc_attr($this->content_type); ?>" enctype="multipart/form-data">
			<div class="fes-inner-wrapper">
				<?php
					if ( !empty( $_GET ) ) {
						$this->display_response_notices( $_GET );
					}

					do_action("bookyourtravel_render_entry_generic_fields", $this->entry_id, $this->entry, $this->content_type);

					do_action("bookyourtravel_render_entry_extra_fields", $this->entry_id, $this->entry, $this->content_type);

					echo "<div class='commands'>";

					wp_nonce_field('bookyourtravel_nonce');

					$submit_button_label = $this->entry_id != null ? esc_html__( 'Update', 'bookyourtravel' ) : esc_html__( 'Create', 'bookyourtravel' );

					$atts = array( 'type' => 'submit', 'role' => 'internal', 'name' => 'submit_button', 'id' => 'fes_submit_button', 'class' => 'btn gradient-button', 'value' =>  $submit_button_label );
					echo $this->frontend_submit_field_helper->render_input($this->entry, $this->content_type, $atts);

					$atts = array( 'type' => 'hidden', 'role' => 'internal', 'name' => 'action', 'id' => 'fes_action', 'value' =>  'frontend_submit' );
					echo $this->frontend_submit_field_helper->render_input($this->entry, $this->content_type, $atts);

					$atts = array( 'type' => 'hidden', 'role' => 'internal', 'name' => 'post_id', 'id' => 'fes_post_id', 'value' =>  $page_post_id );
					echo $this->frontend_submit_field_helper->render_input($this->entry, $this->content_type, $atts);
					echo "</div>";
				?>
			</div>
		</form>
		<?php

		$output .= ob_get_clean();

		return $output;
    }

    function frontend_submit_ajax_request() {

        $result = array();

		// Bail if something fishy is going on
		if ( !wp_verify_nonce( $_POST['_wpnonce'], 'bookyourtravel_nonce' ) ) {
			wp_safe_redirect( esc_url_raw( add_query_arg( array( 'response' => 'fes-error', 'errors' =>  'nonce-failure' ), wp_get_referer() ) ) );
			exit;
        }

		$this->content_type = isset( $_POST['content_type'] ) ? $_POST['content_type'] : '';

		if (!$this->is_demo()) {
			$result = $this->submit_entry();
		} else {
			$result = array( 'success' => true, 'entry_id' => 0, 'errors' => array(), 'content_type' => $this->content_type );
        }

		do_action( 'fes_upload_result', $result );

		// Handle error and success messages, and redirect
        $this->handle_result( $result );

		exit;
    }

	function display_response_notices( $get = array() ) {

		if ( empty( $get ) )
			return;

		$mapping_prefix = '';
		if ($this->is_demo()) {
			$mapping_prefix = 'If this were not a demo, the message would read: ';
		} else if (isset($get['response'])) {
			if ($get['response'] == 'fes-sent' ||
				$get['response'] == 'fes-accommodation-sent' ||
				$get['response'] == 'fes-room_type-sent' ||
				$get['response'] == 'fes-cabin_type-sent' ||
				$get['response'] == 'fes-car_rental-sent' ||
				$get['response'] == 'fes-location-sent' ||
				$get['response'] == 'fes-tour-sent' ||
				$get['response'] == 'fes-cruise-sent' ||
				$get['response'] == 'fes-accommodation_vacancy-sent'||
				$get['response'] == 'fes-accommodation_booking-sent' ||
				$get['response'] == 'fes-car_rental_availability-sent' ||
				$get['response'] == 'fes-car_rental_booking-sent' ||
				$get['response'] == 'fes-tour_schedule-sent' ||
				$get['response'] == 'fes-tour_booking-sent' ||
				$get['response'] == 'fes-cruise_schedule-sent'||
				$get['response'] == 'fes-cruise_booking-sent' ) {
				$mapping_prefix = esc_html__('Success: ', 'bookyourtravel');
			} else if ($get['response'] == 'fes-error') {
				$mapping_prefix = esc_html__('Error: ', 'bookyourtravel');
			}
		}

		$mapping_postfix = '';
		if (!isset($get['insert'])) {
			$mapping_postfix = esc_html__('updated', 'bookyourtravel');
		} else {
			$mapping_postfix = esc_html__('created', 'bookyourtravel');
		}

		$output = '';
		$map = array(
			'fes-sent' => array(
				'text' => sprintf(esc_html__( '%s your file was successfully %s!', 'bookyourtravel' ), $mapping_prefix, $mapping_postfix),
				'class' => 'success',
			),
			'fes-accommodation-sent' => array(
				'text' => sprintf(esc_html__( '%s your accommodation was successfully %s!', 'bookyourtravel' ), $mapping_prefix, $mapping_postfix),
				'class' => 'success',
            ),
			'fes-cruise-sent' => array(
				'text' => sprintf(esc_html__( '%s your cruise was successfully %s!', 'bookyourtravel' ), $mapping_prefix, $mapping_postfix),
				'class' => 'success',
            ),
			'fes-tour-sent' => array(
				'text' => sprintf(esc_html__( '%s your tour was successfully %s!', 'bookyourtravel' ), $mapping_prefix, $mapping_postfix),
				'class' => 'success',
            ),
			'fes-location-sent' => array(
				'text' => sprintf(esc_html__( '%s your location was successfully %s!', 'bookyourtravel' ), $mapping_prefix, $mapping_postfix),
				'class' => 'success',
            ),
			'fes-car_rental-sent' => array(
				'text' => sprintf(esc_html__( '%s your car rental was successfully %s!', 'bookyourtravel' ), $mapping_prefix, $mapping_postfix),
				'class' => 'success',
			),
			'fes-room_type-sent' => array(
				'text' => sprintf(esc_html__( '%s your room type was successfully %s!', 'bookyourtravel' ), $mapping_prefix, $mapping_postfix),
				'class' => 'success',
            ),
			'fes-cabin_type-sent' => array(
				'text' => sprintf(esc_html__( '%s your cabin type was successfully %s!', 'bookyourtravel' ), $mapping_prefix, $mapping_postfix),
				'class' => 'success',
			),
			'fes-accommodation_vacancy-sent' => array(
				'text' => sprintf(esc_html__('%s your accommodation vacancy was successfully %s!', 'bookyourtravel' ), $mapping_prefix, $mapping_postfix),
				'class' => 'success',
			),
			'fes-accommodation_booking-sent' => array(
				'text' => sprintf(esc_html__('%s your accommodation booking was successfully %s!', 'bookyourtravel' ), $mapping_prefix, $mapping_postfix),
				'class' => 'success',
			),
			'fes-tour_schedule-sent' => array(
				'text' => sprintf(esc_html__('%s your tour schedule was successfully %s!', 'bookyourtravel' ), $mapping_prefix, $mapping_postfix),
				'class' => 'success',
			),
			'fes-tour_booking-sent' => array(
				'text' => sprintf(esc_html__('%s your tour booking was successfully %s!', 'bookyourtravel' ), $mapping_prefix, $mapping_postfix),
				'class' => 'success',
			),
			'fes-cruise_schedule-sent' => array(
				'text' => sprintf(esc_html__('%s your cruise schedule was successfully %s!', 'bookyourtravel' ), $mapping_prefix, $mapping_postfix),
				'class' => 'success',
            ),
			'fes-cruise_booking-sent' => array(
				'text' => sprintf(esc_html__('%s your cruise booking was successfully %s!', 'bookyourtravel' ), $mapping_prefix, $mapping_postfix),
				'class' => 'success',
            ),
			'fes-car_rental_availability-sent' => array(
				'text' => sprintf(esc_html__('%s your car rental availabilty was successfully %s!', 'bookyourtravel' ), $mapping_prefix, $mapping_postfix),
				'class' => 'success',
			),
			'fes-car_rental_booking-sent' => array(
				'text' => sprintf(esc_html__('%s your car rental booking was successfully %s!', 'bookyourtravel' ), $mapping_prefix, $mapping_postfix),
				'class' => 'success',
			),
			'fes-error' => array(
				'text' => sprintf(esc_html__( '%s there was an error with your submission', 'bookyourtravel' ), $mapping_prefix),
				'class' => 'failure',
			),
		);

		$edit_notices = array(
			'accommodation' => array (
				'text' => esc_html__('You are currently editing your selected accommodation. Click "Update" to save your changes.', 'bookyourtravel'),
				'class' => 'warning'
			),
			'room_type' => array (
				'text' => esc_html__('You are currently editing your selected room type. Click "Update" to save your changes.', 'bookyourtravel'),
				'class' => 'warning'
			),
			'tour' => array (
				'text' => esc_html__('You are currently editing your selected tour. Click "Update" to save your changes.', 'bookyourtravel'),
				'class' => 'warning'
			),
			'cruise' => array (
				'text' => esc_html__('You are currently editing your selected cruise. Click "Update" to save your changes.', 'bookyourtravel'),
				'class' => 'warning'
			),
			'cabin_type' => array (
				'text' => esc_html__('You are currently editing your selected cabin type. Click "Update" to save your changes.', 'bookyourtravel'),
				'class' => 'warning'
			),
			'car_rental' => array (
				'text' => esc_html__('You are currently editing your selected car rental. Click "Update" to save your changes.', 'bookyourtravel'),
				'class' => 'warning'
            ),
			'location' => array (
				'text' => esc_html__('You are currently editing your selected location. Click "Update" to save your changes.', 'bookyourtravel'),
				'class' => 'warning'
			),
			'accommodation_vacancy' => array (
				'text' => esc_html__('You are currently editing your selected accommodation vacancy. Click "Update" to save your changes.', 'bookyourtravel'),
				'class' => 'warning'
			),
			'accommodation_booking' => array (
				'text' => esc_html__('You are currently editing your selected accommodation booking. Click "Update" to save your changes.', 'bookyourtravel'),
				'class' => 'warning'
			),
			'tour_schedule' => array (
				'text' => esc_html__('You are currently editing your selected tour schedule. Click "Update" to save your changes.', 'bookyourtravel'),
				'class' => 'warning'
			),
			'tour_booking' => array (
				'text' => esc_html__('You are currently editing your selected tour booking. Click "Update" to save your changes.', 'bookyourtravel'),
				'class' => 'warning'
			),
			'cruise_schedule' => array (
				'text' => esc_html__('You are currently editing your selected cruise schedule. Click "Update" to save your changes.', 'bookyourtravel'),
				'class' => 'warning'
            ),
			'cruise_booking' => array (
				'text' => esc_html__('You are currently editing your selected cruise booking. Click "Update" to save your changes.', 'bookyourtravel'),
				'class' => 'warning'
            ),
			'car_rental_availability' => array (
				'text' => esc_html__('You are currently editing your selected car rental availability. Click "Update" to save your changes.', 'bookyourtravel'),
				'class' => 'warning'
			),
			'car_rental_booking' => array (
				'text' => esc_html__('You are currently editing your selected car rental booking. Click "Update" to save your changes.', 'bookyourtravel'),
				'class' => 'warning'
			)
		);

		if ( isset( $get['response'] ) && isset( $map[ $get['response'] ] ) ) {
			$output .= $this->notice_html( $map[ $get['response'] ]['text'] , $map[ $get['response'] ]['class'] );
		}

		if ( !empty( $get['errors' ] ) ) {
			$output .= $this->display_errors( $get['errors' ] );
		}

		if ( !empty( $get['fesid'] ) && isset($this->entry_id) && isset($this->content_type) && $this->entry != null ) {
			$output .= $this->notice_html( $edit_notices[ $this->content_type ]['text'] , $edit_notices[ $this->content_type ]['class'] );
		}

		echo $output;
    }

	function notice_html( $message, $class ) {

		if ( empty( $message ) || empty( $class ) )
			return;
		return sprintf( '<p class="fes-notice %1$s">%2$s</p>', $class, $message );
    }

	function display_errors( $errors ) {

		$errors_arr = explode( ';', $errors );
		$output = '';
		$map = array(
			'nonce-failure' => array(
				'text' => esc_html__( 'Security check failed!', 'bookyourtravel' ),
			),
			'fes-disallowed-mime-type' => array(
				'text' => esc_html__( 'This kind of file is not allowed. Please, try selecting another file.', 'bookyourtravel' ),
				'format' => '%1$s: <br/> File name: %2$s <br/> MIME-TYPE: %3$s',
			),
			'fes-invalid-post' => array(
				'text' =>esc_html__( 'The content you are trying to post is invalid.', 'bookyourtravel' ),
			),
			'fes-error-media' => array(
				'text' =>esc_html__( "Couldn't upload the file", 'bookyourtravel' ),
			),
			'fes-error-post' => array(
				'text' =>esc_html__( "Couldn't create the post", 'bookyourtravel' ),
			),
			'fes-error-accommodation_vacancy-wrong-user' => array(
				'text' =>esc_html__( "User does not own accommodation specified", 'bookyourtravel' ),
			),
			'fes-error-accommodation_booking-wrong-user' => array(
				'text' =>esc_html__( "User does not own accommodation specified", 'bookyourtravel' ),
			),
			'fes-error-accommodation_vacancy-no-acc-obj' => array(
				'text' =>esc_html__( "Could not find accommodation object", 'bookyourtravel' ),
			),
			'fes-error-accommodation_booking-no-acc-obj' => array(
				'text' =>esc_html__( "Could not find accommodation object", 'bookyourtravel' ),
			),
			'fes-error-accommodation_vacancy-no-acc-id' => array(
				'text' =>esc_html__( "Accommodation id was not specified", 'bookyourtravel' ),
			),
			'fes-error-accommodation_booking-no-acc-id' => array(
				'text' =>esc_html__( "Accommodation id was not specified", 'bookyourtravel' ),
			),
			'fes-error-tour_schedule-wrong-user' => array(
				'text' =>esc_html__( "User does not own tour specified", 'bookyourtravel' ),
			),
			'fes-error-tour_schedule-no-tour-obj' => array(
				'text' =>esc_html__( "Could not find tour object", 'bookyourtravel' ),
			),
			'fes-error-tour_schedule-no-tour-id' => array(
				'text' =>esc_html__( "Tour id was not specified", 'bookyourtravel' ),
			),
			'fes-error-tour_booking-wrong-user' => array(
				'text' =>esc_html__( "User does not own tour specified", 'bookyourtravel' ),
			),
			'fes-error-tour_booking-no-tour-obj' => array(
				'text' =>esc_html__( "Could not find tour object", 'bookyourtravel' ),
			),
			'fes-error-tour_booking-no-tour-id' => array(
				'text' =>esc_html__( "Tour id was not specified", 'bookyourtravel' ),
			),
			'fes-error-cruise_schedule-wrong-user' => array(
				'text' =>esc_html__( "User does not own cruise specified", 'bookyourtravel' ),
			),
			'fes-error-cruise_schedule-no-cruise-obj' => array(
				'text' =>esc_html__( "Could not find cruise object", 'bookyourtravel' ),
			),
			'fes-error-cruise_schedule-no-cruise-id' => array(
				'text' =>esc_html__( "Cruise id was not specified", 'bookyourtravel' ),
            ),
			'fes-error-cruise_booking-wrong-user' => array(
				'text' =>esc_html__( "User does not own cruise specified", 'bookyourtravel' ),
			),
			'fes-error-cruise_booking-no-cruise-obj' => array(
				'text' =>esc_html__( "Could not find cruise object", 'bookyourtravel' ),
			),
			'fes-error-cruise_booking-no-cruise-id' => array(
				'text' =>esc_html__( "Cruise id was not specified", 'bookyourtravel' ),
            ),
			'fes-error-car_rental_availability-wrong-user' => array(
				'text' =>esc_html__( "User does not own car rental specified", 'bookyourtravel' ),
			),
			'fes-error-car_rental_availability-no-car-rental-obj' => array(
				'text' =>esc_html__( "Could not find car rental object", 'bookyourtravel' ),
			),
			'fes-error-car_rental_availability-no-car-rental-id' => array(
				'text' =>esc_html__( "Car rental id was not specified", 'bookyourtravel' ),
			),
			'fes-error-car_rental_booking-wrong-user' => array(
				'text' =>esc_html__( "User does not own car rental specified", 'bookyourtravel' ),
			),
			'fes-error-car_rental_booking-no-car-rental-obj' => array(
				'text' =>esc_html__( "Could not find car rental object", 'bookyourtravel' ),
			),
			'fes-error-car_rental_booking-no-car-rental-id' => array(
				'text' =>esc_html__( "Car rental id was not specified", 'bookyourtravel' ),
			)
		);

		foreach ( $errors_arr as $error ) {
			$error_type = explode( '::', $error );
			$error_details = explode( '|', $error_type[1] );
			// Iterate over different errors
			foreach ( $error_details as $single_error ) {
				// And see if there's any additional details
				$details = isset( $single_error ) ? explode( ',,,', $single_error ) : explode( ',,,', $single_error );
				// Add a description to our details array
				array_unshift( $details, $map[ $error_type[0] ]['text']  );
				// If we have a format, let's format an error
				// If not, just display the message
				if ( isset( $map[ $error_type[0] ]['format'] ) )
					$message = vsprintf( $map[ $error_type[0] ]['format'], $details );
				else
					$message = $map[ $error_type[0] ]['text'];
			}
			$output .= $this->notice_html( $message, 'failure' );
		}

		return $output;
	}

	function handle_result( $result = array() ) {

		// Redirect to referrer if repsonse is malformed
		if ( empty( $result ) || !is_array( $result ) ) {
			wp_safe_redirect( wp_get_referer() );
			return;
		}

		// Either redirect to success page if it's set and valid. Or to referrer.
		$errors_formatted = array();

		$url = wp_get_referer();

		// $query_args will hold everything that's needed for displaying notices to user
		$query_args = array();

		// Account for successful uploads
		if ( isset( $result['success'] ) && $result['success'] ) {

			// If it's a post
			if ( isset( $result['entry_id'] ) ) {

				if (!isset($_POST['entry_id'])) {
					$query_args['insert'] = '1';
				} else {
					$url = remove_query_arg( 'insert', $url );
				}

				$query_args['fesid'] = $result['entry_id'];

				if ( $this->content_type == 'room_type' ) {
					$query_args['response'] = 'fes-room_type-sent';
				} else if ( $this->content_type == 'accommodation' ) {
					$query_args['response'] = 'fes-accommodation-sent';
				} else if ( $this->content_type == 'tour' ) {
					$query_args['response'] = 'fes-tour-sent';
				} else if ( $this->content_type == 'cruise' ) {
					$query_args['response'] = 'fes-cruise-sent';
				} else if ( $this->content_type == 'cabin_type' ) {
                    $query_args['response'] = 'fes-cabin_type-sent';
				} else if ( $this->content_type == 'location' ) {
					$query_args['response'] = 'fes-location-sent';
				} else if ( $this->content_type == 'car_rental' ) {
					$query_args['response'] = 'fes-car_rental-sent';
				} else if ( $this->content_type == 'accommodation_vacancy' ) {
					$query_args['response'] = 'fes-accommodation_vacancy-sent';
				} else if ( $this->content_type == 'accommodation_booking' ) {
					$query_args['response'] = 'fes-accommodation_booking-sent';
				} else if ( $this->content_type == 'tour_schedule' ) {
					$query_args['response'] = 'fes-tour_schedule-sent';
				} else if ( $this->content_type == 'tour_booking' ) {
					$query_args['response'] = 'fes-tour_booking-sent';
				} else if ( $this->content_type == 'cruise_schedule' ) {
					$query_args['response'] = 'fes-cruise_schedule-sent';
				} else if ( $this->content_type == 'cruise_booking' ) {
					$query_args['response'] = 'fes-cruise_booking-sent';
				} else if ( $this->content_type == 'car_rental_availability' ) {
					$query_args['response'] = 'fes-car_rental_availability-sent';
				} else if ( $this->content_type == 'car_rental_booking' ) {
					$query_args['response'] = 'fes-car_rental_booking-sent';
				}

			} elseif ( isset( $result['media_ids'] ) && !isset( $result['entry_id'] ) ) {
				// If it's media uploads
				$query_args['response'] = 'fes-sent';
			}
		}

		// Some errors happened. Format a string to be passed as GET value.
		if ( !empty( $result['errors'] ) ) {
			$query_args['response'] = 'fes-error';
			$_errors = array();

			// Iterate through key=>value pairs of errors
			foreach ( $result['errors'] as $key => $error ) {
				if ( isset( $error[0] ) )
					$_errors[$key] = join( ',,,', (array) $error[0] );
			}

			foreach ( $_errors as $key => $value ) {
				$errors_formatted[] = "{$key}::{$value}";
			}

			$query_args['errors'] = join( ';', $errors_formatted );
		}

		// Perform a safe redirect and exit
		wp_safe_redirect( esc_url_raw ( add_query_arg( $query_args, $url ) ) );

		exit;
    }

    function submit_entry() {

        global $bookyourtravel_accommodation_helper, $bookyourtravel_tour_helper, $bookyourtravel_cruise_helper,
        $bookyourtravel_car_rental_helper, $bookyourtravel_location_helper;

		$errors = array();
		$success = true;

		if ( isset($_POST['content_type']) )
			$this->content_type = $_POST['content_type'];
		else
			return -2;

		if ($this->content_type == 'accommodation' ||
			$this->content_type == 'room_type' ||
			$this->content_type == 'tour' ||
			$this->content_type == 'cruise' ||
			$this->content_type == 'cabin_type' ||
            $this->content_type == 'car_rental' ||
            $this->content_type == 'location') {

			$post_type = $this->content_type;

			$this->entry_id = 0;
            $existing_post = null;

            $allowed_tags = BookYourTravel_Theme_Utils::get_allowed_content_tags_array();

			if ( isset($_POST['entry_id']) ) {

				$this->entry_id = intval(wp_kses($_POST['entry_id'], array()));

				$this->initialize_entry();

				if ($this->entry) {
					$this->entry->post->post_content = isset($_POST['post_content']) ? wp_kses(wp_unslash($_POST['post_content']), $allowed_tags) : '';
					$this->entry->post->post_title = isset($_POST['post_title']) ? sanitize_text_field(wp_unslash( $_POST['post_title'] )) : '';
					$this->entry->post->post_status = $this->publish_immediately() ? 'publish' : 'pending';

					if (isset($_POST['post_parent'])) {
						$post_parent = intval(sanitize_text_field( wp_unslash( $_POST['post_parent'] ) ));
						if ($post_parent != $this->entry_id) {
							$this->entry->post->post_parent = $post_parent;
						}
					}

					$this->entry_id = wp_update_post($this->entry->post, true);
				}
            }

			if ( $this->entry_id == 0 ) {

				// Construct post array;
				$post_array = array(
					'post_type' =>  $post_type,
					'post_title'    => sanitize_text_field( wp_unslash( $_POST['post_title'] ) ),
					'post_content'  => (isset($_POST['post_content']) ? wp_kses(wp_unslash($_POST['post_content']), $allowed_tags) : ''),
					'post_status'   => $this->publish_immediately() ? 'publish' : 'pending',
				);

				if (isset($_POST['post_parent'])) {
					$post_parent = intval(sanitize_text_field( wp_unslash( $_POST['post_parent'] ) ));
					$post_array['post_parent'] = $post_parent;
				}

				$author = isset( $_POST['post_author'] ) ? sanitize_text_field( $_POST['post_author'] ) : '';
				$users = get_users( array(
					'search' => $author,
					'fields' => 'ID'
				) );

				if ( isset( $users[0] ) ) {
					$post_array['post_author'] = (int) $users[0];
				}

				$post_array = apply_filters( 'fes_before_create_post', $post_array );

				$this->entry_id = wp_insert_post( wp_slash($post_array), true );
            }

            if ($this->entry_id > 0) {
                if (isset($_POST['featured-image-id'])) {
                    $featured_image_id_array = json_decode($_POST['featured-image-id']);
                    if (is_array($featured_image_id_array) && count($featured_image_id_array) > 0) {
                        $featured_image_id = $featured_image_id_array[0];
                        set_post_thumbnail($this->entry_id, $featured_image_id);
                    }
                }

                if (isset($_POST['gallery-image-ids'])) {
                    $gallery_image_ids_array = json_decode($_POST['gallery-image-ids']);

                    $gallery_images = array();
                    foreach ($gallery_image_ids_array as $gallery_image_id) {
                        if (!empty($gallery_image_id)) {
                            $gallery_images[] = array('image' => $gallery_image_id);
                        }
                    }

                    update_post_meta( $this->entry_id, $this->content_type . '_images', $gallery_images );
                }
            }

			// Something went wrong
			if ( is_wp_error( $this->entry_id ) ) {
				$errors[] = 'fes-error-post';
				$success = false;
			} else {
				do_action( 'fes_after_create_post', $this->entry_id );

				do_action("bookyourtravel_frontend_save_extra_fields", $this->entry_id, $this->entry, $this->content_type);
			}
        } else if ($this->content_type == 'accommodation_vacancy') {
            $this->initialize_entry();
            $this->entry_id = $this->frontend_submit_field_helper->save_accommodation_vacancy($this->entry_id, $this->entry);
			$success = true;
        } else if ($this->content_type == 'accommodation_booking') {
            $this->initialize_entry();
            $this->entry_id = $this->frontend_submit_field_helper->save_accommodation_booking($this->entry_id, $this->entry);
			$success = true;
        } else if ($this->content_type == 'car_rental_booking') {
            $this->initialize_entry();
            $this->entry_id = $this->frontend_submit_field_helper->save_car_rental_booking($this->entry_id, $this->entry);
			$success = true;
        } else if ($this->content_type == 'car_rental_availability') {
            $this->initialize_entry();
            $this->entry_id = $this->frontend_submit_field_helper->save_car_rental_availability($this->entry_id, $this->entry);
			$success = true;
        } else if ($this->content_type == 'tour_schedule') {
            $this->initialize_entry();
            $this->entry_id = $this->frontend_submit_field_helper->save_tour_schedule($this->entry_id, $this->entry);
			$success = true;
        } else if ($this->content_type == 'tour_booking') {
            $this->initialize_entry();
            $this->entry_id = $this->frontend_submit_field_helper->save_tour_booking($this->entry_id, $this->entry);
			$success = true;
        } else if ($this->content_type == 'cruise_schedule') {
            $this->initialize_entry();
            $this->entry_id = $this->frontend_submit_field_helper->save_cruise_schedule($this->entry_id, $this->entry);
			$success = true;
        } else if ($this->content_type == 'cruise_booking') {
            $this->initialize_entry();
            $this->entry_id = $this->frontend_submit_field_helper->save_cruise_booking($this->entry_id, $this->entry);
			$success = true;
		}

		return array( 'success' => $success, 'entry_id' => $this->entry_id, 'errors' => $errors, 'content_type' => $this->content_type );
    }

    function save_extra_fields() {

    }

	/**
	 * Handle uploading of the files
	 *
	 * @param int     $post_id Parent post id
	 * @return array Combined result of media ids and errors if any
	 */
	function upload_files( $post_id = 0 ) {

		$media_ids = $errors = array();

		// Bail if there are no files
		if ( empty( $_FILES ) )
            return false;

		$files = $_FILES;

		foreach ($files as $file) {

			$fields = array( 'name', 'type', 'tmp_name', 'error', 'size' );

			$k = array();

			foreach ( $fields as $field ) {
				$k[$field] = $file[$field];
            }

            $k['name'] = sanitize_file_name( $k['name'] );

			// Skip to the next file if upload went wrong
			if ( $k['tmp_name'] == "" ) {
				continue;
            }

            $type_check = wp_check_filetype_and_ext( $k['tmp_name'], $k['name'], false );

            // set mimetype
            $k['post_mime_type'] = $k['type'];

			// Add an error message if MIME-type is not allowed
			if ( ! in_array( $type_check['type'], (array) $this->allowed_mime_types ) ) {
				$errors['fes-disallowed-mime-type'][] = array( 'name' => $k['name'], 'mime' => $k['type'] );
				continue;
            }

			// Setup some default values, however you can make additional changes on 'fes_after_upload' action
			$caption = '';
			$file_name = pathinfo( $k['name'], PATHINFO_FILENAME );

			$post_overrides = array(
				'post_status' => $this->publish_immediately() ? 'publish' : 'pending',
				'post_title' => sanitize_text_field( $file_name ),
				'post_content' => empty( $caption ) ? esc_html__( 'Unnamed', 'bookyourtravel' ) : $caption,
				'post_excerpt' => empty( $caption ) ? esc_html__( 'Unnamed', 'bookyourtravel' ) : $caption,
			);

			// Trying to upload the file
            $upload_id = media_handle_sideload( $k, (int) $post_id, $post_overrides['post_title'], $post_overrides );

			if ( !is_wp_error( $upload_id ) ) {
				$media_ids[] = $upload_id;
			} else {
				$errors['fes-error-media'][] = $k['name'];
            }

            $fullsize_path = get_attached_file( $upload_id ); // Full path

            // create the thumbnails
            $attach_data = wp_generate_attachment_metadata( $upload_id, $fullsize_path );
            wp_update_attachment_metadata( $upload_id,  $attach_data );
		}

        // $success determines the rest of upload flow.
        // Setting this to true if no errors were produced even if there's was no files to upload
		$success = empty( $errors ) ? true : false;

		// Allow additional setup. Pass array of attachment ids.
		do_action( 'fes_after_upload', $media_ids, $success, $post_id );

		return array( 'success' => $success, 'media_ids' => $media_ids, 'errors' => $errors );
    }

	function delete_extra_field_image() {

		$result = array();

		// Bail if something fishy is going on
		if ( !wp_verify_nonce( $_REQUEST['_wpnonce'], 'bookyourtravel_nonce' ) ) {
			wp_safe_redirect( esc_url_raw( add_query_arg( array( 'response' => 'fes-error', 'errors' =>  'nonce-failure' ), wp_get_referer() ) ) );
			exit;
		}

        if (isset($_REQUEST['image_id']) && isset($_REQUEST['field_id'])) {
            $image_id = (int) $_REQUEST['image_id'];

            if (isset($_REQUEST['entry_id'])) {
				$field_id = $_REQUEST['field_id'];

                $this->initialize_entry();
                if ($this->entry_id > 0 && !empty($field_id)) {
                    delete_post_meta( $this->entry_id, $field_id);
                }
            }

            wp_delete_attachment($image_id, true);
        }

		exit;
	}

	function delete_featured_image() {

		$result = array();

		// Bail if something fishy is going on
		if ( !wp_verify_nonce( $_REQUEST['_wpnonce'], 'bookyourtravel_nonce' ) ) {
			wp_safe_redirect( esc_url_raw( add_query_arg( array( 'response' => 'fes-error', 'errors' =>  'nonce-failure' ), wp_get_referer() ) ) );
			exit;
		}

        if (isset($_REQUEST['image_id'])) {
            $image_id = (int) $_REQUEST['image_id'];

            if (isset($_REQUEST['entry_id'])) {
                $this->initialize_entry();
                if ($this->entry != null && $image_id > 0) {
                    delete_post_thumbnail($this->entry_id);
                }
            }

            wp_delete_attachment($image_id, true);
        }

		exit;
	}

	function delete_gallery_image() {

		$result = array();

		// Bail if something fishy is going on
		if ( !wp_verify_nonce( $_REQUEST['_wpnonce'], 'bookyourtravel_nonce' ) ) {
			wp_safe_redirect( esc_url_raw( add_query_arg( array( 'response' => 'fes-error', 'errors' =>  'nonce-failure' ), wp_get_referer() ) ) );
			exit;
		}

        if (isset($_REQUEST['image_id'])) {

            $image_id = (int) $_REQUEST['image_id'];

            if (isset($_REQUEST['entry_id'])) {
                $this->initialize_entry();

                if ($this->entry != null && $image_id > 0) {

                    $gallery_images = $this->entry->get_images();

                    for ( $i = 0; $i < count($gallery_images); $i++ ) {
                        $image = $gallery_images[$i];
                        $image_meta_id = $image['image'];
                        if ($image_meta_id == $image_id) {
                            unset($gallery_images[$i]);
                        }
                    }

                    update_post_meta( $this->entry_id, $this->content_type . '_images', $gallery_images );
                }
            }

            wp_delete_attachment($image_id, true);
        }

		exit;
	}

	function frontend_extra_field_image_upload() {

		$result = array();

		// Bail if something fishy is going on
		if ( !wp_verify_nonce( $_REQUEST['_wpnonce'], 'bookyourtravel_nonce' ) ) {
			wp_safe_redirect( esc_url_raw( add_query_arg( array( 'response' => 'fes-error', 'errors' =>  'nonce-failure' ), wp_get_referer() ) ) );
			exit;
		}

		$media_result = $this->upload_files( $this->entry_id );

        if ($media_result['success']) {
            if ($this->entry_id > 0 && isset($media_result['media_ids']) && count($media_result['media_ids'])) {
                $upload_id = $media_result['media_ids'][0];
                set_post_thumbnail($this->entry_id, $upload_id);
            }

            echo json_encode($media_result['media_ids']);
        } else {
            echo json_encode($media_result['errors']);
        }

		exit;
	}

	function upload_featured_image() {

		$result = array();

		// Bail if something fishy is going on
		if ( !wp_verify_nonce( $_REQUEST['_wpnonce'], 'bookyourtravel_nonce' ) ) {
			wp_safe_redirect( esc_url_raw( add_query_arg( array( 'response' => 'fes-error', 'errors' =>  'nonce-failure' ), wp_get_referer() ) ) );
			exit;
        }

		$this->entry_id = (int) $_REQUEST['entry_id'];

		// if (!empty($this->entry_id)) {

			$media_result = $this->upload_files( $this->entry_id );

			if ($media_result['success']) {

				if ($this->entry_id > 0 && isset($media_result['media_ids']) && count($media_result['media_ids'])) {
					$upload_id = $media_result['media_ids'][0];

					set_post_thumbnail($this->entry_id, $upload_id);
				}

				echo json_encode($media_result['media_ids']);
			} else {
				echo json_encode($media_result['errors']);
			}
		// } else {
		//  	echo -1;
		// }

		exit;
	}

	function upload_gallery_images() {

        $result = array();

		// Bail if something fishy is going on
		if ( !wp_verify_nonce( $_REQUEST['_wpnonce'], 'bookyourtravel_nonce' ) ) {
			wp_safe_redirect( esc_url_raw( add_query_arg( array( 'response' => 'fes-error', 'errors' =>  'nonce-failure' ), wp_get_referer() ) ) );
			exit;
        }

        $media_result = $this->upload_files( $this->entry_id );

        if ($media_result['success']) {

            $gallery_images = array();
            if ($this->entry) {
                $gallery_images = $this->entry->get_images();
            }
            if (!isset($gallery_images) || !is_array($gallery_images)) {
                $gallery_images = array();
            }

            foreach ($media_result['media_ids'] as $media_id) {
                if (!empty($media_id)) {
                    $gallery_images[] = array('image' => $media_id);
                }
            }

            if ($this->entry_id > 0) {
                update_post_meta( $this->entry_id, $this->content_type . '_images', $gallery_images );
            }

            echo json_encode($media_result['media_ids']);
        } else {
            echo json_encode($media_result['errors']);
        }

		exit;
	}
}

global $frontend_submit;
$frontend_submit = new Frontend_Submit();
$frontend_submit->init();
