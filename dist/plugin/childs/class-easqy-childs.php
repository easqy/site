<?php

if ( ! defined( 'EASQY_CHILDS_DIR' ) ) {
    define( 'EASQY_CHILDS_DIR', EASQY_DIR . '/childs' );
}

class Easqy_Childs
{
    public function __construct(Easqy $easqy) {

        $this->load_dependencies();
        $this->define_admin_hooks();
        $this->define_public_hooks();

        new Easqy_Records($easqy);
        new Easqy_Shortcodes($easqy);
    }


    private function load_dependencies() {
        $dir = dirname(__FILE__);
        require_once $dir . '/records/class-easqy-records.php';
        require_once $dir . '/shortcodes/class-easqy-shortcodes.php';
    }

    private function define_admin_hooks() {

    }

    private function define_public_hooks() {


    }

}