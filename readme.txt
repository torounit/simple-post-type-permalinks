=== Simple Post Type Permalinks ===
Contributors:      Toro_Unit
Donate link:       http://www.amazon.co.jp/registry/wishlist/COKSXS25MVQV
Tags:               permalink,permalinks,custom post type,cms
Requires at least: 4.1.1
Tested up to:      4.2
Stable tag:        0.1.0
License:           GPLv2
License URI:       http://www.gnu.org/licenses/gpl-2.0.html

Easy to change the custom post type of permlink.

== Description ==

Edit the permalink structure of custom post type too easy.

[This Plugin published on GitHub.](https://github.com/torounit/simple-post-type-permalinks)

Please Fork and Pull Request!



== Installation ==

= Manual Installation =

1. Upload the entire `/simple-post-type-permalinks` directory to the `/wp-content/plugins/` directory.
2. Activate Simple Post Type Permalinks through the 'Plugins' menu in WordPress.

== Frequently Asked Questions ==

= Which tag that can be used? =

Only `%post_id%` and `%postname%`.


== Setting on Code. ==


Example:

`
register_post_type( 'foo',
	array(
		"public" => true,
		'has_archive' => true,
		"rewrite" => [
			"with_front" => true
		],
		"sptp_permalink_structure" => "foo/%post_id%"
	)
);
`


== Screenshots ==

* screenshot-1.png

== Changelog ==

= 0.1.0 =
* First release

== Upgrade Notice ==

= 0.1.0 =
First Release
