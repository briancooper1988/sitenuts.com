<?php
// Fetch existing settings
$settings = get_option('gpltimes_whitelabel_settings', array());
?>

<div class="gplt-main-container">
    <div class="gplt-wrap">
        <div class="gplt-content">
            <div class="gplt-header">
                <div class="gplt-header-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="#1a8fc4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <div>
                    <h2>Whitelabel Settings</h2>
                    <p class="gplt-subtitle">Rebrand the plugin with your company's identity and customize user experience</p>
                </div>
            </div>

            <form id="gpltimes-settings-form" method="post" action="">
                <div class="gplt-dashboard">
                    <!-- First Column: New Whitelabel Information -->
                    <div class="gplt-card">
                        <div class="gplt-card-header">
                            <div class="gplt-card-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                </svg>
                            </div>
                            <h3>New Whitelabel Information</h3>
                        </div>
                        <div class="gplt-card-body">
                            <div class="gplt-form-group">
                                <div class="gplt-input-label">Plugin Name</div>
                                <div class="gplt-input-wrapper">
                                    <input name="gpltimes_name" type="text" id="gpltimes_name" value="<?php echo esc_attr($settings['name'] ?? ''); ?>">
                                    <p class="description">Enter the new plugin name.</p>
                                </div>
                            </div>

                            <div class="gplt-form-group">
                                <div class="gplt-input-label">Description</div>
                                <div class="gplt-input-wrapper">
                                    <input name="gpltimes_description" type="text" id="gpltimes_description" value="<?php echo esc_attr($settings['description'] ?? ''); ?>">
                                    <p class="description">Enter the description for the plugin.</p>
                                </div>
                            </div>

                            <div class="gplt-form-group">
                                <div class="gplt-input-label">Author</div>
                                <div class="gplt-input-wrapper">
                                    <input name="gpltimes_author" type="text" id="gpltimes_author" value="<?php echo esc_attr($settings['author'] ?? ''); ?>">
                                    <p class="description">Enter the new author name or your Agency Name.</p>
                                </div>
                            </div>

                            <div class="gplt-form-group">
                                <div class="gplt-input-label">Author URL</div>
                                <div class="gplt-input-wrapper">
                                    <input name="gpltimes_author_url" type="text" id="gpltimes_author_url" value="<?php echo esc_attr($settings['author_url'] ?? ''); ?>">
                                    <p class="description">Enter the author URL or your Agency URL.</p>
                                </div>
                            </div>

                            <div class="gplt-form-group">
                                <div class="gplt-input-label">Logo</div>
                                <div class="gplt-input-wrapper">
                                    <div class="gplt-logo-input">
                                        <input name="gpltimes_logo" type="text" id="gpltimes_logo" value="<?php echo esc_attr($settings['logo'] ?? ''); ?>">
                                        <button type="button" id="upload_logo_button" class="gplt-btn gplt-btn-secondary">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                                <circle cx="8.5" cy="8.5" r="1.5"/>
                                                <polyline points="21 15 16 10 5 21"/>
                                            </svg>
                                            Upload Logo
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Removed Visibility Settings Section - Now available in the Settings page -->
                </div>

                <!-- Visibility Settings -->
                <div class="gplt-card" style="width: 100%; margin-bottom: 20px; margin-top: 20px; border-left: 4px solid #1a8fc4;">
                    <div class="gplt-card-body">
                        <div class="gplt-checkbox-wrapper" style="display: flex; flex-direction: column; gap: 15px;">
                            <div class="gplt-checkbox-group">
                                <label class="gplt-checkbox-label" style="display: flex; align-items: flex-start;">
                                    <div style="margin-right: 15px; margin-top: 2px;">
                                        <input name="gpltimes_hide_settings" type="checkbox" id="gpltimes_hide_settings" value="1"
                                            <?php echo checked(1, (isset($settings['hide_settings']) ? $settings['hide_settings'] : 0), false); ?>>
                                    </div>
                                    <div style="flex: 1;">
                                        <div style="display: flex; align-items: center; margin-bottom: 6px;">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#1a8fc4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px;">
                                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                                <line x1="1" y1="1" x2="23" y2="23"></line>
                                            </svg>
                                            <span style="font-weight: 600; display: block; color: #333; font-size: 15px;">Hide whitelabel settings page?</span>
                                        </div>
                                        <p style="margin-top: 0; color: #666; font-size: 13px; line-height: 1.5; padding-left: 26px;">This setting will hide the whitelabel settings page from the WordPress admin menu. <strong>Remember:</strong> To access this page again, you'll need to deactivate and reactivate the plugin.</p>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="gplt-form-actions">
                    <button type="button" id="gpltimes_save_settings" class="gplt-btn gplt-btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                            <polyline points="9 11 12 14 22 4"></polyline>
                        </svg>
                        Save Changes
                    </button>
                    <button type="button" id="gpltimes_reset_defaults" class="gplt-btn gplt-btn-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"></path>
                            <path d="M3 3v5h5"></path>
                        </svg>
                        Reset Defaults
                    </button>
                    <?php wp_nonce_field('gpltimes_whitelabel_settings', 'gpltimes_whitelabel_nonce'); ?>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Toast notification -->
