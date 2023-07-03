<?php
/**
 * BookYourTravel_Car_Rental class
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */

class BookYourTravel_Car_Rental extends BookYourTravel_Entity
{
    public function __construct( $entity ) {
		parent::__construct( $entity, 'car_rental' );
    }

	public function get_short_description() {
		$short_description = $this->get_custom_field('short_description');
		return apply_filters( 'bookyourtravel_entity_short_description', $short_description, $this );
	}

	public function use_referral_url() {
		$use_referral_url = $this->get_custom_field( 'use_referral_url' );
		return isset($use_referral_url) ? $use_referral_url : 0;
	}

	public function get_referral_url() {
		$referral_url = $this->get_custom_field( 'referral_url' );
		return isset($referral_url) ? $referral_url : '';
	}

	public function get_deposit_percentage() {
		$deposit_percentage = $this->get_custom_field( 'deposit_percentage' );
		return is_numeric($deposit_percentage) ? intval($deposit_percentage) : 100;
	}

	public function get_referral_price() {
		$referral_price = $this->get_custom_field( 'referral_price' );
		return isset($referral_price) ? $referral_price : 0;
	}

	public function get_locations() {
		$location_ids = $this->get_custom_field( 'locations', false );
		return unserialize($location_ids);
	}

	public function get_formatted_locations() {
		$locations = $this->get_locations();
		$locations_str = '';
		if ($locations && count($locations) > 0) {
			foreach ($locations as $location_id) {
				$location_obj = new BookYourTravel_Location((int)$location_id);
                $location_title = $location_obj->get_title();
                if (!empty($location_title)) {
                    $locations_str .= $location_title . ', ';
                }
			}
		}
		$locations_str = rtrim($locations_str, ', ');
		return $locations_str;
	}

	public function get_formatted_address($include_location = true) {
		$address = $this->get_custom_field('address');
		$location_part = $include_location ? $this->get_formatted_locations() : '';
		if (!empty($address)) {
			$address .= apply_filters('bookyourtravel_car_rental_address_location', ', ' . $location_part);
		} else {
			$address = apply_filters('bookyourtravel_car_rental_address_location', $location_part);
		}
		$address = rtrim(trim($address), ',');
		return $address;
	}

    public function get_type_name() {
		$type_objs = wp_get_post_terms( $this->get_id(), 'car_type', array( "fields" => "all" ) );
		return $type_objs ? $type_objs[0]->name : '';
    }

    public function get_type_id() {
		$type_objs = wp_get_post_terms( $this->get_id(), 'car_type', array( "fields" => "all" ) );
		return $type_objs ? $type_objs[0]->term_id : null;
    }

	public function get_tags() {
		return wp_get_post_terms( $this->get_id(), 'car_rental_tag', array( "fields" => "all" ) );
	}

	public function get_tag_ids() {
		$tag_ids = array();
		$tags = wp_get_post_terms( $this->get_id(), 'car_rental_tag', array( "fields" => "all" ) );
		if (count($tags) > 0) {
			foreach ($tags as $tag) {
				$tag_ids[] = $tag->term_id;
			}
		}
		return $tag_ids;
	}

	public function get_min_booking_days() {
		$min_booking_days = intval($this->get_custom_field( 'min_booking_days' ));
		return is_numeric($min_booking_days) && $min_booking_days > 1 ? $min_booking_days : 1;
	}
	
	public function get_max_booking_days() {
		$max_booking_days = intval($this->get_custom_field( 'max_booking_days' ));
		return is_numeric($max_booking_days) && $max_booking_days > 0 ? $max_booking_days : 0;
	}

	public function get_is_reservation_only() {
		$is_reservation_only = intval($this->get_custom_field( 'is_reservation_only' ));
		return is_numeric($is_reservation_only) ? $is_reservation_only : 0;
	}

	public function get_force_disable_calendar() {
		$force_disable_calendar = intval($this->get_custom_field( 'force_disable_calendar' ));
		return is_numeric($force_disable_calendar) ? $force_disable_calendar : 0;
	}

	public function get_field_value($field_name, $use_prefix = true) {
		if ( $field_name == 'car_rental_tag' ) {
			$tag_ids = array();
			$tags = $this->get_tags();
			if ($tags && count($tags) > 0) {
				for( $i = 0; $i < count($tags); $i++) {
					$tag = $tags[$i];
					$tag_ids[] = $tag->term_id;
				}
			}
			return $tag_ids;
		} elseif ( $field_name == 'car_type' )
			return $this->get_type_id();
		elseif ( $field_name == 'post_title' )
			return $this->post ? $this->post->post_title : '';
		elseif ( $field_name == 'post_content' )
			return $this->post ? $this->post->post_content : '';
		else
			return $this->get_custom_field($field_name, $use_prefix);
	}
}
