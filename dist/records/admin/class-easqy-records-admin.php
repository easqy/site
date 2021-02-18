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

    private $admin_colors;

	public function __construct( ) {
	}

    public function define_hooks( $loader )
    {
        $loader->add_action('admin_menu', $this, 'define_menus' );
        $loader->add_action('admin_enqueue_scripts', $this, 'enqueue_styles');
        $loader->add_action('admin_enqueue_scripts', $this, 'enqueue_scripts');

        $loader->add_action('wp_ajax_easqy_records', $this, 'ajax_records');
        $loader->add_action('wp_ajax_easqy_record_del', $this, 'ajax_record_del');

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

        $users = get_users('role=editor');
        foreach ( $users as $user ) {
            echo '<span>' . esc_html( $user->display_name ) . '</span>';
        }

         echo '<div id="easqy-records-adm" class="wrap">records</div>';
    }

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
        wp_enqueue_style(EASQY_NAME . '-records-adm', plugins_url('js/index.css', __FILE__ ),
        ['editor-buttons','wp-components','wp-block-editor']);
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
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

	public function ajax_records() {

	    $a = array();
	    foreach( Easqy_Records_DB::athletes() as $row)
	        $a []= array( 'i' => intval( $row['id'] ), 'n' => $row['nom'], 'p' => $row['prenom']);

        $r = array();
        foreach( Easqy_Records_DB::records() as $row)
            $r []= array(
                'i' => intval( $row['id'] ),
                'c' => intval($row['categorie']),
                'e' => intval($row['epreuve']),
                'in' => intval($row['indoor']),
                'g' => intval($row['genre']),
                'd' => $row['date'],
                'l' => $row['lieu'],
                'p' => $row['performance'],
                'f' => $row['infos']
            );

        $ra = array();
        foreach( Easqy_Records_DB::recordsAthletes() as $row) {
            $item = array(
                'r' => intval($row['record']),
                'a' => intval($row['athlete'])
            );
            if ($row['categorie'])
                $item['c'] = intval($row['categorie']);

            $ra [] = $item;
        }

        $result = array(
            'status' => 'ok',
            'genres' => Easqy_Records_Common::GENRES,
            'categories' => Easqy_Records_Common::CATEGORIES,
            'epreuves' => Easqy_Records_Common::EPREUVES,
            'athletes' => $a,
            'records' => $r,
            'ra' => $ra
        );
        wp_send_json_success( $result );
    }

    public function ajax_record_del() {
	    if (!isset($_POST['recordId']))
        {
            wp_send_json_error( array('status' => 'error', 'message' => 'no record id') );
            return;
        }

        $recordId = $_POST['recordId'];
        $result = Easqy_Records_DB::deleteRecord($recordId);

        $result = array(
            'status' => 'ok'
        );
        wp_send_json_success($result);
    }

}
