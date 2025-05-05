<?php

/**
 * @package  Gpltimes
 */

/*
Plugin Name: GPL Times
Plugin URI: https://www.gpltimes.com/
Description: GPL Times Auto Updater
Version: 4.0.14
Author: GPL Times
Author URI: https://www.gpltimes.com/
License: GPLv2 or later
Text Domain: Gpltimes
Requires at least: 4.7
Requires PHP: 7.0
*/

if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!defined('gpltimes_version')) {
    define('gpltimes_version', '4.0.14');
}

// Check if plugin was updated and run cleanup if needed
function gpltimes_check_version_and_cleanup()
{
    $stored_version = get_option('gpltimes_version', '0');

    // If this is a new installation or an update from an older version
    if (version_compare($stored_version, '4.0.14', '<')) {
        // Clear all old scheduled crons
        wp_clear_scheduled_hook('gpl_cron_hook');
        wp_clear_scheduled_hook('gpl_cron_hook_time');
        wp_clear_scheduled_hook('gpl_cron_hook_member');
        wp_clear_scheduled_hook('gpl_plugin_update_check');
        wp_clear_scheduled_hook('gpl_time_check');
        wp_clear_scheduled_hook('gpl_member_check');
        wp_clear_scheduled_hook('gpltimes_auth_recheck');
        wp_clear_scheduled_hook('gpltimes_delayed_refresh');

        // Clear all transients
        delete_transient('gpltimes_api_result');
        delete_transient('gpltimes_daily_check_transient');
        delete_transient('gpltimes_notice_data');

        // Set up new cron schedules
        if (!wp_next_scheduled('gpl_plugin_update_check')) {
            wp_schedule_event(time(), 'hourly', 'gpl_plugin_update_check');
        }

        if (!wp_next_scheduled('gpltimes_auth_recheck')) {
            wp_schedule_event(time(), 'twicedaily', 'gpltimes_auth_recheck');
        }

        // Force a fresh update check
        if (function_exists('gpltimes_check_updates')) {
            gpltimes_check_updates(true);
        }

        // Update the stored version
        update_option('gpltimes_version', gpltimes_version);
    }
}
add_action('plugins_loaded', 'gpltimes_check_version_and_cleanup');

// Require the simplified autoloader
require_once dirname(__FILE__) . '/autoload.php';

//added to fix the WP 6.4.3 issue
add_filter('unzip_file_use_ziparchive', '__return_false');

// The code that runs during plugin activation
require_once dirname(__FILE__) . '/inc/GplCron/gpl-wp-cron.php';
require_once dirname(__FILE__) . '/inc/GplCron/gpl-auth-recheck.php';


function manual_enqueue_media_scripts()
{
    if (isset($_GET['page']) && $_GET['page'] == 'gpltimes-whitelabel') {
        wp_enqueue_media();
    }
}
add_action('admin_enqueue_scripts', 'manual_enqueue_media_scripts');


function activate_gpltimes_plugin()
{
    // Start output buffering
    ob_start();

    Inc\Base\Activate::activate();

    // Clear old cron jobs
    wp_clear_scheduled_hook('gpl_cron_hook');
    wp_clear_scheduled_hook('gpl_cron_hook_time');
    wp_clear_scheduled_hook('gpl_cron_hook_member');
    wp_clear_scheduled_hook('gpl_plugin_update_check');
    wp_clear_scheduled_hook('gpl_time_check');
    wp_clear_scheduled_hook('gpl_member_check');
    wp_clear_scheduled_hook('gpltimes_auth_recheck');

    // Plugin update check cron
    if (!wp_next_scheduled('gpl_plugin_update_check')) {
        wp_schedule_event(time(), 'hourly', 'gpl_plugin_update_check'); // Changed to 'hourly'
    }

    if (!wp_next_scheduled('gpltimes_auth_recheck')) {
        wp_schedule_event(time(), 'twicedaily', 'gpltimes_auth_recheck'); // 'twicedaily' is a default WP schedule
    }

    $gplplugslug = [];
    $gpldiffslug = [];
    $gplcron = '';
    update_option('gplpluginlistslug', $gplplugslug, true);
    update_option('gpldiffslug', $gpldiffslug, true);
    update_option('gplcrondata', $gplcron, true);
    update_option('gpltokenid', '', true);
    update_option('gplcheckedstatus', '0', true);
    update_option('gpltimes_beta_updates', '0', true);
    flush_rewrite_rules();

    // End output buffering and discard any output
    ob_end_clean();
}

