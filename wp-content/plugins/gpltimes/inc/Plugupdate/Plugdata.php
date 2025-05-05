<?php

namespace Inc\Plugupdate;

use Inc\Plugupdate\Plugupdate;

class Plugdata
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        /*
         * Previously, you called wp_get_themes() and processed data right away.
         * That caused translation loading too early.
         *
         * Instead, we'll hook into 'init', which fires later, and do the same
         * work from there.
         */
        add_action('init', [$this, 'init_plugdata']);
    }

    /**
     * The method called on 'init'.
     * Here we do exactly the same things that were previously in the constructor.
     */
    public function init_plugdata()
    {
        // Load necessary WP files for plugin/theme functions
        require_once(ABSPATH . 'wp-admin/includes/plugin.php');
        require_once(ABSPATH . 'wp-admin/includes/theme.php');

        // Get all plugins and themes
        $all_plugins = get_plugins();
        $all_themes = wp_get_themes();

        // Retrieve your filtered data
        $returndata = $this->getFilteredData();

        // Process each item
        foreach ($returndata as $data) {
            $this->processItemData($data, $all_plugins, $all_themes);
        }
    }

    /**
     * Retrieves and filters the data from options.
     * Uses a transient for caching to improve performance.
     */
    private function getFilteredData()
    {
        // Try to get filtered data from transient first
        $filtered_data = get_transient('gpltimes_filtered_updates');

        // If transient exists and is not empty, return it
        if (false !== $filtered_data && !empty($filtered_data)) {
            return $filtered_data;
        }

        // If no transient, generate filtered data
        $returndata = get_option('gplcrondata', []);

        if (!is_array($returndata)) {
            $returndata = [];
        }

        $gpluncheckdata = get_option('gpluncheckdata', []);

        if (!is_array($gpluncheckdata)) {
            $gpluncheckdata = [];
        }

        // Filter the data
        $filtered_data = array_filter($returndata, function ($data) use ($gpluncheckdata) {
            return isset($data->slug) && !in_array($data->slug, $gpluncheckdata);
        });

        // Store filtered data in transient for 30 minutes
        set_transient('gpltimes_filtered_updates', $filtered_data, 30 * MINUTE_IN_SECONDS);

        return $filtered_data;
    }

    /**
     * Decides whether the item is a plugin or a theme, then updates its data.
     */
    private function processItemData($data, $all_plugins, $all_themes)
    {
        if (isset($data->version, $data->slug)) {
            // Check if this slug is a plugin
            if (array_key_exists($data->slug, $all_plugins)) {
                $currentItemData = $all_plugins[$data->slug];
                $this->updateItemData($data, $currentItemData);
            }
            // Otherwise, check if it is a theme
            elseif (isset($all_themes[$data->slug])) {
                $currentItemData = $all_themes[$data->slug];
                $this->updateItemData($data, $currentItemData);
            }
        }
    }

    /**
     * Gathers and prepares the data, then creates a Plugupdate instance.
     */
    private function updateItemData($data, $currentItemData)
    {
        $dataclass = new \stdClass();
        $dataclass->slug       = $data->slug;
        $dataclass->version    = $data->version;
        // If it's an array (plugins typically), use array key. If theme object, use ->get()
        $dataclass->name       = isset($currentItemData['Name'])
                                    ? $currentItemData['Name']
                                    : $currentItemData->get('Name');
        $dataclass->author     = isset($currentItemData['Author'])
                                    ? $currentItemData['Author']
                                    : $currentItemData->get('Author');
        $dataclass->uri        = 'https://www.gpltimes.com/';
        $dataclass->package    = $data->download_link;
        $dataclass->lastupdate = $data->last_update;

        // Retrieve "beta updates" user preference
        $beta_updates = (get_option('gpltimes_beta_updates', false) === '1');

        // Instantiate your Plugupdate class
        $draft = new Plugupdate($dataclass, $beta_updates);
    }
}
