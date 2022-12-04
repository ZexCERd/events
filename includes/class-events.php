<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://https://github.com/ZexCERd
 * @since      1.0.0
 *
 * @package    Events
 * @subpackage Events/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Events
 * @subpackage Events/includes
 * @author     Ankit manandhar <ankit.sayami@gmail.com>
 */
class Events {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Events_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'EVENTS_VERSION' ) ) {
			$this->version = EVENTS_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'events';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

		/***
		 * This action is used to register the necessary custom post type over init .
		 */
		add_action('init', array( $this, 'register_custom_post_types' ));

		/**
		 * This action is used to show the post type in the WordPress menu
		 */
		add_action('init', array( $this, 'register_custom_taxonomy' ));

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Events_Loader. Orchestrates the hooks of the plugin.
	 * - Events_i18n. Defines internationalization functionality.
	 * - Events_Admin. Defines all hooks for the admin area.
	 * - Events_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-events-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-events-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-events-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-events-public.php';

		$this->loader = new Events_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Events_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Events_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Events_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Events_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Events_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Creates Custom Post Type Events
	 *
	 * @since     1.0.0
	 * @return   null     The version number of the plugin.
	 */
	public function register_custom_post_types(){
		
		$labels = array(
			'name'                  => _x( 'Events', 'Post type general name', 'textdomain' ),
			'singular_name'         => _x( 'Event', 'Post type singular name', 'textdomain' ),
			'menu_name'             => _x( 'Events', 'Admin Menu text', 'textdomain' ),
			'name_admin_bar'        => _x( 'Event', 'Add New on Toolbar', 'textdomain' ),
			'add_new'               => __( 'Add New', 'textdomain' ),
			'add_new_item'          => __( 'Add New Event', 'textdomain' ),
			'new_item'              => __( 'New Event', 'textdomain' ),
			'edit_item'             => __( 'Edit Event', 'textdomain' ),
			'view_item'             => __( 'View Event', 'textdomain' ),
			'all_items'             => __( 'All Events', 'textdomain' ),
			'search_items'          => __( 'Search Events', 'textdomain' ),
			'parent_item_colon'     => __( 'Parent Events:', 'textdomain' ),
			'not_found'             => __( 'No Events found.', 'textdomain' ),
			'not_found_in_trash'    => __( 'No Events found in Trash.', 'textdomain' ),
			'featured_image'        => _x( 'Event Cover Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'textdomain' ),
			'set_featured_image'    => _x( 'Set cover image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'textdomain' ),
			'remove_featured_image' => _x( 'Remove cover image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'textdomain' ),
			'use_featured_image'    => _x( 'Use as cover image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'textdomain' ),
			'archives'              => _x( 'Event archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'textdomain' ),
			'insert_into_item'      => _x( 'Insert into event', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'textdomain' ),
			'uploaded_to_this_item' => _x( 'Uploaded to this event', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'textdomain' ),
			'filter_items_list'     => _x( 'Filter Events list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'textdomain' ),
			'items_list_navigation' => _x( 'Events list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'textdomain' ),
			'items_list'            => _x( 'Events list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'textdomain' ),
		);
		$args = array(
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'show_in_rest'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'event' ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => true,
			'menu_position'      => null,
			'taxonomies'		 => array( 'post_tag' ),
			'supports'           => array( 'title' , 'thumbnail', 'custom_fields', 'author', 'revision', 'editor', 'excerpt' ),
		);
		register_post_type( 'event', $args );
	}

	/**
	 * Creates Custom Taxonomy event_type
	 *
	 * @since     1.0.0
	 * @return    null    The version number of the plugin.
	 */
	public function register_custom_taxonomy(){
		$labels = array(
			'name'              => _x( 'Event Types', 'taxonomy general name', 'textdomain' ),
			'singular_name'     => _x( 'Event Type', 'taxonomy singular name', 'textdomain' ),
			'search_items'      => __( 'Search Event Types', 'textdomain' ),
			'all_items'         => __( 'All Event Types', 'textdomain' ),
			'parent_item'       => __( 'Parent Event Type', 'textdomain' ),
			'parent_item_colon' => __( 'Parent Event Type:', 'textdomain' ),
			'edit_item'         => __( 'Edit Event Type', 'textdomain' ),
			'update_item'       => __( 'Update Event Type', 'textdomain' ),
			'add_new_item'      => __( 'Add New Event Type', 'textdomain' ),
			'new_item_name'     => __( 'New Event Type Name', 'textdomain' ),
			'menu_name'         => __( 'Event Type', 'textdomain' ),
		);
	
		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'event_type' ),
		);
		register_taxonomy( 'event_type', array( 'event' ), $args );
	}
}
