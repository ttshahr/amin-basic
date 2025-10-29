<?php


/**
 * @wordpress-plugin
 * Plugin Name:       Amin Basic - Dev Branch
 * Plugin URI:        https://github.com/ttshahr/amin-basic
 * Description:       Development version of Amin Basic connector - Customized by Viranet. Original plugin by Ali Jannesary.
 * Version:           1.0.0
 * Author:            Viranet - Based on Ali Jannesary's work
 * Author URI:        https://thevira.net
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       amin-basic
 * Domain Path:       /languages
 */


// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Current plugin version - Development Branch
define( 'AMIN_BASIC_VERSION', '1.0.0' );
define( 'AMIN_BASIC_BRANCH', 'development' );
define( 'AMIN_BASIC_DEVELOPMENT_TEAM', 'thevira.net' );
define( 'AMIN_BASIC_DASHBOARD_IMAGE', plugin_dir_url(__FILE__) . 'admin/images/' );

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */

// define( 'AMIN_BASIC_VERSION', '1.1403.06.20' );
define( 'AMIN_BASIC_DASHBOARD_IMAGE', plugin_dir_url(__FILE__) . 'admin/images/' );

include(plugin_dir_path( __FILE__ ).'libs/notificator.php');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-amin-basic-activator.php
 */
function activate_amin_basic() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-amin-basic-activator.php';
	Amin_Basic_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-amin-basic-deactivator.php
 */
function deactivate_amin_basic() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-amin-basic-deactivator.php';
	Amin_Basic_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_amin_basic' );
register_deactivation_hook( __FILE__, 'deactivate_amin_basic' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
add_action('plugins_loaded','awdw');

function awdw() {

	require plugin_dir_path( __FILE__ ) . 'includes/class-amin-basic.php';


/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_amin_basic() {

	$plugin = new Amin_Basic();
	$plugin->run();

}
run_amin_basic();
}

function add_log($text) {
	
	$text = '[' . date("Y-m-d h:i:sa") . ']: ' . $text;
	file_put_contents(plugin_dir_path( __FILE__ ).'logs/'.date("Y-m-d").'.txt', $text.PHP_EOL , FILE_APPEND | LOCK_EX);
}

function read_log() {
	
	$strPath = plugin_dir_path( __FILE__ ).'logs/'.date("Y-m-d").'.txt';

	$files = glob( plugin_dir_path( __FILE__ ).'logs/*');
	foreach($files as $file){
		if(is_file($file) && $file != $strPath)
			unlink($file);
	}

	if( file_exists($strPath) ){
		echo file_get_contents($strPath);
	}
	else{
		echo 'log not found';
	}
}

function delete_log(){
	$count=0;
	$files = glob( plugin_dir_path( __FILE__ ).'logs/*');
	foreach($files as $file){
		if(is_file($file)){
			unlink($file);
			$count++;
		}
	}

	return $count;
}

// در ابتدای amin-basic.php بعد از تعریف add_log:
add_action('parse_request', function() {
    if (strpos($_SERVER['REQUEST_URI'], '/wc/api/') !== false) {
        add_log('📥 AMIN Request Detected: ' . $_SERVER['REQUEST_URI']);
        add_log('📥 Request Method: ' . $_SERVER['REQUEST_METHOD']);
        add_log('📥 Request Data: ' . json_encode($_POST));
    }
});

/**
*add_action('shutdown', function(){
*	global $wpdb;
*	print_r($wpdb->queries);
*});
*/