<div id="gpltimes-toast" class="gpltimes-toast">
    <div class="gpltimes-toast-content">
        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="gpltimes-toast-icon">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
            <polyline points="22 4 12 14.01 9 11.01"></polyline>
        </svg>
        <span id="gpltimes-toast-message"></span>
    </div>
</div>

<style>
/* Main Container Styles */
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
    position: relative;
}

/* Header Styles */
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

/* Dashboard Layout */
.gplt-dashboard {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 24px;
}

/* Card Styles */
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

/* Form Field Styles */
.gplt-form-group {
    margin-bottom: 20px;
    display: flex;
    align-items: flex-start;
    gap: 24px;
}

.gplt-form-group:last-child {
    margin-bottom: 0;
}

.gplt-input-label {
    font-weight: 500;
    color: #333;
    width: 120px;
    padding-top: 10px;
    flex-shrink: 0;
}

.gplt-input-wrapper {
    flex: 1;
}

.gplt-input-wrapper input[type="text"] {
    width: 100%;
    height: 44px;
    padding: 0 16px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    transition: all 0.2s ease;
    font-size: 14px;
}

.gplt-input-wrapper input[type="text"]:focus {
    border-color: #1a8fc4;
    outline: none;
    box-shadow: 0 0 0 3px rgba(26, 143, 196, 0.1);
}

/* Logo Input and Preview */
.gplt-logo-input {
    display: flex;
    gap: 12px;
    align-items: center;
}

.gplt-logo-input input[type="text"] {
    flex: 1;
}

.gplt-logo-preview {
    display: none;
}

/* Description Styles */
.description {
    margin-top: 8px;
    color: #6b7280;
    font-size: 13px;
}

