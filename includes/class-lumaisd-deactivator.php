<?php
/**
 * Fired during plugin deactivation
 *
 * @link       https://www.luminate.ai/
 * @since      1.0.0
 *
 * @package    Lumaisd
 * @subpackage Lumaisd/includes
 */
/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Lumaisd
 * @subpackage Lumaisd/includes
 * @author     Luminate AI <faham@predictly.co>
 */
class Lumaisd_Deactivator {
	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		global $wpdb;
		$row = $wpdb->get_results("SELECT LA_user_schema_tags, LA_user_active_schema_tags FROM ".$wpdb->prefix."posts");
		if(!empty($row)){
	   		$wpdb->query("ALTER TABLE ".$wpdb->prefix."posts DROP LA_user_schema_tags, DROP LA_user_active_schema_tags");
		}
	}
}