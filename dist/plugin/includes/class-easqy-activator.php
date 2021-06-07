<?php
/**
 * Fired during plugin activation
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Easqy
 * @subpackage Easqy/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Easqy
 * @subpackage Easqy/includes
 * @author     Your Name <email@example.com>
 */

class Easqy_Activator {

	public static function activate() {
        require_once EASQY_CHILDS_DIR . '/records/includes/class-easqy-records-activator.php';
        Easqy_Records_Activator::activate();

        require_once EASQY_CHILDS_DIR . '/encadrement/admin/class-easqy-encadrement-activator.php';
        Easqy_Encadrement_Activator::activate();
	}

}