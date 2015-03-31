<?php

class SPTP_Rewrite {

	/** @var array */
	private $queue;

	/**
	 * constructor
	 */
	public function __construct() {
		add_action( 'registered_post_type', array( $this, 'registered_post_type' ), 10, 2 );
		add_action( 'wp_loaded', array( $this, 'register_rewrite_rules' ), 100, 2 );
	}

	/**
	 * after a post type is registered.
	 *
	 * @param string $post_type Post type.
	 * @param object $args      Arguments used to register the post type.
	 */
	public function registered_post_type( $post_type, $args ) {

		if ( $args->_builtin or !$args->publicly_queryable ) {
			return;
		}

		$this->queue[ $post_type ] = array(
			'post_type'         => $post_type,
			'args'              => $args,
		);
	}

	public function register_rewrite_rules() {
		array_walk( $this->queue, array($this, 'register_rewrite_rule') );
	}

	public function reset_rewrite_rules() {
		array_walk( $this->queue, array($this, 'reset_rewrite_rule') );
	}

	public function register_rewrite_rule( $param ) {

		$args = $param['args'];
		$post_type = $param['post_type'];

		$permastruct_args         = $args->rewrite;
		$permastruct_args['feed'] = $permastruct_args['feeds'];

		$queryarg = "post_type={$post_type}&p=";
		$tag      = "%{$post_type}_id%";

		add_rewrite_tag( $tag, '([0-9]+)', $queryarg );

		if( SPTP_Util::get_option( "sptp_{$post_type}_structure" ) ) {
			add_permastruct( $post_type, "{$args->rewrite['slug']}/{$tag}", $permastruct_args );
		}
	}

	public function reset_rewrite_rule( $param ) {
		$args = $param['args'];
		$post_type = $param['post_type'];

		$permastruct_args         = $args->rewrite;
		$permastruct_args['feed'] = $permastruct_args['feeds'];

		if( SPTP_Util::get_option( "sptp_{$post_type}_structure" ) ) {
				add_permastruct( $post_type, "{$args->rewrite['slug']}/%$post_type%", $permastruct_args );
		}

	}


}