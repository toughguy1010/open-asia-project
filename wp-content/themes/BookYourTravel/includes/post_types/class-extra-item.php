<?php
/**
 * BookYourTravel_Extra_Item class
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */
 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class BookYourTravel_Extra_Item extends BookYourTravel_Entity
{
    public function __construct( $entity ) {
		parent::__construct( $entity, 'extra_item' );
    }
	
}