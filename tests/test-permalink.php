<?php

class SPTP_Permalink_Test extends WP_UnitTestCase {

	public function setUp() {

		/** @var WP_Rewrite $wp_rewrite */
		global $wp_rewrite;
		parent::setUp();

		$wp_rewrite->init();
		$wp_rewrite->set_permalink_structure( '/%year%/%monthnum%/%day%/%postname%' );
		create_initial_taxonomies();

		update_option( 'page_comments', true );
		update_option( 'comments_per_page', 5 );

	}

	public function structure_provider() {
		return array(
			array( '%post_id%' ),
			array( '%postname%' ),
			array( '%post_id%.html' ),
			array( '%postname%.html' ),
		);
	}

	/**
	 *
	 * @test
	 * @group permalink
	 * @dataProvider structure_provider
	 */
	public function test_permalink( $structure ) {

		$post_type = rand_str( 12 );
		register_post_type( $post_type,
			array(
				'public'                   => true,
				'sptp_permalink_structure' => $post_type . '/' . $structure,
			)
		);

		$id = $this->factory->post->create( array( 'post_type' => $post_type ) );

		do_action( 'wp_loaded' );//fire SPTP_Rewrite::register_rewrite_rules
		/** @var WP_Rewrite $wp_rewrite */
		global $wp_rewrite;
		$wp_rewrite->flush_rules();

		$this->assertEquals( $id, url_to_postid( get_permalink( $id ) ) );
		$this->go_to( get_permalink( $id ) );
		$this->assertQueryTrue( 'is_single', 'is_singular' );
		$this->go_to( add_query_arg( 'page', 2, get_permalink( $id ) ) );
		$this->assertQueryTrue( 'is_single', 'is_singular' );
		$this->assertEquals( get_query_var( "page"), 2 );

	}

	/**
	 * @test
	 * @group permalink
	 */
	public function test_post_type_link() {

		$post_type = rand_str( 12 );
		$slug      = rand_str( 12 );
		register_post_type( $post_type,
			array(
				'public'                   => true,
				'sptp_permalink_structure' => $slug . '/%post_id%',
				'has_archive'              => true,
			)
		);

		$this->assertEquals( home_url( $slug ), get_post_type_archive_link( $post_type ) );
	}
}