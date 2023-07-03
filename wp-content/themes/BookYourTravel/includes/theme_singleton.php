<?php
/**
 * BookYourTravel_BaseSingleton class
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

//
// http://scotty-t.com/2012/07/09/wp-you-oop/
//
abstract class BookYourTravel_BaseSingleton {
    private static $instance = array();
    protected function __construct() {}
    
	public static function get_instance() {
        $c = get_called_class();
        if ( !isset( self::$instance[$c] ) ) {
            self::$instance[$c] = new $c();
            self::$instance[$c]->init();
        }

        return self::$instance[$c];
    }

    abstract public function init();
}