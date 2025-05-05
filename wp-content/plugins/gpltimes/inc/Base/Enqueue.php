<?php

/**
 * @package Gpltimes
 */

namespace Inc\Base;

use Inc\Base\BaseController;

/**
 *
 */
class Enqueue extends BaseController
{
    public function register()
    {
        add_action('admin_enqueue_scripts', array($this, 'enqueue'));
    }

    public function enqueue()
    {
        // Check if 'page' is set in the GET parameters and if it matches one of your plugin's pages
        if (isset($_GET['page']) && in_array($_GET['page'], array(
            'gpltimes_plugin',
            'disable_update_check',
            'gpltimes-whitelabel',
            'gpltimes_export_import'
        ))) {
            // Enqueue all our scripts with the plugin version for cache busting
            wp_enqueue_script('mypluginscript', $this->plugin_url . 'assets/myscript.js', array('jquery'), defined('gpltimes_version') ? gpltimes_version : '4.0.14', true);

            // Only enqueue beta-updates.js on the main plugin page where it's needed
            if (isset($_GET['page']) && $_GET['page'] === 'gpltimes_plugin') {
                wp_enqueue_script('beta-updates', $this->plugin_url . 'assets/beta-updates.js', array('jquery'), defined('gpltimes_version') ? gpltimes_version : '4.0.14', true);
                wp_localize_script('beta-updates', 'betaUpdatesData', array(
                    'ajaxurl' => admin_url('admin-ajax.php'),
                    'nonce' => wp_create_nonce('gpltimes_beta_updates_nonce')
                ));
            }

            // Add inline admin styles
            wp_add_inline_style('admin-bar', $this->get_admin_styles());
        }
    }

    /**
     * Get admin styles for the plugin
     */
    private function get_admin_styles()
    {
        return "
            /* Basic form styles */
            .gplt-btn {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 10px 16px;
                border-radius: 6px;
                font-weight: 500;
                cursor: pointer;
                transition: all 0.2s ease;
                border: none;
            }

            .gplt-btn-primary {
                background-color: #1a8fc4;
                color: white;
            }

            .gplt-btn-primary:hover {
                background-color: #167bb4;
            }

            .gplt-btn-secondary {
                background-color: #f3f4f6;
                color: #4b5563;
                border: 1px solid #e5e7eb;
            }

            .gplt-btn-secondary:hover {
                background-color: #e5e7eb;
            }
        ";
    }
}
