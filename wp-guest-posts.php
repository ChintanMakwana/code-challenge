<?php
/**
 * Plugin Name: WP Guest Post
 * Description: Guest Post/Page Submission
 * Version: 1.0
 * Author: Chintan Makwana
 * Author URI: https://github.com/ChintanMakwana
 *
 * @package wpgp
 */

// Plugin code starts here.

require_once ABSPATH . 'wp-admin/includes/plugin.php';

/**
 * Main Initialization
 */
class WPGP_Init {

	/**
	 * WPGP_Init constructor.
	 */
	public function __construct() {

		$plugin_data = get_plugin_data( __FILE__ );

		if ( ! defined( 'WPGP_URL' ) ) {
			define( 'WPGP_URL', plugin_dir_url( __FILE__ ) );
			define( 'WPGP_PATH', plugin_dir_path( __FILE__ ) );
			define( 'WPGP_PLUGIN', plugin_basename( __FILE__ ) );
			define( 'WPGP_VERSION', $plugin_data['Version'] );

			/* Use init hook to load up required files */
			add_action( 'init', array( $this, 'required_files' ), 0 );
		}
	}

	/**
	 * Includes required files as needed.
	 */
	public function required_files() {
		require_once WPGP_PATH . 'includes/class-wpgp-cpt-guest-post.php';
		require_once WPGP_PATH . 'includes/class-wpgp-shortcodes.php';
		require_once WPGP_PATH . 'includes/class-wpgp.php';

		$wpgp = new WPGP();
	}
}

$wpgp_init = new WPGP_Init();
