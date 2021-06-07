<?php


class Easqy_Trombinoscope_Public {
	const SCRIPT_STYLE_HANDLE = EASQY_NAME . '-trombinoscope-pub';

	public function __construct(Easqy $easqy) {

		$this->load_dependencies();
		$this->define_public_hooks($easqy->get_loader());
	}

	private function load_dependencies() {
	}


	private function define_public_hooks(Easqy_Loader $loader)
	{
		$loader->add_shortcode('easqy_encadrement', $this, 'easqy_encadrement');
		$loader->add_action('wp_enqueue_scripts', $this, 'enqueue_styles');
		$loader->add_action('wp_enqueue_scripts', $this, 'enqueue_scripts');
	}

	public function enqueue_styles() {
		wp_register_style(
			self::SCRIPT_STYLE_HANDLE,
			plugins_url( 'js/index.css', __FILE__ ), []
		);
	}

	public function enqueue_scripts() {
		$script_asset = require( plugin_dir_path(__FILE__) . 'js/index.asset.php' );
		wp_register_script(
			self::SCRIPT_STYLE_HANDLE,
			plugins_url( 'js/index.js', __FILE__ ),
			$script_asset['dependencies'],
			time(), true );
	}

	public function easqy_encadrement( $atts = [], $content = null, $tag = '') {
		wp_enqueue_style(self::SCRIPT_STYLE_HANDLE);
		wp_enqueue_script(self::SCRIPT_STYLE_HANDLE);

		ob_start();
        ?><div id="easqy-dirigeants"></div><?php
		return ob_get_clean();
	}

}