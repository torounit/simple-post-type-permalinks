<?php

/**
 *
 * Plugin Bootstrap Class.
 *
 * @package SPTP
 * @version 0.1.0
 */
class SPTP_Bootstrap {

	/** @var SPTP_Option */
	private $option;

	/** @var SPTP_Rewrite */
	private $rewrite;

	/** @var SPTP_Admin */
	private $admin;

	/** @var SPTP_Permalink */
	private $permalink;

	public function __construct() {

		register_activation_hook(   SPTP_FILE, array( $this, 'queue_flush_rewrite_rules' ) );
		register_deactivation_hook( SPTP_FILE, array( $this, 'deactivation' ) );
		register_uninstall_hook( SPTP_FILE, array( __CLASS__, 'uninstall' ) );

		add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );
		add_action( 'wp_loaded', array( $this, 'flush_rewrite_rules' ), 999 );
	}


	public function plugins_loaded() {

		$this->load_textdomain();
		$this->load_modules();

	}

	private function load_textdomain() {
		load_plugin_textdomain(
			'sptp',
			false,
			dirname( plugin_basename( SPTP_FILE ) ) . SPTP_LANG_DIR
		);
	}


	private function load_modules() {
		$this->option   = new SPTP_Option();
		$this->rewrite   = new SPTP_Rewrite();
		$this->admin     = new SPTP_Admin();
		$this->permalink = new SPTP_Permalink();
	}


	public function queue_flush_rewrite_rules() {
		update_option( 'sptp_queue_flush_rewrite_rules', 1 );
	}


	public function flush_rewrite_rules() {
		if ( get_option( 'sptp_queue_flush_rewrite_rules' ) ) {
			flush_rewrite_rules();
			update_option( 'sptp_queue_flush_rewrite_rules', 0 );
		}
	}


	public function deactivation() {
		$this->rewrite->reset_rewrite_rules();
		flush_rewrite_rules();
	}

	public static function uninstall() {
		delete_option( 'sptp_queue_flush_rewrite_rules' );
		delete_option( 'sptp_options' );
	}
}