/* Checkbox Styles */
.gplt-checkbox-wrapper {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.gplt-checkbox-group {
    margin-bottom: 0;
}

.gplt-checkbox-label {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    color: #333;
    cursor: pointer;
}

.gplt-checkbox-container {
    position: relative;
    flex-shrink: 0;
    width: 20px;
    height: 20px;
    margin-top: 2px;
}

.gplt-checkbox-container input {
    position: absolute;
    opacity: 0;
    cursor: pointer;
    height: 0;
    width: 0;
}

.gplt-checkmark {
    position: absolute;
    top: 0;
    left: 0;
    height: 20px;
    width: 20px;
    background-color: #fff;
    border: 2px solid #d1d5db;
    border-radius: 4px;
    transition: all 0.2s ease;
}

.gplt-checkbox-container input:checked ~ .gplt-checkmark {
    background-color: #1a8fc4;
    border-color: #1a8fc4;
}

.gplt-checkmark:after {
    content: "";
    position: absolute;
    display: none;
}

.gplt-checkbox-container input:checked ~ .gplt-checkmark:after {
    display: block;
}

.gplt-checkbox-container .gplt-checkmark:after {
    left: 6px;
    top: 2px;
    width: 5px;
    height: 10px;
    border: solid white;
    border-width: 0 2px 2px 0;
    transform: rotate(45deg);
}

.gplt-checkbox-text {
    flex: 1;
}

.gplt-checkbox-text span {
    display: block;
    font-weight: 500;
    margin-bottom: 4px;
}

.gplt-checkbox-description {
    margin: 0;
    color: #6b7280;
    font-size: 13px;
}

/* Button Styles */
.gplt-form-actions {
    margin-top: 25px;
    display: flex;
    gap: 12px;
    align-items: center;
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

.gplt-btn-secondary {
    background: #f3f4f6;
    color: #4b5563;
    height: 46px;
    padding: 0 16px;
    font-size: 14px;
    display: inline-flex;
    align-items: center;
    border: 1px solid #e5e7eb;
}

.gplt-btn-secondary:hover {
    background: #e5e7eb;
    transform: translateY(-2px);
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

/* Responsive Styles */
@media (max-width: 768px) {
    .gplt-header {
        flex-direction: column;
        align-items: flex-start;
        text-align: left;
        gap: 12px;
    }

    .gplt-content {
        padding: 24px 20px;
    }

    .gplt-form-group {
        flex-direction: column;
        gap: 8px;
    }

    .gplt-input-label {
        width: 100%;
        padding-top: 0;
    }
}
</style>

<script>
// Define ajaxurl if it's not already defined
var ajaxurl = ajaxurl || '<?php echo admin_url('admin-ajax.php'); ?>';

document.addEventListener('DOMContentLoaded', function() {
    // Handle media uploader for logo
    document.getElementById('upload_logo_button').addEventListener('click', function(e) {
        e.preventDefault();

        var frame = wp.media({
            title: 'Select Logo',
            button: {
                text: 'Select'
            },
            multiple: false
        });

        frame.on('select', function() {
            var attachment = frame.state().get('selection').first().toJSON();
            var logoUrl = attachment.url;

            // Set the URL in the input field
            document.getElementById('gpltimes_logo').value = logoUrl;
        });

        frame.open();
    });

    // Handle save settings button
    document.getElementById('gpltimes_save_settings').addEventListener('click', function(e) {
        e.preventDefault();

        // Get the form
        var form = document.getElementById('gpltimes-settings-form');

        // Get the nonce
        var nonce = document.querySelector('[name="gpltimes_whitelabel_nonce"]').value;

        // Create FormData object from the form
        var formData = new FormData(form);
        formData.append('action', 'gpltimes_save_whitelabel');
        formData.append('nonce', nonce);

        // Show loading state
        var saveButton = this;
        saveButton.disabled = true;
        saveButton.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v4m0 12v4M4.93 4.93l2.83 2.83m8.48 8.48l2.83 2.83M2 12h4m12 0h4M4.93 19.07l2.83-2.83m8.48-8.48l2.83-2.83"></path></svg> Saving...';

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
                showToast('Settings saved successfully', 'success');

                // Refresh the current page after a short delay
                setTimeout(function() {
                    // Clean URL by removing import parameter
                    const currentUrl = new URL(window.location.href);
                    currentUrl.searchParams.delete('import');
                    window.history.replaceState({}, document.title, currentUrl.toString());
                    // Redirect to dashboard instead of refreshing
                    window.location.href = "<?php echo admin_url('admin.php?page=gpltimes_plugin&settings_updated=true'); ?>";
                }, 800);
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

    // Handle reset defaults button
    document.getElementById('gpltimes_reset_defaults').addEventListener('click', function(e) {
        e.preventDefault();

        // Get the nonce
        var nonce = document.querySelector('[name="gpltimes_whitelabel_nonce"]').value;

        // Create FormData object
        var formData = new FormData();
        formData.append('action', 'gpltimes_reset_whitelabel');
        formData.append('nonce', nonce);

        // Show loading state
        this.disabled = true;
        this.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v4m0 12v4M4.93 4.93l2.83 2.83m8.48 8.48l2.83 2.83M2 12h4m12 0h4M4.93 19.07l2.83-2.83m8.48-8.48l2.83-2.83"></path></svg> Resetting...';

        // Send AJAX request
        fetch(ajaxurl, {
            method: 'POST',
            body: formData,
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success toast
                showToast('Settings have been reset to default values');

                // Refresh the page after a short delay to show the toast
                setTimeout(function() {
                    window.location.reload();
                }, 1000);
            } else {
                // Reset button state
                this.disabled = false;
                this.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"></path><path d="M3 3v5h5"></path></svg> Reset Defaults';

                // Show error toast
                showToast('Error: ' + data.data, 'error');
            }
        })
        .catch(error => {
            // Reset button state
            this.disabled = false;
            this.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"></path><path d="M3 3v5h5"></path></svg> Reset Defaults';

            // Show error toast
            showToast('Error: ' + error.message, 'error');
        });
    });

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

    // Enable form submission when Enter key is pressed in any input field
    const form = document.getElementById('gpltimes-settings-form');
    const saveButton = document.getElementById('gpltimes_save_settings');

    if (form && saveButton) {
        const inputFields = form.querySelectorAll('input[type="text"], input[type="email"], input[type="url"]');
        inputFields.forEach(input => {
            input.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    // Trigger the same action as clicking the Save Changes button
                    saveButton.click();
                }
            });
        });
    }
});
</script>