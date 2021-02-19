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
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'records/includes/class-easqy-records-activator.php';
        Easqy_Records_Activator::activate();
    }

}