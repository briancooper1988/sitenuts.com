<?php

namespace Inc\Plugupdate;

class Plugupdate
{
    private $beta_updates;
    private $returnplugdata;
    private $type; // 'plugin' or 'theme'

    public function __construct($returnplugdata, $beta_updates = false)
    {
        $this->returnplugdata = $returnplugdata;
        $this->beta_updates = $beta_updates;
        $this->type = $this->determineItemType($returnplugdata->slug);

        if ($this->type === 'plugin') {
            add_filter("site_transient_update_plugins", [$this, "setTransientGpltimes"], 99999999);
            add_filter('pre_set_site_transient_update_plugins', [$this, "setTransientGpltimes"], 99999999);
            add_filter("plugins_api", [$this, "setItemInfo"], 99999999, 3);
        } elseif ($this->type === 'theme') {
            add_filter("site_transient_update_themes", [$this, "setTransientGpltimes"], 99999999);
            add_filter('pre_set_site_transient_update_themes', [$this, "setTransientGpltimes"], 99999999);
            add_filter("themes_api", [$this, "setItemInfo"], 99999999, 3);
        }

        add_filter('upgrader_pre_download', '__return_false', 99999999);
    }

    private function isBetaVersion($version)
    {
        return strpos($version, 'beta') !== false;
    }

    private function determineItemType($slug)
    {
        if (strpos($slug, '/') !== false && substr($slug, -4) === '.php') {
            return 'plugin';
        } else {
            return 'theme';
        }
    }

    public function setTransientGpltimes($transient)
    {
        // Ensure $transient is always an object.
        if (!is_object($transient)) {
            $transient = new \stdClass();
        }

        if ($this->type === 'plugin') {
            require_once(ABSPATH . 'wp-admin/includes/plugin.php');
            $all_items = get_plugins();
        } elseif ($this->type === 'theme') {
            require_once(ABSPATH . 'wp-admin/includes/theme.php');
            $all_items = wp_get_themes();
        } else {
            return $transient;
        }

        if (isset($all_items[$this->returnplugdata->slug])) {
            $currentVersion = $this->type === 'plugin' ? $all_items[$this->returnplugdata->slug]['Version'] : $all_items[$this->returnplugdata->slug]->get('Version');

            // Check if the update is a beta version
            $isBetaUpdate = $this->isBetaVersion($this->returnplugdata->version);

            // Show the update only if it's not a beta version or the user has opted for beta updates
            if ((!$isBetaUpdate || $this->beta_updates) && version_compare($currentVersion, $this->returnplugdata->version, '<')) {
                $transient = $this->addUpdateTransient($transient);
            } else {
                $transient = $this->removeUpdateTransient($transient);
            }
        }
        return $transient;
    }

    private function addUpdateTransient($transient)
    {
        if (!isset($transient->response) || !is_array($transient->response)) {
            $transient->response = [];
        }

        if ($this->type === 'plugin') {
            $plugin_data = (object) array(
                'id' => $this->returnplugdata->slug,
                'slug' => basename(dirname($this->returnplugdata->slug)),
                'plugin' => $this->returnplugdata->slug,
                'new_version' => $this->returnplugdata->version,
                'url' => 'https://www.gpltimes.com', // Plugin homepage
                'package' => $this->returnplugdata->package,
                'icons' => array(
                    '1x' => plugin_dir_url(dirname(__DIR__)) . 'assets/icon-128x128.png',
                    '2x' => plugin_dir_url(dirname(__DIR__)) . 'assets/icon-256x256.png'
                ),
                'banners' => array(
                    'low' => plugin_dir_url(dirname(__DIR__)) . 'assets/banner-772x250.png',
                    'high' => plugin_dir_url(dirname(__DIR__)) . 'assets/banner-1544x500.png'
                ),
                'requires_php' => '7.4',
            );

            $transient->response[$this->returnplugdata->slug] = $plugin_data;

        } elseif ($this->type === 'theme') {
            $theme_slug = $this->returnplugdata->slug;

            $theme_data = array(
                'theme' => $theme_slug,
                'new_version' => $this->returnplugdata->version,
                'url' => plugin_dir_url(dirname(__DIR__)) . 'inc/nochangelogs.html', // Theme preview URL
                'package' => $this->returnplugdata->package,
            );

            if (isset($transient->response[$theme_slug])) {
                $transient->response[$theme_slug] = array_merge($transient->response[$theme_slug], $theme_data);
            } else {
                $transient->response[$theme_slug] = $theme_data;
            }

            if (isset($transient->no_update[$theme_slug]) && is_array($transient->no_update[$theme_slug])) {
                $transient->response[$theme_slug] = array_merge($transient->no_update[$theme_slug], $transient->response[$theme_slug]);
                unset($transient->no_update[$theme_slug]);
            }
        }

        return $transient;
    }

