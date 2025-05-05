<?php
/**
 * @package Gpltimes
 */

namespace Inc\templates;

namespace Inc\Base;

use Inc\Base\BaseController;
use Inc\Plugupdate\Plugbasic;

// Fetch the updated state of gpluncheckdata
$gpluncheckdata = get_option('gpluncheckdata', []);
if (!is_array($gpluncheckdata)) {
    $gpluncheckdata = [];
}

$pluginpageurl = get_option('siteurl') . '/wp-admin/plugins.php';
$all_plugins = get_plugins();
$all_themes = wp_get_themes();
$pluginjson = [];
$themejson = [];
$url = 'https://www.gpltimes.com/gplcheck.json';
$option = array('timeout' => 30);
$dataAPIResult = wp_remote_retrieve_body(wp_safe_remote_get($url, $option));
$returndataendpoint = json_decode($dataAPIResult, true);

$whitelabel_settings = get_option('gpltimes_whitelabel_settings', array());
$plugin_name = isset($whitelabel_settings['name']) && !empty($whitelabel_settings['name']) ? $whitelabel_settings['name'] : 'GPL Times';

// Process plugins
foreach ($returndataendpoint as $key => $value) {
    if ($key == 'gpltimes/gpltimes.php') {
        continue; // Skip the GPL Times plugin
    }
    if (array_key_exists($key, $all_plugins)) {
        // Replace the name with the one from the installed plugins
        $value['name'] = $all_plugins[$key]['Name'];
        array_push($pluginjson, $value);
    }
}

// Process themes
foreach ($all_themes as $theme) {
    $theme_slug = $theme->get_stylesheet();
    if (isset($returndataendpoint[$theme_slug])) {
        $theme_data = array(
            'name' => $theme->get('Name'),
            'version' => isset($returndataendpoint[$theme_slug]['version']) ? $returndataendpoint[$theme_slug]['version'] : $theme->get('Version'),
            'slug' => $theme_slug
        );
        array_push($themejson, $theme_data);
    }
}

// Sort the plugins and themes alphabetically by name
usort($pluginjson, function ($a, $b) {
    return strcmp($a['name'], $b['name']);
});
usort($themejson, function ($a, $b) {
    return strcmp($a['name'], $b['name']);
});

$indexcount = 1;
?>

