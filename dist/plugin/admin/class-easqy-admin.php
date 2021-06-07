<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/admin
 * @author     Your Name <email@example.com>
 */
class Easqy_Admin {

	const SCRIPT_STYLE_HANDLE = EASQY_NAME . '-admin';

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct(Easqy $easqy) {
		$this->define_hooks( $easqy->get_loader() );

        $dir = dirname(__FILE__);
        require_once $dir . '/class-easqy-admin-ajax.php';
		$easqyAdminAjax = new Easqy_Admin_Ajax($easqy);
	}

	public function define_hooks( Easqy_Loader $loader )
    {
        $loader->add_action('admin_menu', $this, 'define_menus' );
        $loader->add_action('admin_enqueue_scripts', $this, 'enqueue_styles');
        $loader->add_action('admin_enqueue_scripts', $this, 'enqueue_scripts');
    }

    public function define_menus( )
    {
        add_menu_page(
            'EASQY options',
            'EASQY',
	        'publish_posts',
            Easqy::ADMIN_MAIN_MENU_SLUG,
            array($this, 'menu_page')
        );

	    add_submenu_page(
		    Easqy::ADMIN_MAIN_MENU_SLUG,
		    'Tous les réglages',
		    'Tous les réglages',
		    'publish_posts',
		    Easqy::ADMIN_MAIN_MENU_SLUG,
		    array($this, 'menu_page')
	    );
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {

    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {

        $script_asset = require( plugin_dir_path(__FILE__) . 'js/index.asset.php' );
        wp_enqueue_script(
            self::SCRIPT_STYLE_HANDLE,
            plugins_url( 'js/index.js', __FILE__ ),
            $script_asset['dependencies'],
            time(), true );

        $inlineCode = 'const easqy_admin=' . json_encode( [
            'ajaxurl' => admin_url( 'admin-ajax.php' )
        ] ) . ';';

        wp_add_inline_script(self::SCRIPT_STYLE_HANDLE, $inlineCode,'before');
    }

    function menu_page()
    {
        echo '<div>EASQY options</div>';
    }


}