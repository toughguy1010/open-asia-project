<?php

/*************************** LOAD THE BASE CLASS *******************************
 *******************************************************************************
 * The WP_List_Table class isn't automatically available to plugins, so we need
 * to check if it's available and load it if necessary.
 */
if(!class_exists('WP_List_Table')) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}


class BookYourTravel_Cruise_Schedule_Admin extends BookYourTravel_BaseSingleton {

	private $enable_cruises;

	protected function __construct() {

		global $bookyourtravel_theme_globals;

		$this->enable_cruises = $bookyourtravel_theme_globals->enable_cruises();

        // our parent class might
        // contain shared code in its constructor
        parent::__construct();
	}

    public function init() {

		if ($this->enable_cruises) {

			add_action( 'admin_menu' , array( $this, 'cruise_schedule_admin_page' ) );
			add_action( 'plugins_loaded' , array( $this, 'plugins_loaded' ) );	
			add_action( 'admin_head', array( $this, 'cruise_schedule_admin_head' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts_styles' ) );
		}
	}

	function plugins_loaded() {
		add_filter( 'set-screen-option', array( $this, 'cruise_schedule_set_screen_options' ), 10, 3);
	}		

	function enqueue_admin_scripts_styles() {

		$page = ( isset($_GET['page'] ) ) ? esc_attr( $_GET['page'] ) : false;
		if ('theme_cruise_schedule_admin.php' != $page) {
			return;
		}

		$date_format = get_option('date_format');

		wp_enqueue_script( 'bookyourtravel-admin-script', BookYourTravel_Theme_Utils::get_file_uri ('/js/admin/admin.js'), array('jquery'), BOOKYOURTRAVEL_VERSION);

		global $bookyourtravel_cruise_helper;

		$edit = isset($_GET['edit']) ? absint($_GET['edit']) : "";
		$schedule_object = null;
		$cruise_id = 0;
		$cabin_type_id = 0;
		$is_price_per_person = 0;
		$cruise_type_is_repeated = 0;

		if (!empty($edit)) {
			$schedule_object = $bookyourtravel_cruise_helper->get_cruise_schedule($edit);
		}

		if (isset($_POST['cruise_id'])) {
			$cruise_id = intval(wp_kses($_POST['cruise_id'], array()));
		} else if ($schedule_object != null) {
			$cruise_id = $schedule_object->cruise_id;
		}

		if ($cruise_id) {
			$cruise_obj = new BookYourTravel_Cruise(intval($cruise_id));
			$cruise_id = $cruise_obj->get_base_id();
			$cruise_type_is_repeated = $cruise_obj->get_type_is_repeated();
			$is_price_per_person = $cruise_obj->get_is_price_per_person();
		}

		if (isset($_POST['cabin_types_select'])) {
			$cabin_type_id = intval(wp_kses($_POST['cabin_types_select'], array()));
		} else if ($schedule_object) {
			$cabin_type_id = $schedule_object->cabin_type_id;
		}

		$start_date = null;
		if (isset($_POST['start_date'])) {
			$start_date =  sanitize_text_field($_POST['start_date']);
		} else if ($schedule_object != null) {
			$start_date = $schedule_object->start_date;
		}
		if (isset($start_date)) {
			$start_date = date_i18n( $date_format, strtotime( $start_date ) );
		}

		$end_date = null;
		if (isset($_POST['end_date'])) {
			$end_date =  sanitize_text_field($_POST['end_date']);
		} else if ($schedule_object != null) {
			$end_date = $schedule_object->end_date;
		}
		if (isset($end_date)) {
			$end_date = date_i18n( $date_format, strtotime( $end_date ) );
		}

		wp_localize_script( 'bookyourtravel-admin-script', 'BYTAdminCruises', array(
			'cruiseId' => $cruise_id,
			'cabinTypeId' => $cabin_type_id,
			'cruiseIsPricePerPerson' => $is_price_per_person,
			'cruiseCountChildrenStayFree' => 0,
			'cruiseTypeIsRepeated' => $cruise_type_is_repeated,
			'cruiseStartDateValue' => $start_date,
			'cruiseEndDateValue' => $end_date,
			'cruiseCabinTypes' => array()
	 	) );
	}

	function cruise_schedule_admin_page() {
		$hook = add_submenu_page('edit.php?post_type=cruise', esc_html__('Cruise schedule management', 'bookyourtravel'), esc_html__('Schedule', 'bookyourtravel'), 'edit_posts', basename(__FILE__), array($this, 'cruise_schedule_admin_display'));
		add_action( "load-$hook", array($this, 'cruise_schedule_add_screen_options'));
	}

	function cruise_schedule_set_screen_options($status, $option, $value) {
		if ( 'cruise_schedule_per_page' == $option ) {
			return $value;
		}
	}

	function cruise_schedule_admin_head() {
		$page = ( isset($_GET['page'] ) ) ? esc_attr( $_GET['page'] ) : false;
		if( 'theme_cruise_schedule_admin.php' != $page ) {
			return;
		}
	}

	function cruise_schedule_add_screen_options() {
		global $wp_cruise_schedule_table;
		$option = 'per_page';
		$args = array('label' => esc_html__('Schedule', 'bookyourtravel'),'default' => 50,'option' => 'cruise_schedule_per_page');
		add_screen_option( $option, $args );
		$wp_cruise_schedule_table = new Cruise_Schedule_Admin_List_Table();
	}

	function cruise_schedule_admin_display() {

		global $bookyourtravel_cruise_helper, $bookyourtravel_cabin_type_helper;

		echo '<div class="wrap">';
		echo '<h2>' . esc_html__('Cruise schedule', 'bookyourtravel') . '</h2>';

		global $wp_cruise_schedule_table;
		$wp_cruise_schedule_table->handle_form_submit();

		if (isset($_GET['sub']) && $_GET['sub'] == 'manage') {

			$wp_cruise_schedule_table->render_entry_form();

		} else {
			$year = isset($_GET['year']) ? intval($_GET['year']) : intval(date("Y"));
			$month = isset($_GET['month']) ? intval($_GET['month']) : intval(date("m"));
			$current_day = ($year == intval(date("Y")) && $month  == intval(date("m"))) ? intval(date("j")) : 0;
			$cruise_id = isset($_GET['cruise_id']) ? intval($_GET['cruise_id']) : 0;
			$cabin_type_id = isset($_GET['cabin_type_id']) ? intval($_GET['cabin_type_id']) : 0;

			$cruises_filter = '<select id="cruises_filter" name="cruises_filter" onchange="cruiseFilterRedirect(this.value,' . $cabin_type_id . ',' . $year . ',' . $month . ')">';
			$cruises_filter .= '<option value="">' . esc_html__('Filter by cruise', 'bookyourtravel') . '</option>';

			$author_id = null;
			if (!(current_user_can('editor') || current_user_can('administrator'))) {
				$author_id = get_current_user_id();
			}

			$cruise_results = $bookyourtravel_cruise_helper->list_cruises(0, -1, 'title', 'ASC', array(), false, array(), array(), array(), array(), array(), false, $author_id);

			if ( count($cruise_results) > 0 && $cruise_results['total'] > 0 ) {
				foreach ($cruise_results['results'] as $cruise_result) {
					global $post;
					$post = $cruise_result;
					setup_postdata( $post );
					$cruises_filter .= '<option value="' . $post->ID . '" ' . ($post->ID == $cruise_id ? 'selected' : '') . '>' . $post->post_title . '</option>';
				}
			}
			$cruises_filter .= '</select>';

			wp_reset_postdata();

			echo '<div class="alignleft bookyourtravel-admin-filter">';
			echo '<div class="alignleft actions">' . esc_html__('Filter by cruise: ', 'bookyourtravel') . $cruises_filter . '</div>';
			echo "<div class='alignleft actions'><a class='button-secondary action alignleft' href='edit.php?post_type=cruise&page=theme_cruise_schedule_admin.php'>";
			echo esc_html__('Reset filters', 'bookyourtravel');
			echo "</a></div>";
			echo '</div>';

			$wp_cruise_schedule_table->prepare_items();
			$wp_cruise_schedule_table->display();
		}
	}
}

global $cruise_schedule_admin;
$cruise_schedule_admin = BookYourTravel_Cruise_Schedule_Admin::get_instance();

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
class Cruise_Schedule_Admin_List_Table extends WP_List_Table {

	private $options;
	private $lastInsertedID;
	private $date_format;

	/**
	* Constructor, we override the parent to pass our own arguments.
	* We use the parent reference to set some default configs.
	*/
	function __construct() {
		global $status, $page;

		$this->date_format = get_option('date_format');

		 parent::__construct( array(
			'singular'=> 'schedule', // Singular label
			'plural' => 'schedule', // plural label, also this well be one of the table css class
			'ajax'	=> false // We won't support Ajax for this table
		) );

	}

	function column_default( $item, $column_name ) {
		return $item->$column_name;
	}

	function column_SeasonName($item) {
		return $item->season_name;
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
			$year = isset($_GET['year']) ? intval($_GET['year']) : 0;
			$month = isset($_GET['month']) ? intval($_GET['month']) : 0;
			$day = isset($_GET['day']) ? intval($_GET['day']) : 1;
			$cruise_id = isset($_GET['cruise_id']) ? intval($_GET['cruise_id']) : 0;

			$cruise_title = '';
			if ($cruise_id > 0)
				$cruise_title = get_the_title($cruise_id);
			?>
			<div class="alignleft actions bookyourtravel-admin-top">
				<a href="edit.php?post_type=cruise&page=theme_cruise_schedule_admin.php&sub=manage" class="button-secondary action" ><?php esc_html_e('Add schedule', 'bookyourtravel') ?></a>
			</div>
			<?php
		}
		if ( $which == "bottom" ) {
			//The code that goes after the table is there
			?>
			<div class="alignleft actions bookyourtravel-admin-top">
				<a href="edit.php?post_type=cruise&page=theme_cruise_schedule_admin.php&sub=manage" class="button-secondary action" ><?php esc_html_e('Add schedule', 'bookyourtravel') ?></a>
			</div>
			<?php
		}
	}

	function column_CruiseName($item) {
		return $item->cruise_name;
	}

	function column_CabinType($item) {
		return $item->cabin_type;
	}

	function column_CruiseType($item) {
		$cruise_obj = new BookYourTravel_Cruise($item->cruise_id);
		return $cruise_obj->get_type_name();
	}

	function column_Price($item) {
		if ($item->cruise_is_price_per_person)
			return $item->price . ' / ' . $item->price_child;
		else
			return $item->price;
	}

	function column_CabinCount($item) {
		return $item->cabin_count;
	}

	function column_StartDate($item) {
		return date_i18n($this->date_format, strtotime($item->start_date));
	}

	function column_EndDate($item) {
		if ($item->end_date != null) {
			$year = date('Y', strtotime($item->end_date));
			if ($year > 1970) {
				return date_i18n($this->date_format, strtotime($item->end_date));
			}
		}
		return esc_html__('N/A', 'bookyourtravel');
	}

	function column_Action($item) {
		$cruise_id = isset($_GET['cruise_id']) ? intval($_GET['cruise_id']) : 0;
		$cabin_type_id = isset($_GET['cabin_type_id']) ? intval($_GET['cabin_type_id']) : 0;

		$url_part = '';
		if ($cruise_id > 0)
			$url_part .= "&cruise_id=$cruise_id";
		if ($cabin_type_id > 0)
			$url_part .= "&cabin_type_id=$cabin_type_id";

		$action = "<form method='post' name='delete_schedule_" . $item->Id . "' id='delete_schedule_" . $item->Id . "' style='display:inline;'>
					<input type='hidden' name='delete_schedule' id='delete_schedule' value='" . $item->Id . "' />
					<a href='javascript: void(0);' onclick='confirmDelete(\"#delete_schedule_" . $item->Id . "\", \"" . esc_html__('Are you sure?', 'bookyourtravel') . "\");'>" . esc_html__('Delete', 'bookyourtravel') . "</a>
				</form>";

		$action .= ' | 	<a href="edit.php?post_type=cruise&page=theme_cruise_schedule_admin.php&sub=manage&edit=' . $item->Id . $url_part . '">' . esc_html__('Edit', 'bookyourtravel') . '</a>';
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
			'StartDate'=>esc_html__('Start Date', 'bookyourtravel'),
			'EndDate'=>esc_html__('End Date', 'bookyourtravel'),
			'CruiseName'=>esc_html__('Cruise Name', 'bookyourtravel'),
			'CabinType'=>esc_html__('Cabin Type', 'bookyourtravel'),
			'CruiseType'=>esc_html__('Cruise Type', 'bookyourtravel'),
			'Price'=>esc_html__('Price', 'bookyourtravel'),
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
			'CruiseName'=> array( 'cruises.post_title', true ),
			'CabinType'=> array( 'cabin_types.post_title', true ),
			'StartDate'=> array( 'start_date', true ),
			'CabinCount'=> array( 'cabin_count', true ),
			'Price'=> array( 'price', true ),
		);
		return $sortable_columns;
	}

