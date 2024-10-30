<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.luminate.ai/
 * @since      1.0.0
 *
 * @package    Lumaisd
 * @subpackage Lumaisd/includes
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
 * @package    Lumaisd
 * @subpackage Lumaisd/includes
 * @author     Luminate AI <faham@predictly.co>
 */
class Lumaisd {
	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Lumaisd_Loader    $loader    Maintains and registers all hooks for the plugin.
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
		if ( defined( 'LUMAISD_VERSION' ) ) {
			$this->version = LUMAISD_VERSION;
		} else {
			$this->version = '1.2.1';
		}
		$this->plugin_name = 'LUMAISD_NAME';
		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}
	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Lumaisd_Loader. Orchestrates the hooks of the plugin.
	 * - Lumaisd_i18n. Defines internationalization functionality.
	 * - Lumaisd_Admin. Defines all hooks for the admin area.
	 * - Lumaisd_Public. Defines all hooks for the public side of the site.
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
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-lumaisd-loader.php';
		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-lumaisd-i18n.php';
		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-lumaisd-admin.php';
		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-lumaisd-public.php';
		$this->loader = new Lumaisd_Loader();
	}
	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Lumaisd_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {
		$plugin_i18n = new Lumaisd_i18n();
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
		$plugin_admin = new Lumaisd_Admin( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'add_meta_boxes', $plugin_admin, 'lumaisd_register_admin_metaboxes' );
		$this->loader->add_action( 'init', $plugin_admin, 'lumaisd_register_post_type' );
		$this->loader->add_action( 'save_post', $plugin_admin, 'lumaisd_save_vocabulary', 10, 2 );
		$this->loader->add_action( 'wp_ajax_lumaisd_save_update_post', $plugin_admin, 'lumaisd_save_update_post');
		$this->loader->add_action( 'wp_ajax_lumaisd_set_tag_status', $plugin_admin, 'lumaisd_set_tag_status');
		$this->loader->add_action( 'wp_ajax_lumaisd_fetch_attr_tags', $plugin_admin, 'lumaisd_fetch_attr_tags');
		$this->loader->add_action( 'wp_ajax_lumaisd_search_vocab_names', $plugin_admin, 'lumaisd_search_vocab_names');
		$this->loader->add_action( 'wp_ajax_lumaisd_append_user_tag', $plugin_admin, 'lumaisd_append_user_tag');
		$this->loader->add_action( 'wp_ajax_lumaisd_get_specified_post', $plugin_admin, 'lumaisd_get_specified_post');
		$this->loader->add_action( 'wp_ajax_lumaisd_remove_linked_schemas', $plugin_admin, 'lumaisd_remove_linked_schemas');
		$this->loader->add_filter( 'heartbeat_settings', $plugin_admin, 'lumaisd_wp_heartbeat_settings' );
		$this->loader->add_filter( 'heartbeat_received', $plugin_admin, 'lumaisd_return_schema', 10, 2);
	}
	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {
		$plugin_public = new Lumaisd_Public( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'wp_footer', $plugin_public, 'lumaisd_append_schema' );
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
	 * @return    Lumaisd_Loader    Orchestrates the hooks of the plugin.
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
}