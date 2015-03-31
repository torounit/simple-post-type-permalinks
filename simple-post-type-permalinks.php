<?php
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

/**
 * Copyright (c) 2015 Toro_Unit ( http://www.torounit.com )
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2 or, at
 * your discretion, any later version, as published by the Free
 * Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */



define( 'SPTP_URL',  plugins_url( '', __FILE__ ) );
define( 'SPTP_PATH', dirname( __FILE__ ) );

$sptp = new Simple_Post_Type_Permalinks();
$sptp->register();

class Simple_Post_Type_Permalinks {

private $version = '';
private $langs   = '';

function __construct()
{
    $data = get_file_data(
        __FILE__,
        array( 'ver' => 'Version', 'langs' => 'Domain Path' )
    );
    $this->version = $data['ver'];
    $this->langs   = $data['langs'];
}

public function register()
{
    add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );
}

public function plugins_loaded()
{
    load_plugin_textdomain(
        'sptp',
        false,
        dirname( plugin_basename( __FILE__ ) ).$this->langs
    );

    add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );

    add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
    add_action( 'admin_menu', array( $this, 'admin_menu' ) );
    add_action( 'admin_init', array( $this, 'admin_init' ) );

}

public function admin_menu()
{
    // See http://codex.wordpress.org/Administration_Menus
    add_options_page(
        __( 'Simple Post Type Permalinks', 'sptp' ),
        __( 'Simple Post Type Permalinks', 'sptp' ),
        'manage_options', // http://codex.wordpress.org/Roles_and_Capabilities
        'sptp',
        array( $this, 'options_page' )
    );
}

public function admin_init()
{
    if ( isset( $_POST['_wpnonce_sptp'] ) && $_POST['_wpnonce_sptp'] ){
        if ( check_admin_referer( 'baukksn0lv75vcxrxkz7j0rxnmsoflxr', '_wpnonce_sptp' ) ){

            // save something

            wp_safe_redirect( menu_page_url( 'sptp', false ) );
        }
    }
}

public function options_page()
{
?>
<div id="sptp" class="wrap">
<h2><?php _e( 'Simple Post Type Permalinks', 'sptp' ); ?></h2>

<form method="post" action="<?php echo esc_attr( $_SERVER['REQUEST_URI'] ); ?>">
<?php wp_nonce_field( 'baukksn0lv75vcxrxkz7j0rxnmsoflxr', '_wpnonce_sptp' ); ?>

Admin Panel Here!

<p style="margin-top: 3em;">
    <input type="submit" name="submit" id="submit" class="button button-primary"
            value="<?php _e( "Save Changes", "sptp" ); ?>"></p>
</form>
</div><!-- #sptp -->
<?php
}

public function admin_enqueue_scripts($hook)
{
    if ( 'settings_page_sptp' === $hook ) {
        wp_enqueue_style(
            'admin-sptp-style',
            plugins_url( 'css/admin-simple-post-type-permalinks.min.css', __FILE__ ),
            array(),
            $this->version,
            'all'
        );

        wp_enqueue_script(
            'admin-sptp-script',
            plugins_url( 'js/admin-simple-post-type-permalinks.min.js', __FILE__ ),
            array( 'jquery' ),
            $this->version,
            true
        );
    }
}

public function wp_enqueue_scripts()
{
    wp_enqueue_style(
        'simple-post-type-permalinks-style',
        plugins_url( 'css/simple-post-type-permalinks.min.css', __FILE__ ),
        array(),
        $this->version,
        'all'
    );

    wp_enqueue_script(
        'simple-post-type-permalinks-script',
        plugins_url( 'js/simple-post-type-permalinks.min.js', __FILE__ ),
        array( 'jquery' ),
        $this->version,
        true
    );
}

} // end class Simple_Post_Type_Permalinks

// EOF
