<?php

class Easqy_Trombinoscope_Admin
{
	const SCRIPT_STYLE_HANDLE = EASQY_NAME . '-trombinoscope-adm';

    public function __construct(Easqy $easqy)
    {
        $this->load_dependencies();
        $this->define_hooks($easqy->get_loader());
        new Easqy_Trombinoscope_Ajax($easqy);
    }

    private function load_dependencies()
    {
        $dir = dirname(__FILE__);
        require_once $dir . '/class-easqy-trombinoscope-ajax.php';
    }

    private function define_hooks(Easqy_Loader $loader)
    {
        $loader->add_action('admin_menu', $this, 'define_menus');
		$loader->add_action('admin_enqueue_scripts', $this, 'enqueue_styles');
		$loader->add_action('admin_enqueue_scripts', $this, 'enqueue_scripts');
    }

    public function define_menus()
    {
        add_submenu_page(
            Easqy::ADMIN_MAIN_MENU_SLUG,
            'Trombinoscope',
            'Trombinoscope',
            'manage_options',
            'easqy-options-encadrement',
            array($this, 'menu_page')
        );
        /*
                add_menu_page('My Custom Page', 'My Custom Page', 'manage_options', 'my-top-level-slug');
                add_submenu_page( 'my-top-level-slug', 'My Custom Page', 'My Custom Page',
                    'manage_options', 'my-top-level-slug');
                add_submenu_page( 'my-top-level-slug', 'My Custom Submenu Page', 'My Custom Submenu Page',
                    'manage_options', 'my-secondary-slug');
        */
    }

	public function enqueue_styles() {
		wp_register_style(
			self::SCRIPT_STYLE_HANDLE,
            plugins_url('js/index.css', __FILE__ ),
            ['editor-buttons','wp-components','wp-block-editor']
		);
	}

	public function enqueue_scripts() {

		wp_enqueue_media();
		
		$script_asset = require( plugin_dir_path(__FILE__) . 'js/index.asset.php' );
		wp_register_script(
			self::SCRIPT_STYLE_HANDLE,
			plugins_url( 'js/index.js', __FILE__ ),
			$script_asset['dependencies'],
			time(), true );
	}

    public function menu_page()
    {
		wp_enqueue_style(self::SCRIPT_STYLE_HANDLE);
		wp_enqueue_script(self::SCRIPT_STYLE_HANDLE);
		?>
		<div class="wrap">
			<h1>Trombinoscope</h1>
			<div id="easqy-trombinoscope-adm"></div>
		</div>
		<?php
    }
}
