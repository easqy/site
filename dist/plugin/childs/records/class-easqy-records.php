<?php


class Easqy_Records
{
    public const MANAGE_CAPABILITY = 'easqy_manage_records';

    public function __construct(Easqy $easqy)
    {
        $this->load_dependencies();
	    if (is_admin())
	        $this->define_admin_hooks($easqy->get_loader());
	    else
	        $this->define_public_hooks($easqy->get_loader());

        $ajax = new Easqy_Records_Ajax();
        $ajax->define_hooks($easqy->get_loader());


    }

    public function load_dependencies()
    {
        $dir = dirname(__FILE__);
        require_once $dir . '/includes/class-easqy-records-common.php';
        require_once $dir . '/includes/class-easqy-records-db.php';
        require_once $dir . '/includes/class-easqy-records-ajax.php';
	    if (is_admin())
		    require_once $dir . '/admin/class-easqy-records-admin.php';
	    else
	        require_once $dir . '/public/class-easqy-records-public.php';
    }

    public function set_locale(){

    }

    public function define_admin_hooks($loader){
        $plugin_admin = new Easqy_Records_Admin();
        $plugin_admin->define_hooks($loader);
    }

    public function define_public_hooks($loader){
        $plugin_public = new Easqy_Records_Public();
        $plugin_public->define_hooks($loader);
    }

}