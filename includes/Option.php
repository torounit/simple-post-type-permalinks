<?php

/**
 *
 * Option.
 *
 * @package SPTP
 * @version 0.1.0
 */
class SPTP_Option {


	public function __construct() {
		add_action( 'admin_init', array( $this, 'save_options' ) );
	}

	/**
	 * @param string $post_type
	 *
	 * @return bool|string
	 */
	static function get_structure( $post_type ) {
		$post_type_object = get_post_type_object( $post_type );

		if ( ! empty( $post_type_object->permalink_structure ) ) {
			return $post_type_object->permalink_structure;
		}

		return self::get( "sptp_{$post_type}_structure" );
	}

	/**
	 * @param string $key
	 *
	 * @return bool|string
	 */
	static function get( $key ) {

		$options = get_option( 'sptp_options' );

		if ( empty( $options[ $key ] ) ) {
			return false;
		}

		return $options[ $key ];
	}

	/**
	 *
	 * Save Options.
	 *
	 */
	public function save_options() {

		if ( isset( $_POST['submit'] ) and isset( $_POST['_wp_http_referer'] ) ) {
			if ( false !== strpos( $_POST['_wp_http_referer'], 'options-permalink.php' ) ) {
				$request     = $_POST;
				$new_options = $this->extract_options( $request );

				$post_types = get_post_types(
					array(
						'publicly_queryable' => true,
						'_builtin'           => false,
					)
				);

				foreach ( $post_types as $post_type ) {

					$key        = "sptp_{$post_type}_structure";
					$select_key = "sptp_{$post_type}_structure_select";

					$new_options[ $select_key ] = trim( $new_options[ $select_key ], '/' );

					if ( $new_options[ $select_key ] != 'custom' ) {
						$new_options[ $key ] = $new_options[ $select_key ];
					}

					$new_options[ $key ] = trim( $new_options[ $key ], '/' );

					//If Empty set default.
					if ( empty( $new_options[ $key ] ) ) {
						$new_options[ $select_key ] = false;
					}
				}

				$old_options = get_option( 'sptp_options', array() );
				$options     = array_merge( $old_options, $new_options );
				update_option( 'sptp_options', $options );
			}
		}
	}


	/**
	 *
	 * Filter sptp option.
	 *
	 * @param array $options
	 *
	 * @return array
	 */
	private function extract_options( Array $options ) {
		$extracted = [ ];
		foreach ( $options as $key => $value ) {
			if ( strpos( $key, 'sptp_' ) === 0 ) {
				$extracted[ $key ] = $value;
			}
		}

		return $extracted;
	}

}