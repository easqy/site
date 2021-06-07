<?php

if (! defined('EASQY_CHILDS_DIR')) {
    define('EASQY_CHILDS_DIR', EASQY_DIR . '/childs');
}

if (! defined('EASQY_ENABLE_LAST_RESULTS')) {
    define('EASQY_ENABLE_LAST_RESULTS', true);
}

if (! defined('EASQY_ENABLE_TROMBINOSCOPE')) {
    define('EASQY_ENABLE_TROMBINOSCOPE', false);
}

class Easqy_Childs
{
    public function __construct(Easqy $easqy)
    {
        $this->load_dependencies();
        $this->define_admin_hooks();
        $this->define_public_hooks();

        new Easqy_Records($easqy);

        if (EASQY_ENABLE_LAST_RESULTS) {
            new Easqy_Latest_Results($easqy);
        }

        new Easqy_Effectifs($easqy);
    
        if (EASQY_ENABLE_TROMBINOSCOPE) {
            new Easqy_Trombinoscope($easqy);
        }
    }

    private function load_dependencies()
    {
        $dir = dirname(__FILE__);
        require_once $dir . '/records/class-easqy-records.php';

        if (EASQY_ENABLE_LAST_RESULTS) {
            require_once $dir . '/latest_results/class-easqy-latest-results.php';
        }

        require_once $dir . '/effectifs/class-easqy-effectifs.php';
		
        if (EASQY_ENABLE_TROMBINOSCOPE) {
            require_once $dir . '/trombinoscope/class-easqy-trombinoscope.php';
        }
    }

    private function define_admin_hooks()
    {
    }

    private function define_public_hooks()
    {
    }
}
