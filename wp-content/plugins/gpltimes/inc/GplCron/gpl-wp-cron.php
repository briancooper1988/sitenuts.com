<?php

/**
 * @package Gpltimes
 */

// Set custom hook for wp cron
add_action('gpl_plugin_update_check', 'gpltimes_cron_handler');

// Schedule the event if not already scheduled
if (!wp_next_scheduled('gpl_plugin_update_check')) {
    wp_schedule_event(time(), 'hourly', 'gpl_plugin_update_check');
}

/**
 * Check for plugin and theme updates from GPL Times
 *
 * @param bool $force_refresh Whether to force a fresh API call regardless of cache
 * @return object|false The update data or false on failure
 */
function gpltimes_check_updates($force_refresh = false)
{
    $transient_key = 'gpltimes_api_result';
    $cache_duration = 30 * MINUTE_IN_SECONDS; // 30 minutes cache

    // Check transient cache unless forced refresh
    if (!$force_refresh) {
        $cached_data = get_transient($transient_key);
        if (false !== $cached_data) {
            return $cached_data;
        }
    }

    // Collect plugin and theme data
    $slugarray = array();
    $all_plugins = get_plugins();
    $all_themes = wp_get_themes();
    $gpluncheckdata = get_option('gpluncheckdata', array());

    // Ensure $gpluncheckdata is an array
    if (!is_array($gpluncheckdata)) {
        $gpluncheckdata = array();
    }

    // Process plugins
    foreach ($all_plugins as $key => $value) {
        if (!in_array($key, $gpluncheckdata)) {
            $slugarray[] = $key . '|' . $value['Version'];
        }
    }

    // Process themes
    foreach ($all_themes as $key => $value) {
        if (!in_array($key, $gpluncheckdata)) {
            $slugarray[] = $key . '|' . $value->get('Version');
        }
    }

    // Check authentication data
    $token = esc_attr(get_option('gplstatus'));
    $gpltokenidmain = esc_attr(get_option('gpltokenid'));
    $domain = get_site_url();
    $getinfo = get_option('admin_email');

    // Verify required data
    if (empty($token) || empty($gpltokenidmain) || empty($domain) || empty($getinfo)) {
        error_log('GPL Times: Missing required metadata for update check.');
        return false;
    }

    // Prepare API request data
    $out = implode(",", $slugarray);
    $out_final = $out . '@__@' . $token . '@__@' . '1' . '@__@' . $domain . '@__@' . $getinfo . '@__@' . $gpltokenidmain;
    $out_encode = base64_encode($out_final);

    // Make API request
    $url = 'https://www.gpltimes.com/version_check_post.php';
    $option = array(
        'timeout' => 30,
        'body' => array('data' => $out_encode)
    );

    $response = wp_safe_remote_post($url, $option);

    // Handle API errors
    if (is_wp_error($response)) {
        error_log('GPL Times: Failed to fetch update data: ' . $response->get_error_message());
        return false;
    }

    // Process response
    $dataAPIResult = wp_remote_retrieve_body($response);
    $returndataendpoint = json_decode($dataAPIResult);

    if (!$returndataendpoint) {
        error_log('GPL Times: Invalid API response or decode error');
        return false;
    }

    // Store data in both transient and option
    set_transient($transient_key, $returndataendpoint, $cache_duration);
    update_option('gplcrondata', $returndataendpoint, true);

    // Clear filtered updates transient to force regeneration with new data
    delete_transient('gpltimes_filtered_updates');

    // Update last check timestamp
    update_option('gpltimes_last_update_check', time());

    return $returndataendpoint;
}

/**
 * Cron handler for scheduled update checks
 */
function gpltimes_cron_handler()
{
    // Always check for updates when cron runs (every hour)
    gpltimes_check_updates(true); // Force refresh on cron to ensure latest updates
}

/**
 * Legacy function for backward compatibility
 * This ensures existing code that calls gpl_cron_main() still works
 */
function gpl_cron_main()
{
    return gpltimes_check_updates(false);
}

/**
 * Legacy function for backward compatibility
 * This ensures existing code that calls gpl_cron_main_no_transient() still works
 */
function gpl_cron_main_no_transient()
{
    return gpltimes_check_updates(true);
}

/**
 * Ensure transient exists and recreate it if missing
 */
function gpltimes_ensure_transient_exists()
{
    // Only check on admin pages
    if (!is_admin()) {
        return;
    }

    // Check if transient exists
    $cached_data = get_transient('gpltimes_api_result');

    // If transient doesn't exist, recreate it
    if (false === $cached_data) {
        // Use the option data if available while we refresh in the background
        $option_data = get_option('gplcrondata');

        if ($option_data) {
            // Temporarily restore the transient with the option data
            set_transient('gpltimes_api_result', $option_data, 30 * MINUTE_IN_SECONDS);
        }

        // Schedule an immediate background refresh
        if (!wp_next_scheduled('gpltimes_delayed_refresh')) {
            wp_schedule_single_event(time() + 5, 'gpltimes_delayed_refresh');
        }
    }
}
add_action('admin_init', 'gpltimes_ensure_transient_exists');

/**
 * Background refresh handler
 */
function gpltimes_delayed_refresh_handler()
{
    // Force refresh in the background
    gpltimes_check_updates(true);
}
add_action('gpltimes_delayed_refresh', 'gpltimes_delayed_refresh_handler');
