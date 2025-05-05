<?php
/**
 * @package  Gpltimes
 */

namespace Inc\Pages;

use Inc\Api\SettingsApi;
use Inc\Base\BaseController;
use Inc\Api\Callbacks\AdminCallbacks;

/**
*
*/
class Admin extends BaseController
{
    public $settings;

    public $callbacks;

    public $pages = array();

    public $subpages = array();

    public function register()
    {
        $this->settings = new SettingsApi();

        $this->callbacks = new AdminCallbacks();

        $this->setPages();

        $this->setSubpages();

        $this->setSettings();
        $this->setSections();
        $this->setFields();



        $this->settings->addPages($this->pages)->withSubPage('Dashboard')->addSubPages($this->subpages)->register();
        add_action('admin_post_export_white_label_settings', array($this, 'export_white_label_settings'));
        add_action('admin_post_import_white_label_settings', array($this, 'handle_import_white_label_settings'));
        add_action('wp_ajax_gpltimes_save_beta_updates', array($this, 'save_beta_updates'));
        add_action('wp_ajax_gpltimes_save_visibility_settings', array($this, 'save_visibility_settings'));

    }

    public function setPages()
    {
        // Fetch the whitelabel settings
        $whitelabel_settings = get_option('gpltimes_whitelabel_settings', array());

        $plugin_name = isset($whitelabel_settings['name']) && !empty($whitelabel_settings['name']) ? $whitelabel_settings['name'] : 'GPL Times';
        $plugin_logo = (isset($whitelabel_settings['logo']) && !empty($whitelabel_settings['logo'])) ? 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/wcAAwAB/h2p7QYAAAAASUVORK5CYII=' : plugin_dir_url(dirname(__FILE__, 2)) . 'assets/favicon-32x32.svg';

        $this->pages = array(
            array(
                'page_title' => $plugin_name,
                'menu_title' => $plugin_name,
                'capability' => 'manage_options',
                'menu_slug' => 'gpltimes_plugin',
                'callback' => array( $this->callbacks, 'adminDashboard' ),
                'icon_url' => $plugin_logo,
                'position' => 70
            )
        );
    }


