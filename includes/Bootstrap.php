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

use SPTP\Module\Flusher;
use SPTP\Module\Module;
use SPTP\Module\Admin;
use SPTP\Module\Permalink;
use SPTP\Module\Rewrite;
use SPTP\Module\Option;

class Bootstrap {

	/** @var Module[] */
	private $modules;

	public function __construct() {

		$this->modules = array();

		$this->init();
		$this->setup();
	}

	/**
	 *
	 * for activate and uninstall hooks.
	 *
	 */
	private function setup() {
		register_activation_hook( SPTP_FILE, array( $this, 'activation_action' ) );
		register_deactivation_hook( SPTP_FILE, array( $this, 'deactivation_action' ) );
		$this->register_uninstall_hook();
	}

	private function register_uninstall_hook() {
		register_activation_hook( SPTP_FILE, function() {
			register_uninstall_hook( SPTP_FILE, array( __CLASS__, 'uninstall' ) );
		});
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
		$this->init_modules( $classes );

		do_action( 'sptp_after_load_modules' );

		do_action( 'sptp_modules_loaded' );
	}

	/**
	 * @return array
	 */
	private function get_module_classes() {
		$base_classes = array(
			'option'    => Option::get_class(),
			'admin'     => Admin::get_class(),
			'rewrite'   => Rewrite::get_class(),
			'permalink' => Permalink::get_class(),
			'flusher'   => Flusher::get_class(),
		);

		$classes = array();
		foreach ( $base_classes as $key => $class ) {
			$classes[ $key ] = apply_filters( "sptp_module_${key}_class", $class::get_class() );
		}

		return $classes;
	}

	/**
	 * @param array $classes
	 */
	private function init_modules( array $classes ) {
		/** @var Module[] $modules */
		$modules = array();

		foreach ( $classes as $key => $class ) {
			$module          = apply_filters( "sptp_module_${key}", new $class(), $this );
			$modules[ $key ] = $module;
		}

		foreach ( $modules as $key => $module ) {
			/** @var Option $option */
			$option = $modules['option'];
			$module->set_option_module( $option );
			$module->add_hooks();
		}

		$this->modules = $modules;

	}

	public function deactivation_action() {
		foreach ( $this->modules as $module ) {
			$module->deactivate();
		}
	}

	public function activation_action() {
		foreach ( $this->modules as $module ) {
			$module->activate();
		}
	}

	public function uninstall_action() {
		foreach ( $this->modules as $module ) {
			$module->uninstall();
		}
	}

	/**
	 *
	 * Delete SPTP options.
	 *
	 */
	public static function uninstall() {
		$sptp = new static();
		$sptp->uninstall_action();
	}
}
