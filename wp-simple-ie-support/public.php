<?php
class WPSimpleIESupportPublic {
	private $main = null;
	private $enqueued_shortcode_css_js = false;

	public function __construct() {
		$this->main = WPSimpleIESupport::object();

		add_filter( 'wp_head', array( $this, 'print_shim_js' ), 1 ); // Shim js needs to be printed as early as possible
	}

	public function print_shim_js() {
		// https://github.com/aFarkas/html5shiv
		// https://github.com/ryanelian/ts-polyfill
		// https://github.com/nuxodin/ie11CustomProperties
		?>
		<!-- HTML5 Shiv -->
		<script type="text/javascript">
		<?php include $this->main->get_path() . 'js/html5shiv-printshiv.min.js'; ?>
		</script>
		<!-- TS-Polyfill -->
		<script type="text/javascript">
		<?php include $this->main->get_path() . 'js/ts-polyfill.js'; ?>
		</script>
		<!-- Nodelist Polyfill -->
		<script type="text/javascript">
		<?php include $this->main->get_path() . 'js/nodelist-polyfill.js'; ?>
		</script>
		<!-- CSS Variable Polyfill for IE 11 -->
		<!--[if IE 11]>
		<script type="text/javascript">
		<?php include $this->main->get_path() . 'js/ie11CustomProperties.js'; ?>
		</script>
		<![endif]-->
		<?php
	}

	public function do_shortcode_tag( $shortcode_tag, $params ) {
		if( !$this->enqueued_shortcode_css_js ) {
			$this->enqueued_shortcode_css_js = true;

			wp_enqueue_style( 'ie-support-shortcode', plugins_url( '/css/shortcode.css', __FILE__ ), $this->main->get_version(), true );
		}
	}
}