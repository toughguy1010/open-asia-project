<?php
/**
 * BookYourTravel_Entity class
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */
 abstract class BookYourTravel_Entity {

 	/** @var int The entity (post) ID. */
	public $id;

	/** @var object The actual post object. */
	public $post;

 	/** @var string The entity's type (accommodation, tour, car_rental, cruise, flight, location, room_type etc). */
	public $entity_type = null;

	private $meta = null;

	public $entry_type;

	/**
	 * Constructor gets the post object and sets the ID for the loaded entity.
	 *
	 * @access public
	 * @param int|BookYourTravel_Entity|WP_Post $entity Entity ID, post object, or entity object
	 */
	public function __construct( $entity, $entity_type ) {

		if ( is_numeric( $entity ) ) {
			$this->id   = absint( $entity );
			$this->post = get_post( $this->id );
			$this->entity_type = $entity_type;
		} elseif ( $entity instanceof BookYourTravel_Entity ) {
			$this->id   = absint( $entity->id );
			$this->post = $entity;
			$this->entity_type = $entity_type;
		} elseif ( $entity instanceof WP_Post || isset( $entity->ID ) ) {
			$this->id   = absint( $entity->ID );
			$this->post = $entity;
			$this->entity_type = $entity_type;
		}

		$this->entry_type = $this->get_post_type();
		$this->meta = get_post_custom($this->id);
	}

	private function get_prefix($use_prefix) {
		return $use_prefix ? $this->entity_type . '_' : '';
	}

	public function get_status() {
		return $this->post ? $this->post->post_status : '';
	}

	public function get_post_author() {
		return $this->post ? $this->post->post_author : '';
	}

	/**
	 * is_custom_field_set function.
	 *
	 * @access public
	 * @param mixed $key
	 * @return bool
	 */
	public function is_custom_field_set( $key, $use_prefix = true ) {

		$key_to_use = $this->get_prefix($use_prefix) . $key;

		return isset($this->meta[$key_to_use]);
	}

	/**
	 * get_custom_field function.
	 *
	 * @access public
	 * @param mixed $key
	 * @return mixed
	 */
	public function get_custom_field( $key, $use_prefix = true, $use_base_id = false ) {

		$key_to_use = $this->get_prefix($use_prefix) . $key;

		if ($use_base_id) {
			$post_id = $this->get_base_id();
			$value = get_post_meta($post_id, $key_to_use, true);
		} else {
			// Get values or default if not set
			if ( in_array( $key, array( 'images' ) ) ) {
				$values = isset($this->meta[$key_to_use]) ? $this->meta[$key_to_use][0]: '';
				$value = unserialize($values);
			} elseif ( in_array( $key, array( 'is_price_per_person', 'disabled_room_types', 'count_children_stay_free', 'is_price_per_group', 'price_per_day' ) )   ) {
				$value = isset($this->meta[$key_to_use]) ? $this->meta[$key_to_use][0] : 0;
			} else {
				$value = isset($this->meta[$key_to_use]) ? $this->meta[$key_to_use][0] : null;
			}
		}

		return $value;
	}

	/**
	 * get_custom_field_image_uri function.
	 *
	 * @access public
	 * @param mixed $image_key
	 * @param mixed $image_size
	 * @return mixed
	 */
    public function get_custom_field_image_uri( $image_key, $image_size ) {
		$image_meta = $this->get_custom_field( $image_key );
		if ($image_meta) {
			$temp_image = wp_get_attachment_image_src($image_meta, $image_size);
			if (is_array($temp_image) && count($temp_image) > 0)
				return $temp_image[0];
		}
		return '';
    }

	/**
	 * Get the entity's entity type
	 * @return string
	 */
	public function get_entity_type() {
		return $this->entity_type;
	}

	/**
	 * Get the entity's post data.
	 *
	 * @access public
	 * @return object
	 */
	public function get_post_data() {
		return $this->post;
	}

	public function get_post_type() {
		return isset($this->post) ? $this->post->post_type : '';
	}

	public function get_entry_type() {
		return isset($this->post) ? $this->post->post_type : '';
	}

	/**
	 * Wrapper for get_permalink
	 * @return string
	 */
	public function get_permalink() {
		return apply_filters( 'bookyourtravel_entity_permalink', $this->post ? get_permalink( $this->id ) : '', $this );
	}

	/**
	 * Checks the entity type.
	 *
	 * @access public
	 * @param mixed $type Array or string of types
	 * @return bool
	 */
	public function is_type( $type ) {
		return ( $this->entity_type == $type || ( is_array( $type ) && in_array( $this->entity_type, $type ) ) ) ? true : false;
	}

	/**
	 * Returns whether or not the entity post exists.
	 *
	 * @access public
	 * @return bool
	 */
	public function exists() {
		return empty( $this->post ) ? false : true;
	}

	/**
	 * Get the ID of the post.
	 *
	 * @access public
	 * @return int
	 */
	public function get_id() {
		return apply_filters( 'bookyourtravel_entity_id', $this->post ? $this->id : null, $this );
	}

	/**
	 * Get the base ID of the post (ID of post in default language)
	 *
	 * @access public
	 * @return int
	 */
	public function get_base_id() {
		return apply_filters( 'bookyourtravel_entity_base_id', $this->post ? BookYourTravel_Theme_Utils::get_default_language_post_id($this->post->ID, $this->entity_type ) : null, $this );
	}

	/**
	 * Get the title of the post.
	 *
	 * @access public
	 * @return string
	 */
	public function get_title() {
		$title = '';
		if ($this->post) {
			$title = $this->post->post_title;
			$title = apply_filters('the_title', $title);
		}
		return apply_filters( 'bookyourtravel_entity_title', $title, $this );
	}

	public function get_content($skip_filters = false) {
		$content = $this->post->post_content;
		$content = apply_filters('the_content', $content);
		return apply_filters( 'bookyourtravel_entity_content', $content, $this );
	}

	public function content_and_description_match() {
		if ($this->post) {
			$description = $this->get_description();
			$content = $this->get_content();

			return $content === $description;
		}
		return false;
	}

	/**
	 * Get the description of the post.
	 *
	 * @access public
	 * @return string
	 */
	public function get_description() {
		$description = '';
		if ($this->post) {
			$content = $this->get_content();			

			if ($this->post->post_type == 'accommodation' ||
				$this->post->post_type == 'tour' ||
				$this->post->post_type == 'cruise' ||
				$this->post->post_type == 'car_rental' ||
				$this->post->post_type == 'location') {
				$description = $this->get_custom_field('general_description');
				$short_description = $this->get_custom_field('short_description');

				if (empty($description)) {
					$description = empty($short_description) ? $content : $short_description;
				}
			} else {
				$description = $content;
			}
		}
		return apply_filters( 'bookyourtravel_entity_description', $description, $this );
	}

	/**
	 * Get the excerpt of the post.
	 *
	 * @access public
	 * @return string
	 */
	public function get_excerpt() {
		$description = '';
		if ($this->post) {
			$description = $this->post->post_content;
			$description = apply_filters('the_excerpt', $description);

			$excerpt_length = apply_filters('excerpt_length', 55);
			$excerpt_more = apply_filters('excerpt_more', ' ');
			$description = wp_trim_words( $description, $excerpt_length, $excerpt_more );
		}
		return apply_filters( 'bookyourtravel_entity_excerpt', $description, $this );
	}

	/**
	 * Get the featured image of the post.
	 *
	 * @access public
	 * @return string
	 */
	public function get_main_image($image_size = 'large') {
		$featured_image = '';
		if ($this->post && has_post_thumbnail( $this->post->ID )) {
			$featured_image = wp_get_attachment_image_src( get_post_thumbnail_id( $this->post->ID ), $image_size );
			$featured_image = is_array($featured_image) && count($featured_image) > 0 ? $featured_image[0] : '';
		}
		return apply_filters( 'bookyourtravel_entity_featured_image', $featured_image, $this );
	}

	/**
	 * Get images of the entity
	 *
	 * @access public
	 * @return array
	 */
    public function get_images() {
		return $this->get_custom_field( 'images' );
    }

	/**
	 * Get the parent of the post.
	 *
	 * @access public
	 * @return int
	 */
	public function get_parent() {
		return apply_filters( 'bookyourtravel_entity_parent', absint( $this->post->post_parent ), $this );
	}

	public function get_displayed_featured_element() {
		$featured_element = $this->get_custom_field( 'displayed_featured_element' );
		if (empty($featured_element))
			$featured_element = 'gallery';
		return apply_filters( 'bookyourtravel_entity_featured_element', $featured_element, $this );
	}

	public function get_ribbon_text() {
		$ribbon_text = $this->get_custom_field( 'ribbon_text' );
		$ribbon_text = trim($ribbon_text);
		return apply_filters( 'bookyourtravel_entity_ribbon_text', $ribbon_text, $this );
    }

	function _get_cached_price($min_price_meta_key, $min_price_check_meta_key) {
		$price = 0;
		$last_cache_minutes = 0;

		if ($this->is_custom_field_set($min_price_check_meta_key, false)) {
			$last_cache_seconds = intval($this->get_custom_field($min_price_check_meta_key, false));
			$current_seconds = time();
			if ($last_cache_seconds > 0) {
				$last_cache_minutes = ($current_seconds - $last_cache_seconds) / (60);
			}
		}

		if ($last_cache_minutes > 0 && $last_cache_minutes <= 5) {
			$price = floatval($this->get_custom_field($min_price_meta_key, false));
		}

		return $price;
	}

	function get_static_from_price() {
		return $this->get_custom_field('static_from_price');
	}
 }