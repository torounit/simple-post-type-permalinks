<?php

/**
 *
 * Plugin Admin View Class.
 *
 * @package SPTP
 * @version 0.1.0
 */
class SPTP_Admin {

	public function __construct() {
		add_action( 'admin_init', array( $this, 'admin_fields' ) );
	}


	public function admin_fields() {

		add_settings_section( 'sptp_setting_section',
			__( 'Permalink Setting for custom post type', 'sptp' ),
			array( $this, 'setting_section' ),
			'permalink'
		);

		$post_types = get_post_types(
			array(
				'publicly_queryable' => true,
				'_builtin'           => false,
			)
		);

		array_walk( $post_types, array( $this, 'add_settings_field' ) );
	}


	public function setting_section() {
		?>
		<p>Select Permalink Setting.</p>

	<?php

	}

	/**
	 *
	 * register setting field row.
	 *
	 * @param string $post_type
	 */
	public function add_settings_field( $post_type ) {

		add_settings_field( "sptp_{$post_type}_structure",
			$post_type,
			array( $this, 'setting_field' ),
			'permalink',
			'sptp_setting_section',
			"sptp_{$post_type}_structure"
		);
		register_setting( 'permalink', "sptp_{$post_type}_structure" );
	}

	/**
	 *
	 * setting field row.
	 *
	 * @param $args
	 */
	public function setting_field( $args ) {

		$post_type        = preg_replace( '/sptp_(.+)_structure/', '$1', $args );
		$post_type_object = get_post_type_object( $post_type );
		$permastruct      = SPTP_Option::get_structure( $post_type );


		$values   = array();
		$values[] = array(
			'value' => false,
			'txt'   => home_url( $this->create_permastruct( "{$post_type_object->rewrite['slug']}/%postname%" ) ),
		);
		$values[] = array(
			'value' => "{$post_type_object->rewrite['slug']}/%{$post_type}_id%",
			'txt'   => home_url( $this->create_permastruct( "{$post_type_object->rewrite['slug']}/%post_id%" ) ),
		);

		$this->input_rows( $args, $permastruct, $values );
	}


	/**
	 * @param $string
	 *
	 * @return string
	 */
	private function create_permastruct( $string ) {

		/** @var WP_Rewrite $wp_rewrite */
		global $wp_rewrite;
		$front = substr( $wp_rewrite->front, 1 );

		return user_trailingslashit( $front.trim( $string, '/') );
	}


	/**
	 *
	 * radio button.
	 *
	 * @param string $name
	 * @param mixed $current
	 * @param array $values
	 */
	public function input_rows( $name, $current, $values ) {
		foreach ( $values as $value ):
			?>
			<p>
				<label>
					<input type="radio" name="<?= esc_attr( $name ); ?>" value="<?= esc_attr( $value['value'] ) ?>"
						<?php checked( $current, $value['value'] ); ?> />
					<?= esc_html( $value['txt'] ); ?>
				</label>
			</p>
		<?php
		endforeach;
	}

}