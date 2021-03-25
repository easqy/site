<?php

class Easqy_Effectifs_Admin
{
    public function __construct(Easqy $easqy) {

        $this->load_dependencies();
        $this->define_hooks($easqy->get_loader());
	    new EasqyEffectifsAjax($easqy);
    }


    private function load_dependencies() {
	    $dir = dirname(__FILE__);
	    require_once $dir . '/class-easqy-effectifs-ajax.php';
    }

    private function define_hooks(Easqy_Loader $loader)
    {
	    $loader->add_action('admin_menu', $this, 'define_menus' );
    }

	public function define_menus()
	{
		add_submenu_page(
			Easqy::ADMIN_MAIN_MENU_SLUG,
			'Effectifs',
			'Effectifs',
			'manage_options',
			'easqy-options-effectifs',
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

	public function menu_page()
	{
		?>
		<div class="wrap">
			<h1>Réglages de la page des effectifs</h1>
			<div>
				Pour modifier les valeurs <a href="/le-club/effectifs/" target="_blank">des effectifs</a>,
				il faut modifier le document google sheet lié au <a href="https://accounts.google.com" target="_blank">compte google "easqy.site"</a>.
			</div>
		</div>
		<?php
	}

}