	/**
	 * Prepare the table with different parameters, pagination, columns and table elements
	 */
	function prepare_items() {

		global $bookyourtravel_cruise_helper, $bookyourtravel_cabin_type_helper;
		global $_wp_column_headers;

		$year = isset($_GET['year']) ? intval($_GET['year']) : 0;
		$month = isset($_GET['month']) ? intval($_GET['month']) : 0;
		$day = isset($_GET['day']) ? intval($_GET['day']) : 0;

		$cruise_id = isset($_GET['cruise_id']) ? intval($_GET['cruise_id']) : 0;
		$cruise_id = BookYourTravel_Theme_Utils::get_default_language_post_id($cruise_id, 'cruise');

		$cabin_type_id = isset($_GET['cabin_type_id']) ? intval($_GET['cabin_type_id']) : 0;
		$cabin_type_id = BookYourTravel_Theme_Utils::get_default_language_post_id($cabin_type_id, 'cabin_type');

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
		$paged = !empty($_GET["paged"]) ? intval(wp_kses($_GET["paged"], array())) : 1;
		//Page Number
		if(empty($paged) || !is_numeric($paged) || $paged<=0 ) { $paged=1; }

		$author_id = null;
		if (!(current_user_can('editor') || current_user_can('administrator'))) {
			$author_id = get_current_user_id();
		}

		$cruise_schedule_results = $bookyourtravel_cruise_helper->list_cruise_schedules($paged, $per_page, $orderby, $order, $day, $month, $year, $cruise_id, $cabin_type_id, $search_term, $author_id);

		//Number of elements in your table?
		$totalitems = $cruise_schedule_results['total']; //return the total number of affected rows

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
		$this->items = $cruise_schedule_results['results'];
	}

