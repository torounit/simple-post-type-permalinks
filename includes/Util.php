<?php

/**
 * Created by PhpStorm.
 * User: torounit
 * Date: 15/03/31
 * Time: 15:29
 */
class SPTP_Util {

	static function get_option( $key ) {

		$options = get_option( 'sptp_options' );

		if ( empty( $options[ $key ] ) ) {
			return false;
		}

		return $options[ $key ];
	}
}