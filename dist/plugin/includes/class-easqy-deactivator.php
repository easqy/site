<?php

class Easqy_Deactivator {

	public static function deactivate() {
        require_once EASQY_CHILDS_DIR . '/records/includes/class-easqy-records-deactivator.php';
        Easqy_Records_Deactivator::deactivate();
	}

}