<?php
/**
 * The header for BookYourTravel theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="primary"><div class="wrap">
 *
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */
 
?><!DOCTYPE html>
<!--[if lt IE 9]><script src="<?php echo esc_url( BookYourTravel_Theme_Utils::get_file_uri( '/js/html5shiv.js' ) ); ?>"></script><![endif]-->
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php echo esc_attr( get_bloginfo( 'charset' ) ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
