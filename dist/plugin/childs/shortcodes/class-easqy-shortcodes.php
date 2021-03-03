<?php


class Easqy_Shortcodes
{
    public function __construct(Easqy $easqy) {

        $this->load_dependencies();
        $this->define_admin_hooks();
        $this->define_public_hooks();

        new Easqy_Shortcodes_Latest_Results($easqy);
    }


    private function load_dependencies() {
        $dir = dirname(__FILE__);
        require_once $dir . '/latest_results/class-easqy-shotcodes-latest-results.php';
    }

    private function define_admin_hooks() {

    }

    private function define_public_hooks() {


    }

}