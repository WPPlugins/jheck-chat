<?php
/**
 * Plugin Name:       Jheck Chat
 * Plugin URI:        https://wordpress.org/plugins/jheck-chat
 * Description:       Simple worpdress chat plugin using ajax.
 * Version:           1.4
 * Author:            Jeric Izon
 * Author URI:        https://profiles.wordpress.org/jeric_izon
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       jheck_chat
 * Domain Path:       /languages
 */

/**
 * If this file is called directly, abort.
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Globals
 * @since 1.0
 */

global $wpdb;
global $jc_db_version;
$jc_db_version = '1.4'; 

if (!defined("JC_URL")) {
	/**
	 * http://
	 */
	define("JC_URL", plugin_dir_url( __FILE__ ));	
}

if (!defined("JC_URL_PATH")) {
	/**
	 * public_html/...
	 */
	define("JC_URL_PATH", plugin_dir_path( __FILE__ ));	
}

if (!defined("JC_MYSQL_INBOX")) {
	define("JC_MYSQL_INBOX", $wpdb->prefix . 'jheck_chat_inbox');	
}

if (!defined("JC_ENCRYPTION_KEY")) { 
	define("JC_ENCRYPTION_KEY", 'jHeCkChAt');    
}

/**
 * Use vafpress framework
 * @since 1.0
 */
require_once 'vafpress/bootstrap.php';

/**
 * Include all important files
 * @since 1.0
 */
include_once(ABSPATH . 'wp-includes/pluggable.php');
include JC_URL_PATH . 'inc/functions.php';
include JC_URL_PATH . 'inc/admin-settings.php';
include JC_URL_PATH . 'inc/shortcodes.php';
include JC_URL_PATH . 'inc/process-form.php';

/**
 * Register activation hooks and enqueue scripts and styles
 * @since 1.0
 */	

add_action('init','jc_create_inbox' );
add_action('wp_enqueue_scripts', 'jc_enqueue_scripts');
add_action('wp_loaded','jc_generate_custom_css');

/**
 * Display chat box.
 * @since 1.1
 */	
add_action( 'wp_footer', 'jc_footer_chat_box_code' );

/**
 * If Jheck chat is activated
 * @since 1.3
 */
register_activation_hook( __FILE__, 'jc_plugin_activated' );

/**
 * If Jheck chat is deactivated
 * @since 1.3
 */
register_deactivation_hook( __FILE__, 'jc_plugin_deactivated' ); 


/**
 * Template call fixed
 * @since 1.2
 */

$template_name = jc_option_val('jheck_chat_template');

if (empty($template_name)) {
    define('JC_TEMPLATE_NAME','default');
}else{
    define('JC_TEMPLATE_NAME',$template_name);
}