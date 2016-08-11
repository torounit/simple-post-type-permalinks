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
			array( '/%post_id%' ),
			array( '/%postname%' ),
			array( '/%post_id%.html' ),
			array( '/%postname%.html' ),
			array( '/%year%/%monthnum%/%day%/%postname%' ),
			array( '/%year%/%monthnum%/%day%/%post_id%' ),
			array( '/%author%/%postname%' ),
		);
	}

	/**
	 *
	 * @test
	 * @group permalink
	 * @dataProvider structure_provider
	 *
	 * @param $structure
	 */
	public function test_permalink( $structure ) {

		$post_type = rand_str( 12 );
		register_post_type( $post_type,
			array(
				'public'                   => true,
				'sptp_permalink_structure' => $post_type . $structure,
			)
		);

		do_action( 'wp_loaded' );//fire SPTP_Rewrite::register_rewrite_rules

		/** @var WP_Rewrite $wp_rewrite */
		global $wp_rewrite;
		$wp_rewrite->flush_rules(); //regenerate rewrite rules.

		$id = $this->factory->user->create();
		$post_name = rand_str( 12 );
		$id        = $this->factory->post->create( array( 'post_type' => $post_type, 'post_name' => $post_name, 'post_author' => $id ) );
		$post      = get_post( $id );


		$author_data = get_userdata( $post->post_author );
		$author     = $author_data->user_nicename;

		$post_date = strtotime( $post->post_date );

		$search  = array(
			"%postname%",
			"%post_id%",
			'%year%',
			'%monthnum%',
			'%day%',
			'%hour%',
			'%minute%',
			'%second%',
			'%author%',
		);
		$replace = array(
			$post_name,
			$post->ID,
			date( 'Y', $post_date ),
			date( 'm', $post_date ),
			date( 'd', $post_date ),
			date( 'H', $post_date ),
			date( 'i', $post_date ),
			date( 's', $post_date ),
			$author,
		);

		$url_base = str_replace( $search, $replace, "${post_type}/". trim( $structure, '/' ) );

		$expected = home_url( $url_base );

		$this->assertEquals( $expected, get_permalink( $id ) );

		$this->assertEquals( $id, url_to_postid( get_permalink( $id ) ) );
		$this->go_to( get_permalink( $id ) );
		$this->assertQueryTrue( 'is_single', 'is_singular' );

		$this->go_to( add_query_arg( 'page', 2, get_permalink( $id ) ) );
		$this->assertQueryTrue( 'is_single', 'is_singular' );
		$this->assertEquals( get_query_var( "page" ), 2 );

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