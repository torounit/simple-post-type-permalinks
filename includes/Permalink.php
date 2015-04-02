<?php

/**
 *
 * Permalink filter.
 *
 * @package SPTP
 * @version 0.1.0
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

		$rewritecode = array( "%{$post->post_type}_id%", );
		$rewritereplace = array( $post->ID, );
		return str_replace( $rewritecode, $rewritereplace, $post_link );

	}

}