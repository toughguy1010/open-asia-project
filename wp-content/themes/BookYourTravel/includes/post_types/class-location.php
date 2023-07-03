<?php
/**
 * BookYourTravel_Location class
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */

if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class BookYourTravel_Location extends BookYourTravel_Entity
{
    private $enable_accommodations;
    private $enable_cruises;
    private $enable_tours;
    private $enable_car_rentals;

    public function __construct($entity)
    {
        global $bookyourtravel_theme_globals;
        parent::__construct($entity, 'location');

        $this->enable_accommodations = $bookyourtravel_theme_globals->enable_accommodations();
        $this->enable_cruises = $bookyourtravel_theme_globals->enable_cruises();
        $this->enable_tours = $bookyourtravel_theme_globals->enable_tours();
        $this->enable_car_rentals = $bookyourtravel_theme_globals->enable_car_rentals();
    }

    public function get_short_description()
    {
        $short_description = $this->get_custom_field('short_description');
        return apply_filters('bookyourtravel_entity_short_description', $short_description, $this);
    }

    public function get_tags()
    {
        return wp_get_post_terms($this->get_id(), 'location_tag', array( "fields" => "all" ));
    }

    public function get_types()
    {
        return wp_get_post_terms($this->get_id(), 'location_type', array( "fields" => "all" ));
    }

    public function get_field_value($field_name, $use_prefix = true)
    {
        if ($field_name == 'location_tag') {
            $tag_ids = array();
            $tags = $this->get_tags();
            if ($tags && count($tags) > 0) {
                for ($i = 0; $i < count($tags); $i++) {
                    $tag = $tags[$i];
                    $tag_ids[] = $tag->term_id;
                }
            }
            return $tag_ids;
        } elseif ($field_name == 'location_type') {
            $type_ids = array();
            $types = $this->get_types();
            if ($types && count($types) > 0) {
                for ($i = 0; $i < count($types); $i++) {
                    $type = $types[$i];
                    $type_ids[] = $type->term_id;
                }
            }
            return $type_ids;
        } elseif ($field_name == 'post_title') {
            return $this->post ? $this->post->post_title : '';
        } elseif ($field_name == 'post_content') {
            return $this->post ? $this->post->post_content : '';
        } else {
            return $this->get_custom_field($field_name, $use_prefix);
        }
    }


    public function get_accommodation_count()
    {
        $count = -1;

        if ($this->enable_accommodations) {
            global $bookyourtravel_accommodation_helper;

            if ($this->is_custom_field_set('_location_accommodation_count', false)) {
                $count = $this->get_custom_field('_location_accommodation_count', false);
            } else {
                $count = (int)$bookyourtravel_accommodation_helper->list_accommodations_count(0, -1, 'post_title', 'ASC', array($this->get_id()), array(), array(), array(), false);
                update_post_meta($this->get_id(), '_location_accommodation_count', $count);
            }
        }

        return $count;
    }

    public function get_accommodation_ids()
    {
        $ids = array();

        if ($this->enable_accommodations) {
            global $bookyourtravel_accommodation_helper;

            if ($this->is_custom_field_set('_location_accommodation_ids', false)) {
                $ids = unserialize($this->get_custom_field('_location_accommodation_ids', false));
            } else {
                $accommodation_results = $bookyourtravel_accommodation_helper->list_accommodations(0, -1, 'post_title', 'ASC', array($this->get_id()));

                if (count($accommodation_results) > 0 && $accommodation_results['total'] > 0) {
                    foreach ($accommodation_results['results'] as $accommodation_result) {
                        $ids[] = $accommodation_result->ID;
                    }
                    update_post_meta($this->get_id(), '_location_accommodation_ids', $ids);
                }
            }
        }

        return $ids;
    }

    public function get_tour_count()
    {
        $count = -1;

        if ($this->enable_tours) {
            global $bookyourtravel_tour_helper;

            if ($this->is_custom_field_set('_location_tour_count', false)) {
                $count = $this->get_custom_field('_location_tour_count', false);
            } else {
                $count = (int)$bookyourtravel_tour_helper->list_tours_count(0, -1, 'post_title', 'ASC', array($this->get_id()));
                update_post_meta($this->get_id(), '_location_tour_count', $count);
            }
        }

        return $count;
    }

    public function get_tour_ids()
    {
        $ids = array();

        if ($this->enable_tours) {
            global $bookyourtravel_tour_helper;

            if ($this->is_custom_field_set('_location_tour_ids', false)) {
                $ids = unserialize($this->get_custom_field('_location_tour_ids', false));
            } else {
                $tour_results = $bookyourtravel_tour_helper->list_tours(0, -1, 'post_title', 'ASC', array($this->get_id()));
                if (count($tour_results) > 0 && $tour_results['total'] > 0) {
                    foreach ($tour_results['results'] as $tour_result) {
                        $ids[] = $tour_result->ID;
                    }
                    update_post_meta($this->get_id(), '_location_tour_ids', $ids);
                }
            }
        }

        return $ids;
    }

    public function get_cruise_count()
    {
        $count = -1;

        if ($this->enable_cruises) {
            global $bookyourtravel_cruise_helper;

            if ($this->is_custom_field_set('_location_cruise_count', false)) {
                $count = $this->get_custom_field('_location_cruise_count', false);
            } else {
                $count = $bookyourtravel_cruise_helper->list_cruises_count(0, -1, 'post_title', 'ASC', array($this->get_id()));
                update_post_meta($this->get_id(), '_location_cruise_count', $count);
            }
        }

        return $count;
    }

    public function get_cruise_ids()
    {
        $ids = array();

        if ($this->enable_cruises) {
            global $bookyourtravel_cruise_helper;

            if ($this->is_custom_field_set('_location_cruise_ids', false)) {
                $ids = unserialize($this->get_custom_field('_location_cruise_ids', false));
            } else {
                $cruise_results = $bookyourtravel_cruise_helper->list_cruises(0, -1, 'post_title', 'ASC', array($this->get_id()));
                if (count($cruise_results) > 0 && $cruise_results['total'] > 0) {
                    foreach ($cruise_results['results'] as $cruise_result) {
                        $ids[] = $cruise_result->ID;
                    }
                    update_post_meta($this->get_id(), '_location_cruise_ids', $ids);
                }
            }
        }

        return $ids;
    }

    public function get_car_rental_count()
    {
        $count = -1;

        if ($this->enable_car_rentals) {
            global $bookyourtravel_car_rental_helper;

            if ($this->is_custom_field_set('_location_car_rental_count', false)) {
                $count = $this->get_custom_field('_location_car_rental_count', false);
            } else {
                $count = $bookyourtravel_car_rental_helper->list_car_rentals_count(0, -1, 'post_title', 'ASC', array($this->get_id()));
                update_post_meta($this->get_id(), '_location_car_rental_count', $count);
            }
        }

        return $count;
    }

    public function get_car_rental_ids()
    {
        $ids = array();

        if ($this->enable_car_rentals) {
            global $bookyourtravel_car_rental_helper;

            if ($this->is_custom_field_set('_location_car_rental_ids', false)) {
                $ids = unserialize($this->get_custom_field('_location_car_rental_ids', false));
            } else {
                $car_rental_results = $bookyourtravel_car_rental_helper->list_car_rentals(0, -1, 'post_title', 'ASC', array($this->get_id()));
                if (count($car_rental_results) > 0 && $car_rental_results['total'] > 0) {
                    foreach ($car_rental_results['results'] as $car_rental_result) {
                        $ids[] = $car_rental_result->ID;
                    }
                    update_post_meta($this->get_id(), '_location_car_rental_ids', $ids);
                }
            }
        }

        return $ids;
    }
}