<div class="gplt-main-container">
    <!-- Toast notification container -->
    <div id="gpltimes-toast" class="gpltimes-toast">
        <div class="gpltimes-toast-content">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="gpltimes-toast-icon">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                <polyline points="22 4 12 14.01 9 11.01"></polyline>
            </svg>
            <span id="gpltimes-toast-message"></span>
        </div>
    </div>

    <div class="gplt-wrap">
        <div class="gplt-content">
            <div class="gplt-header">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="#1a8fc4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                    <polyline points="17 8 12 3 7 8"/>
                    <line x1="12" y1="3" x2="12" y2="15"/>
                </svg>
                <div>
                    <h2>Disable Updates</h2>
                    <p class="gplt-subtitle">Manage automatic updates for your plugins and themes</p>
                </div>
            </div>

            <div class="gplt-info-box">
                <div class="gplt-info-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="16" x2="12" y2="12"></line>
                        <line x1="12" y1="8" x2="12.01" y2="8"></line>
                    </svg>
                </div>
                <div class="gplt-info-content">
                    <p>Select which plugins and themes you want to prevent from receiving automatic updates from <?php echo esc_html($plugin_name); ?>. This feature is useful when you have purchased a license directly from the developer or want to manage updates manually.</p>
                </div>
            </div>

            <form id="disable-updates-form" method="post">
                <input type="hidden" name="action" value="gpltimes_save_disable_updates">
                <input type="hidden" name="page" value="disable_update_check">
                <?php wp_nonce_field('gpltimes_disable_updates', 'gpltimes_disable_updates_nonce'); ?>

                <!-- Plugins Section -->
                <div class="gplt-section-header">
                    <h3>Plugins</h3>
                    <?php if (!empty($pluginjson)) : ?>
                        <div class="gplt-actions">
                            <button type="button" class="gplt-btn gplt-btn-secondary" onclick="selectAllPlugins()">Select All</button>
                            <button type="button" class="gplt-btn gplt-btn-secondary" onclick="deselectAllPlugins()">Deselect All</button>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if (!empty($pluginjson)) : ?>
                    <div class="gplt-table-container">
                        <table class="gplt-table">
                            <thead>
                                <tr>
                                    <th style="width: 10%">S/N</th>
                                    <th style="width: 50%">Name</th>
                                    <th style="width: 20%">Version</th>
                                    <th style="width: 20%">Choose</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pluginjson as $plugin) : ?>
                                    <tr>
                                        <td><?php echo esc_html($indexcount++); ?></td>
                                        <td><?php echo esc_html($plugin['name']); ?></td>
                                        <td>v<?php echo esc_html($plugin['version']); ?></td>
                                        <td>
                                            <input type="checkbox" name="gplcheck[]" value="<?php echo esc_attr($plugin['slug']); ?>"
                                                <?php echo in_array($plugin['slug'], $gpluncheckdata) ? 'checked' : ''; ?>
                                                class="plugin-checkbox">
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else : ?>
                    <div class="gplt-empty-state">
                        <p>No plugins found</p>
                    </div>
                <?php endif; ?>

                <!-- Themes Section -->
                <div class="gplt-section-header">
                    <h3>Themes</h3>
                    <?php if (!empty($themejson)) : ?>
                        <div class="gplt-actions">
                            <button type="button" class="gplt-btn gplt-btn-secondary" onclick="selectAllThemes()">Select All</button>
                            <button type="button" class="gplt-btn gplt-btn-secondary" onclick="deselectAllThemes()">Deselect All</button>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if (!empty($themejson)) : ?>
                    <div class="gplt-table-container">
                        <table class="gplt-table">
                            <thead>
                                <tr>
                                    <th style="width: 10%">S/N</th>
                                    <th style="width: 50%">Name</th>
                                    <th style="width: 20%">Version</th>
                                    <th style="width: 20%">Choose</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($themejson as $theme) : ?>
                                    <tr>
                                        <td><?php echo esc_html($indexcount++); ?></td>
                                        <td><?php echo esc_html($theme['name']); ?></td>
                                        <td>v<?php echo esc_html($theme['version']); ?></td>
                                        <td>
                                            <input type="checkbox" name="gplcheck[]" value="<?php echo esc_attr($theme['slug']); ?>"
                                                <?php echo in_array($theme['slug'], $gpluncheckdata) ? 'checked' : ''; ?>
                                                class="theme-checkbox">
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else : ?>
                    <div class="gplt-empty-state">
                        <p>No themes found</p>
                    </div>
                <?php endif; ?>

                <div class="gplt-form-actions">
                    <button type="submit" name="uncheck" class="gplt-btn gplt-btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                            <polyline points="9 11 12 14 22 4"/>
                        </svg>
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.gplt-main-container {
    margin: 20px 20px 20px 0;
    max-width: 1500px;
}

.gplt-wrap {
    width: 100%;
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05), 0 10px 15px -3px rgba(0, 0, 0, 0.1);
}

.gplt-content {
    padding: 30px;
}

.gplt-header {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-bottom: 24px;
}

.gplt-header h2 {
    margin: 0;
    font-size: 24px;
    color: #393E46;
}

.gplt-subtitle {
    margin: 4px 0 0;
    color: #6b7280;
    font-size: 14px;
}

.gplt-info-box {
    background: #e6f4fa;
    border-radius: 10px;
    padding: 25px 30px;
    margin-bottom: 30px;
    display: flex;
    gap: 16px;
    align-items: flex-start;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    border-left: 4px solid #1a8fc4;
}

.gplt-info-icon {
    color: #1a8fc4;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
}

