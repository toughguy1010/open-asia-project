<?php

/*
*******************************************************************************
************************** LOAD THE BASE CLASS *******************************
*******************************************************************************
* The WP_List_Table class isn't automatically available to plugins,
* so we need to check if it's available and load it if necessary.
*/

if(!class_exists('WP_List_Table')) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class BookYourTravel_Car_Rental_Bookings_Admin extends BookYourTravel_BaseSingleton {

	private $enable_car_rentals;
	private $price_decimal_places;
	private $default_currency_symbol;
	private $show_currency_symbol_after;

	protected function __construct() {

		global $bookyourtravel_theme_globals;

		$this->enable_car_rentals = $bookyourtravel_theme_globals->enable_car_rentals();
		$this->default_currency_symbol = $bookyourtravel_theme_globals->get_default_currency_symbol();
		$this->show_currency_symbol_after = $bookyourtravel_theme_globals->show_currency_symbol_after();

        // our parent class might
        // contain shared code in its constructor
        parent::__construct();
	}

  public function init() {

		if ($this->enable_car_rentals) {
			add_action( 'admin_menu' , array( $this, 'bookings_admin_page' ) );
			add_action( 'plugins_loaded' , array( $this, 'plugins_loaded' ) );						
			add_action( 'admin_head', array( $this, 'bookings_admin_head' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts_styles' ) );
		}
	}

	function plugins_loaded() {
		add_filter( 'set-screen-option', array( $this, 'bookings_set_screen_options' ), 10, 3);
	}			

	function enqueue_admin_scripts_styles() {

		$page = ( isset($_GET['page'] ) ) ? esc_attr( $_GET['page'] ) : false;
		if ('theme_car_rental_booking_admin.php' != $page ) {
			return;
		}

		global $bookyourtravel_car_rental_helper;
		$date_format = get_option('date_format');

		$booking_id = 0;
		if (isset($_GET['booking_id'])) {
			$booking_id = (int)$_GET['booking_id'];
		}

		$car_rental_id = 0;
		$car_rental_pick_up_id = 0;
		$car_rental_drop_off_id = 0;
		$booking_object = null;

		if (!empty($booking_id)) {
			$booking_object = $bookyourtravel_car_rental_helper->get_car_rental_booking($booking_id);
			if ($booking_object) {
				$car_rental_id = $booking_object->car_rental_id;
			}
		} else {
			if (isset($_POST['car_rental_id'])) {
				$car_rental_id = (int)$_POST['car_rental_id'];
			}
		}

		if (isset($_GET['car_rental_pick_up_id'])) {
			$car_rental_pick_up_id = absint($_GET['car_rental_pick_up_id']);
		} else if (isset($_POST['car_rental_pick_up_id'])) {
			$car_rental_pick_up_id = intval(sanitize_text_field($_POST['car_rental_pick_up_id']));
		} else if ($booking_object != null) {
			$car_rental_pick_up_id = intval($booking_object->car_rental_pick_up_id);
		}

		if (isset($_GET['car_rental_drop_off_id'])) {
			$car_rental_drop_off_id = absint($_GET['car_rental_drop_off_id']);
		} else if (isset($_POST['car_rental_drop_off_id'])) {
			$car_rental_drop_off_id = intval(sanitize_text_field($_POST['car_rental_drop_off_id']));
		} else if ($booking_object != null) {
			$car_rental_drop_off_id = intval($booking_object->car_rental_drop_off_id);
		}

		wp_enqueue_script( 'bookyourtravel-admin-script', BookYourTravel_Theme_Utils::get_file_uri ('/js/admin/admin.js'), array('jquery'), BOOKYOURTRAVEL_VERSION);

		$date_from = null;
		$date_from_formatted = '';
		if (isset($_POST['date_from'])) {
			$date_from = sanitize_text_field($_POST['date_from']);
		} else if ($booking_object != null) {
			$date_from = $booking_object->start_date;
		}
		if (isset($date_from)) {
			$date_from_formatted = date_i18n( $date_format, strtotime( $date_from ) );
		}

		$date_to = null;
		$date_to_formatted = '';
		if (isset($_POST['date_to'])) {
			$date_to = sanitize_text_field($_POST['date_to']);
		} else if ($booking_object != null) {
			$date_to = $booking_object->end_date;
		}
		if (isset($date_to)) {
			$date_to_formatted = date_i18n( $date_format, strtotime( $date_to ) );
		}

		wp_localize_script( 'bookyourtravel-admin-script', 'BYTAdminCarRentals', array(
			'carRentalId' => $car_rental_id,
			'carRentalLocations' => array(),
			'carRentalPickUpLocationId' => $car_rental_pick_up_id,
			'carRentalDropOffLocationId' => $car_rental_drop_off_id,
			'carRentalDateFromValue' => $date_from_formatted,
			'carRentalDateToValue' => $date_to_formatted,
		) );
	}

	function bookings_admin_page() {

		$hook = add_submenu_page('edit.php?post_type=car_rental', esc_html__('Car Rental Bookings', 'bookyourtravel'), esc_html__('Bookings', 'bookyourtravel'), 'edit_posts', basename(__FILE__), array( $this, 'bookings_admin_display') );
		add_action( "load-$hook", array( $this, 'bookings_add_screen_options') );
	}

	function bookings_set_screen_options($status, $option, $value) {
		if ( 'bookings_per_page' == $option )
			return $value;
	}

	function bookings_admin_head() {
		$page = ( isset($_GET['page'] ) ) ? esc_attr( $_GET['page'] ) : false;
		if( 'theme_car_rental_booking_admin.php' != $page ) {
			return;
		}
	}

	function bookings_add_screen_options() {

		global $wp_car_rental_bookings_table;
		$option = 'per_page';
		$args = array('label' => esc_html__('Bookings', 'bookyourtravel'),'default' => 50,'option' => 'bookings_per_page');
		add_screen_option( $option, $args );
		$wp_car_rental_bookings_table = new Car_Rental_Bookings_Admin_List_Table();
	}

	function bookings_admin_display() {

		echo '</pre><div class="wrap">';
		echo '<h2>' . esc_html__('Car Rental bookings', 'bookyourtravel') . '</h2>';

		global $wp_car_rental_bookings_table;

		$wp_car_rental_bookings_table->handle_form_submit();

		$booking_id = 0;
		if (isset($_GET['booking_id'])) {
			$booking_id = (int)$_GET['booking_id'];
		}

		global $bookyourtravel_car_rental_helper, $booking_insert_success;

		$car_rental_id = 0;

		if (!empty($booking_id)) {

			$booking_object = $bookyourtravel_car_rental_helper->get_car_rental_booking($booking_id);

			if ($booking_object) {
				$car_rental_id = $booking_object->car_rental_id;
			}
		} else {

			if (isset($_POST['car_rental_id'])) {
				$car_rental_id = (int)$_POST['car_rental_id'];
			}
		}

		if (isset($_GET['view'])) {
			$wp_car_rental_bookings_table->render_view_form();
		} else if (isset($_GET['sub']) && $_GET['sub'] == 'manage') {
			$wp_car_rental_bookings_table->render_entry_form($booking_id);
		} else {
			$wp_car_rental_bookings_table->prepare_items();

			if (!empty($_REQUEST['s']))
				$form_uri = esc_url( add_query_arg( 's', sanitize_text_field($_REQUEST['s']), $_SERVER['REQUEST_URI'] ));
			else
				$form_uri = esc_url($_SERVER['REQUEST_URI']);
			?>
			<div class="alignright actions ">
				<form method="get" action="<?php echo esc_url($form_uri); ?>">
					<input type="hidden" name="paged" value="1">
					<input type="hidden" name="post_type" value="car_rental">
					<input type="hidden" name="page" value="theme_car_rental_booking_admin.php">
					<?php
					$wp_car_rental_bookings_table->search_box( 'search', 'search_id' );
					?>
				</form>
			</div>
			<?php
			$wp_car_rental_bookings_table->display();
		}
	}
}

global $car_rental_bookings_admin;
$car_rental_bookings_admin = BookYourTravel_Car_Rental_Bookings_Admin::get_instance();

/************************** CREATE A PACKAGE CLASS *****************************
 *******************************************************************************
 * Create a new list table package that extends the core WP_List_Table class.
 * WP_List_Table contains most of the framework for generating the table, but we
 * need to define and override some methods so that our data can be displayed
 * exactly the way we need it to be.
 *
 * To display this on a page, you will first need to instantiate the class,
 * then call $yourInstance->prepare_items() to handle any data manipulation, then
 * finally call $yourInstance->display() to render the table to the page.
 */
class Car_Rental_Bookings_Admin_List_Table extends WP_List_Table {

	private $options;
	private $date_format;
	private $price_decimal_places;
	private $default_currency_symbol;
	private $show_currency_symbol_after;

	/**
	* Constructor, we override the parent to pass our own arguments.
	* We use the parent reference to set some default configs.
	*/
	function __construct() {

		global $status, $page;
		global $bookyourtravel_theme_globals;

		$this->price_decimal_places = $bookyourtravel_theme_globals->get_price_decimal_places();
		$this->default_currency_symbol = $bookyourtravel_theme_globals->get_default_currency_symbol();
		$this->show_currency_symbol_after = $bookyourtravel_theme_globals->show_currency_symbol_after();
		$this->date_format = get_option('date_format');

		 parent::__construct( array(
			'singular'=> 'booking', // Singular label
			'plural' => 'bookings', // plural label, also this well be one of the table css class
			'ajax'	=> false // We won't support Ajax for this table
		) );

	}

	function column_default( $item, $column_name ) {
		return $item->$column_name;
	}

    protected function display_tablenav( $which ) {
		if ( 'top' === $which ) {
			wp_nonce_field( 'bulk-' . $this->_args['plural'] );
		}
		?>
		<div class="tablenav <?php echo esc_attr( $which ); ?>">
			<?php
			$this->extra_tablenav( $which );
			$this->pagination( $which );
			?>
			<br class="clear" />
		</div>
		<?php
	}

	function extra_tablenav( $which ) {
		if ( $which == "top" ) {
			//The code that goes before the table is here
		?>
		<div class="alignleft actions bookyourtravel-admin-top">
			<a href="edit.php?post_type=car_rental&page=theme_car_rental_booking_admin.php&sub=manage" class="button-secondary action" ><?php esc_html_e('Add Booking', 'bookyourtravel') ?></a>
		</div>
		<?php
		}
		if ( $which == "bottom" ) {
			//The code that goes after the table is there
		?>
		<div class="alignleft actions bookyourtravel-admin-bottom">
			<a href="edit.php?post_type=car_rental&page=theme_car_rental_booking_admin.php&sub=manage" class="button-secondary action" ><?php esc_html_e('Add Booking', 'bookyourtravel') ?></a>
		</div>
		<?php
		}
	}

	function format_price($price) {
		if (!$this->show_currency_symbol_after) {
			return $this->default_currency_symbol . '' . number_format_i18n( $price, $this->price_decimal_places );
		} else {
			return number_format_i18n( $price, $this->price_decimal_places ) . '' . $this->default_currency_symbol;
		}
    }

	function column_Status($item) {
        global $bookyourtravel_theme_globals;

		$status_array = array (
			'pending' => esc_html__('Pending', 'bookyourtravel'),
			'on-hold' => esc_html__('On hold', 'bookyourtravel'),
			'completed' => esc_html__('Completed', 'bookyourtravel'),
			'processing' => esc_html__('Processing', 'bookyourtravel'),
			'cancelled' => esc_html__('Cancelled', 'bookyourtravel'),
			'initiated' => esc_html__('Initiated', 'bookyourtravel'),
		);

        if ($item->woo_order_id > 0 && isset($status_array[$item->woo_status])) {
            $edit_url = admin_url( 'post.php?post=' . $item->woo_order_id ) . '&action=edit';
            return sprintf("<a target='_blank' href='%s'>%s</a>", $edit_url, $status_array[$item->woo_status]);
        } else if ($bookyourtravel_theme_globals->use_woocommerce_for_checkout()) {
            return __("Initiated", "bookyourtravel");
        }
        return $item->woo_status;
	}

	function column_Customer($item) {
		return $item->first_name . ' ' . $item->last_name;
	}

	function column_CartPrice($item) {
		return $this->format_price($item->cart_price);
	}

	function column_Car_Rental($item) {
		return $item->car_rental_name;
	}

	function column_TotalPrice($item) {
		return $this->format_price($item->total_price);
	}

	function column_DateFrom($item) {
		return date_i18n($this->date_format, strtotime($item->start_date));
	}

	function column_DateTo($item) {
		return date_i18n($this->date_format, strtotime($item->end_date));
	}

	function column_Created($item) {
		return date_i18n($this->date_format, strtotime($item->created) );
	}

	function column_Action($item) {
		return  "<a href='edit.php?post_type=car_rental&page=theme_car_rental_booking_admin.php&sub=manage&booking_id=" . $item->Id . "'>" . esc_html__('Edit', 'bookyourtravel') . "</a> |
				<form method='post' name='delete_booking_" . $item->Id . "' id='delete_booking_" . $item->Id . "' style='display:inline;'>"
				. wp_nonce_field('bookyourtravel_nonce') . "
					<input type='hidden' name='delete_booking' id='delete_booking' value='" . $item->Id . "' />
					<a href='javascript: void(0);' onclick='confirmDelete(\"#delete_booking_" . $item->Id . "\", \"" . esc_html__('Are you sure?', 'bookyourtravel') . "\");'>" . esc_html__('Delete', 'bookyourtravel') . "</a>
				</form>";
	}

	/**
	 * Define the columns that are going to be used in the table
	 * @return array $columns, the array of columns to use with the table
	 */
	function get_columns() {
		$columns= array(
			'Id'=>esc_html__('Id', 'bookyourtravel'),
			'Customer'=>esc_html__('Customer', 'bookyourtravel'),
			'DateFrom'=>esc_html__('From', 'bookyourtravel'),
			'DateTo'=>esc_html__('To', 'bookyourtravel'),
			'Car_Rental'=>esc_html__('Car', 'bookyourtravel'),
			'Created'=>esc_html__('Created', 'bookyourtravel'),
			'TotalPrice'=>esc_html__('Price', 'bookyourtravel')
        );

        global $bookyourtravel_theme_globals;
		if ($bookyourtravel_theme_globals->enable_deposit_payments()) {
			$columns['CartPrice'] = esc_html__('Deposited Amount', 'bookyourtravel');
		}

        if ($bookyourtravel_theme_globals->use_woocommerce_for_checkout()) {
            $columns["Status"] = esc_html__('Status', 'bookyourtravel');
		}

		$columns['Action'] = esc_html__('Action', 'bookyourtravel');

        return $columns;
	}

	/**
	 * Decide which columns to activate the sorting functionality on
	 * @return array $sortable, the array of columns that can be sorted by the user
	 */
	public function get_sortable_columns() {
		$sortable_columns = array(
			'Id'=> array( 'Id', true ),
			'Car_Rental'=> array( 'car_rentals.post_title', true ),
			'TotalPrice'=> array( 'total_price', true ),
			'CartPrice'=> array( 'cart_price', true ),
			'DateFrom'=> array( 'start_date', true ),
			'DateTo'=> array( 'end_date', true ),
			'Created'=> array( 'created', true ),
		);
		return $sortable_columns;
	}

	/**
	 * Prepare the table with different parameters, pagination, columns and table elements
	 */
	function prepare_items() {

		global $bookyourtravel_car_rental_helper;
		global $_wp_column_headers;

		$screen = get_current_screen();
		$user = get_current_user_id();
		$option = $screen->get_option('per_page', 'option');
		$per_page = get_user_meta($user, $option, true);
		if ( empty ( $per_page) || $per_page < 1 ) {
			$per_page = $screen->get_option( 'per_page', 'default' );
		}

		$search_term = '';
		if (!empty($_REQUEST['s'])) {
			$search_term = strtolower(sanitize_text_field($_REQUEST['s']));
		}

		$columns = $this->get_columns();
		$hidden = get_hidden_columns( $screen );
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array($columns, $hidden, $sortable);

		/* -- Ordering parameters -- */
		//Parameters that are going to be used to order the result
		$orderby = !empty($_GET["orderby"]) ? sanitize_text_field($_GET["orderby"]) : 'Id';
		$order = !empty($_GET["order"]) ? sanitize_text_field($_GET["order"]) : 'ASC';

		/* -- Pagination parameters -- */
		//How many to display per page?
		//Which page is this?
		$paged = !empty($_GET["paged"]) ? sanitize_text_field($_GET["paged"]) : '';
		//Page Number
		if(empty($paged) || !is_numeric($paged) || $paged<=0 ) { $paged=1; }
		//How many pages do we have in total?

		$author_id = null;
		if (!(current_user_can('editor') || current_user_can('administrator'))) {
			$author_id = get_current_user_id();
		}

		$car_rental_booking_results = $bookyourtravel_car_rental_helper->list_car_rental_bookings($search_term, $orderby, $order, $paged, $per_page, 0, $author_id);
		//Number of elements in your table?
		$totalitems = $car_rental_booking_results['total']; //return the total number of affected rows

		$totalpages = ceil($totalitems/$per_page);

		/* -- Register the pagination -- */
		$this->set_pagination_args( array(
			"total_items" => $totalitems,
			"total_pages" => $totalpages,
			"per_page" => $per_page,
		) );
		//The pagination links are automatically built according to those parameters

		/* -- Register the Columns -- */
		$columns = $this->get_columns();
		$_wp_column_headers[$screen->id]=$columns;

		/* -- Fetch the items -- */
		$this->items = $car_rental_booking_results['results'];
	}

	function handle_form_submit() {

		global $bookyourtravel_car_rental_helper, $bookyourtravel_theme_globals, $bookyourtravel_theme_woocommerce, $booking_insert_success, $enable_extra_items ;

		$use_woocommerce_for_checkout = $bookyourtravel_theme_globals->use_woocommerce_for_checkout();
		$current_user = wp_get_current_user();
		$enable_extra_items = $bookyourtravel_theme_globals->enable_extra_items();

		if (isset($_POST['delete_booking']) && check_admin_referer('bookyourtravel_nonce')) {

			$booking_id = absint($_POST['delete_booking']);

			$bookyourtravel_car_rental_helper->delete_car_rental_booking($booking_id);

			echo '<div class="updated" id="message" onclick="this.parentNode.removeChild(this)">';
			echo '<p>' . esc_html__('Successfully deleted booking!', 'bookyourtravel') . '</p>';
			echo '</div>';

		} else if ((isset($_POST['insert']) || isset($_POST['update'])) && check_admin_referer('bookyourtravel_nonce')) {

			$error = '';
			$car_rental_id = 0;
			$car_rental_obj = null;

			if (empty($_POST['car_rental_id'])) {
				$error = esc_html__('You must select a car rental', 'bookyourtravel');
			} else if (empty($_POST['car_rental_pick_up_id'])) {
				$error = esc_html__('You must select a car rental pick up location', 'bookyourtravel');
			} else if (empty($_POST['car_rental_drop_off_id'])) {
				$error = esc_html__('You must select a car rental drop off location', 'bookyourtravel');
			} else if (empty($_POST['date_from'])) {
				$error = esc_html__('You must select a from date', 'bookyourtravel');
			} else if (empty($_POST['date_to'])) {
				$error = esc_html__('You must select a to date', 'bookyourtravel');
			} else if (empty($_POST['total_price']) || $_POST['total_price'] === 0) {
				$error = esc_html__('A valid total price must be entered', 'bookyourtravel');
			} else {

				$booking_form_fields = $bookyourtravel_theme_globals->get_booking_form_fields();
				foreach ($booking_form_fields as $form_field) {

					$field_required = isset($form_field['required']) ? $form_field['required'] : 0;
					$field_hidden = isset($form_field['hide']) ? $form_field['hide'] : 0;
					$field_id = $form_field['id'];

					if ($field_required && !$field_hidden && (!isset($_REQUEST[$field_id]) || empty($_REQUEST[$field_id]))) {
						$field_label = $form_field['label'];
						$error = sprintf(__('Field %s is required', 'bookyourtravel'), $field_label);
					}
				}
			}

			if (!empty($error)) {

				echo '<div class="error" id="message" onclick="this.parentNode.removeChild(this)">';
				echo '<p>' . $error . '</p>';
				echo '</div>';

			} else {

				$car_rental_id = (int)$_POST['car_rental_id'];
				$car_rental_obj = new BookYourTravel_Car_Rental($car_rental_id);

				$booking_object = $bookyourtravel_car_rental_helper->retrieve_booking_values_from_request(true);

				if (isset($_POST['insert'])) {

					$booking_object->Id = $bookyourtravel_car_rental_helper->create_car_rental_booking($current_user->ID, $booking_object);

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

					$car_rental_is_reservation_only = $car_rental_obj->get_is_reservation_only();

					if (BookYourTravel_Theme_Utils::is_woocommerce_active() && $use_woocommerce_for_checkout && !$car_rental_is_reservation_only) {
						$bookyourtravel_theme_woocommerce->dynamically_create_car_rental_woo_order($booking_object->Id, $booking_object->cart_price, $address_array, $car_rental_id);
					}

					if ($booking_object->Id > 0) {
						echo '<div class="updated" id="message" onclick="this.parentNode.removeChild(this)">';
						echo '<p>' . esc_html__('Successfully inserted new car rental booking entry!', 'bookyourtravel') . '</p>';
						echo '</div>';
						$booking_insert_success = true;
					} else {
						echo '<div class="error" id="message" onclick="this.parentNode.removeChild(this)">';
						echo '<p>' . esc_html__('Oops. Something happened! Failed to insert new car rental booking entry!', 'bookyourtravel') . '</p>';
						echo '</div>';
					}
				} else if (isset($_POST['update'])) {

					$booking_id = isset($_POST['booking_id']) ? absint($_POST['booking_id']) : 0;

					$result = $bookyourtravel_car_rental_helper->update_car_rental_booking ($booking_id, $booking_object);

					if ($result == 1) {
						echo '<div class="updated" id="message" onclick="this.parentNode.removeChild(this)">';
						echo '<p>' . esc_html__('Successfully updated car rental booking entry!', 'bookyourtravel') . '</p>';
						echo '</div>';
					} else {
						echo '<div class="error" id="message" onclick="this.parentNode.removeChild(this)">';
						echo '<p>' . esc_html__('Oops. Something happened! Failed to update car rental booking entry!', 'bookyourtravel') . '</p>';
						echo '</div>';
					}

				}

				return $booking_object->Id;
			}
		}
	}

	function render_entry_form($booking_id) {

		global $booking_insert_success, $bookyourtravel_location_helper, $bookyourtravel_car_rental_helper, $enable_extra_items, $bookyourtravel_theme_globals;

		$booking_object = null;

		if ($booking_id > 0) {
			$booking_object = $bookyourtravel_car_rental_helper->get_car_rental_booking($booking_id);
		}

		$car_rental_id = 0;
		if (isset($_GET['car_rental_id'])) {
			$car_rental_id = absint($_GET['car_rental_id']);
		} else if (isset($_POST['car_rental_id'])) {
			$car_rental_id = intval(sanitize_text_field($_POST['car_rental_id']));
		} else if ($booking_object != null) {
			$car_rental_id = $booking_object->car_rental_id;
		}

		$car_rental_pick_up_id = 0;
		if (isset($_GET['car_rental_pick_up_id'])) {
			$car_rental_pick_up_id = absint($_GET['car_rental_pick_up_id']);
		} else if (isset($_POST['car_rental_pick_up_id'])) {
			$car_rental_pick_up_id = intval(sanitize_text_field($_POST['car_rental_pick_up_id']));
		} else if ($booking_object != null) {
			$car_rental_pick_up_id = $booking_object->car_rental_pick_up_id;
		}

		$car_rental_drop_off_id = 0;
		if (isset($_GET['car_rental_drop_off_id'])) {
			$car_rental_drop_off_id = absint($_GET['car_rental_drop_off_id']);
		} else if (isset($_POST['car_rental_drop_off_id'])) {
			$car_rental_drop_off_id = intval(sanitize_text_field($_POST['car_rental_drop_off_id']));
		} else if ($booking_object != null) {
			$car_rental_drop_off_id = $booking_object->car_rental_drop_off_id;
		}

		$date_from = null;
		$date_from_formatted = '';
		if (isset($_POST['date_from']))
			$date_from = sanitize_text_field($_POST['date_from']);
		else if ($booking_object != null) {
			$date_from = $booking_object->start_date;
		}
		if (isset($date_from))
			$date_from_formatted = date_i18n( $this->date_format, strtotime( $date_from ) );

		$date_to = null;
		$date_to_formatted = '';
		if (isset($_POST['date_to']))
			$date_to = sanitize_text_field($_POST['date_to']);
		else if ($booking_object != null) {
			$date_to = $booking_object->end_date;
		}
		if (isset($date_to))
			$date_to_formatted = date_i18n( $this->date_format, strtotime( $date_to ) );

		$total_price = 0;
		if (isset($_POST['total_price']))
			$total_price = floatval(sanitize_text_field($_POST['total_price']));
		else if ($booking_object != null) {
			$total_price = $booking_object->total_price;
		}
		
		$cart_price = 0;
		if (isset($_POST['cart_price']) && !$booking_insert_success)
			$cart_price = floatval(sanitize_text_field($_POST['cart_price']));
		else if ($booking_object != null) {
			$cart_price = $booking_object->cart_price;
		}

		$total_extra_items_price = 0;
		if (isset($_POST['total_extra_items_price']))
			$total_extra_items_price = floatval(sanitize_text_field($_POST['total_extra_items_price']));
		else if ($booking_object != null) {
			$total_extra_items_price = $booking_object->total_extra_items_price;
		}

		$total_car_rental_price = 0;
		if (isset($_POST['total_car_rental_price']))
			$total_car_rental_price = floatval(sanitize_text_field($_POST['total_car_rental_price']));
		else if ($booking_object != null) {
			$total_car_rental_price = $booking_object->total_car_rental_price;
		}

		if ($booking_object)
			echo '<h3>' . esc_html__('Update Car Rental Booking', 'bookyourtravel') . '</h3>';
		else
			echo '<h3>' . esc_html__('Add Car Rental Booking', 'bookyourtravel') . '</h3>';

		$use_woocommerce_for_checkout = $bookyourtravel_theme_globals->use_woocommerce_for_checkout();

		echo '<form id="car_rental_booking_form" method="post" action="' . esc_url($_SERVER['REQUEST_URI']) . '" style="clear: both;">';

		echo wp_nonce_field('bookyourtravel_nonce');
		echo "<input type='hidden' name='booking_id' id='booking_id' value='" . (isset($booking_id) ? $booking_id : 0) . "' />";
		echo '<table cellpadding="3" class="form-table"><tbody>';

		$car_rental_obj = null;
		if ($car_rental_id > 0) {
			$car_rental_obj = new BookYourTravel_Car_Rental((int)$car_rental_id);
		}

		$author_id = null;
		if (!(current_user_can('editor') || current_user_can('administrator'))) {
			$author_id = get_current_user_id();
		}

		$car_rentals_control = '';

		if ($booking_id > 0 && BookYourTravel_Theme_Utils::is_woocommerce_active() && $use_woocommerce_for_checkout && $car_rental_id > 0) {
			// we are editing a booking and woocommerce is active, so don't allow change of car rental because data will be corrupt then (data sync between woocommerce order and byt booking).

			$car_rentals_control .= "<input type='hidden' value='" . $car_rental_id . "' id='car_rental_id' name='car_rental_id' />";
			$car_rentals_control .= $car_rental_obj->get_title();

		} else {
			$car_rental_results = $bookyourtravel_car_rental_helper->list_car_rentals(0, -1, 'title', 'ASC', array(), false, array(), array(), array(), false, $author_id);

			$car_rentals_control = '<select id="car_rental_id" name="car_rental_id" class="car_rentals_select">';
			$car_rentals_control .= '<option value="">' . esc_html__('Select car', 'bookyourtravel') . '</option>';
			if ( count($car_rental_results) > 0 && $car_rental_results['total'] > 0 ) {
				foreach ($car_rental_results['results'] as $car_rental_result) {
					$car_rentals_control .= '<option value="' . $car_rental_result->ID . '" ' . ($car_rental_result->ID == $car_rental_id ? 'selected' : '') . '>' . $car_rental_result->post_title . '</option>';
				}
			}
			$car_rentals_control .= '</select>';
		}


		$car_rental_pick_up_control = '<select id="car_rental_pick_up_id" name="car_rental_pick_up_id" class="car_rental_pick_up_select">';
		$car_rental_pick_up_control .= '<option value="">' . esc_html__('Select pickup location', 'bookyourtravel') . '</option>';

		if ($car_rental_id > 0) {
			$car_rental_locations = $car_rental_obj->get_locations();
			if ($car_rental_locations && count($car_rental_locations) > 0) {
				foreach ($car_rental_locations as $location_id) {
					$location_obj = new BookYourTravel_Location($location_id);
					$location_title = $location_obj->get_title();
					$car_rental_pick_up_control .= '<option ' . ($car_rental_pick_up_id == $location_id ? "selected" : "") . ' value="' . esc_attr($location_id) . '">' . $location_title . '</option>';
				}
			}
		}

		$car_rental_pick_up_control .= '</select>';

		$car_rental_drop_off_control = '<select id="car_rental_drop_off_id" name="car_rental_drop_off_id" class="car_rental_drop_off_select">';
		$car_rental_drop_off_control .= '<option value="">' . esc_html__('Select drop off location', 'bookyourtravel') . '</option>';
		if ($car_rental_id > 0) {
			if ($car_rental_locations && count($car_rental_locations) > 0) {
				foreach ($car_rental_locations as $location_id) {
					$location_obj = new BookYourTravel_Location($location_id);
					$location_title = $location_obj->get_title();
					$car_rental_drop_off_control .= '<option ' . ($car_rental_drop_off_id == $location_id ? "selected" : "") . ' value="' . esc_attr($location_id) . '">' . $location_title . '</option>';
				}
			}
		}
		$car_rental_drop_off_control .= '</select>';

		echo '<tr class="tr-car-rental">';
		echo '	<th scope="row" valign="top">' . esc_html__('Car', 'bookyourtravel') . ' *</th>';
		echo '	<td>';
		echo $car_rentals_control;
		echo '	<div class="loading" style="display:none;"></div>';
		echo '</td>';
		echo '</tr>';

		echo '<tr style="display:none;" class="car_rental_selected step_1">';
		echo '	<th scope="row" valign="top">' . esc_html__('Pick up location', 'bookyourtravel') . ' *</th>';
		echo '	<td>';
		echo $car_rental_pick_up_control;
		echo '  </td>';
		echo '</tr>';

		echo '<tr style="display:none;" class="car_rental_selected step_1">';
		echo '	<th scope="row" valign="top">' . esc_html__('Drop off location', 'bookyourtravel') . ' *</th>';
		echo '	<td>';
		echo $car_rental_drop_off_control;
		echo '  </td>';
		echo '</tr>';

		echo '<tr style="display:none;" class="car_rental_selected step_1">';
		echo '	<th scope="row" valign="top">' . esc_html__('Date from', 'bookyourtravel') . ' *</th>';
		echo '	<td>';
		echo '  	<input readonly class="datepicker" type="text" name="datepicker_from" id="datepicker_from" />';
		echo '		<input type="hidden" name="date_from" id="date_from" value="' . $date_from . '" />';
		echo '	</td>';
		echo '</tr>';

		echo '<tr style="display:none;" class="car_rental_selected step_1">';
		echo '	<th scope="row" valign="top">' . esc_html__('Date to', 'bookyourtravel') . ' *</th>';
		echo '	<td>';
		echo '  	<input readonly class="datepicker" type="text" name="datepicker_to" id="datepicker_to" />';
		echo '		<input type="hidden" name="date_to" id="date_to" value="' . $date_to . '" />';
		echo '	</td>';
		echo '</tr>';

		$booking_object_other_fields = isset($booking_object->other_fields) ? unserialize($booking_object->other_fields) : array();
		$booking_form_fields = $bookyourtravel_theme_globals->get_booking_form_fields();

		foreach ($booking_form_fields as $booking_field) {

			$field_type = $booking_field['type'];
			$field_hidden = isset($booking_field['hide']) && $booking_field['hide'] == 1 ? true : false;
			$field_id = $booking_field['id'];
			$field_required = isset($booking_field['required']) && $booking_field['required'] == '1' ? true : false;

			$field_value = '';

			if (isset($_POST[$field_id])) {
				$field_value = $_POST[$field_id];
			} else {
				if ($field_id == 'first_name' || $field_id == 'last_name' || $field_id == 'email' || $field_id == 'phone' || $field_id == 'address' || $field_id == 'town' || $field_id == 'zip' || $field_id == 'country' || $field_id == 'special_requirements' || $field_id == 'state' || $field_id == 'address_2' || $field_id == 'company') {
					$field_value = isset($booking_object->{$field_id}) ? $booking_object->{$field_id} : '';
				} else {
					if (isset($booking_object_other_fields[$field_id]))
						$field_value = $booking_object_other_fields[$field_id];
				}
			}

			if (!$field_hidden) {

				echo '<tr style="display:none;" class="car_rental_selected step_1">';
				echo '	<th scope="row" valign="top">' . esc_html($booking_field['label']) . ($field_required ? ' *' : '') . '</th>';
				echo '	<td>';

				if ($field_type == 'email') {
					echo '<input value="' . esc_attr($field_value) . '" ' . ($field_required ? 'data-required' : '') . ' type="email" id="' . esc_attr($field_id) . '" name="' . esc_attr($field_id) . '" />';
				} else if ($field_type == 'textarea') {
					echo '<textarea ' . ($field_required ? 'data-required' : '') . ' name="' . esc_attr($field_id) . '" id="' . esc_attr($field_id) . '" rows="5" cols="50" >' . esc_html($field_value) . '</textarea>';
				} else if ($field_type == 'select' && isset($booking_field['options'])) {
					BookYourTravel_Theme_Admin_Controls::the_dynamic_field_select_control($booking_field, $field_value);
				} else {
					echo '<input value="' . esc_attr($field_value) . '" ' . ($field_required ? 'data-required' : '') . ' type="text" id="' . esc_attr($field_id) . '" name="' . esc_attr($field_id) . '" />';
				}
			}

			echo '  </td>';
			echo '</tr>';
		}

		$enable_extra_items = $bookyourtravel_theme_globals->enable_extra_items();

		if ($enable_extra_items) {

			echo '<tr style="display:none;" class="car_rental_selected step_1">';
			echo '	<th scope="row" valign="top">' . esc_html__('Extra items', 'bookyourtravel') . '</th>';
			echo '	<td>';

			if (isset($booking_object->extra_items)) {
				$extra_items_array = unserialize($booking_object->extra_items);
				if ($extra_items_array && count($extra_items_array) > 0) {

					echo "<table>";
					echo "<thead>";
					echo "<tr>";
					echo "<th>" . esc_html__('Item', 'bookyourtravel') . "</th>";
					echo "<th>" . esc_html__('Quantity', 'bookyourtravel') . "</th>";
					echo "<th>" . esc_html__('Per day?', 'bookyourtravel') . "</th>";
					echo "<th>" . esc_html__('Per person?', 'bookyourtravel') . "</th>";
					echo "<th>" . esc_html__('Price', 'bookyourtravel') . "</th>";
					echo "</tr>";
					echo "</thead>";
					echo "<tbody>";

					$item_count = 0;
					foreach ($extra_items_array as $extra_item_id => $quantity) {

						$car_rental_extra_item_obj = new BookYourTravel_Extra_Item($extra_item_id);
						$item_price = $car_rental_extra_item_obj->get_custom_field('_extra_item_price', false);
						$item_price_per_day = intval($car_rental_extra_item_obj->get_custom_field('_extra_item_price_per_day', false));
						$item_price_per_person = intval($car_rental_extra_item_obj->get_custom_field('_extra_item_price_per_person', false));

						echo "<tr>";
						echo "<td>";
						echo "<input type='hidden' id='extra_item_" . $extra_item_id . "_id' name='extra_items[" . $item_count . "][id]' value='" . $extra_item_id . "' />";
						echo "<input type='hidden' id='extra_item_" . $extra_item_id . "_quantity' name='extra_items[" . $item_count . "][quantity]' value='" . $quantity . "' />";
						echo esc_html($car_rental_extra_item_obj->get_title());
						echo "</td>";
						echo "<td>";
						echo esc_html($quantity);
						echo "</td>";
						echo "<td>";
						echo ($item_price_per_day ? esc_html__('Yes', 'bookyourtravel') : esc_html__('No', 'bookyourtravel'));
						echo "</td>";
						echo "<td>";
						echo ($item_price_per_person ? esc_html__('Yes', 'bookyourtravel') : esc_html__('No', 'bookyourtravel'));
						echo "</td>";
						echo "<td>";
						echo number_format_i18n( $item_price, $this->price_decimal_places );
						echo "</td>";
						echo "</tr>";

						$item_count++;
					}

					echo "</tbody>";
					echo "</table>";

				} else {
					echo "<input type='hidden' name='extra_items[]' id='extra_items[]' />";
					echo esc_html__('None selected', 'bookyourtravel');
				}
			} else {
				echo "<input type='hidden' name='extra_items[]' id='extra_items[]' />";
				echo esc_html__('None selected', 'bookyourtravel');
			}

			echo '</td>';
			echo '</tr>';

			echo '<tr style="display:none;" class="car_rental_selected step_1">';
			echo '	<th scope="row" valign="top">' . esc_html__('Reservation total', 'bookyourtravel') . '</th>';
			echo '	<td><input type="text" name="total_car_rental_price" id="total_car_rental_price" value="' . number_format_i18n($total_car_rental_price, $this->price_decimal_places ) . '" /></td>';
			echo '</tr>';

			echo '<tr style="display:none;" class="car_rental_selected step_1">';
			echo '	<th scope="row" valign="top">' . esc_html__('Extra items total', 'bookyourtravel') . '</th>';
			echo '	<td><input type="text" name="total_extra_items_price" id="total_extra_items_price" value="' . number_format_i18n($total_extra_items_price, $this->price_decimal_places ) . '" /></td>';
			echo '</tr>';

		}

		echo '<tr style="display:none;" class="car_rental_selected step_1">';
		echo '	<th scope="row" valign="top">' . esc_html__('Total price', 'bookyourtravel') . '</th>';
		echo '	<td><input type="text" name="total_price" id="total_price" value="' . number_format_i18n($total_price, $this->price_decimal_places ) . '" /></td>';
		echo '</tr>';

        if ($bookyourtravel_theme_globals->enable_deposit_payments()) {
            echo '<tr style="display:none;" class="car_rental_selected step_1">';
            echo '	<th scope="row" valign="top">' . esc_html__('Deposited amount', 'bookyourtravel') . '</th>';
            echo '	<td><input type="text" name="cart_price" id="cart_price" value="' . number_format_i18n($cart_price, $this->price_decimal_places) . '" /></td>';
            echo '</tr>';
        }

		echo '</table>';
		echo '<p>';
		echo '<a href="edit.php?post_type=car_rental&page=theme_car_rental_booking_admin.php" class="button-secondary">' . esc_html__('Cancel', 'bookyourtravel') . '</a>&nbsp;';
		if ($booking_object) {
			echo '<span style="display:none;" class="car_rental_selected step_1">';
			echo '<input id="booking_id" name="booking_id" value="' . $booking_id . '" type="hidden" />';
			echo '<input class="button-primary" type="submit" name="update" value="' . esc_html__('Update Booking', 'bookyourtravel') . '"/>';
			echo '</span>';
		} else {
			echo '<span style="display:none;" class="car_rental_selected step_1">';
			echo '<input class="button-primary" type="submit" name="insert" value="' . esc_html__('Add Booking', 'bookyourtravel') . '"/>';
			echo '</span>';
		}
		echo '</p>';
		echo '</form>';
	}
}
