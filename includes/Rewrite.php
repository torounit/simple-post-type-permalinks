<?php

/**
 *
 * Manage Rewrite rules.
 *
 * @package SPTP
 * @since   0.1.0
 *
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
		global $wp_post_types;

		if ( $args->_builtin or ! $args->publicly_queryable ) {
			return;
		}

		$this->queue[ $post_type ] = array(
			'post_type' => $post_type,
			'args'      => $args,
		);

		if ( $slug = $this->option->get_front_struct( $post_type ) ) {
			if ( is_array( $wp_post_types[ $post_type ]->rewrite ) ) {
				$original_slug = $wp_post_types[ $post_type ]->rewrite['slug'];
				$wp_post_types[ $post_type ]->rewrite['slug'] = $slug;
				$wp_post_types[ $post_type ]->rewrite['original_slug'] = $original_slug;
			}
		}

	}

	/**
	 *
	 * Override Permastructs.
	 *
	 */
	public function register_rewrite_rules() {

		if ( ! empty( $this->queue ) ) {
			array_walk( $this->queue, array( $this, 'register_rewrite_rule_adapter' ) );
		}
	}

	/**
	 *
	 * @param array $param
	 *
	 */
	public function register_rewrite_rule_adapter( Array $param ) {
		$args      = $param['args'];
		$post_type = $param['post_type'];
		$this->register_rewrite_rule( $post_type, $args );
	}


	/**
	 * after a post type is registered.
	 *
	 * @param string $post_type Post type.
	 * @param object $args Arguments used to register the post type.
	 */
	public function register_rewrite_rule( $post_type, $args ) {

		if ( '' == get_option( 'permalink_structure' ) ) {
			return;
		}

		$permastruct_args         = $args->rewrite;
		$permastruct_args['feed'] = $permastruct_args['feeds'];


		if ( $struct = $this->option->get_structure( $post_type ) ) {

			//$post_type_slug = $args->rewrite['slug'];
			$post_type_slug = $this->option->get_front_struct( $post_type );
			add_rewrite_tag( "%${post_type}_slug%", "(${post_type_slug})", "post_type=${post_type}&slug=" );

			$struct  = str_replace(
				array(
					$post_type_slug,
					'%postname%',

				),
				array(
					"%${post_type}_slug%",
					"%{$post_type}%",
				),
				$struct );

			//$rewrite_args['walk_dirs'] = false;
			add_permastruct( $post_type, $struct, $permastruct_args );

			$slug = $this->option->get_front_struct( $post_type );

			if ( $slug ) {
				add_rewrite_rule( "$slug/page/?([0-9]{1,})/?$", "index.php?paged=\$matches[1]&post_type=$post_type", 'top' );
				add_rewrite_rule( "$slug/?$", "index.php?post_type=$post_type", 'top' );
			}
		}
	}


	/**
	 *
	 * Reset Permastructs.
	 * for deactivation.
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