<?php
/**
 * BookYourTravel_Theme_Ical class
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

require_once BookYourTravel_Theme_Utils::get_file_path('/includes/icalendar/zapcallib.php');

class BookYourTravel_Theme_Ical extends BookYourTravel_BaseSingleton
{
	private $price_decimal_places;
	private $default_currency_symbol;
	private $show_currency_symbol_after;

    protected function __construct()
    {
		global $bookyourtravel_theme_globals;

		$this->price_decimal_places = $bookyourtravel_theme_globals->get_price_decimal_places();
		$this->default_currency_symbol = $bookyourtravel_theme_globals->get_default_currency_symbol();
        $this->show_currency_symbol_after = $bookyourtravel_theme_globals->show_currency_symbol_after();

        // our parent class might contain shared code in its constructor
        parent::__construct();
    }

    public function init()
    {
        add_action('wp_loaded', array($this, 'admin_init'), 100);
    }
    
    public function admin_init() {
        if (isset($_REQUEST['ics']) && $_REQUEST['ics'] == '1') {
            $type = isset($_REQUEST['type']) ? $_REQUEST['type'] : 'accommodation_vacancies';
           
            if ($type == 'accommodation_vacancies') {
                $this->export_accommodation_vacancies();
            } else if ($type == 'accommodation_bookings') {
                $this->export_accommodation_bookings();
            } else if ($type == 'car_rental_availability') {
                $this->export_car_rental_availability();
            } else if ($type == 'car_rental_bookings') {
                $this->export_car_rental_bookings();
            } else if ($type == 'tour_schedules') {
                $this->export_tour_schedules();
            } else if ($type == 'tour_bookings') {
                $this->export_tour_bookings();
            } else if ($type == 'cruise_schedules') {
                $this->export_cruise_schedules();
            } else if ($type == 'cruise_bookings') {
                $this->export_cruise_bookings();
            }

            die();
        }
    }

    private function export_cruise_schedules() {
        global $bookyourtravel_cruise_helper;
        
        $cruise_id = 0;
        $cabin_type_id = 0;

        if (isset($_REQUEST['cruise_id'])) {
            $cruise_id = absint( $_GET['cruise_id'] );
        }

        if (isset($_REQUEST['cabin_type_id'])) {
            $cabin_type_id = absint( $_GET['cabin_type_id'] );
        }

        $icalobj = new ZCiCal();

        if ($cruise_id > 0 && $cabin_type_id > 0) {
            $cruise_obj = new BookYourTravel_Cruise($cruise_id);
            $cabin_type_obj = new BookYourTravel_Cabin_Type($cabin_type_id);
            
            if ($cruise_obj && $cabin_type_obj) {
                $cruise_title = $cruise_obj->get_title();
                $summary_title = "{$cruise_title}";

                $cabin_type_title = $cabin_type_obj->get_title();
                $summary_title .= ":{$cabin_type_title}";

                $cruise_type_is_repeated = $cruise_obj->get_type_is_repeated();
                $cruise_type_day_of_week_indexes = $cruise_obj->get_type_day_of_week_indexes();

                $cruise_schedules = $bookyourtravel_cruise_helper->list_cruise_schedules(null, 0, 'Id', 'ASC', 0, 0, 0, $cruise_id, $cabin_type_id);

                if (count($cruise_schedules) > 0 && $cruise_schedules['total'] > 0) {
                    foreach ($cruise_schedules['results'] as $cruise_schedule) {
                        $start_date = date_i18n('Y-m-d', strtotime($cruise_schedule->start_date));
                        $end_date = '';

                        $price_per_day = $this->format_price($cruise_schedule->price);
                        $season_name = $cruise_schedule->season_name;
                        $cabin_count = $cruise_schedule->cabin_count;

                        if ($cruise_type_is_repeated > 1) {
                            $end_date = date_i18n('Y-m-d', strtotime($cruise_schedule->end_date));

                            if ($start_date < $end_date) {
                                $loop_date = $start_date;

                                while ($loop_date < $end_date) {
                                    $s_date = $loop_date;
                                    $e_date = date_i18n('Y-m-d', strtotime($loop_date) + 60*60);
                                    $create_an_event = false;

                                    if ($cruise_type_is_repeated == 2) {
                                        if (date_i18n('N', strtotime($loop_date)) - 1 < 6) {
                                            // weekday!
                                             $create_an_event = true;
                                        }
                                        // echo esc_html__('This cruise is repeated every weekday (working day).', 'bookyourtravel');
                                    } elseif ($cruise_type_is_repeated == 3 && count($cruise_type_day_of_week_indexes) > 0) {
                                        if (date_i18n('N', strtotime($loop_date)) - 1 == $cruise_type_day_of_week_indexes[0]) {
                                            // on correct day of week
                                            $create_an_event = true;                                            
                                        }
                                        // echo sprintf(esc_html__('This cruise is repeated every week on a %s.', 'bookyourtravel'), $cruise_obj->get_type_day_of_week_day());
                                    } elseif ($cruise_type_is_repeated == 4) {
                                        $index_matches = 0;
                                        foreach ($cruise_type_day_of_week_indexes as $day_of_week_index) {
                                            if (date_i18n('N', strtotime($loop_date)) - 1 == $day_of_week_index) {
                                                $index_matches++;
                                            }
                                        }

                                        if ($index_matches > 0) {
                                            $create_an_event = true;
                                        }
                                        // echo esc_html__('This cruise is repeated every week on multiple days.', 'bookyourtravel');
                                    }

                                    if ($create_an_event) {
                                        $description = sprintf('START: %s\nEND: %s\nPRICE: %s\nCABINS: %d\n', $s_date, $e_date, $price_per_day, $cabin_count);
                                        $uid = md5("{$s_date}/{$e_date}/{$cruise_id}/{$cabin_type_id}/{$season_name}") . '@' . $this->get_website_domain_name();
                
                                        $event = new ZCiCalNode('VEVENT', $icalobj->curnode);
                
                                        $event->addNode(new ZCiCalDataNode( 'UID:' . $uid ));
                                        $event->addNode(new ZCiCalDataNode( 'METHOD:REQUEST' ));
                                        $event->addNode(new ZCiCalDataNode( 'DTSTAMP;VALUE=DATE:' . ZCiCal::fromSqlDateTime(date("Y-m-d H:i:s"))));
                                        $event->addNode(new ZCiCalDataNode( 'DTSTART;VALUE=DATE:' . ZCiCal::fromSqlDateTime($s_date) ));
                                        $event->addNode(new ZCiCalDataNode( 'DTEND;VALUE=DATE:' . ZCiCal::fromSqlDateTime($e_date)));
                                        $event->addNode(new ZCiCalDataNode( 'SUMMARY:SCHEDULE ' . $summary_title ));
                                        $event->addNode(new ZCiCalDataNode( 'DESCRIPTION:' . $description));
                                    }
                                
                                    $loop_date = date_i18n('Y-m-d', strtotime($loop_date) + 60*60*24);
                                }
                            }
                        } else {
                            if ($cruise_type_is_repeated == 1) {
                                $end_date = date_i18n('Y-m-d', strtotime($cruise_schedule->end_date));
                            } else {
                                $end_date = date_i18n('Y-m-d', strtotime($cruise_schedule->start_date) + 60*60);
                            }

                            $description = sprintf('START: %s\nEND: %s\nPRICE: %s\nCABINS: %d\n', $start_date, $end_date, $price_per_day, $cabin_count);
                            $uid = md5("{$start_date}/{$end_date}/{$cruise_id}/{$cabin_type_id}/{$season_name}") . '@' . $this->get_website_domain_name();
    
                            $event = new ZCiCalNode('VEVENT', $icalobj->curnode);
    
                            $event->addNode(new ZCiCalDataNode( 'UID:' . $uid ));
                            $event->addNode(new ZCiCalDataNode( 'METHOD:REQUEST' ));
                            $event->addNode(new ZCiCalDataNode( 'DTSTAMP;VALUE=DATE:' . ZCiCal::fromSqlDateTime(date("Y-m-d H:i:s"))));
                            $event->addNode(new ZCiCalDataNode( 'DTSTART;VALUE=DATE:' . ZCiCal::fromSqlDateTime($start_date) ));
                            $event->addNode(new ZCiCalDataNode( 'DTEND;VALUE=DATE:' . ZCiCal::fromSqlDateTime($end_date)));
                            $event->addNode(new ZCiCalDataNode( 'SUMMARY:SCHEDULE ' . $summary_title ));
                            $event->addNode(new ZCiCalDataNode( 'DESCRIPTION:' . $description));
                        }
                    }
                }

                $ics_filename = 'schedule' . $this->get_website_domain_name() . '-' . $cruise_title . '-'  . $cabin_type_title . '-' . date('Ymd') . '.ics';
        
                header('Content-type: text/calendar; charset=utf-8');
                header('Content-Disposition: inline; filename=' . $ics_filename);
                echo $icalobj->export();
            }
        }
    }

    private function export_cruise_bookings() {
        global $bookyourtravel_cruise_helper, $bookyourtravel_theme_globals;
        
        $cruise_id = 0;
        $cabin_type_id = 0;

        if (isset($_REQUEST['cruise_id'])) {
            $cruise_id = absint( $_GET['cruise_id'] );
        }
        if (isset($_REQUEST['cabin_type_id'])) {
            $cabin_type_id = absint( $_GET['cabin_type_id'] );
        }

        $icalobj = new ZCiCal();

        if ($cruise_id > 0 && $cabin_type_id > 0) {
            $cruise_obj = new BookYourTravel_Cruise($cruise_id);
            $cabin_type_obj = new BookYourTravel_Cabin_type($cabin_type_id);
            
            if ($cruise_obj && $cabin_type_obj) {
                $cruise_title = $cruise_obj->get_title();
                $summary_title = "{$cruise_title}";

                $cabin_type_title = $cabin_type_obj->get_title();
                $summary_title .= ":{$cabin_type_title}";

                $cruise_bookings = $bookyourtravel_cruise_helper->list_cruise_bookings(null, 0, 'Id', 'ASC', null, 0, null, $cruise_id, $cabin_type_id);

                if (count($cruise_bookings) > 0 && $cruise_bookings['total'] > 0) {
                    $completed_bookings = array();
                    foreach ($cruise_bookings['results'] as $booking) {
                        if ((!$bookyourtravel_theme_globals->use_woocommerce_for_checkout() && !isset($booking->woo_status)) || strtolower($booking->woo_status) == 'completed') {
                            $completed_bookings[] = $booking;
                        }
                    }

                    foreach ($cruise_bookings['results'] as $cruise_booking) {
                        $cruise_date = date_i18n('Y-m-d', strtotime($cruise_booking->cruise_date));
                        $cruise_end_date = date_i18n('Y-m-d', strtotime($cruise_booking->cruise_date) + 60*60);
                        $total_price = $this->format_price($cruise_booking->total_price);
                        $adults = $cruise_booking->adults;
                        $children = isset($cruise_booking->children) ? $cruise_booking->children : 0;
                        $first_name = $cruise_booking->first_name;
                        $last_name = $cruise_booking->last_name;
                        $full_name = $first_name . ' ' . $last_name;
                        $email = $cruise_booking->email;

                        $description = sprintf('DATE: %s\nTOTAL: %s\nADULTS: %s\nCHILDREN: %s\nNAME: %s\nEMAIL: %s\n', $cruise_date, $total_price, $adults, $children, $full_name, $email);

                        $uid = md5("{$cruise_date}/{$cruise_id}/{$email}/{$full_name}") . '@' . $this->get_website_domain_name();

                        $event = new ZCiCalNode('VEVENT', $icalobj->curnode);

                        $event->addNode(new ZCiCalDataNode('UID:' . $uid));
                        $event->addNode(new ZCiCalDataNode('DTSTAMP;VALUE=DATE:' . ZCiCal::fromSqlDateTime(date("Y-m-d H:i:s"))));
                        $event->addNode(new ZCiCalDataNode('DTSTART;VALUE=DATE:' . ZCiCal::fromSqlDateTime($cruise_date)));
                        $event->addNode(new ZCiCalDataNode('DTEND;VALUE=DATE:' . ZCiCal::fromSqlDateTime($cruise_end_date)));
                        $event->addNode(new ZCiCalDataNode('SUMMARY:BOOKED ' . $summary_title));
                        $event->addNode(new ZCiCalDataNode('DESCRIPTION:' . $description));
                    }
                }

                $ics_filename = 'bookings' . $this->get_website_domain_name() . '-' . $cruise_title . '-' . $cabin_type_title . '-' . date('Ymd') . '.ics';
        
                header('Content-type: text/calendar; charset=utf-8');
                header('Content-Disposition: inline; filename=' . $ics_filename);
                echo $icalobj->export();
            }
        }
    }

    private function export_tour_schedules() {
        global $bookyourtravel_tour_helper;
        
        $tour_id = 0;

        if (isset($_REQUEST['tour_id'])) {
            $tour_id = absint( $_GET['tour_id'] );
        }

        $icalobj = new ZCiCal();

        if ($tour_id > 0) {
            $tour_obj = new BookYourTravel_Tour($tour_id);
            
            if ($tour_obj) {
                $tour_title = $tour_obj->get_title();
                $summary_title = "{$tour_title}";
                $tour_type_is_repeated = $tour_obj->get_type_is_repeated();
                $tour_type_day_of_week_indexes = $tour_obj->get_type_day_of_week_indexes();

                $tour_schedules = $bookyourtravel_tour_helper->list_tour_schedules(null, 0, 'Id', 'ASC', 0, 0, 0, $tour_id);

                if (count($tour_schedules) > 0 && $tour_schedules['total'] > 0) {
                    foreach ($tour_schedules['results'] as $tour_schedule) {
                        $start_date = date_i18n('Y-m-d', strtotime($tour_schedule->start_date));
                        $end_date = '';

                        $price_per_day = $this->format_price($tour_schedule->price);
                        $season_name = $tour_schedule->season_name;

                        if ($tour_type_is_repeated > 1) {
                            $end_date = date_i18n('Y-m-d', strtotime($tour_schedule->end_date));

                            if ($start_date < $end_date) {
                                $loop_date = $start_date;

                                while ($loop_date < $end_date) {
                                    $s_date = $loop_date;
                                    $e_date = date_i18n('Y-m-d', strtotime($loop_date) + 60*60);
                                    $create_an_event = false;

                                    if ($tour_type_is_repeated == 2) {
                                        if (date_i18n('N', strtotime($loop_date)) - 1 < 6) {
                                            // weekday!
                                             $create_an_event = true;
                                        }
                                        // echo esc_html__('This tour is repeated every weekday (working day).', 'bookyourtravel');
                                    } elseif ($tour_type_is_repeated == 3 && count($tour_type_day_of_week_indexes) > 0) {
                                        if (date_i18n('N', strtotime($loop_date)) - 1 == $tour_type_day_of_week_indexes[0]) {
                                            // on correct day of week
                                            $create_an_event = true;                                            
                                        }
                                        // echo sprintf(esc_html__('This tour is repeated every week on a %s.', 'bookyourtravel'), $tour_obj->get_type_day_of_week_day());
                                    } elseif ($tour_type_is_repeated == 4) {
                                        $index_matches = 0;
                                        foreach ($tour_type_day_of_week_indexes as $day_of_week_index) {
                                            if (date_i18n('N', strtotime($loop_date)) - 1 == $day_of_week_index) {
                                                $index_matches++;
                                            }
                                        }

                                        if ($index_matches > 0) {
                                            $create_an_event = true;
                                        }
                                        // echo esc_html__('This tour is repeated every week on multiple days.', 'bookyourtravel');
                                    }

                                    if ($create_an_event) {
                                        $description = sprintf('START: %s\nEND: %s\nPRICE: %s\n', $s_date, $e_date, $price_per_day);
                                        $uid = md5("{$s_date}/{$e_date}/{$tour_id}/{$season_name}") . '@' . $this->get_website_domain_name();
                
                                        $event = new ZCiCalNode('VEVENT', $icalobj->curnode);
                
                                        $event->addNode(new ZCiCalDataNode( 'UID:' . $uid ));
                                        $event->addNode(new ZCiCalDataNode( 'METHOD:REQUEST' ));
                                        $event->addNode(new ZCiCalDataNode( 'DTSTAMP;VALUE=DATE:' . ZCiCal::fromSqlDateTime(date("Y-m-d H:i:s"))));
                                        $event->addNode(new ZCiCalDataNode( 'DTSTART;VALUE=DATE:' . ZCiCal::fromSqlDateTime($s_date) ));
                                        $event->addNode(new ZCiCalDataNode( 'DTEND;VALUE=DATE:' . ZCiCal::fromSqlDateTime($e_date)));
                                        $event->addNode(new ZCiCalDataNode( 'SUMMARY:SCHEDULE ' . $summary_title ));
                                        $event->addNode(new ZCiCalDataNode( 'DESCRIPTION:' . $description));
                                    }
                                
                                    $loop_date = date_i18n('Y-m-d', strtotime($loop_date) + 60*60*24);
                                }
                            }
                        } else {
                            if ($tour_type_is_repeated == 1) {
                                $end_date = date_i18n('Y-m-d', strtotime($tour_schedule->end_date));
                            } else {
                                $end_date = date_i18n('Y-m-d', strtotime($tour_schedule->start_date) + 60*60);
                            }

                            $description = sprintf('START: %s\nEND: %s\nPRICE: %s\n', $start_date, $end_date, $price_per_day);
                            $uid = md5("{$start_date}/{$end_date}/{$tour_id}/{$season_name}") . '@' . $this->get_website_domain_name();

                            $event = new ZCiCalNode('VEVENT', $icalobj->curnode);

                            $event->addNode(new ZCiCalDataNode('UID:' . $uid));
                            $event->addNode(new ZCiCalDataNode('METHOD:REQUEST'));
                            $event->addNode(new ZCiCalDataNode('DTSTAMP;VALUE=DATE:' . ZCiCal::fromSqlDateTime(date("Y-m-d H:i:s"))));
                            $event->addNode(new ZCiCalDataNode('DTSTART;VALUE=DATE:' . ZCiCal::fromSqlDateTime($start_date)));
                            $event->addNode(new ZCiCalDataNode('DTEND;VALUE=DATE:' . ZCiCal::fromSqlDateTime($end_date)));
                            $event->addNode(new ZCiCalDataNode('SUMMARY:SCHEDULE ' . $summary_title));
                            $event->addNode(new ZCiCalDataNode('DESCRIPTION:' . $description));
                        }
                    }
                }

                $ics_filename = 'schedule' . $this->get_website_domain_name() . '-' . $tour_title . '-' . date('Ymd') . '.ics';
        
                header('Content-type: text/calendar; charset=utf-8');
                header('Content-Disposition: inline; filename=' . $ics_filename);
                echo $icalobj->export();
            }
        }
    }

    private function export_tour_bookings() {
        global $bookyourtravel_tour_helper, $bookyourtravel_theme_globals;
        
        $tour_id = 0;

        if (isset($_REQUEST['tour_id'])) {
            $tour_id = absint( $_GET['tour_id'] );
        }

        $icalobj = new ZCiCal();

        if ($tour_id > 0) {
            $tour_obj = new BookYourTravel_Tour($tour_id);
            
            if ($tour_obj) {
                $tour_title = $tour_obj->get_title();
                $summary_title = "{$tour_title}";

                $tour_bookings = $bookyourtravel_tour_helper->list_tour_bookings(null, 0, 'Id', 'ASC', null, 0, null, $tour_id);

                if (count($tour_bookings) > 0 && $tour_bookings['total'] > 0) {
                    $completed_bookings = array();
                    foreach ($tour_bookings['results'] as $booking) {
                        if ((!$bookyourtravel_theme_globals->use_woocommerce_for_checkout() && !isset($booking->woo_status)) || strtolower($booking->woo_status) == 'completed') {
                            $completed_bookings[] = $booking;
                        }
                    }

                    foreach ($tour_bookings['results'] as $tour_booking) {
                        $tour_date = date_i18n('Y-m-d', strtotime($tour_booking->tour_date));
                        $tour_end_date = date_i18n('Y-m-d', strtotime($tour_booking->tour_date) + 60*60);
                        $total_price = $this->format_price($tour_booking->total_price);
                        $adults = $tour_booking->adults;
                        $children = isset($tour_booking->children) ? $tour_booking->children : 0;                        
                        $first_name = $tour_booking->first_name;
                        $last_name = $tour_booking->last_name;
                        $full_name = $first_name . ' ' . $last_name;
                        $email = $tour_booking->email;

                        $description = sprintf('DATE: %s\nTOTAL: %s\nADULTS: %s\nCHILDREN: %s\nNAME: %s\nEMAIL: %s\n', $tour_date, $total_price, $adults, $children, $full_name, $email);

                        $uid = md5("{$tour_date}/{$tour_id}/{$email}/{$full_name}") . '@' . $this->get_website_domain_name();

                        $event = new ZCiCalNode('VEVENT', $icalobj->curnode);

                        $event->addNode(new ZCiCalDataNode( 'UID:' . $uid ));
                        $event->addNode(new ZCiCalDataNode( 'DTSTAMP;VALUE=DATE:' . ZCiCal::fromSqlDateTime(date("Y-m-d H:i:s"))));
                        $event->addNode(new ZCiCalDataNode( 'DTSTART;VALUE=DATE:' . ZCiCal::fromSqlDateTime($tour_date) ));
                        $event->addNode(new ZCiCalDataNode( 'DTEND;VALUE=DATE:' . ZCiCal::fromSqlDateTime($tour_end_date) ));
                        $event->addNode(new ZCiCalDataNode( 'SUMMARY:BOOKED ' . $summary_title ));
                        $event->addNode(new ZCiCalDataNode( 'DESCRIPTION:' . $description));
                    }
                }

                $ics_filename = 'bookings' . $this->get_website_domain_name() . '-' . $tour_title . '-' . date('Ymd') . '.ics';
        
                header('Content-type: text/calendar; charset=utf-8');
                header('Content-Disposition: inline; filename=' . $ics_filename);
                echo $icalobj->export();
            }
        }
    }

    private function export_car_rental_availability() {
        global $bookyourtravel_car_rental_helper;
        
        $car_rental_id = 0;

        if (isset($_REQUEST['car_rental_id'])) {
            $car_rental_id = absint( $_GET['car_rental_id'] );
        }

        $icalobj = new ZCiCal();

        if ($car_rental_id > 0) {
            $car_rental_obj = new BookYourTravel_Car_Rental($car_rental_id);
            
            if ($car_rental_obj) {
                $car_rental_title = $car_rental_obj->get_title();
                $summary_title = "{$car_rental_title}";

                $car_rental_availabilities = $bookyourtravel_car_rental_helper->list_car_rental_availabilities($car_rental_id);

                if (count($car_rental_availabilities) > 0 && $car_rental_availabilities['total'] > 0) {
                    foreach ($car_rental_availabilities['results'] as $car_rental_availability) {
                        $start_date = date_i18n('Y-m-d', strtotime($car_rental_availability->start_date));
                        $end_date = date_i18n('Y-m-d', strtotime($car_rental_availability->end_date));
                        $price_per_day = $this->format_price($car_rental_availability->price_per_day);
                        $season_name = $car_rental_availability->season_name;

                        $description = sprintf('START: %s\nEND: %s\nPRICE: %s\n', $start_date, $end_date, $price_per_day);

                        $uid = md5("{$start_date}/{$end_date}/{$car_rental_id}/{$season_name}") . '@' . $this->get_website_domain_name();

                        $event = new ZCiCalNode('VEVENT', $icalobj->curnode);

                        $event->addNode(new ZCiCalDataNode( 'UID:' . $uid ));
                        $event->addNode(new ZCiCalDataNode( 'METHOD:REQUEST' ));
                        $event->addNode(new ZCiCalDataNode( 'DTSTAMP;VALUE=DATE:' . ZCiCal::fromSqlDateTime(date("Y-m-d H:i:s"))));
                        $event->addNode(new ZCiCalDataNode( 'DTSTART;VALUE=DATE:' . ZCiCal::fromSqlDateTime($start_date) ));
                        $event->addNode(new ZCiCalDataNode( 'DTEND;VALUE=DATE:' . ZCiCal::fromSqlDateTime($end_date) ));
                        $event->addNode(new ZCiCalDataNode( 'SUMMARY:AVAILABILITY ' . $summary_title ));
                        $event->addNode(new ZCiCalDataNode( 'DESCRIPTION:' . $description));
                    }
                }

                $ics_filename = 'availability' . $this->get_website_domain_name() . '-' . $car_rental_title . '-' . date('Ymd') . '.ics';
        
                header('Content-type: text/calendar; charset=utf-8');
                header('Content-Disposition: inline; filename=' . $ics_filename);
                echo $icalobj->export();
            }
        }
    }

    private function export_car_rental_bookings() {
        global $bookyourtravel_car_rental_helper, $bookyourtravel_theme_globals;
        
        $car_rental_id = 0;

        if (isset($_REQUEST['car_rental_id'])) {
            $car_rental_id = absint( $_GET['car_rental_id'] );
        }

        $icalobj = new ZCiCal();

        if ($car_rental_id > 0) {
            $car_rental_obj = new BookYourTravel_Car_Rental($car_rental_id);
            
            if ($car_rental_obj) {
                $car_rental_title = $car_rental_obj->get_title();
                $summary_title = "{$car_rental_title}";

                $car_rental_bookings = $bookyourtravel_car_rental_helper->list_car_rental_bookings(null,  'Id', 'ASC', null, 0, 0, null, $car_rental_id);

                if (count($car_rental_bookings) > 0 && $car_rental_bookings['total'] > 0) {
                    $completed_bookings = array();
                    foreach ($car_rental_bookings['results'] as $booking) {
                        if ((!$bookyourtravel_theme_globals->use_woocommerce_for_checkout() && !isset($booking->woo_status)) || strtolower($booking->woo_status) == 'completed') {
                            $completed_bookings[] = $booking;
                        }
                    }

                    foreach ($completed_bookings as $car_rental_booking) {
                        $start_date = date_i18n('Y-m-d', strtotime($car_rental_booking->start_date));
                        $end_date = date_i18n('Y-m-d', strtotime($car_rental_booking->end_date));
                        $total_price = $this->format_price($car_rental_booking->total_price);
                        $first_name = $car_rental_booking->first_name;
                        $last_name = $car_rental_booking->last_name;
                        $full_name = $first_name . ' ' . $last_name;
                        $email = $car_rental_booking->email;

                        $description = sprintf('START: %s\nEND: %s\nTOTAL: %s\nNAME: %s\nEMAIL: %s\n', $start_date, $end_date, $total_price, $full_name, $email);

                        $uid = md5("{$start_date}/{$end_date}/{$car_rental_id}/{$email}/{$full_name}") . '@' . $this->get_website_domain_name();

                        $event = new ZCiCalNode('VEVENT', $icalobj->curnode);

                        $event->addNode(new ZCiCalDataNode( 'UID:' . $uid ));
                        $event->addNode(new ZCiCalDataNode( 'DTSTAMP;VALUE=DATE:' . ZCiCal::fromSqlDateTime(date("Y-m-d H:i:s"))));
                        $event->addNode(new ZCiCalDataNode( 'DTSTART;VALUE=DATE:' . ZCiCal::fromSqlDateTime($start_date) ));
                        $event->addNode(new ZCiCalDataNode( 'DTEND;VALUE=DATE:' . ZCiCal::fromSqlDateTime($end_date) ));
                        $event->addNode(new ZCiCalDataNode( 'SUMMARY:BOOKED ' . $summary_title ));
                        $event->addNode(new ZCiCalDataNode( 'DESCRIPTION:' . $description));
                    }
                }

                $ics_filename = 'bookings' . $this->get_website_domain_name() . '-' . $car_rental_title . '-' . date('Ymd') . '.ics';
        
                header('Content-type: text/calendar; charset=utf-8');
                header('Content-Disposition: inline; filename=' . $ics_filename);
                echo $icalobj->export();
            }
        }
    }

    private function export_accommodation_vacancies() {
        global $bookyourtravel_accommodation_helper, $bookyourtravel_room_type_helper;
        
        $accommodation_id = 0;
        $room_type_id = 0;

        if (isset($_REQUEST['accommodation_id'])) {
            $accommodation_id = absint( $_GET['accommodation_id'] );
        }
        if (isset($_REQUEST['room_type_id'])) {
            $room_type_id = absint( $_GET['room_type_id'] );
        }

        $icalobj = new ZCiCal();

        if ($accommodation_id > 0) {
            $accommodation_obj = new BookYourTravel_Accommodation($accommodation_id);
            
            if ($accommodation_obj) {
                $accommodation_title = $accommodation_obj->get_title();
                $summary_title = "{$accommodation_title}";

                $room_type_obj = null;
                $room_type_title = '';
                if ($room_type_id > 0) {
                    $room_type_obj = new BookYourTravel_Room_Type($room_type_id);
                    $room_type_title = $room_type_obj->get_title();
                    $summary_title .= ":{$room_type_title}";
                }

                $accommodation_vacancies = $bookyourtravel_accommodation_helper->list_accommodation_vacancies($accommodation_id, $room_type_id);

                if (count($accommodation_vacancies) > 0 && $accommodation_vacancies['total'] > 0) {
                    foreach ($accommodation_vacancies['results'] as $accommodation_vacancy) {
                        $start_date = date_i18n('Y-m-d', strtotime($accommodation_vacancy->start_date));
                        $end_date = date_i18n('Y-m-d', strtotime($accommodation_vacancy->end_date));
                        $price_per_day = $this->format_price($accommodation_vacancy->price_per_day);
                        $price_per_day_child = $this->format_price($accommodation_vacancy->price_per_day_child);
                        $season_name = $accommodation_vacancy->season_name;
                        $room_count = $accommodation_vacancy->room_count;

                        $description = sprintf('START: %s\nEND: %s\nPRICE: %s\nPRICECHILD: %s\n', $start_date, $end_date, $price_per_day, $price_per_day_child);
                        if ($room_type_id > 0) {
                            $description .= sprintf('ROOMS: %d\n', $room_count);
                        }

                        $uid = md5("{$start_date}/{$end_date}/{$accommodation_id}/{$room_type_id}/{$season_name}") . '@' . $this->get_website_domain_name();

                        $event = new ZCiCalNode('VEVENT', $icalobj->curnode);

                        $event->addNode(new ZCiCalDataNode( 'UID:' . $uid ));
                        $event->addNode(new ZCiCalDataNode( 'METHOD:REQUEST' ));
                        $event->addNode(new ZCiCalDataNode( 'DTSTAMP;VALUE=DATE:' . ZCiCal::fromSqlDateTime(date("Y-m-d H:i:s"))));
                        $event->addNode(new ZCiCalDataNode( 'DTSTART;VALUE=DATE:' . ZCiCal::fromSqlDateTime($start_date) ));
                        $event->addNode(new ZCiCalDataNode( 'DTEND;VALUE=DATE:' . ZCiCal::fromSqlDateTime($end_date) ));
                        $event->addNode(new ZCiCalDataNode( 'SUMMARY:VACANCY ' . $summary_title ));
                        $event->addNode(new ZCiCalDataNode( 'DESCRIPTION:' . $description));
                    }
                }

                $ics_filename = 'vacancies' . $this->get_website_domain_name() . '-' . $accommodation_title . '-' . date('Ymd') . '.ics';
        
                header('Content-type: text/calendar; charset=utf-8');
                header('Content-Disposition: inline; filename=' . $ics_filename);
                echo $icalobj->export();
            }
        }
    }

    private function export_accommodation_bookings() {
        global $bookyourtravel_accommodation_helper, $bookyourtravel_room_type_helper, $bookyourtravel_theme_globals;
        
        $accommodation_id = 0;
        $room_type_id = 0;

        if (isset($_REQUEST['accommodation_id'])) {
            $accommodation_id = absint( $_GET['accommodation_id'] );
        }
        if (isset($_REQUEST['room_type_id'])) {
            $room_type_id = absint( $_GET['room_type_id'] );
        }

        $icalobj = new ZCiCal();

        if ($accommodation_id > 0) {
            $accommodation_obj = new BookYourTravel_Accommodation($accommodation_id);
            
            if ($accommodation_obj) {
                $accommodation_title = $accommodation_obj->get_title();
                $summary_title = "{$accommodation_title}";

                $room_type_obj = null;
                $room_type_title = '';
                if ($room_type_id > 0) {
                    $room_type_obj = new BookYourTravel_Room_Type($room_type_id);
                    $room_type_title = $room_type_obj->get_title();
                    $summary_title .= ":{$room_type_title}";
                }

                $accommodation_bookings = $bookyourtravel_accommodation_helper->list_accommodation_bookings(null, 0, 'Id', 'ASC', null, 0, null, $accommodation_id, $room_type_id);

                if (count($accommodation_bookings) > 0 && $accommodation_bookings['total'] > 0) {
                    $completed_bookings = array();
                    foreach ($accommodation_bookings['results'] as $booking) {
                        if ((!$bookyourtravel_theme_globals->use_woocommerce_for_checkout() && !isset($booking->woo_status)) || strtolower($booking->woo_status) == 'completed') {
                            $completed_bookings[] = $booking;
                        }
                    }

                    foreach ($completed_bookings as $accommodation_booking) {
                        $start_date = date_i18n('Y-m-d', strtotime($accommodation_booking->date_from));
                        $end_date = date_i18n('Y-m-d', strtotime($accommodation_booking->date_to));
                        $total_price = $this->format_price($accommodation_booking->total_price);
                        $adults = $accommodation_booking->adults;
                        $children = isset($accommodation_booking->children) ? $accommodation_booking->children : 0;
                        $first_name = $accommodation_booking->first_name;
                        $last_name = $accommodation_booking->last_name;
                        $full_name = $first_name . ' ' . $last_name;
                        $email = $accommodation_booking->email;

                        $description = sprintf('START: %s\nEND: %s\nTOTAL: %s\nADULTS: %s\nCHILDREN: %s\nNAME: %s\nEMAIL: %s\n', $start_date, $end_date, $total_price, $adults, $children, $full_name, $email);

                        $uid = md5("{$start_date}/{$end_date}/{$accommodation_id}/{$room_type_id}/{$email}/{$full_name}") . '@' . $this->get_website_domain_name();

                        $event = new ZCiCalNode('VEVENT', $icalobj->curnode);

                        $event->addNode(new ZCiCalDataNode( 'UID:' . $uid ));
                        $event->addNode(new ZCiCalDataNode( 'DTSTAMP;VALUE=DATE:' . ZCiCal::fromSqlDateTime(date("Y-m-d H:i:s"))));
                        $event->addNode(new ZCiCalDataNode( 'DTSTART;VALUE=DATE:' . ZCiCal::fromSqlDateTime($start_date) ));
                        $event->addNode(new ZCiCalDataNode( 'DTEND;VALUE=DATE:' . ZCiCal::fromSqlDateTime($end_date) ));
                        $event->addNode(new ZCiCalDataNode( 'SUMMARY:BOOKED ' . $summary_title ));
                        $event->addNode(new ZCiCalDataNode( 'DESCRIPTION:' . $description));
                    }
                }

                $ics_filename = 'bookings' . $this->get_website_domain_name() . '-' . $accommodation_title . '-' . date('Ymd') . '.ics';
        
                header('Content-type: text/calendar; charset=utf-8');
                header('Content-Disposition: inline; filename=' . $ics_filename);
                echo $icalobj->export();
            }
        }
    }
    
	function format_price($price) {
		if (!$this->show_currency_symbol_after) {
			return $this->default_currency_symbol . '' . number_format_i18n( $price, $this->price_decimal_places );
		} else {
			return number_format_i18n( $price, $this->price_decimal_places ) . '' . $this->default_currency_symbol;
		}
	}    

    function get_website_domain_name(){
        $host = parse_url( home_url(), PHP_URL_HOST );
        return preg_replace( '/^www\./', '', $host );
    }    
    
}

// store the instance in a variable to be retrieved later and call init
$bookyourtravel_theme_ical = BookYourTravel_Theme_Ical::get_instance();
