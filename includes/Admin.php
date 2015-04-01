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
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

		add_action( 'admin_init', array( $this, 'admin_fields' ) );
	}


	public function admin_fields() {

		add_settings_section( 'sptp_setting_section',
			__( 'Custom Post Type Permalink Settings', 'sptp' ),
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
		<p>Select permalink setting.</p>

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
			"sptp_{$post_type}_structure_select"
		);
		register_setting( 'permalink', "sptp_{$post_type}_structure_select" );
		register_setting( 'permalink', "sptp_{$post_type}_structure" );
	}

	/**
	 *
	 * setting field row.
	 *
	 * @param $args
	 */
	public function setting_field( $args ) {

		$post_type        = preg_replace( '/sptp_(.+)_structure_select/', '$1', $args );
		$post_type_object = get_post_type_object( $post_type );
		$select           = SPTP_Option::get( $args );
		$with_front = $post_type_object->rewrite['with_front'];


		$values = array(
			false,
			"{$post_type_object->rewrite['slug']}/%post_id%",
			"{$post_type_object->rewrite['slug']}/%postname%.html",
			"{$post_type_object->rewrite['slug']}/%post_id%.html"
		);


		$permastruct = SPTP_Option::get_structure( $post_type );
		?>
		<fieldset class="sptp-fieldset <?=($with_front) ? 'with-front': '';?>">
			<?php
			$this->input_rows( $args, $select, $values, $with_front );
			?>
			<label>
				<input type="radio" name="<?= esc_attr( $args ); ?>" value="custom"
					<?php checked( $select, 'custom' ); ?> />
				<code><?= home_url().'/'.$this->create_permastruct('', $with_front ); ?></code>

				<input name="<?= esc_attr( "sptp_{$post_type}_structure" ); ?>"
				       id="<?= esc_attr( "sptp_{$post_type}_structure" ); ?>"
				       type="text" value="<?= esc_attr( $permastruct ) ?>" class="regular-text code">
			</label>

		</fieldset>
	<?php
	}

	/**
	 *
	 * radio button.
	 *
	 * @param string $name
	 * @param mixed $current
	 * @param array $values
	 */
	public function input_rows( $name, $current, $values, $with_front ) {
		foreach ( $values as $value ):
			$permalink = str_replace( array( '%postname%', '%post_id%' ), array( 'sample-post', '123' ), $value );
			?>

			<label>
				<input type="radio" name="<?= esc_attr( $name ); ?>" value="<?= esc_attr( $value ) ?>"
					<?php checked( $current, $value ); ?> />
				<?php
				if ( $value ):?>
					<code><?= home_url().'/'.$this->create_permastruct( $permalink, $with_front ); ?></code>
				<?php
				else: ?>
					Default.
				<?php
				endif;?>

			</label>
			<br/>
		<?php
		endforeach;
	}



	/**
	 * @param $string
	 *
	 * @return string
	 */
	private function create_permastruct( $string = "" ,$with_front = false ) {

		/** @var WP_Rewrite $wp_rewrite */
		global $wp_rewrite;
		$front = '';
		if( $with_front and substr($wp_rewrite->front, 1)) {
			$front = "<span class='front'>".substr($wp_rewrite->front, 1)."</span>";
		}

		return $front.$string;
	}





	public function admin_enqueue_scripts( $hook ) {
		if ( 'options-permalink.php' === $hook ) {
			wp_enqueue_script(
				'admin-sptp-script',
				plugins_url( 'js/admin-simple-post-type-permalinks.js', SPTP_FILE ),
				array( 'jquery' ),
				SPTP_VER,
				true
			);
		}
	}

}