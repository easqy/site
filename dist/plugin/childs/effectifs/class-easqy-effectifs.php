<?php


class Easqy_Effectifs
{

    public function __construct(Easqy $easqy)
    {
        $this->load_dependencies();
        $this->define_admin_hooks($easqy->get_loader());
        $this->define_public_hooks($easqy->get_loader());

	    new Easqy_Effectifs_Public($easqy);
    }

    public function load_dependencies()
    {
        $dir = dirname(__FILE__);
        require_once $dir . '/public/class-easqy-effectifs-public.php';
    }

    public function set_locale(){

    }

    public function define_admin_hooks($loader){
    }

    public function define_public_hooks($loader){
    }

}