<?php
/**
 * @package SPTP
 * @version 0.1.0
 */
/**
 * Plugin Name: Simple Post Type Permalinks
 * Plugin URI:  http://www.torounit.com
 * Description: Easy to change the custom post type of permlink.
 * Version:     0.1.0
 * Author:      Toro_Unit
 * Author URI:  http://www.torounit.com
 * License:     GPLv2
 * Text Domain: sptp
 * Domain Path: /languages
 */


define( 'SPTP_FILE', __FILE__ );
define( 'SPTP_PATH', dirname( __FILE__ ) );
define( 'SPTP_URL',  plugins_url( '', __FILE__ ) );
define( 'SPTP_BASENAME', plugin_basename( __FILE__ ) );

$data = get_file_data( __FILE__, array( 'ver' => 'Version', 'lang_dir' => 'Domain Path' ) );

define( 'SPTP_VER', $data['ver'] );
define( 'SPTP_LANG_DIR', $data['lang_dir'] );

require SPTP_PATH . '/includes/Option.php';
require SPTP_PATH . '/includes/Rewrite.php';
require SPTP_PATH . '/includes/Permalink.php';
require SPTP_PATH . '/includes/Admin.php';
require SPTP_PATH . '/includes/Bootstrap.php';



$sptp = new SPTP_Bootstrap();

