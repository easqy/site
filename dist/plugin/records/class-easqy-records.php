<?php


class Easqy_Records
{
    public const MANAGE_CAPABILITY = 'easqy_manage_records';

    public function __construct($easqy)
    {
        $this->load_dependencies();
        $this->define_admin_hooks($easqy->get_loader());
        $this->define_public_hooks($easqy->get_loader());
    }

    public function load_dependencies()
    {
        require_once EASQY_DIR . '/records/includes/class-easqy-records-common.php';
        require_once EASQY_DIR . '/records/includes/class-easqy-records-db.php';
        require_once EASQY_DIR . '/records/admin/class-easqy-records-admin.php';
    }

    public function set_locale(){

    }

    public function define_admin_hooks($loader){
        $plugin_admin = new Easqy_Records_Admin( '','' );
        $plugin_admin->define_hooks($loader);

    }

    public function define_public_hooks(){

    }

}