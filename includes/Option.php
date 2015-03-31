<?php

/**
 *
 * Option.
 *
 * @package SPTP
 * @version 0.1.0
 */

class SPTP_Option {

	static function get_option( $key ) {

		$options = get_option( 'sptp_options' );

		if ( empty( $options[ $key ] ) ) {
			return false;
		}

		return $options[ $key ];
	}

	static function get_structure( $post_type ) {
		return self::get_option( "sptp_{$post_type}_structure" );
	}

	public function __construct() {
		add_action( 'admin_init', array( $this, 'save_options' ) );
	}

	/**
	 *
	 * Save Options.
	 *
	 */
	public function save_options() {

		if ( isset( $_POST['submit'] ) and isset( $_POST['_wp_http_referer'] ) ) {
			if ( false !== strpos( $_POST['_wp_http_referer'], 'options-permalink.php' ) ) {
				$request =  $_POST;
				$new_options = $this->extract_options( $request );
				$old_options = get_option( 'sptp_options', array() );
				$options = array_merge( $old_options, $new_options );
				update_option('sptp_options', $options );
			}
		}
	}


	/**
	 *
	 * Filter sptp option.
	 * @param array $options
	 *
	 * @return array
	 */
	private function extract_options( Array $options ) {
		$extracted = [];
		foreach( $options as $key => $value ) {
			if( strpos($key, 'sptp_') === 0 ) {
				$extracted[$key] = $value;
			}
		}
		return $extracted;
	}


}