<?php
/**
 * @package SPTP
 * @version 2.0.0
 */
/**
 * Plugin Name: Simple Post Type Permalinks
 * Plugin URI:  https://github.com/torounit/simple-post-type-permalinks
 * Description: Easy to change Permalink of custom post type.
 * Version:     2.0.0
 * Author:      Toro_Unit
 * Author URI:  http://www.torounit.com
 * License:     GPLv2 or Later
 * Text Domain: simple-post-type-permalinks
 * Domain Path: /languages
 */


define( 'SPTP_FILE', __FILE__ );
define( 'SPTP_PATH', dirname( __FILE__ ) );
define( 'SPTP_URL', plugins_url( '', __FILE__ ) );
define( 'SPTP_BASENAME', plugin_basename( __FILE__ ) );

$sptp_data = get_file_data( __FILE__, array(
	'Name' => 'Plugin Name',
	'PluginURI' => 'Plugin URI',
	'Version' => 'Version',
	'Description' => 'Description',
	'Author' => 'Author',
	'AuthorURI' => 'Author URI',
	'TextDomain' => 'Text Domain',
	'DomainPath' => 'Domain Path',
	'Network' => 'Network',
) );

define( 'SPTP_VER', $sptp_data['Version'] );
define( 'SPTP_DOMAIN_PATH', $sptp_data['DomainPath'] );
define( 'SPTP_TEXT_DOMAIN', $sptp_data['TextDomain'] );
define( 'SPTP_REQUIRE_PHP_VERSION', '5.3' );

unset( $sptp_data );

function sptp_init() {
	load_plugin_textdomain( SPTP_TEXT_DOMAIN, false, dirname( plugin_basename( SPTP_FILE ) ) . SPTP_DOMAIN_PATH );
	if ( version_compare( phpversion(), SPTP_REQUIRE_PHP_VERSION, '>' ) ) {
		require SPTP_PATH . '/includes/SPTP.php';
	} else {
		add_action( 'admin_notices', 'sptp_admin_notices' );
	}
}

/**
 * notices for old php version.
 */
function sptp_admin_notices() {
	$message = sprintf(
		__( '[Simple Post Type Permalinks] Simple Post Type Permalinks requires PHP version %s or higher.', SPTP_TEXT_DOMAIN ),
		SPTP_REQUIRE_PHP_VERSION
	);

	echo sprintf( '<div class="error"><p>%s</p></div>', $message );
}

/**
 * init
 */
sptp_init();







