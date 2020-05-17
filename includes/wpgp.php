<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class WPGP{

	/**
	 * WPGP constructor.
	 */
	function __construct(){
		// Setup custom post type
		$this->setup_post_type();

		$this->shortcode();
	}

	/** 
	 * Create and return an instance of this class.
	 */	
    static public function instance() {
		self::$instance = new self();
        return self::$instance;
    }

    /**
     * Instantiate the class that registers the custom post type.
     */
	function setup_post_type() {        
        new WPGP_CPT_GUEST_POST();
    }

    /** 
     * Instantiate the class that adds shortcodes.
     */
    function shortcode(){
    	new WPGP_SHORTCODES();
    }
}