<?php

class Easqy_Records_Public {

    const SCRIP_STYLE_HANDLE = EASQY_NAME . '-records-pub';

	public function __construct( ) {
	}

    public function define_hooks( $loader )
    {
        $loader->add_shortcode('easqy_records', $this, 'sc_records');
        $loader->add_action('wp_enqueue_scripts', $this, 'enqueue_styles');
        $loader->add_action('wp_enqueue_scripts', $this, 'enqueue_scripts');
    }

	public function enqueue_styles() {
        wp_register_style(
            self::SCRIP_STYLE_HANDLE,
            plugins_url( 'js/index.css', __FILE__ ), ['wp-components']
        );
    }

	public function enqueue_scripts() {
        $script_asset = require( plugin_dir_path(__FILE__) . 'js/index.asset.php' );
        wp_register_script(
	        self::SCRIP_STYLE_HANDLE,
            plugins_url( 'js/index.js', __FILE__ ),
            $script_asset['dependencies'],
            time(), true );
	}

    public function sc_records() {
        ob_start();
        ?>
        <div id="easqy-records">
            Initialisation ...
        </div>
        <?php
	    wp_enqueue_style(self::SCRIP_STYLE_HANDLE);
	    wp_enqueue_script(self::SCRIP_STYLE_HANDLE);
        return ob_get_clean();

    }
}