	function handle_form_submit() {

		global $bookyourtravel_cruise_helper, $bookyourtravel_cabin_type_helper;

		if (isset($_POST['insert']) && check_admin_referer('bookyourtravel_nonce')) {

			$cruise_id = intval(wp_kses($_POST['cruise_id'], array()));
			$cruise_obj = new BookYourTravel_Cruise(intval($cruise_id));
			$cruise_id = $cruise_obj->get_base_id();

			$season_name =  sanitize_text_field($_POST['season_name']);

			$cabin_type_id = isset($_POST['cabin_types_select']) ? intval(wp_kses($_POST['cabin_types_select'], array())) : 0;
			$cabin_type_obj = new BookYourTravel_Cabin_Type(intval($cabin_type_id));
			$cabin_type_id = $cabin_type_obj->get_base_id();

			$cabin_count = isset($_POST['cabin_count']) ? intval(wp_kses($_POST['cabin_count'], array())) : 1;
			$is_price_per_person = $cruise_obj->get_is_price_per_person();
			$cruise_type_is_repeated = $cruise_obj->get_type_is_repeated();

			$start_date =  sanitize_text_field($_POST['start_date']);
			$price_regular = floatval(wp_kses($_POST['price_regular'], array()));
			$price_child = isset($_POST['price_child']) ? floatval(wp_kses($_POST['price_child'], array())) : 0;
			$end_date = isset($_POST['end_date']) ? sanitize_text_field($_POST['end_date']) : null;

			$error = '';

			if (empty ($season_name)) {
				$error = esc_html__('You must enter a season name', 'bookyourtravel');
			} else if(empty($cruise_id)) {
				$error = esc_html__('You must select an cruise', 'bookyourtravel');
			} else if($cabin_type_id <= 0) {
				$error = esc_html__('You must select a cabin type', 'bookyourtravel');
			} else if (empty($cabin_count) || $cabin_count === 0) {
				$error = esc_html__('You must provide a valid cabin count', 'bookyourtravel');
			} else if(empty($start_date)) {
				$error = esc_html__('You must select a schedule date', 'bookyourtravel');
			} else if($price_regular < 0) {
				$error = esc_html__('You must provide a valid regular price', 'bookyourtravel');
			}

			if (!empty($error)) {
				  echo '<div class="error" id="message" onclick="this.parentNode.removeChild(this)">';
				  echo '<p>' . $error . '</p>';
				  echo '</div>';
			} else {

				$bookyourtravel_cruise_helper->create_cruise_schedule($season_name, $cruise_id, $cabin_type_id, $cabin_count, $start_date, $price_regular, $price_child, $end_date);

				echo '<div class="updated" id="message" onclick="this.parentNode.removeChild(this)">';
				echo '<p>' . esc_html__('Successfully inserted new cruise schedule entry!', 'bookyourtravel') . '</p>';
				echo '</div>';

			}
		} else if (isset($_POST['update']) && check_admin_referer('bookyourtravel_nonce')) {

			$cruise_id = intval(wp_kses($_POST['cruise_id'], array()));
			$cruise_obj = new BookYourTravel_Cruise(intval($cruise_id));
			$cruise_id = $cruise_obj->get_base_id();

			$cabin_type_id = isset($_POST['cabin_types_select']) ? intval(wp_kses($_POST['cabin_types_select'], array())) : 0;
			$cabin_type_obj = new BookYourTravel_Cabin_Type(intval($cabin_type_id));
			$cabin_type_id = $cabin_type_obj->get_base_id();

			$season_name =  sanitize_text_field($_POST['season_name']);

			$is_price_per_person = $cruise_obj->get_is_price_per_person();
			$cabin_count = isset($_POST['cabin_count']) ? intval(wp_kses($_POST['cabin_count'], array())) : 1;
			$cruise_type_is_repeated = $cruise_obj->get_type_is_repeated();

			$start_date =  sanitize_text_field($_POST['start_date']);
			$price_regular = floatval(wp_kses($_POST['price_regular'], array()));
			$price_child = isset($_POST['price_child']) ? floatval(wp_kses($_POST['price_child'], array())) : 0;
			$end_date = isset($_POST['end_date']) ? wp_kses($_POST['end_date'], array()) : null;

			$error = '';

			if (empty ($season_name)) {
				$error = esc_html__('You must enter a season name', 'bookyourtravel');
			} else if(empty($cruise_id)) {
				$error = esc_html__('You must select an cruise', 'bookyourtravel');
			} else if($cabin_type_id <= 0) {
				$error = esc_html__('You must select a cabin type', 'bookyourtravel');
			} else if (empty($cabin_count) || $cabin_count === 0) {
				$error = esc_html__('You must provide a valid cabin count', 'bookyourtravel');
			} else if(empty($start_date)) {
				$error = esc_html__('You must select a schedule date', 'bookyourtravel');
			} else if($price_regular < 0) {
				$error = esc_html__('You must provide a valid regular price', 'bookyourtravel');
			}

			if (!empty($error)) {
				  echo '<div class="error" id="message" onclick="this.parentNode.removeChild(this)">';
				  echo '<p>' . $error . '</p>';
				  echo '</div>';
			} else {

				$schedule_id = absint($_POST['schedule_id']);

				$bookyourtravel_cruise_helper->update_cruise_schedule($schedule_id, $season_name, $cruise_id, $cabin_type_id, $cabin_count, $start_date, $price_regular, $price_child, $end_date);

				echo '<div class="updated" id="message" onclick="this.parentNode.removeChild(this)">';
				echo '<p>' . esc_html__('Successfully updated cruise schedule entry!', 'bookyourtravel') . '</p>';
				echo '</div>';

			}

		} else if (isset($_POST['delete_schedule'])) {
			$schedule_id = absint($_POST['delete_schedule']);

			$bookyourtravel_cruise_helper->delete_cruise_schedule($schedule_id);

			echo '<div class="updated" id="message" onclick="this.parentNode.removeChild(this)">';
			echo '<p>' . esc_html__('Successfully deleted cruise schedule entry!', 'bookyourtravel') . '</p>';
			echo '</div>';
		}

	}

