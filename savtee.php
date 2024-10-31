<?php

/**
 * Plugin Name: SavTee &trade;
 * Description: --
 * Version: 1.0
 * Author: owlweb
 * Author URI: https://www.owlweb.de
 *
 * @package --
 * @author dk@owlweb
 */
 
/*
ini_set('display_errors', true);
error_reporting(E_ALL);
*/
$GLOBALS['upconfig'] = (include 'config/main.php');

global $upconfig;

// re setup current plugin paths
$upconfig['PATH']['UP_PLUGIN_PATH'] = plugin_dir_path(__FILE__);
$upconfig['PATH']['UP_PLUGIN_URL'] = plugin_dir_url(__FILE__);

// only for development - activate error reporting
// include $upconfig['PATH']['UP_PLUGIN_INC_PATH'] . 'debug.php';

include $upconfig['PATH']['UP_PLUGIN_INC_PATH'] . 'secure.php';

require $upconfig['PATH']['UP_PLUGIN_CLASS_PATH'] . '__autoloader.php';
require $upconfig['PATH']['UP_PLUGIN_HTTP_PATH'] . 'ajax_functions.php';

$UPAdminOptions = new UPAdminOptions;
$UPAdminPlugin = new UPAdminPlugin;


// Admin options
register_activation_hook(__FILE__, [&$UPAdminOptions, 'install']);
register_uninstall_hook(__FILE__, [&$UPAdminOptions, 'uninstall']);

add_action('admin_menu', [&$UPAdminOptions, 'getOptionPage']);

add_filter(
    'plugin_action_links_' . plugin_basename(__FILE__),
    [
        &$UPAdminOptions,
        'addSettingsActionLink'
    ]
);

// Admin plugin
add_action('admin_menu', [&$UPAdminPlugin, 'getPluginPage']);
add_action('admin_bar_menu', [&$UPAdminPlugin, 'addAdminBarLink']);
add_action('admin_enqueue_scripts',[&$UPAdminPlugin, 'enqueueScripts']);

// Ajax request handling
if (
    isset($UpAjax->_req['action']) &&
    isset($UpAjax->_req['ajax']) &&
    $UpAjax->_req['ajax'] == true
) {
    add_action('wp_ajax_' . $UpAjax->_req['action'], $UpAjax->_req['action']);
    add_action('wp_ajax_nopriv_' . $UpAjax->_req['action'], $UpAjax->_req['action']);
}

include $upconfig['PATH']['UP_PLUGIN_INC_PATH'] . 'meta_tags.php';