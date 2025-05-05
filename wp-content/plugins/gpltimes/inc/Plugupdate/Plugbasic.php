<?php

namespace Inc\Plugupdate;

use Inc\Plugupdate\Plugupdate;

class Plugbasic
{
    private $all_plugins;
    private $all_themes;
    private $gpluncheckdata;

    public function __construct()
    {
        $this->all_plugins   = get_plugins();
        $this->all_themes    = wp_get_themes();
        $this->gpluncheckdata = get_option('gpluncheckdata', []);

        // Ensure gpluncheckdata is always an array
        if (!is_array($this->gpluncheckdata)) {
            $this->gpluncheckdata = [];
        }

        $slugarray = $this->processItems();
        $this->fetchUpdateData($slugarray);
    }

    private function processItems()
    {
        $slugarray = [];
        foreach ($this->all_plugins as $key => $value) {
            if (!in_array($key, $this->gpluncheckdata)) {
                $slugarray[] = $key . '|' . $value['Version'];
            }
        }

        foreach ($this->all_themes as $key => $value) {
            if (!in_array($key, $this->gpluncheckdata)) {
                $theme_version = $value->get('Version');
                $slugarray[] = $key . '|' . $theme_version;
            }
        }

        return $slugarray;
    }

    private function fetchUpdateData($slugarray)
    {
        $token          = esc_attr(get_option('gplstatus'));
        $gpltokenidmain = esc_attr(get_option('gpltokenid'));
        $domain         = get_site_url();
        $getinfo        = get_option('admin_email');

        // Check if all necessary information is available
        if (!$token || !$gpltokenidmain || !$domain || !$getinfo) {
            error_log('Missing required metadata for update check.');
            return; // Exit the function if any data is missing
        }

        $out       = implode(",", $slugarray);
        $out_final = $out . '@__@' . $token . '@__@1@__@' . $domain . '@__@' . $getinfo . '@__@' . $gpltokenidmain;
        $out_encode = base64_encode($out_final);

        // Use the new POST endpoint
        $url = 'https://www.gpltimes.com/version_check_post.php';
        $option = array(
            'timeout' => 30,
            'body'    => array( 'data' => $out_encode )
        );

        $response = wp_safe_remote_post($url, $option);
        if (is_wp_error($response)) {
            error_log('Failed to fetch update data: ' . $response->get_error_message());
            return;
        }

        $dataAPIResult     = wp_remote_retrieve_body($response);
        $returndataendpoint = json_decode($dataAPIResult);

        if (!$returndataendpoint) {
            error_log('Invalid API response or decode error');
            return;
        }

        update_option('gplcrondata', $returndataendpoint, true);
        update_option('packagereturndata', $returndataendpoint);

        // Clear filtered updates transient to force regeneration with new data
        delete_transient('gpltimes_filtered_updates');

        $this->compareVersions($returndataendpoint);
    }

    private function compareVersions($returndata)
    {
        if (empty($returndata)) {
            return;
        }

        // Check plugin updates
        foreach ($returndata as $data) {
            if (!isset($this->all_plugins[$data->slug])) {
                continue;
            }

            $currentplugindata = $this->all_plugins[$data->slug];
            if (version_compare($data->version, $currentplugindata['Version'], '>')) {
                $this->updateItem($data, $currentplugindata);
            }
        }

        // Check theme updates
        foreach ($returndata as $data) {
            if (!isset($this->all_themes[$data->slug])) {
                continue;
            }

            $currentthemedata = $this->all_themes[$data->slug];
            $theme_version = $currentthemedata->get('Version');
            if (version_compare($data->version, $theme_version, '>')) {
                $this->updateItem($data, $currentthemedata);
            }
        }
    }

    private function updateItem($data, $currentItemData)
    {
        $dataclass = new \stdClass();
        $dataclass->slug     = $data->slug;
        $dataclass->version  = $data->version;
        $dataclass->name     = $currentItemData['Name'] ?? $currentItemData->get('Name');
        $dataclass->author   = $currentItemData['Author'] ?? $currentItemData->get('Author');
        $dataclass->uri      = 'https://www.gpltimes.com/';
        $dataclass->package  = $data->download_link;
        $dataclass->lastupdate = $data->last_update;

        // Retrieve the user's beta updates preference from the database
        $beta_updates = (get_option('gpltimes_beta_updates', false) === '1');

        // Create an instance of the Plugupdate class and pass the $beta_updates parameter
        $draft = new Plugupdate($dataclass, $beta_updates);
    }
}