register_activation_hook(__FILE__, 'activate_gpltimes_plugin');


function deactivate_gpltimes_plugin()
{
    // Clear the new cron jobs
    wp_clear_scheduled_hook('gpl_cron_hook');
    wp_clear_scheduled_hook('gpl_cron_hook_time');
    wp_clear_scheduled_hook('gpl_cron_hook_member');
    wp_clear_scheduled_hook('gpl_plugin_update_check');
    wp_clear_scheduled_hook('gpl_time_check');
    wp_clear_scheduled_hook('gpl_member_check');
    wp_clear_scheduled_hook('gpltimes_auth_recheck');
    wp_clear_scheduled_hook('gpltimes_delayed_refresh');

    // Remove the filters that manipulate the plugin updates
    remove_filter('site_transient_update_plugins', 'filter_plugin_updates');
    remove_filter('site_transient_update_plugins', 'filter_plugin_updates_main', 999999999);
    remove_filter('site_transient_update_plugins', 'disable_plugin_updates', 999999999);

    // Remove the filters that manipulate the theme updates
    remove_filter('site_transient_update_themes', 'disable_theme_updates', 999999999);

    // Call the main deactivate method - this handles all option and transient cleanup
    Inc\Base\Deactivate::deactivate();

    // Handle whitelabel settings separately
    $whitelabel_settings = get_option('gpltimes_whitelabel_settings', array());
    if (isset($whitelabel_settings['hide_settings'])) {
        unset($whitelabel_settings['hide_settings']);
        update_option('gpltimes_whitelabel_settings', $whitelabel_settings);
    }

    // Clear update transients and set empty objects
    // This is faster than deleting and letting WordPress rebuild
    set_site_transient('update_plugins', new \stdClass());
    set_site_transient('update_themes', new \stdClass());
}

register_deactivation_hook(__FILE__, 'deactivate_gpltimes_plugin');



function gpltimes_ensure_crons_exist()
{
    wp_clear_scheduled_hook('gpl_cron_hook');
    wp_clear_scheduled_hook('gpl_cron_hook_time');
    wp_clear_scheduled_hook('gpl_cron_hook_member');
    wp_clear_scheduled_hook('gpl_time_check');
    wp_clear_scheduled_hook('gpl_member_check');
    wp_clear_scheduled_hook('gpl_plugin_update_check');
    wp_clear_scheduled_hook('gpltimes_auth_recheck');


    if (!wp_next_scheduled('gpl_plugin_update_check')) {
        wp_schedule_event(time(), 'hourly', 'gpl_plugin_update_check');
    }

    if (!wp_next_scheduled('gpltimes_auth_recheck')) {
        wp_schedule_event(time(), 'twicedaily', 'gpltimes_auth_recheck');
    }

}

function gpltimes_daily_check()
{
    // Check if our transient is set, and if not, run the function
    if (false === get_transient('gpltimes_daily_check_transient')) {
        gpltimes_ensure_crons_exist();
        // Set our transient to expire in 24 hours
        set_transient('gpltimes_daily_check_transient', '1', DAY_IN_SECONDS);
    }
}
add_action('init', 'gpltimes_daily_check');

/**
 * Integrate with WordPress "Check Again" feature
 */
function gpltimes_integrate_with_wp_update_check($transient)
{
    // Use a static variable to track if we've already made a request this page load
    static $already_checked = false;

    // Check if this is a WordPress update check and we haven't already made a request
    if (!$already_checked && isset($_GET['force-check']) && $_GET['force-check'] == 1) {
        // Set the flag to prevent additional requests
        $already_checked = true;

        // Force refresh when WordPress "Check Again" button is used
        gpltimes_check_updates(true);
    }

    return $transient;
}
add_filter('pre_site_transient_update_plugins', 'gpltimes_integrate_with_wp_update_check');
add_filter('pre_site_transient_update_themes', 'gpltimes_integrate_with_wp_update_check');