	function render_entry_form() {

		global $bookyourtravel_cruise_helper;

		$cruise_id = 0;
		$cabin_type_id = 0;
		$schedule_object = null;
		$cruise_obj = null;
		$cabin_type_obj = null;
		$is_price_per_person = 0;
		$cruise_type_is_repeated = 0; // on-off cruise by default

		$edit = isset($_GET['edit']) ? absint($_GET['edit']) : "";

		if (!empty($edit)) {
			$schedule_object = $bookyourtravel_cruise_helper->get_cruise_schedule($edit);
		}

		if (isset($_POST['cruise_id'])) {
			$cruise_id = intval(wp_kses($_POST['cruise_id'], array()));
		} else if ($schedule_object != null) {
			$cruise_id = $schedule_object->cruise_id;
		}

		if ($cruise_id) {
			$cruise_obj = new BookYourTravel_Cruise(intval($cruise_id));
			$cruise_id = $cruise_obj->get_base_id();
			$cruise_type_is_repeated = $cruise_obj->get_type_is_repeated();
			$is_price_per_person = $cruise_obj->get_is_price_per_person();
		}

		if (isset($_POST['cabin_types_select'])) {
			$cabin_type_id = intval(wp_kses($_POST['cabin_types_select'], array()));
		} else if ($schedule_object) {
			$cabin_type_id = $schedule_object->cabin_type_id;
		}

		if (!empty($cabin_type_id)) {
			$cabin_type_id = BookYourTravel_Theme_Utils::get_default_language_post_id($cabin_type_id, 'cabin_type');
		}

		$cruises_select = '<select id="cruise_id" name="cruise_id" class="cruises_select">';
		$cruises_select .= '<option value="">' . esc_html__('Select cruise', 'bookyourtravel') . '</option>';

		$author_id = null;
		if (!(current_user_can('editor') || current_user_can('administrator'))) {
			$author_id = get_current_user_id();
		}

		$cruise_results = $bookyourtravel_cruise_helper->list_cruises(0, -1, 'title', 'ASC', array(),false, array(), array(), array(), array(), array(), false, $author_id);
		if ( count($cruise_results) > 0 && $cruise_results['total'] > 0 ) {
			foreach ($cruise_results['results'] as $cruise_result) {
				global $post;
				$post = $cruise_result;
				setup_postdata( $post );
				$cruises_select .= '<option value="' . $post->ID . '" ' . ($post->ID == $cruise_id ? 'selected' : '') . '>' . $post->post_title . '</option>';
			}
		}
		$cruises_select .= '</select>';

		$cabin_types_select = '<select class="normal" id="cabin_types_select" name="cabin_types_select">';
		$cabin_types_select .= '<option value="">' . esc_html__('Select cabin type', 'bookyourtravel') . '</option>';

		if ($cruise_obj) {
			$cabin_type_ids = $cruise_obj->get_cabin_types();
			if ($cabin_type_ids && count($cabin_type_ids) > 0) {
				for ( $i = 0; $i < count($cabin_type_ids); $i++ ) {
					$temp_id = $cabin_type_ids[$i];
					$cabin_type_obj = new BookYourTravel_Cabin_Type(intval($temp_id));
					$cabin_types_select .= '<option value="' . $temp_id . '" ' . ($temp_id == $cabin_type_id ? 'selected' : '') . '>' . $cabin_type_obj->get_title() . '</option>';
				}
			}
		}

		$cabin_types_select .= '</select>';

		$cabin_count = 1;
		if (isset($_POST['cabin_count'])) {
			$cabin_count = intval(wp_kses($_POST['cabin_count'], array()));
		} else if ($schedule_object && isset($schedule_object->cabin_count)) {
			$cabin_count = $schedule_object->cabin_count;
		}
		if ($cabin_count == 0)
			$cabin_count = 1;

		$price_regular = 0;
		if (isset($_POST['price_regular']))
			$price_regular = floatval(wp_kses($_POST['price_regular'], array()));
		else if ($schedule_object != null) {
			$price_regular = $schedule_object->price;
		}

		$price_child = 0;
		if ($is_price_per_person) {
			if (isset($_POST['price_child']))
				$price_child = floatval(wp_kses($_POST['price_child'], array()));
			else if ($schedule_object != null) {
				$price_child = $schedule_object->price_child;
			}
		}

		$start_date = null;
		if (isset($_POST['start_date']))
			$start_date =  sanitize_text_field($_POST['start_date']);
		else if ($schedule_object != null) {
			$start_date = $schedule_object->start_date;
		}
		if (isset($start_date))
			$start_date = date_i18n( $this->date_format, strtotime( $start_date ) );

		$end_date = null;
		if (isset($_POST['end_date']))
			$end_date =  sanitize_text_field($_POST['end_date']);
		else if ($schedule_object != null) {
			$end_date = $schedule_object->end_date;
		}
		if (isset($end_date))
			$end_date = date_i18n( $this->date_format, strtotime( $end_date ) );

		$season_name = '';
		if (isset($_POST['season_name'])) {
			$season_name = sanitize_text_field($_POST['season_name']);
		} else if ($schedule_object) {
			$season_name = stripslashes($schedule_object->season_name);
		}

		if ($schedule_object)
			echo '<h3>' . esc_html__('Update Cruise Schedule Entry', 'bookyourtravel') . '</h3>';
		else
			echo '<h3>' . esc_html__('Add Cruise Schedule Entry', 'bookyourtravel') . '</h3>';

		echo '<form id="cruise_schedule_entry_form" method="post" action="' . esc_url($_SERVER['REQUEST_URI']) . '" style="clear: both;">';
		echo wp_nonce_field('bookyourtravel_nonce');

		echo '<table cellpadding="3" class="form-table"><tbody>';

		echo '<tr>';
		echo '	<th scope="row" valign="top">' . esc_html__('Season name', 'bookyourtravel') . '</th>';
		echo '	<td><input type="text" name="season_name" id="season_name" value="' . $season_name . '" /></td>';
		echo '</tr>';

		echo '<tr class="tr-cruise">';
		echo '	<th scope="row" valign="top">' . esc_html__('Select cruise', 'bookyourtravel') . ' *</th>';
		echo '	<td>';
		echo $cruises_select;
		echo '	<div class="loading" style="display: none;"></div>';
		echo '	</td>';
		echo '</tr>';

		echo '<tr class="step_1" style="display:none;">';
		echo '	<th scope="row" valign="top">' . esc_html__('Cruise type', 'bookyourtravel') . '</th>';
		echo '  <td>';
		$cruise_type_repeated_style = '';
		$cruise_type_not_repeated_style = '';
		if ($cruise_type_is_repeated) {
			$cruise_type_repeated_style = '';
			$cruise_type_not_repeated_style = 'display:none';
		} else {
			$cruise_type_repeated_style = 'display:none';
			$cruise_type_not_repeated_style = '';
		}
		echo '		<span class="is_repeated" style="' . $cruise_type_repeated_style . '">' . esc_html__('Is repeated', 'bookyourtravel') . '</span>';
		echo '		<span class="is_not_repeated" style="' . $cruise_type_not_repeated_style . '">' . esc_html__('Is not repeated', 'bookyourtravel') . '</span>';
		echo '<p><small>' . __('Please note: Non repeated cruise types do not have an end date.', 'bookyourtravel') . '</small></p>';
		echo '	</td>';
		echo '</tr>';

		echo '<tr style="display:none" class="step_1 tr-cabin">';
		echo '	<th scope="row" valign="top">' . esc_html__('Select cabin type', 'bookyourtravel') . ' *</th>';
		echo '	<td>';
		echo $cabin_types_select;
		echo '	<div class="loading" style="display: none;"></div>';
		echo '	</td>';
		echo '</tr>';

		echo '<tr style="display:none" class="step_2">';
		echo '	<th scope="row" valign="top">' . esc_html__('Number of cabins available', 'bookyourtravel') . '</th>';
		echo '	<td>';
		echo '  <select id="cabin_count" name="cabin_count">';
		for ($i=1;$i<101;$i++) {
			echo '  <option value="' . $i . '" ' . ($cabin_count == $i ? "selected" : "") . '>' . $i . '</option>';
		}
		echo '  </select>';
		echo '	</td>';
		echo '</tr>';

		echo '<tr style="display:none" class="step_2">';
		echo '	<th scope="row" valign="top">' . esc_html__('Start date', 'bookyourtravel') . ' *</th>';
		echo '	<td>';
		echo '  	<input readonly class="datepicker" type="text" name="datepicker_start_date" id="datepicker_start_date" />';
		echo '		<input type="hidden" name="start_date" id="start_date" />';
		echo '	</td>';
		echo '</tr>';

		echo '<tr style="display:none" class="step_2 is_repeated" ' . ($cruise_type_is_repeated ? '' : 'style="display:none"') . '>';
		echo '	<th scope="row" valign="top">' . esc_html__('End date', 'bookyourtravel') . '</th>';
		echo '	<td>';
		echo '  	<input readonly class="datepicker" type="text" name="datepicker_end_date" id="datepicker_end_date" />';
		echo '		<input type="hidden" name="end_date" id="end_date" />';
		echo '	</td>';
		echo '</tr>';

		echo '<tr style="display:none" class="step_2">';
		echo '	<th scope="row" valign="top">' . esc_html__('Price', 'bookyourtravel') . ' <span class="per_person" ' . ($is_price_per_person ? '' : 'style="display:none"') . '>' . esc_html__('per adult', 'bookyourtravel') . '</span> <span class="per_person" ' . (!$is_price_per_person ? '' : 'style="display:none"') . '>' . esc_html__('per cabin', 'bookyourtravel') . '</span> *</th>';
		echo '	<td><input type="text" name="price_regular" id="price_regular" value="' . $price_regular . '" /></td>';
		echo '</tr>';

		echo '<tr style="display:none" class="step_2 per_person" ' . ($is_price_per_person ? '' : 'style="display:none"') . '>';
		echo '	<th scope="row" valign="top">' . esc_html__('Price per child', 'bookyourtravel') . ' *</th>';
		echo '	<td><input type="text" name="price_child" id="price_child" value="' . $price_child . '" /></td>';
		echo '</tr>';

		echo '</table>';
		echo '<p><small>' . __('Please note: fields marked with an * are required and must be filled in.', 'bookyourtravel') . '</small></p>';
		echo '<p>';
		echo '<a href="edit.php?post_type=cruise&page=theme_cruise_schedule_admin.php" class="button-secondary">' . esc_html__('Cancel', 'bookyourtravel') . '</a>&nbsp;';
		if ($schedule_object) {
			echo '<input id="schedule_id" name="schedule_id" value="' . $edit . '" type="hidden" />';
			echo '<input class="button-primary" type="submit" name="update" value="' . esc_html__('Update Cruise Schedule Entry', 'bookyourtravel') . '"/>';
		} else {
			echo '<input class="button-primary" type="submit" name="insert" value="' . esc_html__('Add Cruise Schedule Entry', 'bookyourtravel') . '"/>';
		}

		echo '</p>';

		echo '</form>';
	}

}
