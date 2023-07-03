<?php
/**
 * The custom part of the header for BookYourTravel theme
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */

global $bookyourtravel_theme_globals;

$disable_theme_header = $bookyourtravel_theme_globals->get_disable_theme_header();
?>
<?php if (!$bookyourtravel_theme_globals->hide_loading_animation()) { ?>
    <div class="page-spinner"><div></div></div>
<?php } ?>
<div class="page-wrap">
<?php 
    if (!$disable_theme_header) {
        get_template_part('includes/parts/header/header', $bookyourtravel_theme_globals->get_header_layout());
    }
    if (is_page_template('byt_home.php')) {
        echo '<div class="slider-wrap">';
        get_template_part('includes/parts/header/home-page', 'header');
        echo '</div>';
    }    
    get_sidebar('hero');    
?>
<div class="main">		
	<div class="wrap">