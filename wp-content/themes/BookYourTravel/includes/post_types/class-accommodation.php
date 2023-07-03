<?php
/**
 * BookYourTravel_Accommodation class
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */
 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class BookYourTravel_Accommodation extends BookYourTravel_Entity
{
    public function __construct( $entity ) {
		parent::__construct( $entity, 'accommodation' );
    }
	
	public function get_rent_type() {
		$rent_type = $this->get_custom_field( 'rent_type' );
		return isset($rent_type) ? intval($rent_type) : 0;	
	}
		
	public function get_short_description() {
		$short_description = $this->get_custom_field('short_description');
		return apply_filters( 'bookyourtravel_entity_short_description', $short_description, $this );		
	}		
	
	public function get_formatted_rent_type() {
		$rent_type = $this->get_rent_type();
		
		$rent_type_str = __('night', 'bookyourtravel');
		if ($rent_type == 1) {
			$rent_type_str = __('week', 'bookyourtravel');
		} else if ($rent_type == 2) {
			$rent_type_str = __('month', 'bookyourtravel');
		}
		return $rent_type_str;
	}	
	
	public function get_location() {
		$location_id = $this->get_custom_field('location_post_id');
		return $location_id ? new BookYourTravel_Location(intval($location_id)) : '';
	}
	
	public function get_formatted_address($include_location = true) {
		$location_obj = $this->get_location();
		$address = $this->get_custom_field('address');
		$location_part = $include_location && $location_obj != null && isset($location_obj) ? $location_obj->get_title() : '';
		if (!empty($address)) {
			$address .= apply_filters('bookyourtravel_accommodation_address_location', ', ' . $location_part);	
		} else {
			$address = apply_filters('bookyourtravel_accommodation_address_location', $location_part);
		}

		$address = rtrim(trim($address), ',');
		return $address;
	}
	
    public function get_type_name() {	
		$type_objs = wp_get_post_terms( $this->get_id(), 'accommodation_type', array( "fields" => "all" ) );
		return $type_objs ? $type_objs[0]->name : '';
    }
	
    public function get_type_id() {	
		$type_objs = wp_get_post_terms( $this->get_id(), 'accommodation_type', array( "fields" => "all" ) );
		return $type_objs ? $type_objs[0]->term_id : null;
    }
	
	public function get_is_price_per_person() {
		$is_price_per_person = $this->get_custom_field( 'is_price_per_person' );
		return isset($is_price_per_person) ? intval($is_price_per_person) : 0;
	}
	
	public function use_referral_url() {
		$use_referral_url = $this->get_custom_field( 'use_referral_url' );
		return isset($use_referral_url) ? intval($use_referral_url) : 0;
	}

	public function get_referral_url() {
		$referral_url = $this->get_custom_field( 'referral_url' );
		return isset($referral_url) ? $referral_url : '';	
	}

	public function get_referral_price() {
		$referral_price = $this->get_custom_field( 'referral_price' );
		return isset($referral_price) ? $referral_price : 0;	
	}
	
	public function get_checkin_week_day() {
		$checkin_week_day = $this->get_custom_field( 'checkin_week_day' );
		return is_numeric($checkin_week_day) ? intval($checkin_week_day) : -1;
	}
	
	public function get_checkout_week_day() {
		$checkout_week_day = $this->get_custom_field( 'checkout_week_day' );
		return is_numeric($checkout_week_day) ? intval($checkout_week_day) : -1;
	}
	
	public function get_min_days_stay() {
		$min_days_stay = intval($this->get_custom_field( 'min_days_stay' ));
		return is_numeric($min_days_stay) && $min_days_stay > 1 ? $min_days_stay : 1;
	}
	
	public function get_max_days_stay() {
		$max_days_stay = intval($this->get_custom_field( 'max_days_stay' ));
		return is_numeric($max_days_stay) && $max_days_stay > 0 ? $max_days_stay : 0;
	}

	public function get_min_adult_count() {
		$min_adult_count = intval($this->get_custom_field( 'min_count' ));
		return is_numeric($min_adult_count) && $min_adult_count > 1 ? $min_adult_count : 1;
	}
	
	public function get_max_adult_count() {
		$max_adult_count = intval($this->get_custom_field( 'max_count' ));
		return is_numeric($max_adult_count) && $max_adult_count > 0 ? $max_adult_count : 0;
	}
	
	public function get_min_child_count() {
		$min_child_count = intval($this->get_custom_field( 'min_child_count' ));
		return is_numeric($min_child_count) && $min_child_count > 0 ? $min_child_count : 0;
	}
	
	public function get_max_child_count() {
		$max_child_count = intval($this->get_custom_field( 'max_child_count' ));
		return is_numeric($max_child_count) && $max_child_count > 0 ? $max_child_count : 0;
	}
	
	public function get_disabled_room_types() {
		$disabled_room_types = intval($this->get_custom_field( 'disabled_room_types' ));
		return is_numeric($disabled_room_types) ? $disabled_room_types : 0;
	}

	public function get_deposit_percentage() {
		$deposit_percentage = $this->get_custom_field( 'deposit_percentage' );
		return is_numeric($deposit_percentage) ? intval($deposit_percentage) : 100;
	}

	public function get_is_reservation_only() {
		$is_reservation_only = intval($this->get_custom_field( 'is_reservation_only' ));
		return is_numeric($is_reservation_only) ? $is_reservation_only : 0;
	}

	public function get_force_disable_calendar() {
		$force_disable_calendar = intval($this->get_custom_field( 'force_disable_calendar' ));
		return is_numeric($force_disable_calendar) ? $force_disable_calendar : 0;
	}
	
	public function get_count_children_stay_free() {
		$count_children_stay_free = intval($this->get_custom_field( 'count_children_stay_free' ));
		return is_numeric($count_children_stay_free) ? $count_children_stay_free : 0;
	}

	public function get_room_types() {
		$room_type_ids = $this->get_custom_field( 'room_types', false );
		return unserialize($room_type_ids);
	}
	
	public function get_tags() {
		return wp_get_post_terms( $this->get_id(), 'acc_tag', array( "fields" => "all" ) );
	}
	
	public function get_tag_ids() {
		$tag_ids = array();
		$tags = wp_get_post_terms( $this->get_id(), 'acc_tag', array( "fields" => "all" ) );
		if (count($tags) > 0) {
			foreach ($tags as $tag) {
				$tag_ids[] = $tag->term_id;
			}
		}
		return $tag_ids;
	}
	
	public function get_facilities() {
		return wp_get_post_terms($this->get_id(), 'facility', array('orderby' => 'name', 'order' => 'ASC', 'fields' => 'all'));	
	}
	
	public function get_field_value($field_name, $use_prefix = true) {
		if ( $field_name == 'facilities' || $field_name == 'facility' ) {
			$facility_ids = array();
			$facilities = $this->get_facilities();
			if ($facilities && count($facilities) > 0) {
				for( $i = 0; $i < count($facilities); $i++) {
					$facility = $facilities[$i];
					$facility_ids[] = $facility->term_id;
				}
			}
			return $facility_ids;
		} else if ( $field_name == 'acc_tag' ) {
			$tag_ids = array();
			$tags = $this->get_tags();
			if ($tags && count($tags) > 0) {
				for( $i = 0; $i < count($tags); $i++) {
					$tag = $tags[$i];
					$tag_ids[] = $tag->term_id;
				}
			}
			return $tag_ids;
		} elseif ( $field_name == 'accommodation_type' )
			return $this->get_type_id();
		elseif ( $field_name == 'room_types' )
			return $this->get_room_types();
		elseif ( $field_name == 'post_title' )
			return $this->post ? $this->post->post_title : '';
		elseif ( $field_name == 'post_content' )
			return $this->post ? $this->post->post_content : '';
		else
			return $this->get_custom_field($field_name, $use_prefix);			
	}

}