<?php

class Easqy_Records_Public {

	public function __construct( ) {
	}

    public function define_hooks( $loader )
    {
        $loader->add_shortcode('easqy_records', $this, 'sc_records');
        $loader->add_action('wp_enqueue_scripts', $this, 'enqueue_styles');
        $loader->add_action('wp_enqueue_scripts', $this, 'enqueue_scripts');
    }

	public function enqueue_styles() {
        $handle = EASQY_NAME . '-records-pub';
        wp_enqueue_style(
            $handle,
            plugins_url( 'js/index.css', __FILE__ ), ['wp-components']
        );
    }

	public function enqueue_scripts() {
        //error_log('Easqy_Records_Public::enqueue_scripts');
        //error_log(plugins_url( 'js/index.js', __FILE__ ));
        //error_log( print_r( $this->shortcodes, true ) );
        $script_asset = require( plugin_dir_path(__FILE__) . 'js/index.asset.php' );
        $handle = EASQY_NAME . '-records-pub';
        wp_enqueue_script(
            $handle,
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
        /*
        $script_asset_path = 'js/index.asset.php';
        $index_js= 'js/index.js';
        $styles  = 'js/index.css';
        $script_asset = require( $script_asset_path );

        wp_enqueue_script(
            'easqy-records',
            plugins_url( $index_js, __FILE__ ),
            $script_asset['dependencies'],
            time(), true );
        wp_enqueue_style(
            'easqy-records',
            plugins_url( $styles, __FILE__ )
        );
        */
        return ob_get_clean();

    }
}
