<?php
/**
 * BookYourTravel_Theme_Controls class
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

class BookYourTravel_Theme_Controls {

	public static function the_breadcrumbs() {

		global $bookyourtravel_theme_globals;

		ob_start();

		if (is_home()) {

		} else {

			wp_reset_postdata();
			wp_reset_query();

			echo '<!--breadcrumbs--><nav class="breadcrumbs">';
			echo '<ul>';
			echo '<li><a href="' . home_url('/') . '" title="' . esc_html__('Home', 'bookyourtravel') . '">' . esc_html__('Home', 'bookyourtravel') . '</a></li>';
			if (is_single()) {

				global $post;

				$post_type = get_post_type();

				if($post_type != 'post') {

					$post_type_object = get_post_type_object($post_type);

					$post_type_archive_url = '';
					if (isset($post_type_object->rewrite) && isset($post_type_object->rewrite['slug'])) {
						$post_type_archive_url = home_url('/') . $post_type_object->rewrite['slug'];
					}

					echo '<li><a href="' . $post_type_archive_url . '" title="' . $post_type_object->labels->name . '">' . $post_type_object->labels->name . '</a></li>';
				}

				if( $post->post_parent ){

					// If child page, get parents
					$ancestors = get_post_ancestors( $post->ID );

					// Get parents in the right order
					$ancestors = array_reverse($ancestors);

					// Parent loop
					if ( !isset( $parents ) ) $parents = null;
					foreach ( $ancestors as $ancestor ) {
						$parents .= '<li><a href="' . get_permalink($ancestor) . '" title="' . get_the_title($ancestor) . '">' . get_the_title($ancestor) . '</a></li>';
					}

					// Display parent pages
					echo $parents;
				}

				echo "<li>";
				echo the_title();
				echo "</li>";

			} else if (is_page()) {

				global $post;
				if( $post->post_parent ){

					// If child page, get parents
					$ancestors = get_post_ancestors( $post->ID );

					// Get parents in the right order
					$ancestors = array_reverse($ancestors);

					// Parent loop
					if ( !isset( $parents ) ) $parents = null;
					foreach ( $ancestors as $ancestor ) {
						$parents .= '<li><a href="' . get_permalink($ancestor) . '" title="' . get_the_title($ancestor) . '">' . get_the_title($ancestor) . '</a></li>';
					}

					// Display parent pages
					echo $parents;
				}

				echo "<li>";
				echo the_title();
				echo "</li>";
			} else if (is_category()) {
				echo "<li>";
				the_category('');
				echo "</li>";
			} else if (is_archive()) {
				echo "<li>";
				the_archive_title('');
				echo "</li>";
			} elseif (is_404()) {
				echo "<li>" . esc_html__('Error 404 - Page not found', 'bookyourtravel') . "</li>";
			} elseif (is_search()) {
				echo "<li>";
				echo esc_html__('Search results for: ', 'bookyourtravel');
				echo '"<em>';
				echo get_search_query();
				echo '</em>"';
				echo "</li>";
			}

			echo '</ul>';
			echo '</nav><!--//breadcrumbs-->';
		}

		$breadcrumbs = ob_get_contents();
		ob_end_clean();
		$hide_breadcrumbs = $bookyourtravel_theme_globals->get_hide_breadcrumbs();
		if (!$hide_breadcrumbs) {
			echo apply_filters( 'bookyourtravel_breadcrumbs', $breadcrumbs );
		}

		wp_reset_postdata();
	}

	public static function the_entity_title_start($title, $permalink, $external = false) {
		echo '<h3>';
		if (strlen($permalink) > 0) {
            echo '<a href="' . esc_url($permalink) . '"';
            if ($external)
                echo ' target="_blank" ';
            echo ' title="' . esc_attr($title) . '" class = "post_title">';
		}
	}

	public static function the_entity_title_end($permalink) {
		if (strlen($permalink) > 0) {
			echo '</a>';
		}
		echo '</h3>';
	}

	public static function the_entity_title_middle($title, $entity_status) {
		echo $title;
		if ($entity_status == 'draft' || $entity_status == 'private')
			echo '<span class="private">' . esc_html__('Pending', 'bookyourtravel') . '</span>';
	}


	public static function get_entity_ribbon_html($text, $extra_class = '') {
		$text = trim($text);
		if (!empty($text)) {
			return sprintf("<div class='promo-ribbon %s'><span>%s</span></div>", $extra_class, $text);
		}
		return "";
	}

	public static function the_entity_title($title, $permalink, $entity_status, $external = false) {
		self::the_entity_title_start($title, $permalink, $external);
		self::the_entity_title_middle($title, $entity_status);
		self::the_entity_title_end($permalink);
	}

	public static function the_entity_description($description) {
		echo sprintf("<div class='description'>%s</div>", $description);
	}
	
	public static function the_entity_figure_start($title, $permalink, $external = false) {
		if (strlen($permalink) > 0) {
            echo '<a href="' . esc_url($permalink) . '"';
            if ($external)
                echo ' target="_blank" ';
            echo ' title="' . esc_attr($title) . '">';
		}
		echo "<figure>";
	}

	public static function the_entity_figure_end($permalink) {
		echo "</figure>";
		if (strlen($permalink) > 0) {
			echo '</a>';
		}
	}

	public static function the_entity_figure_middle($thumbnail_html, $ribbon_text = "") {
		$ribbon_html = self::get_entity_ribbon_html($ribbon_text);
		echo $ribbon_html;
		echo $thumbnail_html;
	}

	public static function the_entity_figure($title, $permalink, $thumbnail_html, $ribbon_text = "", $external = false) {
		self::the_entity_figure_start($title, $permalink, $external);
		self::the_entity_figure_middle($thumbnail_html, $ribbon_text);
		self::the_entity_figure_end($permalink);
	}

	public static function the_entity_address($address) {
		if (!empty($address)) {?>
		<span class="address">
			<?php echo $address; ?>
		</span>
		<?php
		}
	}

	public static function the_entity_tags($tags, $taxonomy_name) {
		if (count($tags) > 0) {
		?>
		<div class="tags">
			<ul>
				<?php
					foreach ($tags as $tag) {
						$tag_link = get_term_link( (int)$tag->term_id, $taxonomy_name );
						echo '<li><a href="' . $tag_link . '" class = "post_tags">' . $tag->name . '</a></li>';
					}
				?>
			</ul>
		</div>
		<?php
		}
	}

	public static function the_top_review($entity_id) {
		global $bookyourtravel_review_helper, $bookyourtravel_theme_globals;

		if ($bookyourtravel_theme_globals->enable_reviews()) {
			$all_reviews_query = $bookyourtravel_review_helper->list_reviews($entity_id);
			if ($all_reviews_query->have_posts()) {
				while ($all_reviews_query->have_posts()) {
					$all_reviews_query->the_post();
					global $post;
					$likes = get_post_meta($post->ID, 'review_likes', true);
					$author = get_the_author();
				?>
					<article class="testimonials">
						<blockquote><?php echo $likes; ?></blockquote>
						<span class="name"><?php echo $author; ?></span>
					</article>
			<?php
					break;
				}
			}
			wp_reset_postdata();
		}
	}

	public static function the_entity_stars($star_count) {
		global $bookyourtravel_theme_globals;

		if (!$bookyourtravel_theme_globals->disable_star_count('accommodation') && $star_count > 0) {
		?>
		<span class="stars">
		<?php
		for ( $i = 0; $i < $star_count; $i++ ) { ?>
			<i class="material-icons">&#xE838;</i>
		<?php } ?>
		</span>
		<?php
		}
	}

	public static function the_entity_price($entity_price, $price_label, $container_style = "") {
		global $bookyourtravel_theme_globals, $price_decimal_places, $default_currency_symbol, $show_currency_symbol_after;
		?>
		<div class="item_price" style="<?php echo esc_attr($container_style); ?>">
			<?php echo $price_label; ?>
			<?php BookYourTravel_Theme_Controls::the_entity_price_inner($entity_price); ?>
		</div>
		<?php
	}

	public static function the_entity_price_inner($entity_price) {
		global $bookyourtravel_theme_globals, $price_decimal_places, $default_currency_symbol, $show_currency_symbol_after;
		$entity_price = BookYourTravel_Theme_Utils::get_price_in_current_currency($entity_price);
		?>
			<span class="price">
				<em>
				<?php if (!$show_currency_symbol_after) { ?>
				<span class="curr"><?php echo esc_html($default_currency_symbol); ?></span>
				<span class="amount"><?php echo isset($entity_price) && $entity_price > 0 ? number_format_i18n( $entity_price, $price_decimal_places ) : ''; ?></span>
				<?php } else { ?>
				<span class="amount"><?php echo isset($entity_price) && $entity_price > 0 ? number_format_i18n( $entity_price, $price_decimal_places ) : ''; ?></span>
				<span class="curr"><?php echo esc_html($default_currency_symbol); ?></span>
				<?php } ?>
				</em>
			</span>
		<?php
	}

	public static function the_entity_reviews_score($entity_id, $review_score) {

		global $bookyourtravel_review_helper, $bookyourtravel_theme_globals;

		if ($bookyourtravel_theme_globals->enable_reviews() && $entity_id > 0 && $review_score > 0) {

			$reviews_total = $bookyourtravel_review_helper->get_reviews_count($entity_id);

			$score_out_of_10 = 0;
			if ($reviews_total > 0) {
				$score_out_of_10 = round($review_score * 10);
			}

			if ($score_out_of_10 > 0) {	?>
			<span class="rating">
				<?php echo sprintf("%d / 10", $score_out_of_10); ?>
			</span>
			<?php
			}
		}
	}

	public static function the_pager_outer($max_num_pages) {
		if ($max_num_pages > 1) {
			?>
			<nav class="page-navigation bottom-nav">
				<!--pager-->
				<div class="pager">
					<?php BookYourTravel_Theme_Controls::the_pager($max_num_pages, true); ?>
				</div>
			</nav>
			<?php
		}
	}

	/**
	 * Function that renders link button in the form of
	 * <a href="$href" class="$link_css_class" id="$link_id" title="$text">$text</a>
	 */
	public static function the_link_button($href, $link_css_class, $link_id, $text, $echo = true, $external = false, $entity_id = 0)  {
		$ret_val = sprintf("<a href='%s' class='%s' ", esc_url($href), esc_attr($link_css_class));
        if (!empty($link_id)) {
            $ret_val .= sprintf(" id='%s' ", esc_attr($link_id));
        }

        if ($external) {
            $ret_val .= " target='_blank' ";
        }

        if ($entity_id > 0) {
            $ret_val .= sprintf(" data-id='%d' ", esc_attr($entity_id));
        }

		$ret_val .= sprintf(" title='%s'>%s</a>", esc_attr($text), esc_html($text));

		$ret_val = apply_filters('bookyourtravel_render_link_button', $ret_val, $href, $link_css_class, $link_id, $text);
		if ($echo)
			echo $ret_val;
		else
			return $ret_val;
	}

	/**
	 * Function that renders submit button in the form of
	 * <input type="submit" value="$text" id="$submit_id" name="$submit_id" class="$submit_css_class" />
	 */
	public static function the_submit_button($submit_css_class, $submit_id, $text, $echo = true)  {
		$ret_val = sprintf("<input type='submit' class='%s' id='%s' name='%s' value='%s' />", esc_attr($submit_css_class), esc_attr($submit_id), esc_attr($submit_id), esc_attr($text));
		$ret_val = apply_filters('bookyourtravel_render_link_button', $ret_val, $submit_css_class, $submit_id, $text);
		if ($echo)
			echo $ret_val;
		else
			return $ret_val;
	}

	public static function the_pager($max_num_pages, $custom_bookyourtravel_paged = false, $q_args = array()) {

		$pattern = '#(www\.|https?:\/\/) {1}[a-zA-Z0-9\-]{2,254}\.[a-zA-Z0-9]{2,20}[a-zA-Z0-9.?&=_/]*#i';

		$big = 999999999; // need an unlikely integer
		$pagenum_link = str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) );
		$url_parts    = explode( '?', $pagenum_link );
		$pagenum_link = trailingslashit( $url_parts[0] );

		$pager_settings = array(
			'base' => $pagenum_link,
			'total' => $max_num_pages,
			'prev_text'    => esc_html__('←', 'bookyourtravel'),
			'next_text'    => esc_html__('→', 'bookyourtravel'),
			'type'		   => 'array'
		);

		if ( get_query_var('paged-byt') ) {
			$paged = get_query_var('paged-byt');
		} else if ( get_query_var('paged') ) {
			$paged = get_query_var('paged');
		} else if ( get_query_var('page') ) {
			$paged = get_query_var('page');
		} else {
			$paged = 1;
		}

		if ($custom_bookyourtravel_paged) {
			$pager_settings['format'] = '?paged-byt=%#%';
		} else {
			$pager_settings['format'] = '?paged=%#%';
		}

		$pager_settings['current'] = max( 1, $paged );

		$pager_settings["add_args"] = $q_args;

		$pager_links = paginate_links( $pager_settings );

		$count_links = is_array($pager_links) ? count($pager_links) : 0;
		if ($count_links > 0) {

			$first_link = $pager_links[0];
			$last_link = $first_link;
			preg_match_all($pattern, $first_link, $matches, PREG_PATTERN_ORDER);
			echo '<span style ="display: none"><a href="' . get_pagenum_link(1) . '">' . esc_html__('First page', 'bookyourtravel') . '</a></span>';
			for ($i=0; $i<$count_links; $i++) {
				$pager_link = $pager_links[$i];
				if (!BookYourTravel_Theme_Utils::string_contains($pager_link, 'current'))
					echo '<span>' . $pager_link . '</span>';
				else
					echo $pager_link;
				$last_link = $pager_link;
			}
			preg_match_all($pattern, $last_link, $matches, PREG_PATTERN_ORDER);
			echo '<span style ="display: none" ><a href="' . get_pagenum_link($max_num_pages) . '">' . esc_html__('Last page', 'bookyourtravel') . '</a></span>';
		}
	}

	public static function the_price_ribbon($location_id, $location_permalink, $show_accommodation_prices_in_location_items, $show_car_rental_prices_in_location_items, $show_cruise_prices_in_location_items, $show_tour_prices_in_location_items) {

        $first_half_post_type = '';
        $second_half_post_type = '';
        $first_half_data_attribute = "";
        $second_half_data_attribute = "";

        if ($show_accommodation_prices_in_location_items) {
            $first_half_post_type = "accommodation";
            $first_half_data_attribute = "data-location-id='" . $location_id . "' data-min-price-type='accommodation'";
        }

        if ($show_car_rental_prices_in_location_items) {
            if (empty($first_half_post_type)) {
                $first_half_post_type = "car_rental";
                $first_half_data_attribute = "data-location-id='" . $location_id . "' data-min-price-type='car_rental'";
            } else if (empty($second_half_post_type)) {
                $second_half_post_type = "car_rental";
                $second_half_data_attribute = "data-location-id='" . $location_id . "' data-min-price-type='car_rental'";
            }
        }

        if ($show_cruise_prices_in_location_items) {
            if (empty($first_half_post_type)) {
                $first_half_post_type = "cruise";
                $first_half_data_attribute = "data-location-id='" . $location_id . "' data-min-price-type='cruise'";
            } else if (empty($second_half_post_type)) {
                $second_half_post_type = "cruise";
                $second_half_data_attribute = "data-location-id='" . $location_id . "' data-min-price-type='cruise'";
            }
        }

        if ($show_tour_prices_in_location_items) {
            if (empty($first_half_post_type)) {
                $first_half_post_type = "tour";
                $first_half_data_attribute = "data-location-id='" . $location_id . "' data-min-price-type='tour'";
            } else if (empty($second_half_post_type)) {
                $second_half_post_type = "tour";
                $second_half_data_attribute = "data-location-id='" . $location_id . "' data-min-price-type='tour'";
            }
        }

        if (!empty($first_half_post_type) || !empty($second_half_post_type)) {
            echo '<div class="ribbon" style="display:none">';

            if (!empty($first_half_post_type)) {
                echo sprintf("<div class='half %s' %s style='display:none'>", $first_half_post_type, $first_half_data_attribute);
                echo sprintf("<a href='%s' title='" . esc_attr__('View all', 'bookyourtravel') . "'>", $location_permalink);
                echo "<span class='small'>" . esc_html__("from", "bookyourtravel") . "</span>";
                BookYourTravel_Theme_Controls::the_entity_price_inner('0');
                echo "</a>";
                echo "</div><!--half-->";
            }

            if (!empty($second_half_post_type)) {
                echo sprintf("<div class='half %s' %s style='display:none'>", $second_half_post_type, $second_half_data_attribute);
                echo sprintf("<a href='%s' title='" . esc_attr__('View all', 'bookyourtravel') . "'>", $location_permalink);
                echo "<span class='small'>" . esc_html__("from", "bookyourtravel") . "</span>";
                BookYourTravel_Theme_Controls::the_entity_price_inner('0');
                echo "</a>";
                echo "</div><!--half-->";
            }

			echo '</div><!--ribbon-->';
		}
	}
}

