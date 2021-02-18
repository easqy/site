<?php

class Easqy_Deactivator {

	public static function deactivate() {
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'records/includes/class-easqy-records-deactivator.php';
        Easqy_Records_Deactivator::deactivate();
	}

}