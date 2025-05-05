<?php

namespace Inc\Plugupdate;

class Pluginfocheck
{
    public function __construct()
    {
        require_once(ABSPATH . 'wp-admin/includes/plugin.php');
        require_once(ABSPATH . 'wp-admin/includes/theme.php');

        $all_plugins = get_plugins();
        $all_themes = wp_get_themes();
        $returndata = get_option('packagereturndata', []);
        $gpluncheckdata = get_option('gpluncheckdata', []);

        list($not_check_slug, $check_slug) = $this->processItemData($returndata, $gpluncheckdata, $all_plugins, $all_themes);

        update_option('gpldiffslug', $check_slug);
    }

    private function processItemData($returndata, $gpluncheckdata, $all_plugins, $all_themes)
    {
        $not_check_slug = [];
        $check_slug = [];

        // Ensure $returndata is an array or object before iterating
        if (!is_array($returndata) && !is_object($returndata)) {
            return [$not_check_slug, $check_slug];
        }

        foreach ($returndata as $data) {
            if (empty($data->slug) || empty($data->version)) {
                continue;
            }

            if (isset($all_plugins[$data->slug])) {
                $currentItemData = $all_plugins[$data->slug];
            } elseif (isset($all_themes[$data->slug])) {
                $currentItemData = $all_themes[$data->slug];
                $currentItemData['Version'] = $currentItemData->get('Version');
            } else {
                continue;
            }

            if (version_compare($data->version, $currentItemData['Version'], '>=')) {
                if (!in_array($data->slug, $gpluncheckdata)) {
                    $not_check_slug[] = $data->slug;
                } else {
                    $check_slug[] = $data->slug;
                }
            }
        }

        return [$not_check_slug, $check_slug];
    }
}
