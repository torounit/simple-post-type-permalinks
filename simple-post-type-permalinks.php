<?php
/**
 * @package SPTP
 * @version 1.0.3
 */
/**
 * Plugin Name: Simple Post Type Permalinks
 * Plugin URI:  https://github.com/torounit/simple-post-type-permalinks
 * Description: Easy to change Permalink of custom post type.
 * Version:     1.0.3
 * Author:      Toro_Unit
 * Author URI:  http://www.torounit.com
 * License:     GPLv2
 * Text Domain: sptp
 * Domain Path: /languages
 */


define( 'SPTP_FILE', __FILE__ );
define( 'SPTP_PATH', dirname( __FILE__ ) );
define( 'SPTP_URL', plugins_url( '', __FILE__ ) );
define( 'SPTP_BASENAME', plugin_basename( __FILE__ ) );

$data = get_file_data( __FILE__, array( 'ver' => 'Version', 'lang_dir' => 'Domain Path' ) );

define( 'SPTP_VER', $data['ver'] );
define( 'SPTP_LANG_DIR', $data['lang_dir'] );

if ( version_compare( phpversion(), '5.3', '>' ) ) {
	require SPTP_PATH . '/includes/SPTP.php';
} else {
	add_action( 'admin_notices', 'sptp_admin_notices' );
}

function sptp_admin_notices() {
	echo '<div class="error"><p>[Simple Post Type Permalinks] Simple Post Type Permalinks requires PHP version 5.3 or higher.</p></div>';
}





