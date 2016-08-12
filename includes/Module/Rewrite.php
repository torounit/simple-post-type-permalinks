<?php

/**
 *
 * Manage Rewrite rules.
 *
 * @package SPTP
 * @since   0.1.0
 *
 */

namespace SPTP\Module;

class Rewrite extends Module {


	/** @var array */
	private $queue;

	/**
	 * add_hooks
	 */
	public function add_hooks() {
		add_action( 'registered_post_type', array( $this, 'registered_post_type' ), 10, 2 );
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

		$this->override_post_type_slug( $post_type );

		$this->queue[ $post_type ] = array(
			'post_type' => $post_type,
			'args'      => $args,
		);

		$this->register_rewrite_rule( $post_type, $args );

	}

	/**
	 * override slug and save original.
	 *
	 * @param $post_type
	 */
	private function override_post_type_slug( $post_type ) {
		global $wp_post_types;
		if ( $slug = $this->option->get_front_struct( $post_type ) ) {
			if ( is_array( $wp_post_types[ $post_type ]->rewrite ) ) {
				$original_slug                                         = $wp_post_types[ $post_type ]->rewrite['slug'];
				$wp_post_types[ $post_type ]->rewrite['slug']          = $slug;
				$wp_post_types[ $post_type ]->rewrite['original_slug'] = $original_slug;
			}
		}
	}

	/**
	 * after a post type is registered.
	 *
	 * @param string $post_type Post type.
	 * @param object $args Arguments used to register the post type.
	 */
	public function register_rewrite_rule( $post_type, $args ) {
		global $wp_rewrite;
		if ( '' == get_option( 'permalink_structure' ) ) {
			return;
		}

		$permastruct_args         = $args->rewrite;
		$permastruct_args['feed'] = $permastruct_args['feeds'];

		if ( $struct = $this->option->get_structure( $post_type ) ) {

			$post_type_slug = $this->option->get_front_struct( $post_type );
			add_rewrite_tag( "%${post_type}_slug%", "(${post_type_slug})", "post_type=${post_type}&slug=" );

			$struct = str_replace( array( $post_type_slug, '%postname%' ), array( "%${post_type}_slug%", "%{$post_type}%" ), $struct );

			//$rewrite_args['walk_dirs'] = false;
			add_permastruct( $post_type, $struct, $permastruct_args );

			$slug = $this->option->get_front_struct( $post_type );

			if ( $slug ) {
				add_rewrite_rule( "$slug/?$", "index.php?post_type=$post_type", 'top' );

				if ( $args->rewrite['feeds'] && $wp_rewrite->feeds ) {
					$feeds = '(' . trim( implode( '|', $wp_rewrite->feeds ) ) . ')';
					add_rewrite_rule( "{$slug}/feed/$feeds/?$", "index.php?post_type=$post_type" . '&feed=$matches[1]', 'top' );
					add_rewrite_rule( "{$slug}/$feeds/?$", "index.php?post_type=$post_type" . '&feed=$matches[1]', 'top' );
				}

				if ( $args->rewrite['pages'] ) {
					add_rewrite_rule( "$slug/{$wp_rewrite->pagination_base}/?([0-9]{1,})/?$", "index.php?post_type=$post_type" . '&paged=$matches[1]', 'top' );
				}
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
			add_permastruct( $post_type, "{$args->rewrite['original_slug']}/%$post_type%", $permastruct_args );
		}

	}

	/**
	 * deactivation action.
	 */
	public function deactivate() {
		$this->reset_rewrite_rules();
		flush_rewrite_rules();
	}

	/**
	 * deactivation action.
	 */
	public function uninstall() {
		$this->deactivate();
	}
}
