<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.luminate.ai/
 * @since      1.0.0
 *
 * @package    Lumaisd
 * @subpackage Lumaisd/admin
 */
/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Lumaisd
 * @subpackage Lumaisd/admin
 * @author     Luminate AI <faham@predictly.co>
 */
class Lumaisd_Admin {
	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;
	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}
	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/lumaisd-admin.css', array(), $this->version, 'all' );
		wp_enqueue_style($this->plugin_name . 'font-awesome', plugin_dir_url(__FILE__) . 'css/lumaisd-fontawesome.css', array(), $this->version, 'all');
	}
	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-widget' );
		wp_enqueue_script( 'jquery-ui-autocomplete' );
		wp_enqueue_script( 'jquery-ui-button' );
		wp_enqueue_script( 'jquery-ui-menu' );
		wp_enqueue_script( 'jquery-ui-tooltip' );
		wp_enqueue_script($this->plugin_name.'-jquery-validate-min', plugin_dir_url( __FILE__ ) . '/js/lumaisd-jquery.validate.min.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/lumaisd-admin.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-widget', 'jquery-ui-autocomplete', 'jquery-ui-button', 'jquery-ui-menu', 'jquery-ui-tooltip' ), $this->version, false );
		wp_localize_script($this->plugin_name, 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
	}
	/**
	 * Register the metaboxes for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function lumaisd_register_admin_metaboxes() {
		$post_types = array('post', 'page');
		$args = array(
			'public' => true,
			'_builtin' => false,
		);
		$output = 'names'; // names or objects, note names is the default
		$operator = 'and'; // 'and' or 'or'
		$fetch_post_types = get_post_types($args, $output, $operator);
		foreach ($fetch_post_types as $key => $value) {
			if ($value != 'vocabulary' && $value != 'augmentation') {
				array_push($post_types, $value);
			}
		}
		add_meta_box('lumaisd_post_type_metabox', __('Luminate AI', 'lumaisd'), array($this, 'lumaisd_post_type_meta_box'), $post_types, 'side', 'high', null);
		add_meta_box('lumaisd_vocabulary_metabox', __('Schema Data', 'lumaisd'), array($this, 'lumaisd_vocabulary_meta_box'), 'vocabulary', 'normal', 'low', null);
	}
	/**
	 * HTML for the metaboxes in the admin area.
	 *
	 * @since    1.0.0
	 */
	public function lumaisd_post_type_meta_box() {
		/**
		 * This registers metaboxes in all available post types except vocabulary for displaying API response data.
		 *
		 */
		global $post, $wpdb;
		delete_post_meta($post->ID, 'execution_counter');
		_e('<div class="lumaisd-post-data lumaisd-post-loader" data-content="Luminate" data-related_post="' . $post->ID . '">', 'lumaisd');
		_e('</div>', 'lumaisd');
	}
	/**
	 * HTML for the metaboxes in the vocabulary post type in the admin area.
	 *
	 * @since    1.0.0
	 */
	public function lumaisd_vocabulary_meta_box() {
		/**
		 * This registers metaboxes in vocabulary for displaying API response data.
		 *
		 */
		global $post;
		if ($post->post_status == 'publish') {
			if (get_post_meta($post->ID, $key = 'lumaisd_vocabulary_meta_data', $single = true)) {
				$lumaisd_meta_elements = get_post_meta($post->ID, $key = 'lumaisd_vocabulary_meta_data', $single = true);
				foreach ($lumaisd_meta_elements as $key => $value) {
					_e('<div class="wrap lumaisd-wrap">', 'lumaisd');
					_e('<div class="wp-list-table widefat fixed striped">', 'lumaisd');
					_e('<div class="lumaisd_username">', 'lumaisd');
					_e('<p class="username">', 'lumaisd');
					_e('<label for="' . esc_attr($key) . '">' . $key . '</label>', 'lumaisd');
					_e('<input type="text" name="lumaisd_' . esc_attr($key) . '" id="lumaisd_' . esc_attr($key) . '" value="' . esc_attr($value) . '">', 'lumaisd');
					_e('</p>', 'lumaisd');
					_e('</div>', 'lumaisd');
					_e('</div>', 'lumaisd');
					_e('</div>', 'lumaisd');
				}
			} else {
				_e('<div class="wrap lumaisd-wrap">', 'lumaisd');
				_e('<div class="wp-list-table widefat fixed striped">', 'lumaisd');
				_e('<div class="lumaisd_username">', 'lumaisd');
				_e('<p class="username">', 'lumaisd');
				_e('<label for="' . esc_attr('at_type') . '">@type</label>', 'lumaisd');
				_e('<input type="text" name="lumaisd_' . esc_attr('at_type') . '" id="lumaisd_' . esc_attr('at_type') . '" value="">', 'lumaisd');
				_e('</p>', 'lumaisd');
				_e('</div>', 'lumaisd');
				_e('</div>', 'lumaisd');
				_e('</div>', 'lumaisd');
				_e('<div class="wrap lumaisd-wrap">', 'lumaisd');
				_e('<div class="wp-list-table widefat fixed striped">', 'lumaisd');
				_e('<div class="lumaisd_username">', 'lumaisd');
				_e('<p class="username">', 'lumaisd');
				_e('<label for="' . esc_attr('url') . '">url</label>', 'lumaisd');
				_e('<input type="text" name="lumaisd_' . esc_attr('url') . '" id="lumaisd_' . esc_attr('url') . '" value="">', 'lumaisd');
				_e('</p>', 'lumaisd');
				_e('</div>', 'lumaisd');
				_e('</div>', 'lumaisd');
				_e('</div>', 'lumaisd');
			}
			if (get_post_meta($post->ID, $key = 'linked_post', $single = true)) {
				$lumaisd_linked_posts = get_post_meta($post->ID, $key = 'linked_post', $single = true);
				foreach ($lumaisd_linked_posts as $key => $value) {
					_e('<div class="wrap lumaisd-wrap">', 'lumaisd');
					_e('<div class="wp-list-table widefat fixed striped">', 'lumaisd');
					_e('<div class="lumaisd_username">', 'lumaisd');
					_e('<p class="username">', 'lumaisd');
					_e('<label for="lumaisd_linked_post">' . $key . '</label>', 'lumaisd');
					_e('<input type="text" name="lumaisd_linked_post" id="lumaisd_linked_post" value="' . implode(',', $value) . '">', 'lumaisd');
					_e('</p>', 'lumaisd');
					_e('</div>', 'lumaisd');
					_e('</div>', 'lumaisd');
					_e('</div>', 'lumaisd');
				}
			} else {
				_e('<div class="wrap lumaisd-wrap">', 'lumaisd');
				_e('<div class="wp-list-table widefat fixed striped">', 'lumaisd');
				_e('<div class="lumaisd_username">', 'lumaisd');
				_e('<p class="username">', 'lumaisd');
				_e('<label for="lumaisd_linked_post">Linked Post</label>', 'lumaisd');
				_e('<input type="text" name="lumaisd_linked_post" id="lumaisd_linked_post" value="">', 'lumaisd');
				_e('</p>', 'lumaisd');
				_e('</div>', 'lumaisd');
				_e('</div>', 'lumaisd');
				_e('</div>', 'lumaisd');
			}
		} else {
			_e('Add necessary data and hit publish to generate tags for your desired post/page.', 'lumaisd');
		}
	}
	/**
	 * Registers a new post type
	 * @uses $wp_post_types Inserts new post type object into the list
	 *
	 * @param string  Post type key, must not exceed 20 characters
	 * @param array|string  See optional args description above.
	 * @return object|WP_Error the registered post type object, or an error object
	 */
	public function lumaisd_register_post_type() {
		/**
		 * This registers post type vocabulary.
		 * @param Array $vocab_labels
		 * @param Array $vocab_args
		 *
		 */
		$vocab_labels = array(
			'name' => __('Vocabulary', 'lumaisd'),
			'singular_name' => __('Vocabulary', 'lumaisd'),
			'add_new' => _x('Add New Vocabulary', 'lumaisd', 'lumaisd'),
			'add_new_item' => __('Add New Vocabulary', 'lumaisd'),
			'edit_item' => __('Edit Vocabulary', 'lumaisd'),
			'new_item' => __('New Vocabulary', 'lumaisd'),
			'view_item' => __('View Vocabulary', 'lumaisd'),
			'search_items' => __('Search Vocabulary', 'lumaisd'),
			'not_found' => __('No Vocabulary found', 'lumaisd'),
			'not_found_in_trash' => __('No Vocabulary found in Trash', 'lumaisd'),
			'parent_item_colon' => __('Parent Vocabulary:', 'lumaisd'),
			'menu_name' => __('Vocabulary', 'lumaisd'),
		);
		$vocab_args = array(
			'labels' => $vocab_labels,
			'hierarchical' => false,
			'description' => 'description',
			'taxonomies' => array(),
			'public' => true,
			'show_ui' => true,
			'show_in_menu' => true,
			'show_in_admin_bar' => true,
			'menu_position' => null,
			'menu_icon' => null,
			'show_in_nav_menus' => true,
			'publicly_queryable' => true,
			'exclude_from_search' => false,
			'has_archive' => false,
			'query_var' => true,
			'can_export' => false,
			'rewrite' => true,
			'capability_type' => 'post',
			'supports' => array(
				'title',
				'editor',
				'author',
				'thumbnail',
				'excerpt',
			),
		);
		register_post_type('vocabulary', $vocab_args);
	}
	/**
	 * Returns Schema in the admin area.
	 *
	 * @since    1.0.0
	 */
	public function lumaisd_wp_heartbeat_settings( $settings ) {
	    $settings['interval'] = 15; //Anything between 15-120
	    return $settings;
	}
	public function lumaisd_return_schema($response, $data) {
		/**
		 * This returns schema data on heartbeat call.
		 * @param Array $data
		 * @param Array $response
		 * @param Variable $lumaisd_received_data
		 * @param Variable $meta_key
		 * @param Variable $lumaisd_schema_meta
		 * @param JSON object response
		 */
		global $wpdb;
		$lumaisd_received_data = $data['schema_meta'];
		if ($lumaisd_received_data == '') {
			$post_id = $data['wp-refresh-post-lock']['post_id'];
			if (isset($data['schema_related']) && $data['schema_related'] != '') {
				$post_id = $data['schema_related'];
			}
			$lumaisd_schema_meta = '<div class="lumaisd-structured-data-container">';
			$lumaisd_schema_meta .= __('<input type="text" name="lumaisd_tag_search" id="lumaisd_tag_search" value="" placeholder="Type to search">', 'lumaisd');
			$lumaisd_schema_meta .= '</div>';
			$lumaisd_schema_meta .= __('<h3>Structured Data</h3>', 'lumaisd');
			$lumaisd_schema_meta .= '<div class="lumaisd-structured-data-container">';
			$lumaisd_schema_meta .= __('<p class="lumaisd-addtag-container"><i class="lumaisd-addstrucuturedtag dashicons dashicons-plus" data-target="' . $post_id . '"></i></p>', 'lumaisd');
			$lumaisd_schema_meta .= '</div>';
			$lumaisd_schema_meta .= '<div id="lumaisdModal" class="lumaisd-modal">';
			$lumaisd_schema_meta .= '<div class="lumaisd-modal-content">';
			$lumaisd_schema_meta .= '<span class="lumaisd-close-modal">&times;</span>';
			$lumaisd_schema_meta .= '<h1 class="lumaisd-post-loader" data-content="Luminate">Luminate</h1>';
			$lumaisd_schema_meta .= '</div>';
			$lumaisd_schema_meta .= '</div>';
			$lumaisd_schema_meta .= '<input name="lumaisd_post_id" id="lumaisd_post_id" value="' . $post_id . '" type="hidden">';
			$row = $wpdb->get_results("SELECT LA_user_schema_tags, LA_user_active_schema_tags FROM ".$wpdb->prefix."posts WHERE ID=".(int)$post_id, OBJECT);
			$row = $row[0];
			$lumaisd_schema_meta .= '<div class="lumaisd-schema-container">';
			if (!is_null($row->LA_user_active_schema_tags) || !is_null($row->LA_user_schema_tags)) {
				$lumaisd_user_schema_active_Ids = $row->LA_user_active_schema_tags;
				$lumaisd_user_schema_active_Ids = explode(',', $lumaisd_user_schema_active_Ids);
				foreach ($lumaisd_user_schema_active_Ids as $lumaisd_user_schema_active_Id) {
					if (!empty($lumaisd_user_schema_active_Id)) {
						$post_data = get_post((int)$lumaisd_user_schema_active_Id);
						$post_meta = get_post_meta($lumaisd_user_schema_active_Id, $key = 'lumaisd_vocabulary_meta_data', $single = true);
						$lumaisd_schema_type = str_replace("http://schema.org/", "", $post_meta['@type']);
						$lumaisd_schema_meta .= '<p class="lumaisd-post-edit-link" data-target="' . $post_data->ID . '" data-parent="' . $post_id . '"><span class="lumaisd-toggle-tag-status active">' . $post_data->post_title . ' <span class="lumaisd-type-' . $lumaisd_schema_type . '">(' . $lumaisd_schema_type . ')</span></span></p>';
					}
				}
				$lumaisd_user_schema_Ids = $row->LA_user_schema_tags;
				$lumaisd_user_schema_Ids = explode(',', $lumaisd_user_schema_Ids);
				foreach ($lumaisd_user_schema_Ids as $lumaisd_user_schema_Id) {
					if (!empty($lumaisd_user_schema_Id)) {
						$post_data = get_post((int)$lumaisd_user_schema_Id);
						$post_meta = get_post_meta($lumaisd_user_schema_Id, $key = 'lumaisd_vocabulary_meta_data', $single = true);
						if (!$post_meta) {
							$post_meta = get_post_meta($lumaisd_user_schema_Id, $key = 'seoaipdl_vocabulary_meta_data', $single = true);
						}
						$lumaisd_schema_type = str_replace("http://schema.org/", "", $post_meta['@type']);
						$lumaisd_schema_meta .= '<p class="lumaisd-post-edit-link" data-target="' . $post_data->ID . '" data-parent="' . $post_id . '"><span class="lumaisd-toggle-tag-status">' . $post_data->post_title . ' <span class="lumaisd-type-' . $lumaisd_schema_type . '">(' . $lumaisd_schema_type . ')</span></span></p>';
					}
				}
			} else {
				$lumaisd_schema_meta = __('<h4>Sorry!! No data was found would you like to add your own?</h4>', 'lumaisd');
				$lumaisd_schema_meta .= '<div class="lumaisd-structured-data-container">';
				$lumaisd_schema_meta .= __('<input type="text" name="lumaisd_tag_search" id="lumaisd_tag_search" value="" placeholder="Type to search">', 'lumaisd');
				$lumaisd_schema_meta .= '</div>';
				$lumaisd_schema_meta .= __('<h3>Structured Data</h3>', 'lumaisd');
				$lumaisd_schema_meta .= '<div class="lumaisd-structured-data-container">';
				$lumaisd_schema_meta .= __('<p class="lumaisd-addtag-container"><i class="lumaisd-addstrucuturedtag dashicons dashicons-plus" data-target="' . $post_id . '"></i></p>', 'lumaisd');
				$lumaisd_schema_meta .= '</div>';
				$lumaisd_schema_meta .= '<div id="lumaisdModal" class="lumaisd-modal">';
				$lumaisd_schema_meta .= '<div class="lumaisd-modal-content">';
				$lumaisd_schema_meta .= '<span class="lumaisd-close-modal">&times;</span>';
				$lumaisd_schema_meta .= '<h1 class="lumaisd-post-loader" data-content="Luminate">Luminate</h1>';
				$lumaisd_schema_meta .= '</div>';
				$lumaisd_schema_meta .= '</div>';
			}
			$response['schema_meta'] = $lumaisd_schema_meta;
		}
		return $response;
	}
	/**
	 * Deletes a schema element in the admin area.
	 *
	 * @since    1.0.0
	 */
	public function lumaisd_remove_linked_schemas() {
		/**
		 * This removes linked schema data from the post upon ajax call.
		 * @param Array $_POST
		 * @param Variable $post_id
		 * @param Variable $meta_key
		 * @param Variable $delete_post_meta
		 * @param JSON object response
		 */
		global $wpdb;
		$post_id = sanitize_text_field($_POST['post_id']);
		$parent_post = sanitize_text_field($_POST['parent_post']);
		$result = array('status' => 'fail');
		$row = $wpdb->get_results("SELECT LA_user_schema_tags, LA_user_active_schema_tags FROM ".$wpdb->prefix."posts WHERE ID=".(int)$parent_post);
		$row = $row[0];
		if (!is_null($row->LA_user_active_schema_tags) || !is_null($row->LA_user_schema_tags)) {
			$lumaisd_user_schema_active_Ids = $row->LA_user_active_schema_tags;
			$lumaisd_user_schema_active_Ids = explode(',', $lumaisd_user_schema_active_Ids);
			$lumaisd_schema_key = array_search($post_id, $lumaisd_user_schema_active_Ids);
			if ($lumaisd_schema_key) {
				unset($lumaisd_user_schema_active_Ids[$lumaisd_schema_key]);
				$lumaisd_user_schema_active_Ids = array_values($lumaisd_user_schema_active_Ids);
				$lumaisd_user_schema_active_Ids = implode(',', $lumaisd_user_schema_active_Ids);
				$wpdb->query("UPDATE ".$wpdb->prefix."posts SET LA_user_active_schema_tags='".$lumaisd_user_schema_active_Ids."' WHERE ID=".(int)$parent_post);
				$result = array('status' => 'success');
			}
			$lumaisd_user_schema_Ids = $row->LA_user_schema_tags;
			$lumaisd_user_schema_Ids = explode(',', $lumaisd_user_schema_Ids);
			$lumaisd_schema_key = array_search($post_id, $lumaisd_user_schema_Ids);
			if ($lumaisd_schema_key) {
				unset($lumaisd_user_schema_Ids[$lumaisd_schema_key]);
				$lumaisd_user_schema_Ids = array_values($lumaisd_user_schema_Ids);
				$lumaisd_user_schema_Ids = implode(',', $lumaisd_user_schema_Ids);
				$wpdb->query("UPDATE ".$wpdb->prefix."posts SET LA_user_schema_tags='".$lumaisd_user_schema_Ids."' WHERE ID=".(int)$parent_post);
				$result = array('status' => 'success');
			}
			$lumaisd_schema_linked_post = get_post_meta($post_id, $meta_key = 'linked_post', $single = true);
			if ($lumaisd_schema_linked_post && in_array($parent_post, $lumaisd_schema_linked_post)) {
				$lumaisd_schema_key = array_search($parent_post, $lumaisd_schema_linked_post);
				unset($lumaisd_schema_linked_post[$lumaisd_schema_key]);
				$lumaisd_schema_linked_post = array_values($lumaisd_schema_linked_post);
				update_post_meta($post_id, $meta_key = 'linked_post', $lumaisd_schema_linked_post);
			}
			if ($post_id != '') {
				$result = array('status' => 'failed');
				$removed_schemas = get_post_meta($parent_post, $key = 'lumaisd_removed_schemas', $single = true);
				if (empty($removed_schemas)) {
					$removed_schemas = array();
				}
				if ($removed_schemas && is_array($removed_schemas) && !in_array($post_id, $removed_schemas)) {
					$removed_schemas[] = $post_id;
				}
				if ($removed_schemas) {
					$update = update_post_meta($parent_post, 'lumaisd_removed_schemas', $removed_schemas);
					$result = array('status' => 'success');
				} else {
					$add = add_post_meta($parent_post, 'lumaisd_removed_schemas', $removed_schemas);
					$result = array('status' => 'success');
				}
				delete_post_meta($parent_post, 'execution_counter');
			}
		}
		wp_send_json($result);
	}
	/**
	 * Save vocabulary data on post save/update action in the admin area.
	 *
	 * @since    1.0.0
	 */
	public function lumaisd_save_vocabulary($id, $data) {
		/**
		 * This verifies user's edit capabilities and modifies post meta for vocabulary.
		 * @param Array $lumaisd_meta_elements
		 * @param Array $schemabuilder
		 *
		 */
		if (!current_user_can("edit_post", $id)) {
			return $data;
		}
		if (defined("DOING_AUTOSAVE") && DOING_AUTOSAVE) {
			return $data;
		}
		$schemabuilder = array();
		if ($data->post_type != 'vocabulary') {
			return $data;
		}
		$lumaisd_meta_elements = get_post_meta($id, $key = 'lumaisd_vocabulary_meta_data', $single = true);
		if (!empty($lumaisd_meta_elements)) {
			foreach ($lumaisd_meta_elements as $key => $value) {
				if (isset($_POST['lumaisd_' . esc_attr($key)])) {
					$schemabuilder[$key] = sanitize_text_field($_POST['lumaisd_' . esc_attr($key)]);
				} else {
					$schemabuilder[$key] = $value;
				}
			}
			update_post_meta($id, $key = 'lumaisd_vocabulary_meta_data', $schemabuilder);
		} elseif ($_POST['lumaisd_@type'] != '' && $_POST['lumaisd_url'] != '') {
			if (isset($_POST['lumaisd_@type'])) {
				$schemabuilder['@type'] = sanitize_text_field($_POST['lumaisd_@type']);
			}
			if (isset($_POST['lumaisd_url'])) {
				$schemabuilder['url'] = sanitize_text_field($_POST['lumaisd_url']);
			}
			add_post_meta($id, $key = 'lumaisd_vocabulary_meta_data', $schemabuilder);
		} else {
			$lumaisd_meta_elements = get_post_meta($id, $key = 'seoaipdl_vocabulary_meta_data', $single = true);
			if (!empty($lumaisd_meta_elements)) {
				foreach ($lumaisd_meta_elements as $key => $value) {
					if (isset($_POST['lumaisd_' . esc_attr($key)])) {
						$schemabuilder[$key] = sanitize_text_field($_POST['lumaisd_' . esc_attr($key)]);
					} else {
						$schemabuilder[$key] = $value;
					}
				}
				add_post_meta($id, $key = 'lumaisd_vocabulary_meta_data', $schemabuilder);
			}
		}
		$lumaisd_linked_posts = get_post_meta($id, $key = 'linked_post', $single = true);
		if (!empty($lumaisd_linked_posts)) {
			foreach ($lumaisd_linked_posts as $key => $value) {
				if (isset($_POST['lumaisd_linked_post'])) {
					$lumaisd_linked_posts[$key] = explode(',', sanitize_text_field($_POST['lumaisd_linked_post']));
				}
			}
			update_post_meta($id, $key = 'linked_post', $lumaisd_linked_posts);
		} elseif ($_POST['lumaisd_linked_post'] != '') {
			if (isset($_POST['lumaisd_linked_post'])) {
				$lumaisd_linked_posts['Linked Posts'] = explode(',', sanitize_text_field($_POST['lumaisd_linked_post']));
			}
			add_post_meta($id, $key = 'linked_post', $lumaisd_linked_posts);
		}
		foreach ($lumaisd_linked_posts['Linked Posts'] as $linked_posts) {
			$removed_schemas = get_post_meta($linked_posts, $key = 'lumaisd_removed_schemas', $single = true);
			$inserts = get_post_meta($linked_posts, 'lumaisd_post_schemas', $single = true);
			$schemaIds = get_post_meta($linked_posts, 'lumaisd_post_schemasIds', $single = true);
			if (!in_array($id, $removed_schemas)) {
				array_push($schemaIds, $id);
			}
			$inserts .= '<p class="lumaisd-post-edit-link" data-target="' . $id . '"><a href="' . get_edit_post_link($id, '') . '" target="_blank">' . $data->post_title . '</a></p>';
			if (get_post_meta($linked_posts, 'lumaisd_post_schemas', $single = true)) {
				$update = update_post_meta($linked_posts, 'lumaisd_post_schemas', $inserts);
			} else {
				$add = add_post_meta($linked_posts, 'lumaisd_post_schemas', $inserts);
			}
			if (get_post_meta($linked_posts, 'lumaisd_post_schemasIds', $single = true)) {
				$update = update_post_meta($linked_posts, 'lumaisd_post_schemasIds', $schemaIds);
			} else {
				$add = add_post_meta($linked_posts, 'lumaisd_post_schemasIds', $schemaIds);
			}
		}
		return $data;
	}
	/**
	 * Retrieves vocabulary data on ajax call.
	 *
	 * @since    1.0.0
	 */
	public function lumaisd_get_specified_post() {
		$post = get_post(sanitize_text_field($_POST['parent_post']));
		$post_data = get_post(sanitize_text_field($_POST['post_id']));
		$parent_type = sanitize_text_field($_POST['parent_type']);
		$post_type = 'http://schema.org/'.$parent_type;
		$verification = get_option('api_verification');
		$requiredFields = array();
		$curl_response = wp_remote_post(LUMAISD_API . 'SchemaProperty', array('method' => 'GET', 'timeout' => 15, 'redirection' => 5, 'httpversion' => '1.0', 'blocking' => true, 'headers' => array(), 'body' => array('url' => $post_type), 'cookies' => array()));
		$post_meta = array();
		if ($post_data->post_type == 'vocabulary') {
			$post_meta = get_post_meta(sanitize_text_field($_POST['post_id']), $key = 'lumaisd_vocabulary_meta_data', $single = true);
		} elseif ($post_data->post_type == 'augmentation') {
			$post_meta = get_post_meta(sanitize_text_field($_POST['post_id']), $key = 'lumaisd_augmentation_meta_data', $single = true);
		}
		$post_html = '<form method="post" action="" id="lumaisd_edit_'.sanitize_text_field($_POST['post_id']).'">';
			$post_html .= '<h3>' . $post_data->post_title . '</h3>';
			$post_html .= '<p class="lumaisd-post-edit-link lumaisd-post-save-link" ><input name="lumaisd_post_save" id="lumaisd_post_save" class="button button-primary button-large" value="Save" type="submit"></p>';
			$post_html .= '<p class="lumaisd-post-edit-link lumaisd-post-delete-link" data-target="' . $post_data->ID . '" data-parent="' . $post->ID . '"><input name="lumaisd_post_delete" id="lumaisd_post_delete" class="button button-primary button-large dashicons dashicons-trash" value="Delete" type="submit" data-target="' . $post_data->ID . '"></p>';
			$post_html .= '<div class="wrap lumaisd-wrap">';
				$post_html .= '<div class="wp-list-table widefat fixed striped">';
					$post_html .= '<div class="lumaisd-specified-post">';
						$post_html .= '<div class="lumaisd_username">';
							$post_html .= '<p class="username">';
								$post_html .= '<label for="lumaisd_desc">description</label>';
								$post_html .= '<textarea name="lumaisd_desc" id="lumaisd_desc" required="required">' . $post_data->post_content . '</textarea>';
								$post_html .= '<input name="lumaisd_post_id" id="lumaisd_post_id" value="' . $post_data->ID . '" type="hidden">';
								$post_html .= '<input name="lumaisd_parent_post_id" id="lumaisd_parent_post_id" value="' . $post->ID . '" type="hidden">';
								$post_html .= '<input name="lumaisd_post_title" id="lumaisd_post_title" value="' . $post_data->post_title . '" type="hidden">';
								$post_html .= '<input name="lumaisd_post_action" id="lumaisd_post_action" value="update" type="hidden">';
							$post_html .= '</p>';
							array_push($requiredFields, 'lumaisd_desc');
						$post_html .= '</div>';
						$keylist = array();
						$post_keylist = array();
						$post_keyvaluelist = array();
						if ($post_meta) {
							foreach ($post_meta as $key => $value) {
								if ($key != 'at_type') {
									array_push($post_keylist, $key);
									$post_keyvaluelist[$key] = $value;
								}
								$key = str_replace('@', 'at_', $key);
								if ($key == 'at_type') {
									$post_html .= '<div class="lumaisd_username">';
										$post_html .= '<p class="username">';
											$post_html .= '<label for="' . esc_attr($key) . '">type</label>';
											$post_html .= '<select name="lumaisd_at_type_selector" id="lumaisd_at_type_selector" data-target="'.$post->ID.'" required="required">';
												$post_html .= '<option value="">'.__("Select", "lumaisd").'</option>';
												$post_html .= '<option '.($value == str_replace("http://schema.org/", "", "http://schema.org/Article") ? "selected='selected'": "").' value="'.esc_url("http://schema.org/Article").'">'.__("Article", "lumaisd").'</option>';
												$post_html .= '<option '.($value == str_replace("http://schema.org/", "", "http://schema.org/AggregateRating") ? "selected='selected'": "").' value="'.esc_url("http://schema.org/AggregateRating").'">'.__("AggregateRating", "lumaisd").'</option>';
												$post_html .= '<option '.($value == str_replace("http://schema.org/", "", "http://schema.org/Blog") ? "selected='selected'": "").' value="'.esc_url("http://schema.org/Blog").'">'.__("Blog", "lumaisd").'</option>';
												$post_html .= '<option '.($value == str_replace("http://schema.org/", "", "http://schema.org/Book") ? "selected='selected'": "").' value="'.esc_url("http://schema.org/Book").'">'.__("Book", "lumaisd").'</option>';
												$post_html .= '<option '.($value == str_replace("http://schema.org/", "", "http://schema.org/BreadcrumbList") ? "selected='selected'": "").' value="'.esc_url("http://schema.org/BreadcrumbList").'">'.__("BreadcrumbList", "lumaisd").'</option>';
												$post_html .= '<option '.($value == str_replace("http://schema.org/", "", "http://schema.org/CreativeWork") ? "selected='selected'": "").' value="'.esc_url("http://schema.org/CreativeWork").'">'.__("CreativeWork", "lumaisd").'</option>';
												$post_html .= '<option '.($value == str_replace("http://schema.org/", "", "http://schema.org/Event") ? "selected='selected'": "").' value="'.esc_url("http://schema.org/Event").'">'.__("Event", "lumaisd").'</option>';
												$post_html .= '<option '.($value == str_replace("http://schema.org/", "", "http://schema.org/ImageObject") ? "selected='selected'": "").' value="'.esc_url("http://schema.org/ImageObject").'">'.__("ImageObject", "lumaisd").'</option>';
												$post_html .= '<option '.($value == str_replace("http://schema.org/", "", "http://schema.org/LocalBusiness") ? "selected='selected'": "").' value="'.esc_url("http://schema.org/LocalBusiness").'">'.__("LocalBusiness", "lumaisd").'</option>';
												$post_html .= '<option '.($value == str_replace("http://schema.org/", "", "http://schema.org/Offer") ? "selected='selected'": "").' value="'.esc_url("http://schema.org/Offer").'">'.__("Offer", "lumaisd").'</option>';
												$post_html .= '<option '.($value == str_replace("http://schema.org/", "", "http://schema.org/Organization") ? "selected='selected'": "").' value="'.esc_url("http://schema.org/Organization").'">'.__("Organization", "lumaisd").'</option>';
												$post_html .= '<option '.($value == str_replace("http://schema.org/", "", "http://schema.org/Person") ? "selected='selected'": "").' value="'.esc_url("http://schema.org/Person").'">'.__("Person", "lumaisd").'</option>';
												$post_html .= '<option '.($value == str_replace("http://schema.org/", "", "http://schema.org/Place") ? "selected='selected'": "").' value="'.esc_url("http://schema.org/Place").'">'.__("Place", "lumaisd").'</option>';
												$post_html .= '<option '.($value == str_replace("http://schema.org/", "", "http://schema.org/Product") ? "selected='selected'": "").' value="'.esc_url("http://schema.org/Product").'">'.__("Product", "lumaisd").'</option>';
												$post_html .= '<option '.($value == str_replace("http://schema.org/", "", "http://schema.org/Rating") ? "selected='selected'": "").' value="'.esc_url("http://schema.org/Rating").'">'.__("Rating", "lumaisd").'</option>';
												$post_html .= '<option '.($value == str_replace("http://schema.org/", "", "http://schema.org/Recipe") ? "selected='selected'": "").' value="'.esc_url("http://schema.org/Recipe").'">'.__("Recipe", "lumaisd").'</option>';
												$post_html .= '<option '.($value == str_replace("http://schema.org/", "", "http://schema.org/Review") ? "selected='selected'": "").' value="'.esc_url("http://schema.org/Review").'">'.__("Review", "lumaisd").'</option>';
												$post_html .= '<option '.($value == str_replace("http://schema.org/", "", "http://schema.org/SearchAction") ? "selected='selected'": "").' value="'.esc_url("http://schema.org/SearchAction").'">'.__("SearchAction", "lumaisd").'</option>';
												$post_html .= '<option '.($value == str_replace("http://schema.org/", "", "http://schema.org/VideoObject") ? "selected='selected'": "").' value="'.esc_url("http://schema.org/VideoObject").'">'.__("VideoObject", "lumaisd").'</option>';
												$post_html .= '<option '.($value == str_replace("http://schema.org/", "", "http://schema.org/WebPage") ? "selected='selected'": "").' value="'.esc_url("http://schema.org/WebPage").'">'.__("WebPage", "lumaisd").'</option>';
											$post_html .= '</select>';
										$post_html .= '</p>';
									$post_html .= '</div>';
									array_push($keylist, esc_html($key));
									$post_keyvaluelist[$key] = $value;
								}
							}
						}
						if (!is_wp_error($curl_response)) {
							$result = json_decode($curl_response['body']);
							$result = $result->result;
							$setType = str_replace('http://schema.org/', '', $post_type);
							$post_html .= '<input name="lumaisd_parent_post_id" id="lumaisd_parent_post_id" value="' . $post_id . '" type="hidden">';
							$post_html .= '<input name="lumaisd_at_type" id="lumaisd_at_type" value="' . $setType . '" type="hidden">';
							$keylist = array('at_type');
							$post_html .= '<div class="lumaisd_attr_holder">';
								if ($result && is_array($result)) {
									foreach ($result as $vals) {
										$boundation = trim($vals->flag) == 'none' ? '' : ' (' . trim($vals->flag) . ')';
										$required = trim($vals->flag) == 'required ' ? "required='required'" : '';
										$key = str_replace(' ', '', $vals->prop_nam);
										$value = '';
										
										if ($key != 'uri' && $key != 'image') {
											if (in_array($key, $post_keylist)) {
												$value = $post_keyvaluelist[$key];
											}
											if ($vals->prop_ect != ' Number ') {
												$post_html .= '<div class="lumaisd_username">';
												if ($key != 'name' && $key != 'description' && $key != 'uri') {
													$post_html .= '<p class="username">';
														$post_html .= '<label for="' . esc_attr($vals->prop_nam) . '">' . $key . $boundation . '</label>';
														array_push($keylist, esc_html($key));
														$post_html .= '<input type="text" name="lumaisd_' . esc_html($key) . '" id="lumaisd_' . esc_html($key) . '" ' . $required . ' value="'.$value.'">';
														$post_html .= '</p>';
													if (trim($vals->flag) == 'required') {
														array_push($requiredFields, 'lumaisd_' . esc_html($key));
													}
												}
												$post_html .= '</div>';
											} else {
												$post_html .= '<div class="lumaisd_username">';
												if ($key != 'name' && $key != 'description' && $key != 'uri') {
													$post_html .= '<p class="username">';
														$post_html .= '<label for="' . esc_attr($key) . '">' . $key . $boundation . '</label>';
														array_push($keylist, esc_html($key));
														$post_html .= '<input type="number" name="lumaisd_' . esc_html($key) . '" id="lumaisd_' . esc_html($key) . '" ' . $required . ' value="'.$value.'">';
													$post_html .= '</p>';
													if (trim($vals->flag) == 'required') {
														array_push($requiredFields, 'lumaisd_' . esc_html($key));
													}
												}
												$post_html .= '</div>';
											}
										}
										if ($key == 'image') {
											$post_html .= '<div class="lumaisd_username">';
												$post_html .= '<p class="username">';
												$post_html .= '<label for="' . esc_attr($key) . '">' . $key . $boundation . '</label>';
												array_push($keylist, esc_html($key));
												$post_html .= '<input type="text" name="lumaisd_' . esc_html($key) . '" id="lumaisd_' . esc_html($key) . '" ' . $required . ' value="'.get_the_post_thumbnail_url(sanitize_text_field($_POST['post_id']), 'full').'">';
												$post_html .= '</p>';
											$post_html .= '</div>';
										}
									}
								}
								$requiredFields = implode(',', $requiredFields);
								$post_html .= '<input name="lumaisd_post_requiredFields" id="lumaisd_post_requiredFields" value="' . $requiredFields . '" type="hidden">';
							$post_html .= '</div>';
						}
						$post_html .= '<div class="lumaisd_username">';
							$post_html .= '<p class="username">';
								$post_html .= '<label for="image">image</label>';
								$post_html .= '<input type="text" name="lumaisd_image" id="lumaisd_image" value="' . wp_get_attachment_url(get_post_thumbnail_id($post_data->ID)) . '">';
							$post_html .= '</p>';
						$post_html .= '</div>';
						array_push($keylist, 'image');
						$keylist = implode(',', $keylist);
						$post_html .= '<input name="lumaisd_post_keylist" id="lumaisd_post_keylist" value="' . $keylist . '" type="hidden">';
						$post_html .= '<p class="lumaisd-post-edit-link lumaisd-post-save-link" ><input name="lumaisd_post_save" id="lumaisd_post_save" class="button button-primary button-large" value="Save" type="submit"></p>';
						$post_html .= '<p class="lumaisd-post-edit-link lumaisd-post-delete-link" data-target="' . $post_data->ID . '" data-parent="' . $post->ID . '"><input name="lumaisd_post_delete" id="lumaisd_post_delete" class="button button-primary button-large dashicons dashicons-trash" value="Delete" type="submit" data-target="' . $post_data->ID . '"></p>';
					$post_html .= '</div>';
				$post_html .= '</div>';
			$post_html .= '</div>';
		$post_html .= '</form>';
		$result = array(
			'status' => 'success',
			'html' => $post_html,
		);
		wp_send_json($result);
	}
	/**
	 * Adds searched tag to user's list.
	 *
	 * @since    1.0.0
	 */
	public function lumaisd_append_user_tag() {
		global $wpdb;
		$target_post = sanitize_text_field($_POST['post_id']);
		$parent_post = (int) sanitize_text_field($_POST['parent_post']);
		$removed_schemas = get_post_meta($parent_post, $key = 'lumaisd_removed_schemas', $single = true);
		if (!$removed_schemas || is_null($removed_schemas)) {
			$removed_schemas = array();
		}
		$result = array(
			'status'	=>	'fail',
		);
		$lumaisd_user_schema_Ids = array();
		$lumaisd_user_schema_active_Ids = array();
		$row = $wpdb->get_results("SELECT LA_user_schema_tags, LA_user_active_schema_tags FROM ".$wpdb->prefix."posts WHERE ID=".(int)$parent_post);
		$row = $row[0];
		if (!is_null($row->LA_user_schema_tags) || !is_null($row->LA_user_active_schema_tags)) {
			$lumaisd_user_schema_Ids = $row->LA_user_schema_tags;
			$lumaisd_user_schema_Ids = explode(',', $lumaisd_user_schema_Ids);
			$lumaisd_user_schema_active_Ids = $row->LA_user_active_schema_tags;
			$lumaisd_user_schema_active_Ids = explode(',', $lumaisd_user_schema_active_Ids);
			$lumaisd_schema_key = array_search($parent_post, $lumaisd_user_schema_active_Ids);
			if (!in_array($target_post, $lumaisd_user_schema_Ids) && !in_array($target_post, $lumaisd_user_schema_active_Ids) && !in_array($target_post, $removed_schemas)) {
				array_push($lumaisd_user_schema_Ids, $target_post);
				$lumaisd_user_schema_Ids = implode(',', $lumaisd_user_schema_Ids);
				$count = $wpdb->query("UPDATE ".$wpdb->prefix."posts SET LA_user_schema_tags='".$lumaisd_user_schema_Ids."' WHERE ID=".(int)$parent_post);
			} else {
				$result = array(
					'status'	=>	'success',
					'message'	=>	'Tag already exists'
				);
			}
		} else {
			if (!in_array($target_post, $removed_schemas)) {
				$lumaisd_user_schema_Ids = array();
				array_push($lumaisd_user_schema_Ids, $target_post);
				$lumaisd_user_schema_Ids = implode(',', $lumaisd_user_schema_Ids);
				$count = $wpdb->query("UPDATE ".$wpdb->prefix."posts SET LA_user_schema_tags='".$lumaisd_user_schema_Ids."' WHERE ID=".(int)$parent_post);
			}
		}
		if (!in_array($target_post, $lumaisd_user_schema_Ids) && !in_array($target_post, $lumaisd_user_schema_active_Ids) && !in_array($target_post, $removed_schemas)) {
			array_push($lumaisd_user_schema_Ids, $target_post);
			$update = add_post_meta($parent_post, $meta_key = 'lumaisd_post_user_schemasIds', $lumaisd_schema_user_Ids);
			if ($update) {
				$linked_post = get_post_meta((int)$target_post, $key = 'linked_post', $single = true);
				if($linked_post && is_array($linked_post)){
					array_push($linked_post['Linked Posts'], $target_post);
				} elseif (!is_array($linked_post)) {
					array_push($linked_post['Linked Posts'], $target_post);
				} else {
					$linked_post = array(
						'Linked Posts'	=> array()
					);
					array_push($linked_post['Linked Posts'], $target_post);
				}
				$update = add_post_meta($target_post, $key = 'linked_post', $linked_post);
				if ($update) {
					$result = array(
						'status'	=>	'success'
					);
				}
			}
		}
		wp_send_json($result);
	}
	/**
	 * Retrieves vocabulary title matching the search criteria on ajax call.
	 *
	 * @since    1.0.0
	 */
	public function lumaisd_search_vocab_names() {
		global $wpdb;
		$post_title = sanitize_text_field($_REQUEST['term']);
		$uiItem = array();
		$lumaisd_search_sql = "SELECT ID, post_title FROM " . $wpdb->prefix . "posts WHERE post_title LIKE '%" . $post_title . "%' AND post_type = 'vocabulary' AND post_status = 'publish'";
		$post_results = $wpdb->get_results($lumaisd_search_sql);
		if ($post_results) {
			$item = array();
			foreach ($post_results as $post_result) {
				$item = array(
					'value'	=>	$post_result->post_title,
					'id'	=>	$post_result->ID
				);
				array_push($uiItem, $item);
			}
		}
		wp_send_json( $uiItem);
	}
	/**
	 * Retrieves form fields on ajax call.
	 *
	 * @since    1.0.0
	 */
	public function lumaisd_fetch_attr_tags() {
		$setType = sanitize_text_field($_POST['setType']);
		$post_id = sanitize_text_field($_POST['post_id']);
		$curl_response = wp_remote_post(LUMAISD_API . 'SchemaProperty', array('method' => 'GET', 'timeout' => 15, 'redirection' => 5, 'httpversion' => '1.0', 'blocking' => true, 'headers' => array(), 'body' => array('url' => $setType), 'cookies' => array()));
		$html = '<span class="lumaisd-close-modal">&times;</span>Some Error occured. Please try again in a little while.';
		if (!is_wp_error($curl_response)) {
			$result = json_decode($curl_response['body']);
			$result = $result->result;
			$html = '';
			$setType = str_replace('http://schema.org/', '', $setType);
			$html .= '<input name="lumaisd_parent_post_id" id="lumaisd_parent_post_id" value="' . $post_id . '" type="hidden">';
			$html .= '<input name="lumaisd_at_type" id="lumaisd_at_type" value="' . $setType . '" type="hidden">';
			$keylist = array('at_type');
			if ($result && is_array($result)) {
				foreach ($result as $vals) {
					$boundation = $vals->flag == 'none ' ? '' : ' (' . $vals->flag . ')';
					$required = $vals->flag == 'required ' ? "required='required'" : '';
					if ($vals->prop_ect != ' Number ') {
						$html .= '<div class="lumaisd_username">';
						$html .= '<p class="username">';
						$key = str_replace(' ', '', $vals->prop_nam);
						if ($key != 'name' && $key != 'description') {
							$html .= '<label for="' . esc_attr($vals->prop_nam) . '">' . $key . $boundation . '</label>';
							array_push($keylist, esc_html($key));
							$html .= '<input type="text" name="lumaisd_' . esc_html($key) . '" id="lumaisd_' . esc_html($key) . '" ' . $required . '>';
						}
						$html .= '</p>';
						$html .= '</div>';
					} else {
						$html .= '<div class="lumaisd_username">';
						$html .= '<p class="username">';
						$html .= '<label for="' . esc_attr($vals->prop_nam) . '">' . $key . $boundation . '</label>';
						$key = str_replace(' ', '', $vals->prop_nam);
						array_push($keylist, esc_html($key));
						$html .= '<input type="number" name="lumaisd_' . esc_html($key) . '" id="lumaisd_' . esc_html($key) . '" ' . $required . '>';
						$html .= '</p>';
						$html .= '</div>';
					}
				}
			}
			$html .= '<input name="lumaisd_post_action" id="lumaisd_post_action" value="save" type="hidden">';
			$html .= '<input name="lumaisd_post_save" id="lumaisd_post_save" class="button button-primary button-large" value="Save" type="submit">';
			$keylist = implode(',', $keylist);
			$html .= '<input name="lumaisd_post_keylist" id="lumaisd_post_keylist" value="' . $keylist . '" type="hidden">';
		}
		$response = array(
			'status' => 'success',
			'html' => $html,
		);
		wp_send_json($response);
	}
	/**
	 * Inserts or updates vocabulary post on modal form submission.
	 *
	 * @since    1.0.0
	 */
	public function lumaisd_save_update_post() {
		global $wp_version, $wpdb;
		$image_url = '';
		$image = '';
		$image_name = '';
		$upload_dir = '';
		$image_data = '';
		$unique_file_name = '';
		$filename = '';
		$post_title = sanitize_text_field($_POST['post_title']);
		$post_content = '';
		if (version_compare($wp_version, '4.7', '>=')) {
			$post_content = sanitize_textarea_field($_POST['post_desc']);
		} else {
			$post_content = esc_textarea($_POST['post_desc']);
		}
		$meta_input = array();
		$valuelist = $_POST['valuelist'];
		$id = sanitize_text_field($_POST['parent_post']);
		$parent_post = 0;
		$target_post = 0;
		if (isset($_POST['target_post'])) {
			$target_post = sanitize_text_field($_POST['target_post']);
		}
		if (isset($_POST['parent_post'])) {
			$parent_post = sanitize_text_field($_POST['parent_post']);
		}
		$post_action = sanitize_text_field($_POST['post_action']);
		$removed_schemas = get_post_meta($id, $key = 'lumaisd_removed_schemas', $single = true);
		$schemaIds = array();
		$row = $wpdb->get_results("SELECT LA_user_schema_tags FROM ".$wpdb->prefix."posts WHERE ID=".(int)$parent_post);
		$row = $row[0];
		if (!is_null($row->LA_user_schema_tags)) {
			$schemaIds = $row->LA_user_schema_tags;
		}
		$result = array(
			'status' => 'failed',
		);
		$acted = '';
		if ($valuelist) {
			foreach ($valuelist as $key => $value) {
				$key = str_replace('at_', '@', $key);
				if ($key != 'image') {
					$meta_input[$key] = $value;
				}
				if ($key == 'image' && $value != '') {
					$image_url = $value;
					$image = pathinfo($image_url);
					$image_name = $image['basename'];
					if (strpos($image_name, '?') === false) {
					} else {
						$image_name = substr($image_name, 0, strpos($image_name, '?'));
					}
					if (substr($image_name, strlen($image_name) - 5 == '.svg')) {
						$image_name = substr($image_name, 0, strlen($image_name) - 4);
						$image_name = $image_name . '.png';
					}
					$upload_dir = wp_upload_dir();
					$image_data = file_get_contents($image_url);
					$unique_file_name = wp_unique_filename($upload_dir['path'], $image_name);
					$filename = basename($unique_file_name);
				}
			}
		}
		$insert_id = $parent_post;
		if ($post_action == 'save') {
			$postarr = array(
				'post_title' => $post_title,
				'post_content' => $post_content,
				'post_type' => 'vocabulary',
				'post_status' => 'publish',
				'meta_input' => array(
					'lumaisd_vocabulary_meta_data' => $meta_input,
					'linked_post' => array('Linked Posts' => array($id)),
				),
			);
			$insert_id = wp_insert_post($postarr, true);
			if (!is_null($row->LA_user_schema_tags)) {
				$lumaisd_user_schema_Ids = $row->LA_user_schema_tags;
				$lumaisd_user_schema_Ids = explode(',', $lumaisd_user_schema_Ids);
				array_push($lumaisd_user_schema_Ids, $insert_id);
				$lumaisd_user_schema_Ids = implode(',', $lumaisd_user_schema_Ids);
				$wpdb->query("UPDATE ".$wpdb->prefix."posts SET LA_user_schema_tags='".$lumaisd_user_schema_Ids."' WHERE ID=".(int)$parent_post);
				$result = array('status' => 'success');
			} else {
				$lumaisd_user_schema_Ids = array();
				array_push($lumaisd_user_schema_Ids, $insert_id);
				$lumaisd_user_schema_Ids = implode(',', $lumaisd_user_schema_Ids);
				$count = $wpdb->query("UPDATE ".$wpdb->prefix."posts SET LA_user_schema_tags='".$lumaisd_user_schema_Ids."' WHERE ID=".(int)$parent_post);
				$result = array('status' => 'success');
			}
			$acted = 'Created';
		} elseif ($post_action == 'update') {
			$postarr = array('ID' => $target_post, 'post_content' => $post_content);
			$updated_post = wp_update_post($postarr, $wp_error = true);
			$update = update_post_meta($target_post, $key = 'lumaisd_vocabulary_meta_data', $meta_input);
			$insert_id = $target_post;
			$acted = 'Updated';
		}
		if (!is_wp_error($insert_id)) {
			if ($image != '') {
				if (wp_mkdir_p($upload_dir['path'])) {
					$file = $upload_dir['path'] . '/' . $filename;
				} else {
					$file = $upload_dir['basedir'] . '/' . $filename;
				}
				file_put_contents($file, $image_data);
				$wp_filetype = wp_check_filetype($filename, null);
				$attachment = array(
					'post_mime_type' => $wp_filetype['type'],
					'post_title' => sanitize_file_name($filename),
					'post_content' => '',
					'post_status' => 'inherit',
				);
				$attach_id = wp_insert_attachment($attachment, $file, $insert_id);
				require_once ABSPATH . 'wp-admin/includes/image.php';
				$attach_data = wp_generate_attachment_metadata($attach_id, $file);
				wp_update_attachment_metadata($attach_id, $attach_data);
				$thumbnail = set_post_thumbnail($insert_id, $attach_id);
				array_push($attached, $attach_id);
			}
		}
		$result = array(
			'status' => 'success',
			'acted' => $acted,
		);
		wp_send_json( $result);
	}
	/**
	 * Sets tag as active or inactive on ajax call.
	 *
	 * @since    1.0.0
	 */
	public function lumaisd_set_tag_status() {
		global $wpdb;
		$vocab = sanitize_text_field($_POST['post_id']);
		$setActivation = sanitize_text_field($_POST['tag_status']);
		$parent_post = sanitize_text_field($_POST['parent_post']);
		$removed_schemas = get_post_meta($parent_post, $key = 'lumaisd_removed_schemas', $single = true);
		if (!$removed_schemas || is_null($removed_schemas)) {
			$removed_schemas = array();
		}
		$result = array('status' => 'fail');
		$row = $wpdb->get_results("SELECT LA_user_schema_tags, LA_user_active_schema_tags FROM ".$wpdb->prefix."posts WHERE ID=".(int)$parent_post);
		$row = $row[0];
		if (!is_null($row->LA_user_schema_tags) || !is_null($row->LA_user_active_schema_tags)) {
			$lumaisd_user_schema_Ids = $row->LA_user_schema_tags;
			$lumaisd_user_schema_Ids = explode(',', $lumaisd_user_schema_Ids);
			$lumaisd_user_schema_active_Ids = $row->LA_user_active_schema_tags;
			$lumaisd_user_schema_active_Ids = explode(',', $lumaisd_user_schema_active_Ids);
			if ((int) $setActivation == 1) {
				if (!in_array($vocab, $lumaisd_user_schema_active_Ids) && !in_array($vocab, $removed_schemas)) {
					array_push($lumaisd_user_schema_active_Ids, $vocab);
					$lumaisd_schema_key = array_search($vocab, $lumaisd_user_schema_Ids);
					unset($lumaisd_user_schema_Ids[$lumaisd_schema_key]);
					$lumaisd_user_schema_Ids = array_values($lumaisd_user_schema_Ids);
					$result = array('status' => 'success');
				}
			} else {
				if (!in_array($vocab, $lumaisd_user_schema_Ids) && !in_array($vocab, $removed_schemas)) {
					array_push($lumaisd_user_schema_Ids, $vocab);
					$lumaisd_schema_key = array_search($vocab, $lumaisd_user_schema_active_Ids);
					unset($lumaisd_user_schema_active_Ids[$lumaisd_schema_key]);
					$lumaisd_user_schema_active_Ids = array_values($lumaisd_user_schema_active_Ids);
					$result = array('status' => 'success');
				}
			}
			$lumaisd_user_schema_Ids = array_values($lumaisd_user_schema_Ids);
			$lumaisd_user_schema_Ids = implode(',', $lumaisd_user_schema_Ids);
			$lumaisd_user_schema_active_Ids = array_values($lumaisd_user_schema_active_Ids);
			$lumaisd_user_schema_active_Ids = implode(',', $lumaisd_user_schema_active_Ids);
			$count = $wpdb->query("UPDATE ".$wpdb->prefix."posts SET LA_user_schema_tags='".$lumaisd_user_schema_Ids."', LA_user_active_schema_tags='".$lumaisd_user_schema_active_Ids."' WHERE ID=".(int)$parent_post);
		}
		wp_send_json($result);
	}
}