    private function removeUpdateTransient($transient)
    {
        if (isset($transient->response[$this->returnplugdata->slug])) {
            unset($transient->response[$this->returnplugdata->slug]);
        }
        return $transient;
    }

    public function setItemInfo($result, $action, $args)
    {
        if ($action !== 'plugin_information' && $action !== 'theme_information') {
            return $result;
        }

        $args_slug = isset($args->slug) ? $args->slug : '';

        if ($args_slug !== basename(dirname($this->returnplugdata->slug)) && $args_slug !== $this->returnplugdata->slug) {
            return $result;
        }

        $response = new \stdClass();
        $response->name = $this->returnplugdata->name;
        $response->slug = basename(dirname($this->returnplugdata->slug));
        $response->version = $this->returnplugdata->version;
        $response->author = $this->returnplugdata->author;
        $response->requires_php = '7.4';
        $response->last_updated = $this->returnplugdata->lastupdate;
        $response->banners = array(
            'low' => plugin_dir_url(dirname(__DIR__)) . 'assets/banner-772x250.png',
            'high' => plugin_dir_url(dirname(__DIR__)) . 'assets/banner-1544x500.png',
        );

        if ($this->type === 'plugin') {
            $response->homepage = 'https://www.gpltimes.com'; // Plugin homepage
            $response->download_link = $this->returnplugdata->package;
            $response->plugin = $this->returnplugdata->slug; // Full path for the main plugin file
        } else {
            $response->homepage = plugin_dir_url(dirname(__DIR__)) . 'inc/nochangelogs.html'; // Theme preview URL
            $response->theme = $this->returnplugdata->slug;
        }

        // Default changelog content
        $changelog_content = 'No changelogs found.';
        
        // For GPLTimes plugin specifically, use the CHANGELOGS.md file
        if ($this->type === 'plugin' && $this->returnplugdata->slug === 'gpltimes/gpltimes.php') {
            $changelog_content = $this->getGplTimesChangelog();
        }

        $response->sections = array(
            'description' => sprintf('%s is a premium WordPress product provided by GPL Times. Visit <a href="https://www.gpltimes.com">GPL Times</a> to access more premium WP plugins and themes with auto-updates.', esc_html($this->returnplugdata->name)),
            'installation' => '<ol>
                               <li>Download the plugin/theme zip file from your GPL Times account.</li>
                               <li>Log in to your WordPress admin area.</li>
                               <li>Go to Plugins > Add New and click on the Upload Plugin button or Go to Appearance > Themes > Add New and click on the Upload Theme button</li>
                               <li>Choose the downloaded zip file and click Install Now.</li>
                               <li>After installation, click Activate Plugin/Theme to start using the product.</li>
                               </ol>',
            'changelog' => $changelog_content,
        );

        return $response;
    }

    /**
     * Parse the CHANGELOGS.md file and return formatted HTML
     * 
     * @return string Formatted HTML of the changelog
     */
    private function getGplTimesChangelog() {
        $changelog_path = plugin_dir_path(dirname(__DIR__)) . 'CHANGELOGS.md';
        
        if (!file_exists($changelog_path)) {
            return 'No changelog file found.';
        }
        
        $changelog_content = file_get_contents($changelog_path);
        if (empty($changelog_content)) {
            return 'No changelog content found.';
        }
        
        // Convert markdown to HTML
        $changelog_html = '<div class="gpltimes-changelog">';
        
        // Process the markdown content
        $lines = explode("\n", $changelog_content);
        $in_list = false;
        
        foreach ($lines as $line) {
            // Skip the first line which is just "# Changelog"
            if (trim($line) === '# Changelog') {
                continue;
            }
            
            // Process version headers (## v4.0.13)
            if (strpos($line, '## v') === 0) {
                if ($in_list) {
                    $changelog_html .= '</ul>';
                    $in_list = false;
                }
                $version = str_replace('## ', '', $line);
                $changelog_html .= '<h3>' . esc_html($version) . '</h3>';
            } 
            // Process section headers (### Enhancements)
            elseif (strpos($line, '### ') === 0) {
                if ($in_list) {
                    $changelog_html .= '</ul>';
                    $in_list = false;
                }
                $section = str_replace('### ', '', $line);
                $changelog_html .= '<h4>' . esc_html($section) . '</h4>';
            }
            // Process list items
            elseif (strpos(trim($line), '* ') === 0) {
                if (!$in_list) {
                    $changelog_html .= '<ul>';
                    $in_list = true;
                }
                $item = str_replace('* ', '', trim($line));
                $changelog_html .= '<li>' . esc_html($item) . '</li>';
            }
            // Close list if we encounter an empty line
            elseif (trim($line) === '' && $in_list) {
                $changelog_html .= '</ul>';
                $in_list = false;
            }
        }
        
        // Close any open list
        if ($in_list) {
            $changelog_html .= '</ul>';
        }
        
        $changelog_html .= '</div>';
        
        return $changelog_html;
    }
}
