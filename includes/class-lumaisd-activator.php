<?php
/**
 * Fired during plugin activation
 *
 * @link       https://www.luminate.ai/
 * @since      1.0.0
 *
 * @package    Lumaisd
 * @subpackage Lumaisd/includes
 */
/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Lumaisd
 * @subpackage Lumaisd/includes
 * @author     Luminate AI <faham@predictly.co>
 */
class Lumaisd_Activator {
	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		global $wpdb;
		$row = $wpdb->get_results("SELECT LA_user_schema_tags FROM ".$wpdb->prefix."posts");
		if(empty($row)){
	   		$wpdb->query("ALTER TABLE ".$wpdb->prefix."posts ADD LA_user_schema_tags TEXT NULL");
		}
		$row = $wpdb->get_results("SELECT LA_user_active_schema_tags FROM ".$wpdb->prefix."posts");
		if(empty($row)){
	   		$wpdb->query("ALTER TABLE ".$wpdb->prefix."posts ADD LA_user_active_schema_tags TEXT NULL");
		}
	}
}