<?php

/**
 * @package  Gpltimes
 */

// Function to recheck authentication
function gpltimes_recheck_authentication()
{


    $domain = get_site_url();
    $parsedUrl = parse_url($domain, PHP_URL_HOST);
    $normalized_domain = preg_replace('/^www\./', '', $parsedUrl);


    // URL of the remote API endpoint
    $remote_check_url = 'https://www.gpltimes.com/banned_domains.php?domain=' . urlencode($normalized_domain);
    $response = wp_remote_get($remote_check_url, array('timeout' => 20));

    if (is_wp_error($response) || wp_remote_retrieve_response_code($response) != 200) {
        // Error communicating with the remote server
        update_option('gplstatus', '');
        update_option('gpltokenid', '');
        return;
    }

    $body = wp_remote_retrieve_body($response);
    $result = json_decode($body, true);

    if (!empty($result['banned'])) {
        // Domain is banned, reset the options
        wp_clear_scheduled_hook('gpl_cron_hook');
        wp_clear_scheduled_hook('gpl_cron_hook_time');
        wp_clear_scheduled_hook('gpl_cron_hook_member');
        wp_clear_scheduled_hook('gpl_plugin_update_check');
        wp_clear_scheduled_hook('gpl_time_check');
        wp_clear_scheduled_hook('gpl_member_check');
        wp_clear_scheduled_hook('gpltimes_auth_recheck');
        delete_option('gplstatus');
        delete_option('gpltokenid');
        delete_option('username');
        delete_option('password');
        delete_option('gpltimestatus');

        delete_option('gpldiffslug');
        delete_option('gplpluginlistslug');
        delete_option('gplcrondata');
        delete_option('gplcheckedstatus');
        delete_option('current_time_gpl');
        delete_option('gplpluginactive');
        delete_option('gpltimes_whitelabel_settings');
        delete_option('gplcrondatamember');
        delete_option('gpluncheckdata');
        delete_transient('gpltimes_api_result');
        delete_transient('gpltimes_daily_check_transient');
        delete_transient('gpltimes_filtered_updates');

        delete_transient('update_plugins');
        set_site_transient('update_plugins', null);
        return;
    }

    $username = get_option('username');
    $password = get_option('password');

    if (!empty($username) && !empty($password)) {
        $main_url = 'https://www.gpltimes.com/wp-json/jwt-auth/v1/token';
        $received_values = array();
        $received_values['username'] = $username;
        $received_values['password'] = $password;
        $options = array('timeout' => 20, 'body' => $received_values);
        $return_request = wp_safe_remote_post($main_url, $options);

        if (!empty($return_request)) {
            $retuen_response_code = $return_request['response']['code'];
            if ($retuen_response_code == 200) {
                $response = wp_remote_retrieve_body($return_request);
                $response_decode = json_decode($response);
                $tokengpltime = $response_decode->token;
                $gpltokenid = $response_decode->id;

                // Update the options with new token values
                update_option('gplstatus', $tokengpltime);
                update_option('gpltokenid', $gpltokenid);

                $jwt_token = get_option('gplstatus');
                $user_id = get_option('gpltokenid');
                // URL of the remote API endpoint for adding the domain and user ID
                $add_domain_url = 'https://www.gpltimes.com/gplmanager.php?user_id=' . urlencode($user_id) . '&domain=' . urlencode($normalized_domain) . '&token=' . urlencode($jwt_token);
                $add_domain_response = wp_remote_get($add_domain_url, array('timeout' => 20));

                // Clear filtered updates transient to force regeneration with new authentication
                delete_transient('gpltimes_filtered_updates');

            } else {
                // Invalid authentication, so reset the options
                update_option('gplstatus', '');
                update_option('gpltokenid', '');
            }
        }
    } else {
        // No credentials set, so reset the options
        update_option('gplstatus', '');
        update_option('gpltokenid', '');
    }
}

add_action('gpltimes_auth_recheck', 'gpltimes_recheck_authentication');
