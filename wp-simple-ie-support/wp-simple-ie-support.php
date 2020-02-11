<?php
/*
Plugin Name: WP Simple IE Support
Plugin URI: https://github.com/msigley
Description: Adds compatibility code layer for IE visitors. Provides a banner prompting them to download Edge.
Version: 1.0.0
Requires at least: 4.8.0
Author: Matthew Sigley
License: GPL2
*/

class WPSimpleIESupport {
	private static $version = '1.0.0';
	private static $object = null;
	private static $contruct_args = array( 'DEFINE_NAME' => 'arg1_name', 'arg2_name' );
	private static $flipped_construct_args = false;

	private $path = '';

	private $shortcodes = array( 'is_ie_banner' );

	private $api = null;
	private $public = null;

	private $is_ie = false;

	static function &object( $args=array() ) {
		if ( ! self::$object instanceof WPSimpleIESupport ) {
			if( ! self::$flipped_construct_args ) {
				self::$contruct_args = array_flip( self::$contruct_args );
				self::$flipped_construct_args = true;
			}
			self::$object = new WPSimpleIESupport( $args );
		}
		return self::$object;
	}

	private function __construct( $args ) {
		foreach( $args as $arg_name => $arg_value ) {
			if( empty($arg_value) || !isset( self::$contruct_args[$arg_name] ) )
				continue;

			if( !empty( self::$contruct_args[$arg_name] ) && defined( self::$contruct_args[$arg_name] ) )
				$this->$arg_name = constant( self::$contruct_args[$arg_name] );
			else
				$this->$arg_name = $arg_value;
		}
	}

	public function init() {
		$this->shortcodes = array_combine( $this->shortcodes, $this->shortcodes );

		//Plugin path
		$this->path = plugin_dir_path( __FILE__ );

		$agent = strtolower( $_SERVER['HTTP_USER_AGENT'] );
		$ie_ids = array(
			'msie', //IE 7 - 10
			'trident', //IE 11
			'edge' //Edge AKA IE 12 (non chromium based). The new chromium based Edge reports as "Edg" instead of "Edge"
		);
		foreach( $ie_ids as $ie_id ) {
			if( false !== stripos( $agent, $ie_id ) ) {
				$this->is_ie = true;
			}
		}

		//General API
		require_once $this->path . 'api.php';
		$this->api = WPSimpleIESupportAPI::object();

		//Shortcode API
		add_action( 'init', array( $this, 'add_shortcodes' ) );

		if( !is_admin() && $this->is_ie() ) {
			require_once $this->path . 'public.php';
			$this->public = new WPSimpleIESupportPublic();
		}
	}

	/*
	 * Shortcode Handling
	 */
	public function add_shortcodes() {
		foreach( $this->shortcodes as $shortcode )
			add_shortcode( $shortcode, array( $this, 'shortcode_callback' ) );
	}

	public function shortcode_callback( $atts, $content, $shortcode_tag ) {
		$params = array();
		if( is_array( $atts ) )
			$params = $atts;
		$output = call_user_func( $shortcode_tag, $params );
		return $output;
	}

	public function do_shortcode_tag( $shortcode_tag, $params ) {
		if( !isset( $this->shortcodes[$shortcode_tag] ) )
			return;
		
		if( method_exists( $this->public, 'do_shortcode_tag' ) )
			$this->public->do_shortcode_tag( $shortcode_tag, $params );
	}

	/*
	 * Helper functions
	 */
	public function get_version() {
		return self::$version;
	}

	public function get_path() {
		return $this->path;
	}

	public function is_ie() {
		return $this->is_ie;
	}
}

$plugin_obj = WPSimpleIESupport::object();
if( !empty( $plugin_obj ) )
	$plugin_obj->init();