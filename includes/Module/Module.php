<?php
/**
 * Created by PhpStorm.
 * User: torounit
 * Date: 2016/08/12
 * Time: 4:01
 */

namespace SPTP\Module;

abstract class Module {

	/** @var  Option */
	protected $option;

	public function set_option_module( Option $option ) {
		$this->option = $option;
	}

	public static function get_class() {
		return get_called_class();
	}

	abstract public function add_hooks();

	public function deactivate() {

	}

	public function activate() {

	}

	public function uninstall() {

	}
}