.gplt-info-content p {
    margin: 0;
    color: #334155;
    font-size: 14px;
    line-height: 1.6;
}

.gplt-section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin: 30px 0 16px;
    padding-bottom: 10px;
    border-bottom: 1px solid #e2e8f0;
}

.gplt-section-header h3 {
    margin: 0;
    font-size: 18px;
    color: #1e293b;
    display: flex;
    align-items: center;
    gap: 8px;
}

.gplt-section-header h3::before {
    content: '';
    display: inline-block;
    width: 4px;
    height: 18px;
    background-color: #1a8fc4;
    border-radius: 2px;
}

.gplt-actions {
    display: flex;
    gap: 8px;
}

.gplt-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    border: none;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
}

.gplt-btn svg {
    width: 18px;
    height: 18px;
}

.gplt-btn-primary {
    background: #1a8fc4;
    color: white;
    padding: 12px 24px;
    font-size: 14px;
    height: 44px;
    font-weight: 600;
}

.gplt-btn-primary:hover {
    background: #1a8fc4;
    transform: translateY(-1px);
}

.gplt-btn-secondary {
    background: #f1f5f9;
    color: #475569;
    border: 1px solid #e2e8f0;
    padding: 8px 12px;
    font-size: 12px;
    border-radius: 6px;
}

.gplt-btn-secondary:hover {
    background: #e2e8f0;
    transform: translateY(-1px);
}

.gplt-table-container {
    background: #f8fafc;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    margin-bottom: 24px;
    border: 1px solid #e2e8f0;
}

.gplt-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
}

.gplt-table th {
    background: #f1f5f9;
    padding: 14px 20px;
    text-align: left;
    font-size: 13px;
    font-weight: 600;
    color: #475569;
    border-bottom: 1px solid #e2e8f0;
}

.gplt-table td {
    padding: 14px 20px;
    font-size: 14px;
    color: #334155;
    background: white;
    border-bottom: 1px solid #e2e8f0;
    transition: background-color 0.15s ease;
}

.gplt-table tr:hover td {
    background-color: #f8fafc;
}

.gplt-table tr:last-child td {
    border-bottom: none;
}

/* Remove custom checkbox styling and use WordPress defaults */
.gplt-table input[type="checkbox"] {
    margin: 0;
    vertical-align: middle;
}

.gplt-empty-state {
    text-align: center;
    padding: 40px;
    background: #f8fafc;
    border-radius: 8px;
    color: #64748b;
    border: 1px dashed #cbd5e1;
}