// Initialize all the core classes of the plugin
if (class_exists('Inc\\Init')) {
    Inc\Init::register_services();
}

$result_slug = get_option('gpldiffslug');

function filter_plugin_updates($value)
{
    $result_slug = get_option('gpldiffslug');

    if ($result_slug != null) {
        foreach ($result_slug as $plugin) {
            if (isset($value->response[$plugin])) {
                unset($value->response[$plugin]);
            }
        }
    }

    return $value;
}

add_filter('automatic_updates_is_vcs_checkout', '__return_false', 1);
add_filter('site_transient_update_plugins', 'filter_plugin_updates');

$returndata = get_option('gplcrondata');
$token = esc_attr(get_option('gplstatus'));

if (empty($token)) {
    function filter_plugin_updates_main($value)
    {
        $returndata = get_option('gplcrondata');
        if ($returndata != null) {
            if (!empty($returndata)) {
                foreach ($returndata as $data) {
                    $returnslug = $data->slug;

                    if (isset($value->response[$returnslug])) {
                        unset($value->response[$returnslug]);
                    }
                }
            }
        }

        return $value;
    }

    add_filter('site_transient_update_plugins', 'filter_plugin_updates_main', 999999999);
    add_filter('site_transient_update_themes', 'filter_plugin_updates_main', 999999999);
}


function disable_plugin_updates($value)
{
    $pluginsToDisable = get_option('gpluncheckdata');

    if (!empty($pluginsToDisable)) {
        if (isset($value) && is_object($value)) {
            foreach ($pluginsToDisable as $plugin) {
                if (isset($value->response[$plugin])) {
                    unset($value->response[$plugin]);
                }
            }
        }
    }

    return $value;
}

function disable_theme_updates($value)
{
    $themesToDisable = get_option('gpluncheckdata');

    if (!empty($themesToDisable)) {
        if (isset($value) && is_object($value)) {
            foreach ($themesToDisable as $theme) {
                if (isset($value->response[$theme])) {
                    unset($value->response[$theme]);
                }
            }
        }
    }

    return $value;
}


$gplcheckedstatus = get_option('gplcheckedstatus');

if ($gplcheckedstatus == 1) {
    add_filter('site_transient_update_plugins', 'disable_plugin_updates', 999999999);
    add_filter('site_transient_update_themes', 'disable_theme_updates', 999999999);
    update_option('gplcheckedstatus', '0', true);
}

add_action('admin_init', 'gpltimes_check_update_on_plugins_page');

// Function to disable all admin notices if the option is enabled
function gpltimes_disable_all_admin_notices()
{
    $whitelabel_settings = get_option('gpltimes_whitelabel_settings', array());

    if (isset($whitelabel_settings['disable_all_admin_notices']) && $whitelabel_settings['disable_all_admin_notices'] == 1) {
        // Remove all admin notices
        remove_all_actions('admin_notices');
        remove_all_actions('all_admin_notices');
        remove_all_actions('user_admin_notices');
        remove_all_actions('network_admin_notices');
    }
}
add_action('admin_init', 'gpltimes_disable_all_admin_notices', 1);

function gpltimes_check_update_on_plugins_page()
{
    // Only run on relevant admin pages
    if (current_user_can('administrator') &&
    (strpos($_SERVER['REQUEST_URI'], 'plugins.php') !== false ||
     strpos($_SERVER['REQUEST_URI'], 'themes.php') !== false ||
     strpos($_SERVER['REQUEST_URI'], 'update-core.php') !== false)) {

        $last_check = get_option('gpltimes_last_update_check', 0);
        // Ensure $last_check is an integer
        if (!is_numeric($last_check)) {
            $last_check = 0;
        }
        $last_check = (int)$last_check;
        $current_time = time();

        // If more than 30 minutes have passed since the last check
        if ($current_time - $last_check >= 30 * MINUTE_IN_SECONDS) {
            // Use the new function with force refresh
            gpltimes_check_updates(true);
        }
    }
}


add_action('admin_init', 'gpltimes_check_update_after_plugin_activation');