    public function save_beta_updates()
    {
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'gpltimes_beta_updates_nonce')) {
            wp_send_json_error('Security check failed');
            return;
        }

        if (isset($_POST['beta_updates'])) {
            $beta_updates = $_POST['beta_updates'] === '1' ? '1' : '0';
            update_option('gpltimes_beta_updates', $beta_updates);
            wp_send_json_success();
        } else {
            wp_send_json_error();
        }
    }

    public function setSubpages()
    {
        $whitelabel_settings = get_option('gpltimes_whitelabel_settings', array());

        // Initialize the subpages array
        $this->subpages = array();

        // Conditionally add the 'Disable Updates' submenu
        if (!isset($whitelabel_settings['disable_updates_visibility']) || $whitelabel_settings['disable_updates_visibility'] != 1) {
            $this->subpages[] = array(
                'parent_slug' => 'gpltimes_plugin',
                'page_title' => 'Disable Updates',
                'menu_title' => 'Disable Updates',
                'capability' => 'manage_options',
                'menu_slug' => 'disable_update_check',
                'callback' => array( $this->callbacks, 'disableupdate' )
            );
        }

        // Only add the Whitelabel settings subpage if "hide this settings page" is not checked
        if (!isset($whitelabel_settings['hide_settings']) || $whitelabel_settings['hide_settings'] != 1) {
            // Add the submenu for Whitelabel Settings
            $this->subpages[] = array(
                'parent_slug' => 'gpltimes_plugin',
                'page_title' => 'Whitelabel Settings',
                'menu_title' => 'Whitelabel Settings',
                'capability' => 'manage_options',
                'menu_slug' => 'gpltimes-whitelabel',
                'callback' => 'gpltimes_whitelabel_settings_page'
            );
        }

        $this->subpages[] = array(
                'parent_slug' => 'gpltimes_plugin',
                'page_title' => 'Settings',
                'menu_title' => 'Settings',
                'capability' => 'manage_options',
                'menu_slug' => 'gpltimes_export_import',
                'callback' => array($this, 'exportImportPage')
            );
    }



    public function setSettings()
    {
        $args = array(
            array(
                'option_group' => 'gpl_options_group',
                'option_name' => 'username',
                'callback' => array( $this->callbacks, 'gplOptionsGroup' )
            ),
            array(
                'option_group' => 'gpl_options_group',
                'option_name' => 'password'
            ),
            array(
                'option_group' => 'gpltimes_whitelabel_group',
                'option_name' => 'gpltimes_whitelabel_settings'
            )
        );

        $this->settings->setSettings($args);
    }

    public function setSections()
    {
        // Fetch the whitelabel settings
        $whitelabel_settings = get_option('gpltimes_whitelabel_settings', array());

        $plugin_name = isset($whitelabel_settings['name']) && !empty($whitelabel_settings['name']) ? $whitelabel_settings['name'] : 'GPL Times';

        $args = array(
            array(
                'id' => 'gpl_admin_index',
               'title' => '',
                'callback' => array( $this->callbacks, 'gplAdminSection' ),
                'page' => 'gpltimes_plugin'
            )
        );

        $this->settings->setSections($args);
    }


    public function setFields()
    {
        $args = array(
            array(
                'id' => 'username',
                'title' => '',
                'callback' => array($this->callbacks, 'gpltimesusername'),
                'page' => 'gpltimes_plugin',
                'section' => 'gpl_admin_index',
                'args' => array(
                    'label_for' => 'username'
                )
            ),
            array(
                'id' => 'password',
                'title' => '',
                'callback' => array($this->callbacks, 'gpltimespassword'),
                'page' => 'gpltimes_plugin',
                'section' => 'gpl_admin_index',
                'args' => array(
                    'label_for' => 'password'
                )
            ),
            array(
                'id' => 'beta_updates',
                'title' => '',
                'callback' => array($this->callbacks, 'betaUpdatesField'),
                'page' => 'gpltimes_plugin',
                'section' => 'gpl_admin_index',
                'args' => array(
                    'label_for' => 'beta_updates',
                    'class' => 'gpl-beta-updates'
                )
            )
        );

        $this->settings->setFields($args);
    }

    public function exportImportPage()
    {
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
                        <div class="gplt-header-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="#1a8fc4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                <polyline points="17 8 12 3 7 8"></polyline>
                                <line x1="12" y1="3" x2="12" y2="15"></line>
                            </svg>
                        </div>
                        <div>
                            <h2>Settings</h2>
                            <p class="gplt-subtitle">Configure plugin visibility and manage import/export functionality</p>
                        </div>
                    </div>

                    <?php $this->display_import_messages(); ?>

                    <!-- Visibility Settings Section (Full Width) -->
                    <div class="gplt-card" style="width: 100%; margin-bottom: 20px;">
                        <div class="gplt-card-header">
                            <div class="gplt-card-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                            </div>
                            <h3>Visibility Settings</h3>
                        </div>
                        <div class="gplt-card-body">
                            <?php
                            // Get the current whitelabel settings
                            $settings = get_option('gpltimes_whitelabel_settings', array());
                            ?>
                            <form method="post" action="options.php" id="visibility-settings-form">
                                <?php settings_fields('gpltimes_whitelabel_group'); ?>
                                <div class="gplt-form-content">
                                    <p class="gplt-description">Configure which plugin components are visible in the WordPress admin area.</p>
                                    <div class="gplt-checkbox-wrapper" style="display: flex; flex-direction: column; gap: 15px;">
                                        <div class="gplt-checkbox-group">
                                            <label class="gplt-checkbox-label" style="display: flex; align-items: flex-start;">
                                                <div style="margin-right: 10px; margin-top: 4px;">
                                                    <input name="disable_updates_visibility" type="checkbox"
                                                        id="gpltimes_disable_updates_visibility" value="1"
                                                        <?php echo !empty($settings['disable_updates_visibility']) ? 'checked' : ''; ?>>
                                                </div>
                                                <div style="flex: 1;">
                                                    <span style="font-weight: 500; display: block; margin-bottom: 5px;">Hide 'Disable Updates' Menu</span>
                                                    <p style="margin-top: 0; color: #666; font-size: 13px;">This setting will hide the disable updates menu from the plugin settings.</p>
                                                </div>
                                            </label>
                                        </div>

                                        <div class="gplt-checkbox-group">
                                            <label class="gplt-checkbox-label" style="display: flex; align-items: flex-start;">
                                                <div style="margin-right: 10px; margin-top: 4px;">
                                                    <input name="disable_all_admin_notices" type="checkbox"
                                                        id="gpltimes_disable_all_admin_notices" value="1"
                                                        <?php echo !empty($settings['disable_all_admin_notices']) ? 'checked' : ''; ?>>
                                                </div>
                                                <div style="flex: 1;">
                                                    <span style="font-weight: 500; display: block; margin-bottom: 5px;">Disable All Admin Notices</span>
                                                    <p style="margin-top: 0; color: #666; font-size: 13px;">This will hide all WordPress admin notices across the dashboard.</p>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="gplt-form-actions">
                                        <button type="button" id="gpltimes_save_visibility_settings" class="gplt-btn gplt-btn-primary">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                                                <polyline points="9 11 12 14 22 4"></polyline>
                                            </svg>
                                            Save Changes
                                        </button>
                                    </div>
                                    <?php
                                    // Create nonce directly instead of using wp_nonce_field
                                    $nonce = wp_create_nonce('gpltimes_visibility_settings');
                                    echo '<input type="hidden" id="gpltimes_visibility_nonce" name="gpltimes_visibility_nonce" value="' . esc_attr($nonce) . '" />';
                                    ?>
                                </div>
                            </form>

                            <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                const saveButton = document.getElementById('gpltimes_save_visibility_settings');

                                if (saveButton) {
                                    saveButton.addEventListener('click', function(e) {
                                        e.preventDefault();

                                        // Update button state
                                        saveButton.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" class="gplt-loading-icon" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"></path></svg> Saving...';
                                        saveButton.disabled = true;

                                        // Get form data
                                        const disableUpdatesVisibility = document.getElementById('gpltimes_disable_updates_visibility').checked ? 1 : 0;
                                        const disableAllAdminNotices = document.getElementById('gpltimes_disable_all_admin_notices').checked ? 1 : 0;
                                        const nonce = document.getElementById('gpltimes_visibility_nonce').value;

                                        // Send AJAX request
                                        jQuery.post(ajaxurl, {
                                            action: 'gpltimes_save_visibility_settings',
                                            disable_updates_visibility: disableUpdatesVisibility,
                                            disable_all_admin_notices: disableAllAdminNotices,
                                            nonce: nonce
                                        }, function(response) {
                                            if (response.success) {
                                                showToast('Settings saved successfully');
                                                // Refresh page after 800ms to see changes
                                                setTimeout(function() {
                                                    // Clean URL by removing import parameter
                                                    const currentUrl = new URL(window.location.href);
                                                    currentUrl.searchParams.delete('import');
                                                    window.history.replaceState({}, document.title, currentUrl.toString());
                                                    window.location.reload();
                                                }, 800);
                                            } else {
                                                showToast('Error: ' + response.data, 'error');
                                            }

                                            // Restore button state
                                            saveButton.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path><polyline points="9 11 12 14 22 4"></polyline></svg> Save Changes';
                                            saveButton.disabled = false;
                                        }).fail(function() {
                                            showToast('Connection error. Please try again.', 'error');

                                            // Restore button state
                                            saveButton.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path><polyline points="9 11 12 14 22 4"></polyline></svg> Save Changes';
                                            saveButton.disabled = false;
                                        });
                                    });
                                }

                                // Add styling for loading icon animation if needed
                                const style = document.createElement('style');
                                style.textContent = `
                                    .gplt-loading-icon {
                                        animation: gplt-spin 1s linear infinite;
                                    }
                                    @keyframes gplt-spin {
                                        0% { transform: rotate(0deg); }
                                        100% { transform: rotate(360deg); }
                                    }
                                `;
                                document.head.appendChild(style);
                            });
                            </script>
                        </div>
                    </div>

                    <!-- Export and Import Section (Two Cards in One Row) -->
                    <div class="gplt-dashboard" style="display: flex; gap: 20px;">
                        <!-- Export Section -->
                        <div class="gplt-card" style="flex: 1;">
                            <div class="gplt-card-header">
                                <div class="gplt-card-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                        <polyline points="7 10 12 15 17 10"/>
                                        <line x1="12" y1="15" x2="12" y2="3"/>
                                    </svg>
                                </div>
                                <h3>Export Settings</h3>
                            </div>
                            <div class="gplt-card-body">
                                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                                    <input type="hidden" name="action" value="export_white_label_settings">
                                    <?php wp_nonce_field('gpltimes_export_settings', 'gpltimes_export_nonce'); ?>
                                    <div class="gplt-form-content">
                                        <p class="gplt-description">Download your current whitelabel settings as a JSON file. You can use this file to backup your configuration or transfer it to another installation.</p>
                                        <button type="submit" class="gplt-btn gplt-btn-primary">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                                <polyline points="7 10 12 15 17 10"/>
                                                <line x1="12" y1="15" x2="12" y2="3"/>
                                            </svg>
                                            Export Settings
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Import Section -->
                        <div class="gplt-card" style="flex: 1;">
                            <div class="gplt-card-header">
                                <div class="gplt-card-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                        <polyline points="17 8 12 3 7 8"/>
                                        <line x1="12" y1="3" x2="12" y2="15"/>
                                    </svg>
                                </div>
                                <h3>Import Settings</h3>
                            </div>
                            <div class="gplt-card-body">
                                <form method="post" enctype="multipart/form-data" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                                    <input type="hidden" name="action" value="import_white_label_settings">
                                    <?php wp_nonce_field('gpltimes_import_settings', 'gpltimes_import_nonce'); ?>
                                    <div class="gplt-form-content">
                                        <p class="gplt-description">Upload a previously exported JSON file to restore your whitelabel settings.</p>
                                        <div class="gplt-file-input">
                                            <label for="settings_file" class="gplt-file-label">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path>
                                                    <polyline points="13 2 13 9 20 9"></polyline>
                                                </svg>
                                                <span>Choose File</span>
                                            </label>
                                            <input type="file" name="settings_file" id="settings_file" accept=".json" class="gplt-file-input-field">
                                            <div id="file-name" class="gplt-selected-file">No file selected</div>
                                        </div>
                                        <button type="submit" name="import_settings" id="import_settings_button" class="gplt-btn gplt-btn-primary" disabled>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                                <polyline points="17 8 12 3 7 8"/>
                                                <line x1="12" y1="3" x2="12" y2="15"/>
                                            </svg>
                                            Import Settings
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Function to show toast notification
            window.showToast = function(message, type = 'success') {
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
            };

            // Check for import status in URL
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('import')) {
                if (urlParams.get('import') === 'success') {
                    showToast('Settings imported successfully', 'success');

                    // Clean URL by removing import parameter
                    const currentUrl = new URL(window.location.href);
                    currentUrl.searchParams.delete('import');
                    window.history.replaceState({}, document.title, currentUrl.toString());
                } else if (urlParams.get('import') === 'fail') {
                    const errorMessage = urlParams.has('error')
                        ? urlParams.get('error')
                        : 'There was an error during the import.';

                    showToast('Import Error: ' + errorMessage, 'error');

                    // Clean URL by removing import and error parameters
                    const currentUrl = new URL(window.location.href);
                    currentUrl.searchParams.delete('import');
                    currentUrl.searchParams.delete('error');
                    window.history.replaceState({}, document.title, currentUrl.toString());
                }
            }

            // Add event listener for export form
            const exportForm = document.querySelector('form[action*="export_white_label_settings"]');
            if (exportForm) {
                exportForm.addEventListener('submit', function() {
                    showToast('Exporting settings...', 'info');
                });
            }

            // Add event listener for import form
            const importForm = document.querySelector('form[action*="import_white_label_settings"]');
            if (importForm) {
                importForm.addEventListener('submit', function(e) {
                    const fileInput = document.getElementById('settings_file');
                    if (!fileInput.files.length) {
                        e.preventDefault();
                        showToast('Please select a file to import', 'error');
                        return false;
                    }

                    showToast('Importing settings...', 'info');
                });
            }

            // Update file name display when a file is selected
            const fileInput = document.getElementById('settings_file');
            const fileNameDisplay = document.getElementById('file-name');
            const importButton = document.getElementById('import_settings_button');

            if (fileInput && fileNameDisplay) {
                fileInput.addEventListener('change', function() {
                    if (this.files.length > 0) {
                        fileNameDisplay.textContent = this.files[0].name;
                        fileNameDisplay.classList.add('file-selected');
                        // Enable the import button
                        importButton.disabled = false;
                    } else {
                        fileNameDisplay.textContent = 'No file selected';
                        fileNameDisplay.classList.remove('file-selected');
                        // Disable the import button
                        importButton.disabled = true;
                    }
                });
            }
        });
        </script>

        <style>
            .gplt-main-container {
                margin: 20px 20px 20px 0;
            }

            .gplt-wrap {
                width: 100%;
                background: #fff;
                border-radius: 12px;
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
            }

            .gplt-content {
                padding: 32px;
            }

            .gplt-header {
                display: flex;
                align-items: center;
                gap: 18px;
                margin-bottom: 32px;
                padding-bottom: 24px;
                border-bottom: 1px solid #f0f2f5;
            }

            .gplt-header-icon {
                background: rgba(26, 143, 196, 0.1);
                padding: 12px;
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .gplt-header h2 {
                margin: 0;
                font-size: 26px;
                font-weight: 600;
                color: #333;
                line-height: 1.3;
            }

            .gplt-subtitle {
                margin: 6px 0 0;
                color: #6b7280;
                font-size: 15px;
            }

            .gplt-dashboard {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
                gap: 24px;
                margin-top: 20px;
            }

            .gplt-card {
                background: #fff;
                border-radius: 12px;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
                border: 1px solid #f0f2f5;
                overflow: hidden;
                transition: transform 0.2s ease, box-shadow 0.2s ease;
            }

            .gplt-card:hover {
                transform: translateY(-2px);
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
            }

            .gplt-card-header {
                padding: 20px 24px;
                background: #f8f9fa;
                display: flex;
                align-items: center;
                gap: 16px;
                border-bottom: 1px solid #f0f2f5;
            }

            .gplt-card-icon {
                background: rgba(26, 143, 196, 0.1);
                width: 42px;
                height: 42px;
                border-radius: 10px;
                display: flex;
                align-items: center;
                justify-content: center;
                color: #1a8fc4;
            }

            .gplt-card-header h3 {
                margin: 0;
                font-size: 18px;
                font-weight: 600;
                color: #333;
            }

            .gplt-card-body {
                padding: 24px;
            }

            .gplt-form-content {
                display: flex;
                flex-direction: column;
                gap: 20px;
            }

            .gplt-description {
                margin: 0;
                color: #4b5563;
                font-size: 14px;
                line-height: 1.6;
            }

            .gplt-file-input {
                display: flex;
                flex-direction: column;
                gap: 12px;
            }

            .gplt-file-label {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                background-color: #f0f2f5;
                border-radius: 6px;
                padding: 10px 18px;
                cursor: pointer;
                font-weight: 500;
                color: #4b5563;
                border: 1px solid #e5e7eb;
                transition: all 0.2s ease;
                width: fit-content;
            }

            .gplt-file-label:hover {
                background-color: #e5e7eb;
            }

            .gplt-file-input-field {
                position: absolute;
                width: 1px;
                height: 1px;
                padding: 0;
                margin: -1px;
                overflow: hidden;
                clip: rect(0, 0, 0, 0);
                white-space: nowrap;
                border-width: 0;
            }

            .gplt-selected-file {
                padding: 10px;
                background-color: #f8f9fa;
                border-radius: 6px;
                border: 1px dashed #ddd;
                font-size: 14px;
                color: #6b7280;
            }

            .gplt-selected-file.file-selected {
                color: #333;
                background-color: #f0fff4;
                border-color: #c6f6d5;
            }

            .gplt-btn {
                display: inline-flex;
                align-items: center;
                gap: 10px;
                border: none;
                border-radius: 8px;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.2s ease;
            }

            .gplt-btn svg {
                width: 18px;
                height: 18px;
            }

            .gplt-btn-primary {
                width: fit-content;
                background: #1a8fc4;
                color: white;
                padding: 12px 24px;
                font-size: 14px;
                height: 46px;
                box-shadow: 0 1px 2px rgba(26, 143, 196, 0.1);
            }

            .gplt-btn-primary:hover {
                background: #167bb4;
                transform: translateY(-2px);
                box-shadow: 0 4px 6px rgba(26, 143, 196, 0.1);
            }

            .notice {
                margin: 0 0 24px 0;
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
                border-radius: 10px;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                font-weight: 500;
            }

            .gpltimes-toast-content svg {
                flex-shrink: 0;
            }

            /* Responsive tweaks */
            @media (max-width: 768px) {
                .gplt-dashboard {
                    grid-template-columns: 1fr;
                }

                .gplt-header {
                    flex-direction: column;
                    align-items: flex-start;
                    text-align: left;
                    gap: 12px;
                }

                .gplt-content {
                    padding: 24px 20px;
                }
            }

            .gplt-btn:disabled {
                opacity: 0.6;
                cursor: not-allowed;
                transform: none !important;
                box-shadow: none !important;
            }
        </style>
        <?php
    }

    private function display_import_messages()
    {
        // We're now using toast notifications instead of WordPress notices
        // This function is kept for backward compatibility but doesn't output anything
    }



    public function export_white_label_settings()
    {
        // Verify nonce
        if (!isset($_POST['gpltimes_export_nonce']) || !wp_verify_nonce($_POST['gpltimes_export_nonce'], 'gpltimes_export_settings')) {
            wp_die('Security check failed. Please try again.');
        }

        if (!current_user_can('manage_options')) {
            wp_die('You do not have sufficient permissions to access this page.');
        }

        $default_settings = [
            'name' => '',
            'description' => '',
            'author' => '',
            'author_url' => '',
            'logo' => '',
            'hide_settings' => 0,
            'disable_updates_visibility' => 0,
            'disable_all_admin_notices' => 0
        ];

        $settings = get_option('gpltimes_whitelabel_settings', $default_settings);
        $json_settings = json_encode($settings);

        header('Content-Disposition: attachment; filename="white_label_settings.json"');
        header('Content-Type: application/json');
        header('Content-Length: ' . strlen($json_settings));
        header('Connection: close');
        echo $json_settings;
        exit;
    }


    public function handle_import_white_label_settings()
    {
        // Verify nonce
        if (!isset($_POST['gpltimes_import_nonce']) || !wp_verify_nonce($_POST['gpltimes_import_nonce'], 'gpltimes_import_settings')) {
            wp_die('Security check failed. Please try again.');
        }

        if (!current_user_can('manage_options')) {
            wp_die('You do not have sufficient permissions to access this page.');
        }

        if (isset($_FILES['settings_file'])) {
            if ($_FILES['settings_file']['error'] !== UPLOAD_ERR_OK) {
                $this->redirect_with_error('File upload error.');
            }

            $file = $_FILES['settings_file'];
            $json_data = file_get_contents($file['tmp_name']);
            $settings = json_decode($json_data, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->redirect_with_error('Invalid JSON file. Please ensure you are uploading a valid settings file exported from GPL Times plugin.');
            }

            if (!is_array($settings) || !$this->is_valid_settings($settings)) {
                $this->redirect_with_error('Invalid settings structure. The file does not contain the expected settings format for GPL Times plugin. Please use a file exported from the plugin\'s export feature.');
            }

            // Remove any notice-related settings if they exist in the imported file
            // except for the disable_all_admin_notices option which we want to keep
            if (isset($settings['disable_admin_notice'])) {
                unset($settings['disable_admin_notice']);
            }
            if (isset($settings['disable_all_admin_notices_except_updates'])) {
                unset($settings['disable_all_admin_notices_except_updates']);
            }

            update_option('gpltimes_whitelabel_settings', $settings);
            wp_redirect(admin_url('admin.php?page=gpltimes_export_import&import=success'));
            exit;
        } else {
            $this->redirect_with_error('No file uploaded.');
        }
    }

    private function redirect_with_error($error_message)
    {
        wp_redirect(admin_url('admin.php?page=gpltimes_export_import&import=fail&error=' . urlencode($error_message)));
        exit;
    }

    private function is_valid_settings($settings)
    {
        // Define the required keys and their expected types
        $required_keys = [
            'name' => 'string',
            'description' => 'string',
            'author' => 'string',
            'author_url' => 'string',
            'logo' => 'string',
            'hide_settings' => 'integer',
            'disable_updates_visibility' => 'integer',
            'disable_all_admin_notices' => 'integer'
        ];

        foreach ($required_keys as $key => $type) {
            if (!isset($settings[$key]) || gettype($settings[$key]) !== $type) {
                return false;
            }
        }

        return true;
    }

    // AJAX handler for saving visibility settings
    public function save_visibility_settings() {
        // Check nonce for security
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'gpltimes_visibility_settings')) {
            wp_send_json_error('Security check failed');
            return;
        }

        // Get current settings
        $current_settings = get_option('gpltimes_whitelabel_settings', array());

        // Update settings based on form data
        $current_settings['disable_updates_visibility'] = isset($_POST['disable_updates_visibility']) && $_POST['disable_updates_visibility'] == 1 ? 1 : 0;
        $current_settings['disable_all_admin_notices'] = isset($_POST['disable_all_admin_notices']) && $_POST['disable_all_admin_notices'] == 1 ? 1 : 0;

        // Save updated settings
        update_option('gpltimes_whitelabel_settings', $current_settings);

        // Send success response
        wp_send_json_success('Settings saved successfully');
    }

}
