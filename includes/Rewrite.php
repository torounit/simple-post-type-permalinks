<?php

/**
 *
 * Manage Rewrite rules.
 *
 * @package SPTP
 * @since   0.1.0
 * @version 1.0.2
 */

namespace SPTP;

class Rewrite {


	/** @var array */
	private $queue;

	/** @var  Option */
	private $option;

	public function __construct( Option $option ) {
		$this->option = $option;
	}

	/**
	 * add_hooks
	 */
	public function add_hooks() {
		add_action( 'registered_post_type', array( $this, 'registered_post_type' ), 10, 2 );
		add_action( 'wp_loaded', array( $this, 'register_rewrite_rules' ), 100, 2 );
	}

	/**
	 * after a post type is registered.
	 *
	 * @param string $post_type Post type.
	 * @param object $args Arguments used to register the post type.
	 */
	public function registered_post_type( $post_type, $args ) {

		if ( $args->_builtin or ! $args->publicly_queryable ) {
			return;
		}

		$this->queue[ $post_type ] = array(
			'post_type' => $post_type,
			'args'      => $args,
		);
	}

	/**
	 *
	 * Override Permastructs.
	 *
	 */
	public function register_rewrite_rules() {

		if ( ! empty( $this->queue ) ) {
			array_walk( $this->queue, array( $this, 'register_rewrite_rule' ) );
		}
	}


	/**
	 *
	 * @param array $param
	 *
	 */
	public function register_rewrite_rule( Array $param ) {

		if ( '' == get_option( 'permalink_structure' ) ) {
			return;
		}

		$args      = $param['args'];
		$post_type = $param['post_type'];

		$permastruct_args         = $args->rewrite;
		$permastruct_args['feed'] = $permastruct_args['feeds'];

		$queryarg = "post_type={$post_type}&p=";
		$tag      = "%{$post_type}_id%";

		add_rewrite_tag( $tag, '([0-9]+)', $queryarg );

		if ( $struct = $this->option->get_structure( $post_type ) ) {

			$search  = array( '%postname%', '%post_id%' );
			$replace = array( "%{$post_type}%", "%{$post_type}_id%" );
			$struct  = str_replace( $search, $replace, $struct );

			add_permastruct( $post_type, $struct, $permastruct_args );
		}
	}


	/**
	 *
	 * Reset Permastructs.
	 *
	 */
	public function reset_rewrite_rules() {
		array_walk( $this->queue, array( $this, 'reset_rewrite_rule' ) );
	}


	/**
	 *
	 * set default permastruct.
	 * for deactivation.
	 *
	 * @param array $param
	 *
	 */
	public function reset_rewrite_rule( $param ) {
		$args      = $param['args'];
		$post_type = $param['post_type'];

		$permastruct_args         = $args->rewrite;
		$permastruct_args['feed'] = $permastruct_args['feeds'];

		if ( $this->option->get_structure( $post_type ) ) {
			add_permastruct( $post_type, "{$args->rewrite['slug']}/%$post_type%", $permastruct_args );
		}

	}


}