function gpltimes_check_update_after_plugin_activation()
{
    if (isset($_GET['activate']) && $_GET['activate'] == 'true') {
        gpltimes_check_updates(true);
    }
}

add_action('switch_theme', 'gpltimes_check_update_after_theme_activation');

function gpltimes_check_update_after_theme_activation()
{
    gpltimes_check_updates(true);
}


//WPMU Dev update disable
add_action('admin_init', 'clear_plugin_update_option');
function clear_plugin_update_option()
{
    global $pagenow;
    if ($pagenow == 'plugins.php') {
        update_option('wdp_un_updates_available', '');
    }
}


function gpltimes_whitelabel_settings_page()
{
    // Just include the template - form processing is handled by admin_post hook
    include(plugin_dir_path(__FILE__) . 'templates/whitelabel-settings.php');
}

// Register AJAX handlers
add_action('wp_ajax_gpltimes_reset_whitelabel', 'gpltimes_reset_whitelabel_ajax');
add_action('wp_ajax_gpltimes_save_whitelabel', 'gpltimes_save_whitelabel_ajax');

function gpltimes_reset_whitelabel_ajax()
{
    // Check nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'gpltimes_whitelabel_settings')) {
        wp_send_json_error('Security check failed');
    }

    // Reset to default values
    $whitelabel_settings = array(
        'name' => '',
        'description' => '',
        'author' => '',
        'author_url' => '',
        'logo' => '',
        'hide_settings' => 0,
        'disable_updates_visibility' => 0,
        'disable_all_admin_notices' => 0,
    );

    // Update the option
    $result = update_option('gpltimes_whitelabel_settings', $whitelabel_settings);

    if ($result) {
        wp_send_json_success('Settings reset successfully');
    } else {
        wp_send_json_error('Failed to reset settings');
    }
}

function gpltimes_save_whitelabel_ajax()
{
    // Check nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'gpltimes_whitelabel_settings')) {
        wp_send_json_error('Security check failed. Please try again.');
    }

    // Save user inputs
    $whitelabel_settings = array(
        'name' => isset($_POST['gpltimes_name']) ? sanitize_text_field($_POST['gpltimes_name']) : '',
        'description' => isset($_POST['gpltimes_description']) ? sanitize_text_field($_POST['gpltimes_description']) : '',
        'author' => isset($_POST['gpltimes_author']) ? sanitize_text_field($_POST['gpltimes_author']) : '',
        'author_url' => isset($_POST['gpltimes_author_url']) ? esc_url_raw($_POST['gpltimes_author_url']) : '',
        'logo' => isset($_POST['gpltimes_logo']) ? sanitize_text_field($_POST['gpltimes_logo']) : '',
        'hide_settings' => isset($_POST['gpltimes_hide_settings']) ? 1 : 0,
        'disable_updates_visibility' => isset($_POST['gpltimes_disable_updates_visibility']) ? 1 : 0,
        'disable_all_admin_notices' => isset($_POST['gpltimes_disable_all_admin_notices']) ? 1 : 0,
    );

    // Update the option
    $result = update_option('gpltimes_whitelabel_settings', $whitelabel_settings);

    if ($result) {
        wp_send_json_success('Settings saved successfully');
    } else {
        wp_send_json_error('No changes were made or an error occurred');
    }
}

add_filter('plugin_row_meta', 'gpltimes_modify_plugin_row_meta', 10, 4);

function gpltimes_modify_plugin_row_meta($plugin_meta, $plugin_file, $plugin_data, $status)
{
    if ($plugin_file === 'gpltimes/gpltimes.php') {
        $whitelabel_settings = get_option('gpltimes_whitelabel_settings', array());

        // Modify Author link
        if (isset($whitelabel_settings['author']) && !empty($whitelabel_settings['author'])) {
            $author_url = isset($whitelabel_settings['author_url']) && !empty($whitelabel_settings['author_url']) ? esc_url($whitelabel_settings['author_url']) : $plugin_data['AuthorURI'];
            $plugin_meta[1] = '<a href="' . $author_url . '">' . esc_html($whitelabel_settings['author']) . '</a>';
        }

        // Modify "Visit plugin site" link
        if (isset($whitelabel_settings['author_url']) && !empty($whitelabel_settings['author_url'])) {
            $plugin_meta[2] = '<a href="' . esc_url($whitelabel_settings['author_url']) . '">' . __('Visit plugin site') . '</a>';
        }
    }
    return $plugin_meta;
}


