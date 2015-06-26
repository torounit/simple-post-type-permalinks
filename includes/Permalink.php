<?php

/**
 *
 * Permalink filter.
 *
 * @package SPTP
 * @since   0.1.0
 * @version 1.0.2
 *
 */

namespace SPTP;

class Permalink {

	/** @var  Option */
	private $option;

	public function __construct( Option $option ) {
		$this->option = $option;
	}

	public function add_hooks() {
		add_filter( 'post_type_link', array( $this, 'post_type_link' ), 10, 2 );
		add_filter( 'post_type_archive_link', array( $this, 'post_type_archive_link' ), 10, 2 );
	}

	/**
	 *
	 * Fix post_type permalink from postname to id.
	 *
	 * @param string $post_link The post's permalink.
	 * @param \WP_Post $post The post in question.
	 *
	 * @return string
	 */
	public function post_type_link( $post_link, \WP_Post $post ) {

		if ( ! $this->option->get_structure( $post->post_type ) ) {
			return $post_link;
		}

		$rewritecode    = array( "%{$post->post_type}_id%", );
		$rewritereplace = array( $post->ID, );

		return str_replace( $rewritecode, $rewritereplace, $post_link );

	}

	/**
	 * Filter the post type archive permalink.
	 *
	 * @since 3.1.0
	 *
	 * @param string $link The post type archive permalink.
	 * @param string $post_type Post type name.
	 *
	 * @return string
	 */
	public function post_type_archive_link( $link, $post_type ) {
		$post_type_obj = get_post_type_object( $post_type );

		if ( get_option( 'permalink_structure' ) && is_array( $post_type_obj->rewrite ) ) {

			$struct = $this->option->get_front_struct( $post_type );

			$link = home_url( user_trailingslashit( $struct, 'post_type_archive' ) );
		}

		return $link;
	}

}