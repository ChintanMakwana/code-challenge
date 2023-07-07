<?php
/**
 * This class will register the Guest Posts custom post type
 *
 * @package wpgp
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WPGP_CPT_GUEST_POST class
 */
class WPGP_CPT_GUEST_POST {
	/**
	 * WPGP_CPT_GUEST_POST instance.
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * WPGP_CPT_GUEST_POST constructor.
	 */
	public function __construct() {
		$this->register();
	}

	/**
	 * Registers the guest custom post type
	 */
	public function register() {

		$labels = array(
			'name'                  => _x( 'Guest Posts', 'Post Type General Name', 'wpgp' ),
			'singular_name'         => _x( 'Guest Post', 'Post Type Singular Name', 'wpgp' ),
			'menu_name'             => __( 'Guest Posts', 'wpgp' ),
			'name_admin_bar'        => __( 'Guest Post', 'wpgp' ),
			'all_items'             => __( 'All Guest Posts', 'wpgp' ),
			'add_new'               => __( 'Add New Guest Post', 'wpgp' ),
			'new_item'              => __( 'New Guest Post', 'wpgp' ),
			'edit_item'             => __( 'Edit Guest Post', 'wpgp' ),
			'update_item'           => __( 'Update Guest Post', 'wpgp' ),
			'view_item'             => __( 'View Guest Post', 'wpgp' ),
			'view_items'            => __( 'View Guest Posts', 'wpgp' ),
			'search_items'          => __( 'Search Guest Post', 'wpgp' ),
			'not_found'             => __( 'Not found', 'wpgp' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'wpgp' ),
			'items_list'            => __( 'Guest Posts list', 'wpgp' ),
			'items_list_navigation' => __( 'Guest Posts list navigation', 'wpgp' ),
			'filter_items_list'     => __( 'Filter Guest Posts list', 'wpgp' ),
		);
		$args   = array(
			'label'               => __( 'Guest Post', 'wpgp' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_position'       => 5,
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => true,
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'capability_type'     => 'page',
			'show_in_rest'        => true,
		);
		register_post_type( 'wpgp_guest_post', $args );

	}

}
