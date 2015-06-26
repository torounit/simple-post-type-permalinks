<?php

class SPTP_Option_Test extends WP_UnitTestCase {

	/** @var SPTP\Option */
	private $option;

	public function setUp() {

		parent::setUp();
		$this->option = new SPTP\Option();

	}

	public function test_sample() {
		$this->assertTrue( true );
	}

	/**
	 *
	 * @test
	 */
	public function test_get_structure() {

		$post_type = rand_str( 12 );

		update_option( 'sptp_options', array(
			"sptp_{$post_type}_structure" => "{$post_type}/%post_id%.html",
		) );

		$this->assertEquals( $this->option->get_structure( $post_type ), "{$post_type}/%post_id%.html" );

	}

	/**
	 * @test
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function test_get_structure_defined() {

		$post_type = rand_str( 12 );
		update_option( 'sptp_options', array(
			"sptp_{$post_type}_structure" => "{$post_type}/%post_id%.html",
		) );

		register_post_type( $post_type,
			array(
				'public'                   => true,
				'sptp_permalink_structure' => "{$post_type}/%post_id%",
			)
		);

		$this->assertEquals( $this->option->get_structure( $post_type ), "{$post_type}/%post_id%" );

	}

	public function test_extract_front_struct() {
		$slug = rand_str( 12 );
		$this->assertEquals( $this->option->extract_front_struct( "$slug/%post_id%" ), $slug );
		$this->assertEquals( $this->option->extract_front_struct( "$slug/$slug/%post_id%" ), "$slug/$slug" );
	}
}