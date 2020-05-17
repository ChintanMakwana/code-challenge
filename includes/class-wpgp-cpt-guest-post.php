<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * This class will register the Guest Posts custom post type
 */
class WPGP_CPT_GUEST_POST {
	
	static public function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
	 * WPGP_CPT_GUEST_POST constructor.
	 */
	function __construct(){
		$this->register();	// register the custom post type
	}

	/**
	 * Registers the guest post cpt
	 */
	function register() {

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
		$args = array(
			'label'                 => __( 'Guest Post', 'wpgp' ),
			'labels'                => $labels,
			'supports'              => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
			'hierarchical'          => false,
			'public'                => true,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'menu_position'         => 5,
			'show_in_admin_bar'     => true,
			'show_in_nav_menus'     => true,
			'can_export'            => true,
			'has_archive'           => false,
			'exclude_from_search'   => false,
			'publicly_queryable'    => true,
			'capability_type'       => 'page',
			'show_in_rest'          => true,
		);
		register_post_type( 'wpgp_guest_post', $args );

	}

}