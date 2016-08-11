<?php

/**
 *
 * Plugin Bootstrap Class.
 *
 * @package SPTP
 * @since   0.1.0
 *
 */
namespace SPTP;

use SPTP\Module\Module;
use SPTP\Module\Admin;
use SPTP\Module\Permalink;
use SPTP\Module\Rewrite;
use SPTP\Module\Option;

class Bootstrap {

	/** @var Module[] */
	private $modules;


	/** @var Option */
	private $option;

	/** @var Rewrite */
	private $rewrite;

	/** @var Admin */
	private $admin;

	/** @var Permalink */
	private $permalink;

	public function __construct() {

		$this->modules = array();

		$this->setup();
		$this->init();
		add_action( 'wp_loaded', array( $this, 'flush_rewrite_rules' ), 999 );

	}

	/**
	 *
	 * for activate and uninstall hooks.
	 *
	 */
	private function setup() {
		register_activation_hook( SPTP_FILE, array( $this, 'queue_flush_rewrite_rules' ) );
		register_deactivation_hook( SPTP_FILE, array( $this, 'deactivation' ) );
		register_uninstall_hook( SPTP_FILE, array( __CLASS__, 'uninstall' ) );
	}

	/**
	 *
	 * Delete SPTP options.
	 *
	 */
	public static function uninstall() {
		delete_option( 'sptp_queue_flush_rewrite_rules' );
		delete_option( 'sptp_options' );
	}

	/**
	 *
	 * initialize.
	 *
	 */
	public function init() {

		$this->load_modules();

	}


	/**
	 *
	 * Load Plugin modules.
	 *
	 */
	private function load_modules() {

		do_action( 'sptp_before_load_modules' );

		$classes = $this->get_module_classes();
		$this->modules = $this->init_modules( $classes );

		do_action( 'sptp_after_load_modules' );

		do_action( 'sptp_modules_loaded' );
	}

	/**
	 * @return array
	 */
	private function get_module_classes( ) {
		$base_classes = array(
			'option' =>    Option::get_class(),
			'admin'  =>    Admin::get_class(),
			'rewrite' =>   Rewrite::get_class(),
			'permalink' => Permalink::get_class(),
		);

		$classes = array();
		foreach ( $base_classes as $key => $class ) {
			$classes[ $key ] = apply_filters( "sptp_module_${key}_class", $class::get_class() );
		}
		return $classes;
	}

	/**
	 * @param array $classes
	 *
	 * @return Module[]
	 */
	private function init_modules( array $classes ) {
		/** @var Module[] $modules */
		$modules = array();

		foreach ( $classes as $key => $class ) {
			$module = apply_filters( "sptp_module_${key}", new $class(), $this );
			$modules[ $key ] = $module;
		}

		foreach ( $modules as $key => $module ) {
			/** @var Option $option */
			$option = $modules['option'];
			$module->set_option_module( $option );
			$module->add_hooks();
		}

		return $modules;

	}

	/**
	 *
	 * queue reset rewrite rules for next request.
	 *
	 */
	public function queue_flush_rewrite_rules() {
		update_option( 'sptp_queue_flush_rewrite_rules', 1 );
	}

	/**
	 *
	 *  Re-Create Rewrite Rules.
	 *
	 */
	public function flush_rewrite_rules() {
		if ( get_option( 'sptp_queue_flush_rewrite_rules' ) ) {
			flush_rewrite_rules();
			update_option( 'sptp_queue_flush_rewrite_rules', 0 );
		}
	}

	/**
	 *
	 *  Reset rules.
	 *
	 */
	public function deactivation() {
		$this->rewrite->reset_rewrite_rules();
		flush_rewrite_rules();
	}
}