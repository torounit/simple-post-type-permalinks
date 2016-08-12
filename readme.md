# Simple Post Type Permalinks
Donate link:       http://www.amazon.co.jp/registry/wishlist/COKSXS25MVQV
License:           GPLv2 or Later
License URI:       http://www.gnu.org/licenses/gpl-2.0.html

Easy to change Permalink of custom post type.


[![Build Status](https://travis-ci.org/torounit/simple-post-type-permalinks.svg)](https://travis-ci.org/torounit/simple-post-type-permalinks)
[![](https://img.shields.io/wordpress/plugin/dt/simple-post-type-permalinks.svg)](https://wordpress.org/plugins/simple-post-type-permalinks/)
[![](https://img.shields.io/wordpress/v/simple-post-type-permalinks.svg)](https://wordpress.org/plugins/simple-post-type-permalinks/)
[![](https://img.shields.io/wordpress/plugin/r/simple-post-type-permalinks.svg)](https://wordpress.org/plugins/simple-post-type-permalinks/)

[![](http://www.torounit.com/wp-content/uploads/2015/04/banner-1544x5001.png)](https://wordpress.org/plugins/simple-post-type-permalinks/)

## Description

Edit the permalink structure of custom post type too easy.

Simple Post Type Permalinks is Simple and Smart than [Custom Post Type Permalinks](https://wordpress.org/plugins/custom-post-type-permalinks/).

Available tags are `%post_id%`, `%postname%`, `%year%`, `%monthnum%`, `%day%`, `%hour%`, `%minute%`, `%second%`, `%author%`.

Requires PHP version 5.3 or higher.

[This Plugin published on GitHub.](https://github.com/torounit/simple-post-type-permalinks)

Please Fork and Pull Request!


## Installation

### Manual Installation

1. Upload the entire `/simple-post-type-permalinks` directory to the `/wp-content/plugins/` directory.
2. Activate Simple Post Type Permalinks through the 'Plugins' menu in WordPress.

Access the permalinks setting by going to *Settings -> Permalinks*.

## Frequently Asked Questions

### Which tag that can be used?

Only `%post_id%` and `%postname%`.


## Setting on Code


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


## Screenshots

* screenshot-1.png

## Changelog

### 2.0.0

* Change of class structure.
* Change namespace.
* Add abstract Module class.
* Use autoloader.
* Remove constructor injection and add setter injection for modules.
* Support `%year%`, `%monthnum%`, `%day%`, `%hour%`, `%minute%`, `%second%`, `%author%`.

### 1.3.1

* fix pagination link.

### 1.2.0

* fix textdomain.

### 1.1.0

* Test with WooCommerce and WPML.
* Admin Bug Fix.
* Support `get_post_type_archive`.


### 1.0.3

* Admin Bug Fix.

### 1.0.2

* Coding Standard Fix.

### 1.0.0

* Drop PHP 5.2.

### 0.1.0

* First release

## Upgrade Notice

### 1.0.0

* Drop PHP 5.2.

### 0.1.0
First Release