// Filter to modify plugin details in the plugins array
add_filter('all_plugins', 'gpltimes_modify_all_plugins');

function gpltimes_modify_all_plugins($plugins)
{
    $whitelabel_settings = get_option('gpltimes_whitelabel_settings', array());
    if (isset($plugins['gpltimes/gpltimes.php'])) {
        if (isset($whitelabel_settings['name']) && !empty($whitelabel_settings['name'])) {
            $plugins['gpltimes/gpltimes.php']['Name'] = $whitelabel_settings['name'];
        }
        if (isset($whitelabel_settings['description']) && !empty($whitelabel_settings['description'])) {
            $plugins['gpltimes/gpltimes.php']['Description'] = $whitelabel_settings['description'];
        }
        if (isset($whitelabel_settings['author']) && !empty($whitelabel_settings['author'])) {
            $plugins['gpltimes/gpltimes.php']['Author'] = $whitelabel_settings['author'];
            $plugins['gpltimes/gpltimes.php']['AuthorName'] = $whitelabel_settings['author'];
        }

        if (isset($whitelabel_settings['author_url']) && !empty($whitelabel_settings['author_url'])) {
            $plugins['gpltimes/gpltimes.php']['AuthorURI'] = $whitelabel_settings['author_url'];
        }


    }
    return $plugins;
}

add_action('admin_head', 'gpltimes_custom_logo_css');

function gpltimes_custom_logo_css()
{
    $whitelabel_settings = get_option('gpltimes_whitelabel_settings', array());
    if (isset($whitelabel_settings['logo']) && !empty($whitelabel_settings['logo'])) {
        echo '<style>
            #adminmenu .toplevel_page_gpltimes_plugin div.wp-menu-image {
                background: url("' . esc_url($whitelabel_settings['logo']) . '") no-repeat center center !important;
                background-size: 16px 16px !important;
            }
            #adminmenu .toplevel_page_gpltimes_plugin div.wp-menu-image img {
                visibility: hidden;
            }
        </style>';
    }
}


function gpltimes_deactivation()
{
    // Verify nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'gpltimes_deactivation_nonce')) {
        wp_send_json_error('Security check failed');
        return;
    }

    if (!current_user_can('manage_options')) {
        wp_send_json_error('Insufficient permissions');
        return;
    }

    // Add your deactivation logic here

    $user_id = get_option('gpltokenid');
    $domain = get_site_url();
    $parsedUrl = parse_url($domain, PHP_URL_HOST);
    $normalized_domain = preg_replace('/^www\./', '', $parsedUrl);


    // Send request to your endpoint to remove the data using a GET request
    $deactivation_url = 'https://www.gpltimes.com/deactivate_gplmanager.php?user_id=' . urlencode($user_id) . '&domain=' . urlencode($normalized_domain);
    $deactivation_response = wp_remote_get($deactivation_url, array('timeout' => 20));

    // Optional: Check the response from your endpoint
    if (is_wp_error($deactivation_response)) {
        wp_send_json_error('Error communicating with the deactivation endpoint');
        return;
    }

    // Clear all scheduled hooks
    wp_clear_scheduled_hook('gpl_cron_hook');
    wp_clear_scheduled_hook('gpl_cron_hook_time');
    wp_clear_scheduled_hook('gpl_cron_hook_member');
    wp_clear_scheduled_hook('gpl_plugin_update_check');
    wp_clear_scheduled_hook('gpl_time_check');
    wp_clear_scheduled_hook('gpl_member_check');
    wp_clear_scheduled_hook('gpltimes_auth_recheck');
    wp_clear_scheduled_hook('gpltimes_delayed_refresh');

    // Remove the filters that manipulate the plugin updates
    remove_filter('site_transient_update_plugins', 'filter_plugin_updates');
    remove_filter('site_transient_update_plugins', 'filter_plugin_updates_main', 999999999);
    remove_filter('site_transient_update_plugins', 'disable_plugin_updates', 999999999);
    remove_filter('site_transient_update_themes', 'disable_theme_updates', 999999999);

    // Clear all options
    update_option('username', '');
    update_option('password', '');
    update_option('gplstatus', '');
    update_option('gplpluginactive', '0');
    update_option('gpltokenid', '');
    update_option('gpltimestatus', '');
    update_option('packagereturndata', '');
    update_option('gpltimes_last_update_check', '');
    update_option('gpldiffslug', array());
    update_option('gplpluginlistslug', array());
    update_option('gplcrondata', '');
    update_option('gplcheckedstatus', '0');
    update_option('current_time_gpl', '');
    update_option('gplcrondatamember', '');
    update_option('gpluncheckdata', '');
    update_option('gpltimes_beta_updates', '0');
    update_option('gpl_membership_details', '');

    // Clear all transients
    delete_transient('gpltimes_api_result');
    delete_transient('gpltimes_daily_check_transient');
    delete_transient('gpltimes_notice_data');
    delete_transient('gpltimes_filtered_updates');

    // Clear update transients to restore original WordPress updates
    delete_transient('update_plugins');
    delete_site_transient('update_plugins');
    delete_transient('update_themes');
    delete_site_transient('update_themes');

    // WordPress will automatically rebuild these transients when needed
    // Removed wp_update_plugins() and wp_update_themes() calls to improve performance

    // Send a success response
    wp_send_json_success('Plugin has been successfully deactivated');
}

