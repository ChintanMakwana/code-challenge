<?php
/**
 * This class will instanciate the custom post registration
 *
 * @package wpgp
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * WPGP Class
 */
class WPGP {

	/**
	 * WPGP constructor.
	 */
	public function __construct() {
		// Setup custom post type.
		$this->setup_post_type();
		$this->shortcode();
	}

	/**
	 * Create and return an instance of this class.
	 */
	public static function instance() {
		self::$instance = new self();
		return self::$instance;
	}

	/**
	 * Instantiate the class that registers the custom post type.
	 */
	public function setup_post_type() {
		new WPGP_CPT_GUEST_POST();
	}

	/**
	 * Instantiate the class that adds shortcodes.
	 */
	public function shortcode() {
		new WPGP_SHORTCODES();
	}
}
