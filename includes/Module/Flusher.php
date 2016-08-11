<?php
/**
 * Created by PhpStorm.
 * User: torounit
 * Date: 2016/08/12
 * Time: 5:57
 */

namespace SPTP\Module;


class Flusher extends Module {

	public function add_hooks() {

	}

	public function activate() {
		parent::activate(); // TODO: Change the autogenerated stub
		$this->queue_flush_rewrite_rules();
	}

	public function uninstall() {
		delete_option( 'sptp_queue_flush_rewrite_rules' );
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

}