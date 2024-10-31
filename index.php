<?php
/**
 * @link              https://www.planaday.nl
 * @since             1.0.0
 * @package           Planaday_Api
 *
 * @wordpress-plugin
 * Plugin Name:       Planaday API
 * Plugin URI:        https://planaday.freshdesk.com/support/solutions/articles/11000058859-wordpress-in-website-met-publieke-api
 * Description:       Toon en boek cursussen samen met Planaday in een wordpress website. Dit kan middels een lijst, op cursussoort, label of alle cursussen en in verschillende formats met eigen opmaak
 * Version:           11.1
 * Author:            Planaday development
 * Author URI:        https://www.planaday.nl
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       planaday-api
 * Domain Path:       /languages
 */

! defined( 'ABSPATH' ) and exit;

require_once( 'src/includes/validate.php' );
require_once( 'src/includes/date.php' );
require_once( 'src/includes/functions.php' );
require_once( 'src/includes/database.php' );
require_once( 'src/api/api.php' );

add_action( 'plugins_loaded', [ api::planaday_api_get_instance(), 'planaday_api_plugin_setup' ] );
add_action( 'init', 'plugin_load_textdomain' );
add_action( 'init', 'register_script' );
add_action( 'wp_enqueue_scripts', 'enqueue_style' );
add_action( 'widgets_init', 'my_register_custom_widget' );
add_action( 'admin_enqueue_scripts', 'editor_scripts' );
add_action( 'paytium_after_pt_show_payment_details', 'paytium_redirect_after_payment', 10, 1 );
add_action( 'wp_enqueue_scripts', 'registreer_extra_stylesheet', 98 );
add_action( 'plugins_loaded', 'sccss_maybe_print_css2' );

function pad_cron_activation(): void {
	if ( ! wp_next_scheduled( 'pad_cron_update_all_courses' ) ):
		wp_schedule_event( time(), 'hourly', 'pad_cron_update_all_courses' );
	endif;
}

add_action( 'wp', 'pad_cron_activation' );
add_action( 'pad_cron_update_all_courses', 'pad_cron_update_all_courses' );

$pluginData    = get_file_data( __FILE__, [ 'Version' => 'Version' ], false );
$pluginVersion = $pluginData['Version'];
define( 'PLANADAYAPI_CURRENT_VERSION', $pluginVersion );
define( 'PLANADAYAPI_CURRENT_VERSION_INT', str_replace( '.', '_', $pluginVersion ) );

pad_update_to_latest_version();
