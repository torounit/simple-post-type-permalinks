<?php

/**
 *
 * Option.
 *
 * @package SPTP
 * @since   0.1.0
 *
 */

namespace SPTP\Module;

class Option extends Module {


	public function add_hooks() {
		add_action( 'admin_init', array( $this, 'save_options' ) );
	}

	/**
	 * @param string $post_type
	 *
	 * @return bool|string
	 */
	public function get_structure( $post_type ) {
		$post_type_object = get_post_type_object( $post_type );

		if ( $this->is_defined_structure( $post_type ) ) {
			return $post_type_object->sptp_permalink_structure;
		}

		return trim( $this->get( "sptp_{$post_type}_structure" ), '/' );
	}

	/**
	 * @param string $post_type
	 *
	 * @return bool|string
	 */
	public function get_front_struct( $post_type ) {
		$structure = $this->get_structure( $post_type );

		return $this->extract_front_struct( $structure );
	}

	/**
	 * @param string $structure
	 *
	 * @return string
	 */
	public function extract_front_struct( $structure ) {
		return trim( mb_substr( $structure, 0, mb_strpos( $structure, '%' ) ), '/' );
	}

	/**
	 * @param string $post_type
	 *
	 * @return bool
	 */
	public function is_defined_structure( $post_type ) {
		$post_type_object = get_post_type_object( $post_type );

		return ! empty( $post_type_object->sptp_permalink_structure );
	}

	/**
	 * @param string $key
	 *
	 * @return bool|string
	 */
	public function get( $key ) {

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

			if ( empty( $_POST['_wpnonce'] ) ) {
				return false;
			}

			if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'update-permalink' ) ) {
				return false;
			}

			if ( false === strpos( $_POST['_wp_http_referer'], 'options-permalink.php' ) ) {
				return false;
			}

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

				if ( 'custom' != $new_options[ $select_key ] ) {
					$new_options[ $key ] = $new_options[ $select_key ];
				}

				$new_options[ $key ] = trim( $new_options[ $key ], '/' );

				unset( $new_options[ $select_key ] );
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


	/**
	 *
	 * Filter sptp option.
	 *
	 * @param array $options
	 *
	 * @return array
	 */
	private function extract_options( array $options ) {
		$extracted = array();
		foreach ( $options as $key => $value ) {
			if ( strpos( $key, 'sptp_' ) === 0 ) {
				$extracted[ $key ] = $value;
			}
		}

		return $extracted;
	}

	public function uninstall() {
		delete_option( 'sptp_options' );
	}
}