.gplt-empty-state p {
    margin: 0;
    font-size: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.gplt-empty-state p::before {
    content: '';
    display: inline-block;
    width: 20px;
    height: 20px;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='20' height='20' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z'%3E%3C/path%3E%3Cpolyline points='13 2 13 9 20 9'%3E%3C/polyline%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: center;
}

.gplt-form-actions {
    margin-top: 24px;
    display: flex;
    justify-content: flex-start;
}

/* Toast notification styles */
.gpltimes-toast {
    position: fixed;
    top: 80px;
    right: 30px;
    z-index: 9999;
    visibility: hidden;
    min-width: 250px;
    transform: translateX(30px);
    opacity: 0;
    transition: all 0.3s ease;
}

.gpltimes-toast.show {
    visibility: visible;
    transform: translateX(0);
    opacity: 1;
}

.gpltimes-toast-content {
    display: flex;
    align-items: center;
    gap: 12px;
    background-color: #4CAF50;
    color: white;
    padding: 16px 20px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    font-weight: 500;
}

.gpltimes-toast-content svg {
    flex-shrink: 0;
}

/* Remove old toast styles */
.gplt-toast {
    display: none;
}
</style>

<script>
// Toast notification functionality
document.addEventListener('DOMContentLoaded', function() {
    // Define ajaxurl if it's not already defined
    if (typeof ajaxurl === 'undefined') {
        var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
    }

    // Function to show toast notification
    function showToast(message, type = 'success') {
        var toast = document.getElementById('gpltimes-toast');
        var toastMessage = document.getElementById('gpltimes-toast-message');
        var toastIcon = toast.querySelector('.gpltimes-toast-icon');

        // Ensure the toast is visible in the DOM
        toast.style.display = 'block';

        // Set message
        toastMessage.textContent = message;

        // Create a style element to override the inline styles with !important
        var styleElement = document.createElement('style');
        document.head.appendChild(styleElement);
        var styleSheet = styleElement.sheet;

        // Set color and icon based on type
        var bgColor = '#4CAF50'; // Default success color
        var iconPath = '';

        if (type === 'error') {
            bgColor = '#f44336';
            // Error icon
            iconPath = '<path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"></path>';
        } else if (type === 'warning') {
            bgColor = '#ff9800';
            // Warning icon
            iconPath = '<path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"></path>';
        } else if (type === 'info') {
            bgColor = '#2196F3';
            // Info icon
            iconPath = '<path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"></path>';
        } else {
            // Success icon (default)
            iconPath = '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline>';
        }

        // Update the icon
        toastIcon.innerHTML = iconPath;

        // Add a rule with !important to override the inline style
        var toastId = 'toast-' + Date.now();
        toast.querySelector('.gpltimes-toast-content').id = toastId;
        styleSheet.insertRule('#' + toastId + ' { background-color: ' + bgColor + ' !important; }', 0);

        // Show toast
        toast.classList.add('show');

        // Hide toast after 3 seconds
        setTimeout(function() {
            toast.classList.remove('show');

            // After the transition completes, reset display and remove the style element
            setTimeout(function() {
                toast.style.display = '';
                document.head.removeChild(styleElement);
            }, 300);
        }, 3000);
    }

    // Handle form submission via AJAX
    document.getElementById('disable-updates-form').addEventListener('submit', function(e) {
        e.preventDefault();

        var saveButton = document.querySelector('button[name="uncheck"]');
        saveButton.disabled = true;
        saveButton.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v4m0 12v4M4.93 4.93l2.83 2.83m8.48 8.48l2.83 2.83M2 12h4m12 0h4M4.93 19.07l2.83-2.83m8.48-8.48l2.83-2.83"></path></svg> Saving...';

        // Create FormData object
        var formData = new FormData(this);

        // Send AJAX request
        fetch(ajaxurl, {
            method: 'POST',
            body: formData,
            credentials: 'same-origin'
        })
        .then(function(response) {
            return response.json();
        })
        .then(function(data) {
            if (data.success) {
                // Show success toast
                showToast(data.data, 'success');
            } else {
                // Show error toast
                showToast('Error: ' + data.data, 'error');
            }

            // Reset button state
            saveButton.disabled = false;
            saveButton.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path><polyline points="9 11 12 14 22 4"></polyline></svg> Save Changes';
        })
        .catch(function(error) {
            // Show error toast
            showToast('Error: ' + error.message, 'error');

            // Reset button state
            saveButton.disabled = false;
            saveButton.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path><polyline points="9 11 12 14 22 4"></polyline></svg> Save Changes';
        });
    });
});

function selectAllPlugins() {
    var checkboxes = document.getElementsByClassName('plugin-checkbox');
    for (var i = 0; i < checkboxes.length; i++) {
        checkboxes[i].checked = true;
    }
}

function deselectAllPlugins() {
    var checkboxes = document.getElementsByClassName('plugin-checkbox');
    for (var i = 0; i < checkboxes.length; i++) {
        checkboxes[i].checked = false;
    }
}

function selectAllThemes() {
    var checkboxes = document.getElementsByClassName('theme-checkbox');
    for (var i = 0; i < checkboxes.length; i++) {
        checkboxes[i].checked = true;
    }
}

function deselectAllThemes() {
    var checkboxes = document.getElementsByClassName('theme-checkbox');
    for (var i = 0; i < checkboxes.length; i++) {
        checkboxes[i].checked = false;
    }
}
</script>