add_action('wp_ajax_gpltimes_deactivation', 'gpltimes_deactivation');



function gpltimes_activation()
{
    // Verify nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'gpltimes_activation_nonce')) {
        wp_send_json_error('Security check failed');
        return;
    }

    // Ensure the user has the required capability to perform this action
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Insufficient permissions');
        return;
    }

    $domain = get_site_url();
    $parsedUrl = parse_url($domain, PHP_URL_HOST);
    $normalized_domain = preg_replace('/^www\./', '', $parsedUrl);

    // URL of the remote API endpoint
    $remote_check_url = 'https://www.gpltimes.com/banned_domains.php?domain=' . urlencode($normalized_domain);
    $response = wp_remote_get($remote_check_url, array('timeout' => 20));

    if (is_wp_error($response) || wp_remote_retrieve_response_code($response) != 200) {
        wp_send_json_error('Failed to check domain status');
        return;
    }

    $body = wp_remote_retrieve_body($response);
    $result = json_decode($body, true);

    if (!empty($result['banned'])) {
        wp_send_json_error('Website banned. Visit www.gpltimes.com/domains/ to manage');
        return;
    }

    // Retrieve the username and password from the AJAX request
    $username = sanitize_text_field($_POST['username']);
    $password = sanitize_text_field($_POST['password']);

    // Save the username and password in the WordPress options
    update_option('username', $username);
    update_option('password', $password);

    // Initialize variables
    $token = '';
    $user_id = '';

    // Define the URL for JWT authentication
    $url = 'https://www.gpltimes.com/wp-json/jwt-auth/v1/token';

    // Prepare the request data
    $data = array(
      'username' => $username,
      'password' => $password,
    );

    $timeout = 30; // 30 seconds


    // Send the request to the authentication endpoint
    $response = wp_safe_remote_post($url, array(
      'body' => $data,
      'headers' => array('Content-Type' => 'application/x-www-form-urlencoded'),
      'timeout' => $timeout,

    ));

    // Check if the request was successful
    if (is_wp_error($response)) {
        wp_send_json_error('Failed to connect to the authentication server');
        return;
    }

    if (wp_remote_retrieve_response_code($response) !== 200) {
        wp_send_json_error('Invalid username or password');
        return;
    }

    // Decode the JSON response
    $body = wp_remote_retrieve_body($response);
    $decoded_body = json_decode($body, true);

    // Extract the token and user ID
    if (isset($decoded_body['token']) && !empty($decoded_body['token'])) {
        $token = $decoded_body['token'];
        $user_id = $decoded_body['id']; // Use 'id' instead of 'user_id'
    } else {
        wp_send_json_error('Failed to retrieve the token');
        return;
    }

    // Save the token and user ID in the WordPress options
    update_option('gplstatus', $token);
    update_option('gpltokenid', $user_id);

    $jwt_token = get_option('gplstatus');
    // URL of the remote API endpoint for adding the domain and user ID
    $add_domain_url = 'https://www.gpltimes.com/gplmanager.php?user_id=' . urlencode($user_id) . '&domain=' . urlencode($normalized_domain) . '&token=' . urlencode($jwt_token);
    $add_domain_response = wp_remote_get($add_domain_url, array('timeout' => 20));

    // Call the new function with force refresh
    gpltimes_check_updates(true);

    // Return a success response
    wp_send_json_success('Plugin has been successfully activated');

}
add_action('wp_ajax_gpltimes_activation', 'gpltimes_activation');


