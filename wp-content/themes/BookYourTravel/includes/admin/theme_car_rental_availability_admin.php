<?php

/*************************** LOAD THE BASE CLASS *******************************
 *******************************************************************************
 * The WP_List_Table class isn't automatically available to plugins, so we need
 * to check if it's available and load it if necessary.
 */
if(!class_exists('WP_List_Table')) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class BookYourTravel_Car_Rental_Availability_Admin extends BookYourTravel_BaseSingleton {

	private $enable_car_rentals;

	protected function __construct() {

		global $bookyourtravel_theme_globals;

		$this->enable_car_rentals = $bookyourtravel_theme_globals->enable_car_rentals();

        // our parent class might
        // contain shared code in its constructor
        parent::__construct();
	}

    public function init() {

		if ($this->enable_car_rentals) {

			add_action( 'admin_menu' , array( $this, 'car_rental_availability_admin_page' ) );
			add_action( 'admin_head', array( $this, 'car_rental_availability_admin_head' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts_styles' ) );

			add_action( 'plugins_loaded' , array( $this, 'plugins_loaded' ) );									
		}
	}

	function plugins_loaded() {
		add_filter( 'set-screen-option', array( $this, 'car_rental_availability_set_screen_options' ), 10, 3);
	}		

	function enqueue_admin_scripts_styles() {

		$page = ( isset($_GET['page'] ) ) ? esc_attr( $_GET['page'] ) : false;
		if ('theme_car_rental_availability_admin.php' != $page) {
			return;
		}

		$date_format = get_option('date_format');
		global $bookyourtravel_car_rental_helper;

		$car_rental_id = 0;
		$availability_id = isset($_GET['edit']) ? absint($_GET['edit']) : 0;

		$availability_object = null;
		if ($availability_id > 0) {
			$availability_object = $bookyourtravel_car_rental_helper->get_car_rental_availability($availability_id);
		}

		if (isset($_POST['car_rental_id'])) {
			$car_rental_id = intval(wp_kses($_POST['car_rental_id'], array()));
		} else if ($availability_object != null) {
			$car_rental_id = $availability_object->car_rental_id;
		}

		wp_enqueue_script( 'bookyourtravel-admin-script', BookYourTravel_Theme_Utils::get_file_uri ('/js/admin/admin.js'), array('jquery'), BOOKYOURTRAVEL_VERSION);

		$start_date = null;
		if (isset($_POST['start_date'])) {
			$start_date =  sanitize_text_field($_POST['start_date']);
		} else if ($availability_object != null) {
			$start_date = $availability_object->start_date;
		}
		if (isset($start_date)) {
			$start_date = date_i18n( $date_format, strtotime( $start_date ) );
		}

		$end_date = null;
		if (isset($_POST['end_date'])) {
			$end_date =  sanitize_text_field($_POST['end_date']);
		} else if ($availability_object != null) {
			$end_date = $availability_object->end_date;
		}
		if (isset($end_date)) {
			$end_date = date_i18n( $date_format, strtotime( $end_date ) );
		}

		wp_localize_script( 'bookyourtravel-admin-script', 'BYTAdminCarRentals', array(
			'carRentalId' => $car_rental_id,
			'carRentalLocations' => array(),
			'carRentalPickUpLocationId' => 0,
			'carRentalDropOffLocationId' => 0,
			'carRentalDateFromValue' => $start_date,
			'carRentalDateToValue' => $end_date,
		) );

	}

	function car_rental_availability_admin_page() {
		$hook = add_submenu_page('edit.php?post_type=car_rental', esc_html__('Availability', 'bookyourtravel'), esc_html__('Availability', 'bookyourtravel'), 'edit_posts', basename(__FILE__), array($this, 'car_rental_availability_admin_display'));
		add_action( "load-$hook", array($this, 'car_rental_availability_add_screen_options'));
	}

	function car_rental_availability_set_screen_options($status, $option, $value) {
		if ( 'car_rental_availability_per_page' == $option )
			return $value;
	}

	function car_rental_availability_admin_head() {
		$page = ( isset($_GET['page'] ) ) ? esc_attr( $_GET['page'] ) : false;

		if ('theme_car_rental_availability_admin.php' != $page ) {
			return;
		}
	}


	function car_rental_availability_add_screen_options() {
		global $wp_car_rental_availability_table;
		$option = 'per_page';
		$args = array('label' => esc_html__('Availability', 'bookyourtravel'),'default' => 50,'option' => 'car_rental_availability_per_page');
		add_screen_option( $option, $args );
		$wp_car_rental_availability_table = new Car_Rental_Availability_Admin_List_Table();
	}

	function car_rental_availability_admin_display() {

		global $bookyourtravel_car_rental_helper;
		echo '<div class="wrap">';
		echo '<h2>' . esc_html__('Availability', 'bookyourtravel') . '</h2>';

		global $wp_car_rental_availability_table;
		$wp_car_rental_availability_table->handle_form_submit();

		if (isset($_GET['sub']) && $_GET['sub'] == 'manage') {

			$wp_car_rental_availability_table->render_entry_form();
		} else {

			$year = isset($_GET['year']) ? intval($_GET['year']) : intval(date("Y"));
			$month = isset($_GET['month']) ? intval($_GET['month']) : intval(date("m"));
			$current_day = ($year == intval(date("Y")) && $month  == intval(date("m"))) ? intval(date("j")) : 0;
			$car_rental_id = isset($_GET['car_rental_id']) ? intval($_GET['car_rental_id']) : 0;

			$car_rentals_filter = '<select id="car_rentals_filter" name="car_rentals_filter" onchange="carRentalFilterRedirect(this.value, ' . $year . ', ' . $month . ')">';
			$car_rentals_filter .= '<option value="">' . esc_html__('Filter by car rental', 'bookyourtravel') . '</option>';

			$author_id = null;
			if (!(current_user_can('editor') || current_user_can('administrator'))) {
				$author_id = get_current_user_id();
			}

			$car_rental_results = $bookyourtravel_car_rental_helper->list_car_rentals(0, -1, 'title', 'ASC', array(), false, array(), array(), array(), false, $author_id);
			if ( count($car_rental_results) > 0 && $car_rental_results['total'] > 0 ) {
				foreach ($car_rental_results['results'] as $car_rental_result) {
					global $post;
					$post = $car_rental_result;
					setup_postdata( $post );
					$car_rentals_filter .= '<option value="' . $post->ID . '" ' . ($post->ID == $car_rental_id ? 'selected' : '') . '>' . $post->post_title . '</option>';
				}
			}
			$car_rentals_filter .= '</select>';

			echo '<div class="alignleft bookyourtravel-admin-filter">';
			echo "<div class='alignleft actions'>" . esc_html__('Filter by car rental: ', 'bookyourtravel') . $car_rentals_filter . "</div>";
			echo "<div class='alignleft actions'><a class='button-secondary action alignleft' href='edit.php?post_type=car_rental&page=theme_car_rental_availability_admin.php'>";
			echo esc_html__('Reset filters', 'bookyourtravel');
			echo "</a></div>";
			echo '</div>';

			$wp_car_rental_availability_table->prepare_items();
			$wp_car_rental_availability_table->display();
		}
	}

}

global $car_rental_availability_admin;
$car_rental_availability_admin = BookYourTravel_Car_Rental_Availability_Admin::get_instance();

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
class Car_Rental_Availability_Admin_List_Table extends WP_List_Table {

	private $options;
	private $lastInsertedID;
	private $date_format;

	/**
	* Constructor, we override the parent to pass our own arguments.
	* We use the parent reference to set some default configs.
	*/
	function __construct() {
		global $status, $page;

		 parent::__construct( array(
			'singular'=> 'availability', // Singular label
			'plural' => 'availability', // plural label, also this well be one of the table css class
			'ajax'	=> false // We won't support Ajax for this table
		) );

		$this->date_format = get_option('date_format');
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
			<div class="alignleft actions">
				<a href="edit.php?post_type=car_rental&page=theme_car_rental_availability_admin.php&sub=manage" class="button-secondary action" ><?php esc_html_e('Add availability', 'bookyourtravel') ?></a>
			</div>
			<?php
		}
		if ( $which == "bottom" ) {
			//The code that goes after the table is there
			?>
			<div class="alignleft actions">
				<a href="edit.php?post_type=car_rental&page=theme_car_rental_availability_admin.php&sub=manage" class="button-secondary action" ><?php esc_html_e('Add availability', 'bookyourtravel') ?></a>
			</div>
			<?php
		}
	}

	function column_SeasonName($item) {
		return $item->season_name;
	}

	function column_CarRentalName($item) {
		return $item->car_rental_name;
	}

	function column_PricePerDay($item) {
		return $item->price_per_day;
	}

	function column_NumberOfCars($item) {
		return $item->number_of_cars;
	}

	function column_StartDate($item) {
		return date_i18n($this->date_format, strtotime($item->start_date));
	}

	function column_EndDate($item) {
		return date_i18n($this->date_format, strtotime($item->end_date));
	}

	function column_Action($item) {

		$action = "<form method='post' name='delete_availability_" . $item->Id . "' id='delete_availability_" . $item->Id . "' style='display:inline;'>
					<input type='hidden' name='delete_availability' id='delete_availability' value='" . $item->Id . "' />
					<a href='javascript: void(0);' onclick='confirmDelete(\"#delete_availability_" . $item->Id . "\", \"" . esc_html__('Are you sure?', 'bookyourtravel') . "\");'>" . esc_html__('Delete', 'bookyourtravel') . "</a>
				</form>";

		$action .= ' | 	<a href="edit.php?post_type=car_rental&page=theme_car_rental_availability_admin.php&sub=manage&edit=' . $item->Id . '">' . esc_html__('Edit', 'bookyourtravel') . '</a>';

		return $action;
	}

	/**
	 * Define the columns that are going to be used in the table
	 * @return array $columns, the array of columns to use with the table
	 */
	function get_columns() {
		return $columns= array(
			'Id'=>esc_html__('Id', 'bookyourtravel'),
			'SeasonName'=>esc_html__('Season Name', 'bookyourtravel'),
			'CarRentalName'=>esc_html__('Car Rental Name', 'bookyourtravel'),
			'StartDate'=>esc_html__('Start Date', 'bookyourtravel'),
			'EndDate'=>esc_html__('End Date', 'bookyourtravel'),
			'NumberOfCars'=>esc_html__('Number of Cars', 'bookyourtravel'),
			'PricePerDay'=>esc_html__('Price Per Day', 'bookyourtravel'),
			'Action'=>esc_html__('Action', 'bookyourtravel'),
		);
	}

	/**
	 * Decide which columns to activate the sorting functionality on
	 * @return array $sortable, the array of columns that can be sorted by the user
	 */
	public function get_sortable_columns() {
		$sortable_columns = array(
			'Id'=> array( 'Id', true ),
			'SeasonName'=> array( 'season_name', true ),
			'CarRentalName'=> array( 'car_rentals.post_title', true ),
			'StartDate'=> array( 'start_date', true ),
			'EndDate'=> array( 'end_date', true ),
			'NumberOfCars'=> array( 'number_of_cars', true ),
			'PricePerDay'=> array( 'price_per_day', true ),
		);
		return $sortable_columns;
	}

	/**
	 * Prepare the table with different parameters, pagination, columns and table elements
	 */
	function prepare_items() {

		global $wpdb, $_wp_column_headers, $bookyourtravel_car_rental_helper;

		$car_rental_id = isset($_GET['car_rental_id']) ? intval($_GET['car_rental_id']) : 0;

		$screen = get_current_screen();
		$user = get_current_user_id();
		$option = $screen->get_option('per_page', 'option');
		$per_page = get_user_meta($user, $option, true);
		if ( empty ( $per_page) || $per_page < 1 ) {
			$per_page = $screen->get_option( 'per_page', 'default' );
		}

		$search_term = '';
		if (!empty($_REQUEST['s'])) {
			$search_term = esc_sql(strtolower($_REQUEST['s']));
		}

		$columns = $this->get_columns();
		$hidden = get_hidden_columns($screen);
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array($columns, $hidden, $sortable);

		/* -- Ordering parameters -- */
		//Parameters that are going to be used to order the result
		$orderby = !empty($_GET["orderby"]) ? esc_sql($_GET["orderby"]) : 'Id';
		$order = !empty($_GET["order"]) ? esc_sql($_GET["order"]) : 'ASC';

		/* -- Pagination parameters -- */
		//How many to display per page?
		//Which page is this?
		$paged = !empty($_GET["paged"]) ? esc_sql($_GET["paged"]) : '';
		//Page Number
		if(empty($paged) || !is_numeric($paged) || $paged<=0 ) { $paged=1; }

		$author_id = null;
		if (!(current_user_can('editor') || current_user_can('administrator'))) {
			$author_id = get_current_user_id();
		}

		$car_rental_availability_results = $bookyourtravel_car_rental_helper->list_car_rental_availabilities($car_rental_id, $paged, $per_page, $orderby, $order, $author_id);

		//Number of elements in your table?
		$totalitems = $car_rental_availability_results['total']; //return the total number of affected rows

		//How many pages do we have in total?
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
		$this->items = $car_rental_availability_results['results'];
	}

	function handle_form_submit() {

		global $bookyourtravel_car_rental_helper;

		if (isset($_POST['insert']) && check_admin_referer('bookyourtravel_nonce')) {

			$car_rental_id = intval(wp_kses($_POST['car_rental_id'], array()));
			$car_rental_obj = new BookYourTravel_Car_Rental(intval($car_rental_id));
			$car_rental_id = $car_rental_obj->get_base_id();

			$season_name =  sanitize_text_field($_POST['season_name']);
			$number_of_cars = intval(sanitize_text_field($_POST['number_of_cars']));
			$start_date =  sanitize_text_field($_POST['start_date']);
			$end_date =  sanitize_text_field($_POST['end_date']);
			$price_per_day = floatval(wp_kses($_POST['price_per_day'], array()));

			$error = '';

			if (empty ($season_name)) {
				$error = esc_html__('You must enter a season name', 'bookyourtravel');
			} else if(empty($car_rental_id)) {
				$error = esc_html__('You must select a car rental', 'bookyourtravel');
			} else if($number_of_cars <= 0) {
				$error = esc_html__('You must specify a number of cars', 'bookyourtravel');
			} else if(empty($start_date)) {
				$error = esc_html__('You must select a start date', 'bookyourtravel');
			} else if(empty($end_date)) {
				$error = esc_html__('You must select an end date', 'bookyourtravel');
			} else if($price_per_day < 0) {
				$error = esc_html__('You must provide a valid price', 'bookyourtravel');
			}

			if (!empty($error)) {
				  echo '<div class="error" id="message" onclick="this.parentNode.removeChild(this)">';
				  echo '<p>' . $error . '</p>';
				  echo '</div>';
			} else {

				$bookyourtravel_car_rental_helper->create_car_rental_availability($season_name, $car_rental_id, $start_date, $end_date, $number_of_cars, $price_per_day);

				echo '<div class="updated" id="message" onclick="this.parentNode.removeChild(this)">';
				echo '<p>' . esc_html__('Successfully inserted new car rental availability!', 'bookyourtravel') . '</p>';
				echo '</div>';
			}
		} else if (isset($_POST['update']) && check_admin_referer('bookyourtravel_nonce')) {

			$car_rental_id = intval(wp_kses($_POST['car_rental_id'], array()));
			$car_rental_obj = new BookYourTravel_Car_Rental(intval($car_rental_id));
			$car_rental_id = $car_rental_obj->get_base_id();

			$season_name =  sanitize_text_field($_POST['season_name']);
			$number_of_cars = intval(sanitize_text_field($_POST['number_of_cars']));
			$start_date =  sanitize_text_field($_POST['start_date']);
			$end_date =  sanitize_text_field($_POST['end_date']);
			$price_per_day = floatval(wp_kses($_POST['price_per_day'], array()));

			$error = '';

			if (empty ($season_name)) {
				$error = esc_html__('You must enter a season name', 'bookyourtravel');
			} else if(empty($car_rental_id)) {
				$error = esc_html__('You must select an car rental', 'bookyourtravel');
			} else if($number_of_cars <= 0) {
				$error = esc_html__('You must specify a number of cars', 'bookyourtravel');
			} else if(empty($start_date)) {
				$error = esc_html__('You must select a start date', 'bookyourtravel');
			} else if(empty($end_date)) {
				$error = esc_html__('You must select an end date', 'bookyourtravel');
			} else if($price_per_day < 0) {
				$error = esc_html__('You must provide a valid price', 'bookyourtravel');
			}

			if (!empty($error)) {
				  echo '<div class="error" id="message" onclick="this.parentNode.removeChild(this)">';
				  echo '<p>' . $error . '</p>';
				  echo '</div>';
			} else {

				$availability_id = absint($_POST['availability_id']);

				$bookyourtravel_car_rental_helper->update_car_rental_availability($availability_id, $car_rental_id, $season_name, $start_date, $end_date, $number_of_cars, $price_per_day);

				echo '<div class="updated" id="message" onclick="this.parentNode.removeChild(this)">';
				echo '<p>' . esc_html__('Successfully updated car rental availability entry!', 'bookyourtravel') . '</p>';
				echo '</div>';

			}

		} else if (isset($_POST['delete_availability'])) {
			$availability_id = absint($_POST['delete_availability']);

			$bookyourtravel_car_rental_helper->delete_car_rental_availability($availability_id);

			echo '<div class="updated" id="message" onclick="this.parentNode.removeChild(this)">';
			echo '<p>' . esc_html__('Successfully deleted car rental availability entry!', 'bookyourtravel') . '</p>';
			echo '</div>';
		}

	}

	function render_entry_form() {

		global $bookyourtravel_car_rental_helper;
		$car_rental_id = 0;
		$availability_object = null;
		$car_rental_obj = null;

		$edit = isset($_GET['edit']) ? absint($_GET['edit']) : "";

		if (!empty($edit)) {
			$availability_object = $bookyourtravel_car_rental_helper->get_car_rental_availability($edit);
		}

		if (isset($_POST['car_rental_id'])) {
			$car_rental_id = intval(wp_kses($_POST['car_rental_id'], array()));
		} else if ($availability_object != null) {
			$car_rental_id = $availability_object->car_rental_id;
		}

		if ($car_rental_id) {
			$car_rental_obj = new BookYourTravel_Car_Rental(intval($car_rental_id));
			$car_rental_id = $car_rental_obj->get_base_id();
		}

		$car_rentals_select = '<select id="car_rental_id" name="car_rental_id" class="car_rentals_select">';
		$car_rentals_select .= '<option value="">' . esc_html__('Select car rental', 'bookyourtravel') . '</option>';

		$author_id = null;
		if (!(current_user_can('editor') || current_user_can('administrator'))) {
			$author_id = get_current_user_id();
		}

		$car_rental_results = $bookyourtravel_car_rental_helper->list_car_rentals(0, -1, 'title', 'ASC', array(), false, array(), array(), array(), false, $author_id);
		if ( count($car_rental_results) > 0 && $car_rental_results['total'] > 0 ) {
			foreach ($car_rental_results['results'] as $car_rental_result) {
				global $post;
				$post = $car_rental_result;
				setup_postdata( $post );
				$car_rentals_select .= '<option value="' . $post->ID . '" ' . ($post->ID == $car_rental_id ? 'selected' : '') . '>' . $post->post_title . '</option>';
			}
		}
		$car_rentals_select .= '</select>';

		$start_date = null;
		if (isset($_POST['start_date']))
			$start_date =  sanitize_text_field($_POST['start_date']);
		else if ($availability_object != null) {
			$start_date = $availability_object->start_date;
		}
		if (isset($start_date))
			$start_date = date_i18n( $this->date_format, strtotime( $start_date ) );

		$end_date = null;
		if (isset($_POST['end_date']))
			$end_date =  sanitize_text_field($_POST['end_date']);
		else if ($availability_object != null) {
			$end_date = $availability_object->end_date;
		}
		if (isset($end_date))
			$end_date = date_i18n( $this->date_format, strtotime( $end_date ) );

		$price_per_day = 0;
		if (isset($_POST['price_per_day']))
			$price_per_day = floatval(wp_kses($_POST['price_per_day'], array()));
		else if ($availability_object != null) {
			$price_per_day = $availability_object->price_per_day;
		}

		$number_of_cars = 0;
		if (isset($_POST['number_of_cars']))
			$number_of_cars = floatval(wp_kses($_POST['number_of_cars'], array()));
		else if ($availability_object != null) {
			$number_of_cars = $availability_object->number_of_cars;
		}

		$season_name = '';
		if (isset($_POST['season_name'])) {
			$season_name = sanitize_text_field($_POST['season_name']);
		} else if ($availability_object) {
			$season_name = stripslashes($availability_object->season_name);
		}

		if ($availability_object)
			echo '<h3>' . esc_html__('Update Car Rental Availability Entry', 'bookyourtravel') . '</h3>';
		else
			echo '<h3>' . esc_html__('Add Car Rental Availability Entry', 'bookyourtravel') . '</h3>';

		echo '<form id="car_rental_availability_entry_form" method="post" action="' . esc_url($_SERVER['REQUEST_URI']) . '" style="clear: both;">';
		echo wp_nonce_field('bookyourtravel_nonce');
		echo '<table cellpadding="3" class="form-table"><tbody>';

		echo '<tr>';
		echo '	<th scope="row" valign="top">' . esc_html__('Season name', 'bookyourtravel') . ' *</th>';
		echo '	<td><input type="text" name="season_name" id="season_name" value="' . $season_name . '" /></td>';
		echo '</tr>';

		echo '<tr class="tr-car-rental">';
		echo '	<th scope="row" valign="top">' . esc_html__('Select car rental', 'bookyourtravel') . ' *</th>';
		echo '	<td>';
		echo $car_rentals_select;
		echo '	<div class="loading" style="display: none;"></div>';
		echo '</td>';
		echo '</tr>';

		echo '<tr style="display:none" class="step_1">';
		echo '	<th scope="row" valign="top">' . esc_html__('Start date', 'bookyourtravel') . ' *</th>';
		echo '	<td>';
		echo '  	<input readonly class="datepicker" type="text" name="datepicker_from" id="datepicker_from" />';
		echo '		<input type="hidden" name="start_date" id="date_from" />';
		echo '	</td>';
		echo '</tr>';

		echo '<tr style="display:none" class="is_repeated step_1">';
		echo '	<th scope="row" valign="top">' . esc_html__('End date', 'bookyourtravel') . ' *</th>';
		echo '	<td>';
		echo '  	<input readonly class="datepicker" type="text" name="datepicker_to" id="datepicker_to" />';
		echo '		<input type="hidden" name="end_date" id="date_to" />';
		echo '	</td>';
		echo '</tr>';

		echo '<tr style="display:none" class="step_1">';
		echo '	<th scope="row" valign="top">' . esc_html__('Number of cars', 'bookyourtravel') . ' *</th>';
		echo '	<td><input type="number" min="1" name="number_of_cars" id="number_of_cars" value="' . esc_attr($number_of_cars) . '" /></td>';
		echo '</tr>';

		echo '<tr style="display:none" class="step_1">';
		echo '	<th scope="row" valign="top">' . esc_html__('Price per day', 'bookyourtravel') . ' *</th>';
		echo '	<td><input type="text" name="price_per_day" id="price_per_day" value="' . esc_attr($price_per_day) . '" /></td>';
		echo '</tr>';

		echo '</table>';
		echo '<p><small>' . __('Please note: fields marked with an * are required and must be filled in.', 'bookyourtravel') . '</small></p>';
		echo '<p>';
		echo '<a href="edit.php?post_type=car_rental&page=theme_car_rental_availability_admin.php" class="button-secondary">' . esc_html__('Cancel', 'bookyourtravel') . '</a>&nbsp;';
		if ($availability_object) {
			echo '<input id="availability_id" name="availability_id" value="' . esc_attr($edit) . '" type="hidden" />';
			echo '<input class="button-primary" type="submit" name="update" value="' . esc_html__('Update Car Rental Availability Entry', 'bookyourtravel') . '"/>';
		} else {
			echo '<input class="button-primary" type="submit" name="insert" value="' . esc_html__('Add Car Rental Availability Entry', 'bookyourtravel') . '"/>';
		}

		echo '</p>';

		echo '</form>';
	}

}
