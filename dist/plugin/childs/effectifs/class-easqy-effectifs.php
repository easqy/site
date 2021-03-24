<?php


class Easqy_Effectifs
{

    public function __construct(Easqy $easqy)
    {
        $this->load_dependencies();
	    if (is_admin()) {
		    $this->define_admin_hooks($easqy->get_loader());
		    new Easqy_Effectifs_Admin( $easqy );
	    }
	    else {
		    $this->define_public_hooks($easqy->get_loader());
		    new Easqy_Effectifs_Public( $easqy );
	    }
    }

    public function load_dependencies()
    {
        $dir = dirname(__FILE__);
	    if (is_admin())
		    require_once $dir . '/admin/class-easqy-effectifs-admin.php';
	    else
		    require_once $dir . '/public/class-easqy-effectifs-public.php';
    }

    public function set_locale() {
    }

    public function define_admin_hooks($loader){
    }

    public function define_public_hooks($loader){
    }

}