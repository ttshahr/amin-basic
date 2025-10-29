<?php

/**
 * Fired during plugin activation
 *
 * @link       https://AliJannesary.ir
 * @since      1.0.0
 *
 * @package    Amin_Basic
 * @subpackage Amin_Basic/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Amin_Basic
 * @subpackage Amin_Basic/includes
 * @author     Ali Jannesary <https://AliJannesary.ir>
 */

class Amin_Basic_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		
		$php = '7.2';
		$wp = '5.4';

		global $wp_version;

		if(version_compare( $wp_version, $wp, '<')){
			wp_die(
				sprintf('You must have atleast wordpress version %s, your current version is %s', $wp, $wp_version)
			);
		}
		
		if(version_compare( PHP_VERSION, $php, '<')){
			wp_die(
				sprintf('You must have atleast php version %s', $php)
			);
		}

		global $wpdb;
		$table1_name = $wpdb->prefix . 'abp_attributes';
		$table2_name = $wpdb->prefix . 'amin_basic_new_order_statuses';
		$charset_collate = $wpdb->get_charset_collate();
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		$abpAttributes_sql = "CREATE TABLE IF NOT EXISTS " . $table1_name . " (
			attribute_id int NOT NULL AUTO_INCREMENT,
			abpCode int NOT NULL,
			abpRecordId varchar(36) NOT NULL,
			PRIMARY KEY (attribute_id)
		) ". $charset_collate .";";
		dbDelta( $abpAttributes_sql );
		$table2_sql = "CREATE TABLE IF NOT EXISTS " . $table2_name . " (
			id int NOT NULL AUTO_INCREMENT,
			gateway varchar(255) NOT NULL,
			order_status varchar(255) NOT NULL,
			PRIMARY KEY (id)
		) ". $charset_collate .";";
		dbDelta( $table2_sql );
		
		add_log("Active Plugin");

		notificator_send_message('Plugin Amin Basic activated at ' . home_url());
	}

}
