<?php
class WPSimpleIESupportAPI {
	private static $object = null;
	private static $main = null;

	public static function &object() {
		if ( ! self::$object instanceof WPSimpleIESupportAPI )
			self::$object = new WPSimpleIESupportAPI();
		return self::$object;
	}

	private function __construct() {
		self::$main = WPSimpleIESupport::object();

		$class_methods = get_class_methods( $this );
		foreach( $class_methods as $api_function_name ) {
			if( 'object' == $api_function_name || '__' == substr( $api_function_name, 0, 2 ) || function_exists( $api_function_name ) )
				continue;

			// Declare the class method in the global namespace.
			// This is an appropriate and secure use of eval() as no user input is involved.
			eval(
				'function ' . $api_function_name . '( $args = array() ) { 
					return ' . self::class . '::' . $api_function_name . '( $args ); 
				}'
			);
		}
	}

	// This gets run on every API Function call
	public static function __callStatic( $function_name, $args ) {
		self::$function_name( $arguments[0] );
		self::$main->do_shortcode_tag( $function_name, $args );
	}

	/*
	 * API Functions. 
	 * They will automatically be declared as a global function for template use.
	 * All functions need to be private static and accept an array as their first parameter.
	 */
	private static function is_ie_banner() {
		if( !self::$main->is_ie() )
			return;
		?>
		<div class="is_ie_banner">
			Looks like you're using an outdated web browser.<br />
			This website will no longer support outdated versions of Internet Explorer and Edge.<br />
			<u>Click here to download and install the latest version of Microsoft Edge to use all of the features of this website.</u>
			<a href="https://www.microsoft.com/en-us/edge" class="link" target="_blank"></a>
		</div>
		<?php
	}
}
