<?php
/*
Plugin Name: WP Guest Post 
Description: Guest Post/Page Submission
Version: 1.0
Author: Chintan Makwana
Author URI: https://creolestudios.com
*/
require_once( ABSPATH.'wp-admin/includes/plugin.php' );

/**
 * Main Initialization 
 */
class WPGP_Init{
	
	/**
	 * WPGP_Init constructor.
	 */
	public function __construct(){

		$plugin_data = get_plugin_data( __FILE__ );

		if ( ! defined( 'wpgp_url' ) ) {
			define( 'wpgp_url', plugin_dir_url( __FILE__ ) );
			define( 'wpgp_path', plugin_dir_path( __FILE__ ) );
			define( 'wpgp_plugin', plugin_basename( __FILE__ ) );
			define( 'wpgp_version', $plugin_data['Version'] );			

			/* Activation and deactivation hooks */
			register_activation_hook( __FILE__, array( $this, 'activate' ) );
			register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

			/* Use init hook to load up required files */
			add_action( 'init', array( $this, 'required_files'), 0 );
		}
	}

	/**
	 * Includes required files as needed.
	 */
	function required_files(){
		require_once wpgp_path . 'includes/class-wpgp-cpt-guest-post.php';
		require_once wpgp_path . 'includes/class-wpgp-shortcode.php';
		require_once wpgp_path . 'includes/wpgp.php';

		$wpgp = new WPGP();
	}
}

$wpgp_init = new WPGP_Init();