function disable_thim_core_hooks()
{
    // Ensure the class has been loaded by checking for its existence.
    if (class_exists('Thim_Auto_Upgrader') && method_exists('Thim_Auto_Upgrader', 'instance')) {
        // Get the singleton instance of the Thim_Auto_Upgrader
        $updater_instance = Thim_Auto_Upgrader::instance();

        // Remove filters and actions using the instance
        remove_filter('http_request_args', [$updater_instance, 'exclude_check_update_themes_from_wp_org'], 100);
        remove_filter('http_request_args', [$updater_instance, 'exclude_check_update_plugins_from_wp_org'], 100);
        remove_filter('pre_site_transient_update_themes', [$updater_instance, 'inject_update_themes'], 100);
        remove_filter('upgrader_package_options', [$updater_instance, 'pre_update_theme'], 100);
        remove_filter('pre_site_transient_update_plugins', [$updater_instance, 'inject_update_plugins'], 100);
        remove_filter('upgrader_package_options', [$updater_instance, 'pre_update_plugin'], 100);
        remove_filter('upgrader_pre_download', [$updater_instance, 'pre_filter_download_plugin'], 100, 3);
        remove_filter('pre_set_site_transient_update_plugins', [$updater_instance, 'add_check_update_plugins']);
        remove_action('thim_core_check_update_external_plugins', [$updater_instance, 'check_update_external_plugins']);
    }
}

add_action('plugins_loaded', 'disable_thim_core_hooks', 20);

function delete_transients_on_plugin_update($upgrader_object, $options)
{
    // Check if an update action is performed for plugins
    if ($options['action'] == 'update' && $options['type'] == 'plugin') {
        // Check if the 'plugins' index exists in the $options array
        if (isset($options['plugins']) && is_array($options['plugins'])) {
            // Loop through the updated plugins
            foreach ($options['plugins'] as $plugin) {
                // Check if the updated plugin is the one we are targeting
                if ($plugin == 'gpltimes/gpltimes.php') {
                    // Delete the transients
                    delete_transient('update_plugins');
                    delete_site_transient('update_plugins');

                    delete_transient('update_themes');
                    delete_site_transient('update_themes');
                    gpltimes_check_updates(true);

                }
            }
        }
    }
}

// Hook into the upgrader_process_complete action
add_action('upgrader_process_complete', 'delete_transients_on_plugin_update', 10, 2);

// AJAX handler for disable updates
function gpltimes_save_disable_updates_ajax()
{
    // Verify nonce
    if (!isset($_POST['gpltimes_disable_updates_nonce']) || !wp_verify_nonce($_POST['gpltimes_disable_updates_nonce'], 'gpltimes_disable_updates')) {
        wp_send_json_error('Security check failed. Please try again.');
    }

    $uncheck = isset($_POST["gplcheck"]) ? $_POST["gplcheck"] : [];
    update_option('gpluncheckdata', $uncheck, true);
    update_option('gplcheckedstatus', '1', true);

    // Clear plugin and theme update transients
    delete_site_transient('update_plugins');
    delete_site_transient('update_themes');

    // Clear filtered updates transient to force regeneration with new settings
    delete_transient('gpltimes_filtered_updates');

    // Instantiate Plugbasic to process any updates
    $alll_new = new \Inc\Plugupdate\Plugbasic();

    wp_send_json_success('Changes saved successfully!');
}
add_action('wp_ajax_gpltimes_save_disable_updates', 'gpltimes_save_disable_updates_ajax');
