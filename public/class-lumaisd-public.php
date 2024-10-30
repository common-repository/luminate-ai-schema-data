<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.luminate.ai/
 * @since      1.0.0
 *
 * @package    Lumaisd
 * @subpackage Lumaisd/public
 */
/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Lumaisd
 * @subpackage Lumaisd/public
 * @author     Luminate AI <faham@predictly.co>
 */
class Lumaisd_Public {
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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}
	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function lumaisd_append_schema() {
		global $post, $wpdb;
		echo '<!--Luminate Schemabuilder-->';
		$removed_schemas = get_post_meta($post->ID, $key = 'lumaisd_removed_schemas', $single = true);
		if (!$removed_schemas || is_null($removed_schemas)) {
			$removed_schemas = array();
		}
		$row = $wpdb->get_results("SELECT LA_user_active_schema_tags FROM ".$wpdb->prefix."posts WHERE ID=".(int)$post->ID);
		$row = $row[0];
		if (!empty($row->LA_user_active_schema_tags)) {
			$lumaisd_user_schema_active_Ids = $row->LA_user_active_schema_tags;
			$lumaisd_user_schema_active_Ids = explode(',', $lumaisd_user_schema_active_Ids);
			$schemabuilder = $lumaisd_user_schema_active_Ids;
			$args = array(
				'post__in' => $schemabuilder,
				'post_type' => 'vocabulary',
				'post_status' => 'published',
			);
			$lumaisd_post_data = get_posts($args);
			if (!empty($lumaisd_post_data)) {
				foreach ($lumaisd_post_data as $lumaisd_post) {
					if (!in_array($lumaisd_post->ID, $removed_schemas)) {
						echo '<script type="application/ld+json">';
						echo '{';
						echo '"@context": "http://schema.org",';
						echo '"name": "' . $lumaisd_post->post_title . '",';
						echo '"image": "' . get_the_post_thumbnail_url($lumaisd_post->ID) . '",';
						foreach (get_post_meta($lumaisd_post->ID, $key = 'lumaisd_vocabulary_meta_data', $single = true) as $key => $value) {
							if ($key != 'uri' && $key != 'name' && $key != 'description') {
								if ($key == 'publisher') {
									echo '"' . $key . '": "{"';
										echo '"@type": "Organization",';
										echo '"name": "' . esc_attr($value) . '"';
									echo '"}",';
								} elseif($key == 'author') {
									echo '"' . $key . '": "{"';
										echo '"@type": "Person",';
										echo '"name": "' . esc_attr($value) . '"';
									echo '"}",';
								} else {
									echo '"' . $key . '": "' . esc_attr($value) . '",';
								}
							}
						}
						echo '"description": "' . esc_attr($lumaisd_post->post_content) . '"';
						echo '}';
						echo '</script>';
					}
				}
			}
		}
	}
}