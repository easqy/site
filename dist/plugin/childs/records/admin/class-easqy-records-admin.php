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
class Easqy_Records_Admin {

	public function __construct( ) {
	}

    public function define_hooks( $loader )
    {

        $loader->add_action('admin_menu', $this, 'define_menus' );

        // admin_enqueue_scripts is used for enqueuing both scripts and styles.
        $loader->add_action('admin_enqueue_scripts', $this, 'enqueue_styles');
        $loader->add_action('admin_enqueue_scripts', $this, 'enqueue_scripts');

        $loader->add_action('set_current_user', $this, 'set_current_user');

        /*
            $user = wp_get_current_user(); // getting & setting the current user
            $roles = ( array ) $user->roles; // obtaining the role
            var_dump($roles);

            $role = get_role( 'editor' );
            var_dump($role->capabilities);
            var_dump(current_user_can( Easqy_Records::MANAGE_CAPABILITY ));
            die();
        */
    }

    public function set_current_user()
    {
        $user= wp_get_current_user();

        if ( in_array( 'administrator', (array) $user->roles ) )
        {
            $canManage = true;
        }
        else
        {
            $meta = get_user_meta( $user->ID, 'easqy_records_can_manage', true );
            $canManage = ($meta === '1');
        }

        if ($canManage === true)
            $user->add_cap(Easqy_Records::MANAGE_CAPABILITY, true);
    }

    public function define_menus()
    {
        add_submenu_page(
            Easqy::ADMIN_MAIN_MENU_SLUG,
            'Records',
            'Records',
            Easqy_Records::MANAGE_CAPABILITY,
            'easqy-options-records',
            array($this, 'menu_page')
        );
    }

    public function menu_page()
    {
        if ( !current_user_can( Easqy_Records::MANAGE_CAPABILITY ) )  {
            wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
        }

        if ( current_user_can( 'manage_options' ) ) {

            echo '<div id="easqy-records-adm-users"></div>';

            /*
            $users = get_users('role=administrator');
            foreach ($users as $user) {
                echo '<span>' . esc_html($user->display_name) . '</span>';
            }
            */
        }
        else
            echo '<div id="easqy-records-adm"></div>';
    }

	public function enqueue_styles() {
        wp_enqueue_style(
            EASQY_NAME . '-records-adm',
            plugins_url('js/index.css', __FILE__ ),
            ['editor-buttons','wp-components','wp-block-editor']
        );
	}

	public function enqueue_scripts() {

        $script_asset = require( plugin_dir_path(__FILE__) . 'js/index.asset.php' );
        $handle = EASQY_NAME . '-records-adm';
        wp_enqueue_script(
            $handle,
            plugins_url( 'js/index.js', __FILE__ ),
            $script_asset['dependencies'],
            time(), true );

        $inlineCode = 'const easqy_records_adm=';
        $inlineCode.= json_encode( [
            'ajaxurl' => admin_url( 'admin-ajax.php' )
        ] ) ;
        $inlineCode.= ';';
        wp_add_inline_script($handle, $inlineCode,'before');
	}


}
