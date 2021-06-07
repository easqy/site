<?php
/*
  Plugin Name: EASQY
  Description: Ensemble d'outils pour gérer le site de l'EASQY
  Author: Frachop
  Version: 1.0.0
 */
/*
Comme tout site web, des cookies sont écrits sur votre ordinateur.
Les cookies utilisés sur Murviel-Info-Béziers sont ceux de Google Analytics et wordPress, rien de plus.
*/

// If this file is called directly, abort.
if (! defined('WPINC')) {
    die;
}
const EASQY_VERSION = '1.0.0';
define('EASQY_NAME', trim(dirname(plugin_basename(__FILE__)), '/'));
const EASQY_DIR = WP_PLUGIN_DIR . '/' . EASQY_NAME;
const EASQY_URL = WP_PLUGIN_URL . '/' . EASQY_NAME;
require EASQY_DIR . '/includes/class-easqy-settings.php';

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-plugin-name-activator.php
 */
function activate_plugin_easqy()
{
    require_once EASQY_DIR . '/includes/class-easqy-activator.php';
    Easqy_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-plugin-name-deactivator.php
 */
function deactivate_plugin_easqy()
{
    require_once EASQY_DIR . '/includes/class-easqy-deactivator.php';
    Easqy_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_plugin_easqy');
register_deactivation_hook(__FILE__, 'deactivate_plugin_easqy');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-easqy.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_Easqy()
{
	$easqy = new Easqy();
	$easqy->run();
}
run_Easqy();
