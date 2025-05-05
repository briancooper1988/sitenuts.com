<?php

/**
 * @package  Gpltimes
 */

namespace Inc\Base;

class Deactivate
{
    public static function deactivate()
    {
        // Clear all options related to updates
        $returndataendpoint = '';
        update_option('gplcrondata', '');
        update_option('gpltokenid', '');
        update_option('gplstatus', '');
        update_option('username', '');
        update_option('password', '');
        update_option('gpltimestatus', '');
        update_option('packagereturndata', '');
        update_option('gpltimes_last_update_check', '');
        update_option('gpldiffslug', array());
        update_option('gplpluginlistslug', array());
        update_option('gplcheckedstatus', '0');
        update_option('current_time_gpl', '');
        update_option('gplpluginactive', '0');
        update_option('gplcrondatamember', '');
        update_option('gpluncheckdata', '');
        update_option('gpltimes_beta_updates', '0');
        update_option('gpl_membership_details', '');
        update_option('gpltimes_version', '');
        update_option('gpltimes_whitelabel_settings', array());

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

        flush_rewrite_rules();
    }
}
