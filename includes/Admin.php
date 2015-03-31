<?php
/**
 * Created by PhpStorm.
 * User: torounit
 * Date: 15/03/31
 * Time: 12:02
 */

class SPTP_Admin {

	function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'admin_init', array( $this, 'admin_fields' ) );
		add_action( 'admin_init', array( $this, 'save_options' ) );
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
				'_builtin'          => false,
				)
		);

		array_walk( $post_types, array( $this, 'add_settings_field') );
	}


	public function setting_section() {
		?>
		<p>Select Permalink Setting.</p>

	<?php

	}

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

	public function setting_field( $args ) {
		global /** @var WP_Rewrite $wp_rewrite */
		$wp_rewrite;

		$post_type = preg_replace( '/sptp_(.+)_structure/','$1', $args );
		$permastruct = $wp_rewrite->get_extra_permastruct( $post_type );

		$permastruct_base = str_replace( array( "%{$post_type}%", "%{$post_type}_id%"), "%s" , $permastruct );

		$permastruct_id = sprintf( $permastruct_base, "%{$post_type}_id%" );
		$permastruct_name = sprintf( $permastruct_base, "%{$post_type}%" );

		?>

		<p>
			<label>
				<input type="radio" name="<?=esc_attr($args);?>" value="0"
					<?php checked($permastruct, $permastruct_name ); ?> />
				<?=home_url( user_trailingslashit( sprintf( $permastruct_base, "%postname%" ) ) );?> ( Default )
			</label>
		</p>

		<p>
			<label>
				<input type="radio" name="<?=esc_attr($args);?>" value="<?=$permastruct_id?>"
					<?php checked( $permastruct, $permastruct_id ); ?> />
				<?=home_url( user_trailingslashit( sprintf( $permastruct_base, "%post_id%" ) ) );?>
			</label>
		</p>


		<?php
	}

	public function save_options() {
		if ( isset( $_POST['submit'] ) and isset( $_POST['_wp_http_referer'] ) ) {
			if ( false !== strpos( $_POST['_wp_http_referer'], 'options-permalink.php' ) ) {
				$request =  $_POST;
				$new_options = $this->extract_options( $request );
				$old_options = get_option( 'sptp_options', array() );
				$options = array_merge( $old_options, $new_options );
				update_option('sptp_options', $options );
			}
		}
	}


	/**
	 * @param array $options
	 *
	 * @return array
	 */
	private function extract_options( Array $options ) {
		$extracted = [];
		foreach( $options as $key => $value ) {
			if( strpos($key, 'sptp_') === 0 ) {
				$extracted[$key] = $value;
			}
		}
		return $extracted;
	}




	public function admin_enqueue_scripts( $hook ) {
		if ( 'settings_page_sptp' === $hook ) {
			wp_enqueue_style(
				'admin-sptp-style',
				plugins_url( 'css/admin-simple-post-type-permalinks.min.css', __FILE__ ),
				array(),
				SPTP_VER,
				'all'
			);

			wp_enqueue_script(
				'admin-sptp-script',
				plugins_url( 'js/admin-simple-post-type-permalinks.min.js', __FILE__ ),
				array( 'jquery' ),
				SPTP_VER,
				true
			);
		}
	}
}