class BookYourTravel_Menu_With_Description extends Walker_Nav_Menu {
    function start_el(&$output, $item, $depth = 0, $args = Array(), $id = 0) {
		global $wp_query;

        $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

        $item_output = $class_names = $value = '';

        $classes = !isset($item->classes) || empty( $item->classes ) ? array() : (array) $item->classes;

        $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) );
        $class_names = ' class="' . esc_attr( $class_names ) . '"';

		if (isset($item->ID)) {
			$output .= $indent . '<li id="menu-item-'. $item->ID . '"' . $value . $class_names .'>';
		}

        $attributes = ! empty( $item->attr_title ) ? ' title="' . esc_attr( $item->attr_title ) .'"' : '';
        $attributes .= ! empty( $item->target ) ? ' target="' . esc_attr( $item->target ) .'"' : '';
        $attributes .= ! empty( $item->xfn ) ? ' rel="' . esc_attr( $item->xfn ) .'"' : '';
        $attributes .= ! empty( $item->url ) ? ' href="' . esc_attr( $item->url ) .'"' : '';

		if (isset($args)) {
			if (isset($args->before)) {
				$item_output = $args->before;
			}
			$item_output .= '<a'. $attributes .'>';
			if (isset($args->link_before)) {
				$item_output .= $args->link_before;
			}
			$item_output .= apply_filters( 'the_title', $item->title, $item->ID );
			if (isset($args->link_after)) {
				$item_output .= $args->link_after;
			}
			if (isset($args->description)) {
				$item_output .= '<span class="sub">' . $item->description . '</span>';
			}
			$item_output .= '</a>';
			if (isset($args->after)) {
				$item_output .= $args->after;
			}
		}

        $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
    }
}
