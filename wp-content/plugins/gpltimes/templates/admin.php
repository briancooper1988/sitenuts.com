<?php settings_errors(); ?>

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
        <div class="gplt-login-container">
            <div class="gplt-login-header">
                <div class="gplt-brand">
                    <div class="gplt-logo-wrapper">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="40" height="40" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="gplt-logo">
                            <path d="M20 7h-7c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h7c1.1 0 2-.9 2-2V9c0-1.1-.9-2-2-2z" fill="#1a8fc4" stroke="#1a8fc4"/>
                            <path d="M14 7V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h2" stroke="#393E46"/>
                            <circle cx="16.5" cy="15.5" r="2.5" fill="white"/>
                        </svg>
                    </div>
                    <div class="gplt-brand-text">
                        <h1>Plugin Authentication</h1>
                        <p>Activate your plugin license</p>
                    </div>
                </div>
            </div>

            <div class="gplt-flex-layout">
                <div class="gplt-login-box">
                    <div class="gplt-card">
                        <div class="gplt-card-content">
                            <form method="post" action="" class="gplt-login-form">
                                <?php
                                    settings_fields('gpl_options_group');
do_settings_sections('gpltimes_plugin');
$gplstatus = get_option('gplstatus');
$is_activated = !empty($gplstatus);

// Add nonce fields for security
wp_nonce_field('gpltimes_activation_nonce', 'gpltimes_activation_nonce');
wp_nonce_field('gpltimes_deactivation_nonce', 'gpltimes_deactivation_nonce');
?>

                                <div class="gplt-button-group">
                                    <button type="submit" name="submit" id="activate-button" class="gplt-button gplt-button-activate" <?php disabled($is_activated); ?>>
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="18" height="18" class="gplt-button-icon">
                                            <path fill="currentColor" d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/>
                                        </svg>
                                        Activate
                                    </button>

                                    <button type="submit" name="deactivate_plugin" id="deactivate_plugin" class="gplt-button gplt-button-deactivate" <?php disabled(!$is_activated); ?>>
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="18" height="18" class="gplt-button-icon">
                                            <path fill="currentColor" d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                                        </svg>
                                        Deactivate
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <?php if ($is_activated): ?>
                    <div class="gplt-status-card">
                        <div class="gplt-status-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                <polyline points="22 4 12 14.01 9 11.01"></polyline>
                            </svg>
                        </div>
                        <div class="gplt-status-text">
                            <span>Status: <strong>Activated</strong></span>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="gplt-info-card">
                        <div class="gplt-info-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="16" x2="12" y2="12"></line>
                                <line x1="12" y1="8" x2="12.01" y2="8"></line>
                            </svg>
                        </div>
                        <div class="gplt-info-text">
                            <span>Status: <strong>Not Activated</strong></span>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.gplt-main-container {
    margin: 20px 20px 20px 0;
    max-width: 1800px;
}

.gplt-wrap {
    width: 100%;
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    overflow: hidden;
}

.gplt-login-container {
    padding: 40px;
}

.gplt-login-header {
    margin-bottom: 30px;
}

.gplt-flex-layout {
    display: block;
}

.gplt-login-box {
    width: 100%;
    max-width: 700px;
    min-width: auto;
    margin: 0;
}

.gplt-brand {
    display: flex;
    align-items: center;
    gap: 20px;
    margin-bottom: 30px;
}

.gplt-logo-wrapper {
    background: #e6f4fa;
    padding: 16px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 4px rgba(26, 143, 196, 0.1);
    transition: all 0.3s ease;
}

.gplt-logo-wrapper:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 8px rgba(26, 143, 196, 0.2);
}

.gplt-brand-text h1 {
    margin: 0;
    color: #393E46;
    font-size: 28px;
    font-weight: 600;
    line-height: 1.2;
}

.gplt-brand-text p {
    margin: 5px 0 0;
    color: #6c757d;
    font-size: 16px;
}

.gplt-card {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    margin-bottom: 24px;
    transition: all 0.3s ease;
    border: 1px solid #f0f0f0;
    overflow: hidden;
}

.gplt-card:hover {
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
    transform: translateY(-2px);
}

.gplt-card-content {
    padding: 24px;
}

.gplt-login-form .form-table {
    margin: 0;
    background: #f8f9fa;
    border-radius: 8px;
    padding: 20px 20px 10px 20px;
    width: 100%;
}

.gplt-login-form .form-table,
.gplt-login-form .form-table tbody,
.gplt-login-form .form-table tr {
    display: block;
    width: 100%;
}

.gplt-login-form .form-table td {
    display: block;
    padding: 5px;
    max-width: 100%;
}

.gplt-login-form .form-table th {
    display: none;
}

.gplt-input-group {
    position: relative;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 15px;
}

.gplt-input-group .gplt-input-icon {
    width: 24px;
    height: 24px;
    padding: 12px;
    background: #e6f4fa;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(26, 143, 196, 0.1);
    transition: all 0.2s ease;
}

.gplt-input-group:hover .gplt-input-icon {
    transform: scale(1.05);
    box-shadow: 0 4px 8px rgba(26, 143, 196, 0.15);
}

.gplt-input {
    width: 90%;
    padding: 0 20px !important;
    height: 48px;
    border: 2px solid #e1e5eb !important;
    border-radius: 10px !important;
    transition: all 0.3s ease;
    font-size: 15px;
    color: #393E46 !important;
    background: #fff;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02) !important;
}

.gplt-input:focus {
    outline: none;
    border-color: #1a8fc4 !important;
    box-shadow: 0 0 0 3px rgba(26, 143, 196, 0.1) !important;
}

.gplt-input::placeholder {
    color: #9ca3af;
}

.gplt-input:disabled {
    background-color: #f3f4f6;
    cursor: not-allowed;
    border-color: #d1d5db;
}

.gplt-login-info {
    text-align: left;
    color: #4B5563;
    font-size: 15px;
    margin-bottom: 20px;
    font-weight: 400;
    line-height: 1.5;
    opacity: 0.9;
    padding: 0 2px;
}

.gplt-button-group {
    display: flex;
    gap: 12px;
    margin-top: 23px;
}

.gplt-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 14px 28px;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    font-size: 15px;
    cursor: pointer;
    transition: all 0.3s ease;
    min-width: 140px;
}

.gplt-button-activate {
    background: #1a8fc4;
    color: white;
}

.gplt-button-activate:hover {
    background: #1a8fc4;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(26, 143, 196, 0.2);
}

.gplt-button-deactivate {
    background: #393E46;
    color: white;
}

.gplt-button-deactivate:hover {
    background: #2d3238;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(57, 62, 70, 0.2);
}

.gplt-button:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
}

.gplt-status-card {
    display: flex;
    align-items: center;
    gap: 12px;
    background: #e6f9ee;
    padding: 12px 18px;
    border-radius: 8px;
    margin-top: 15px;
    box-shadow: 0 2px 4px rgba(21, 128, 61, 0.1);
    transition: all 0.3s ease;
    max-width: 700px;
    border-left: 4px solid #15803d;
}

.gplt-status-icon {
    background: #15803d;
    width: 32px;
    height: 32px;
    min-width: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.gplt-status-icon svg {
    color: white;
}

.gplt-status-text {
    color: #15803d;
    font-size: 15px;
}

.gplt-status-text strong {
    font-weight: 600;
}

.gplt-info-card {
    display: flex;
    align-items: center;
    gap: 12px;
    background: #f0f9ff;
    padding: 12px 18px;
    border-radius: 8px;
    margin-top: 15px;
    box-shadow: 0 2px 4px rgba(14, 165, 233, 0.1);
    max-width: 700px;
    border-left: 4px solid #0ea5e9;
}

.gplt-info-icon {
    background: #0ea5e9;
    width: 32px;
    height: 32px;
    min-width: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.gplt-info-icon svg {
    color: white;
}

.gplt-info-text {
    color: #0369a1;
    font-size: 15px;
}

.gplt-info-text strong {
    font-weight: 600;
}

/* Toast notification styles */
.gpltimes-toast {
    position: fixed;
    top: 80px;
    right: 30px;
    z-index: 9999;
    display: none;
}

.gpltimes-toast.show {
    display: block;
    animation: slideIn 0.3s ease forwards;
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
    min-width: 300px;
    max-width: 450px;
}

.gpltimes-toast-icon {
    flex-shrink: 0;
}

#gpltimes-toast-message {
    font-size: 14px;
    font-weight: 500;
}

@keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@keyframes slideOut {
    from {
        transform: translateX(0);
        opacity: 1;
    }
    to {
        transform: translateX(100%);
        opacity: 0;
    }
}

.gpltimes-toast.hide {
    animation: slideOut 0.3s ease forwards;
}

.gplt-tooltip .gplt-tooltiptext {
    visibility: hidden;
    width: 250px;
    background-color: #555;
    color: #fff;
    text-align: center;
    border-radius: 6px;
    padding: 8px;
    position: absolute;
    z-index: 1;
    bottom: 125%;
    left: 50%;
    margin-left: -125px;
    opacity: 0;
    transition: opacity 0.3s;
    font-size: 12px;
    font-weight: normal;
}

.gplt-tooltip .gplt-tooltiptext::after {
    content: "";
    position: absolute;
    top: 100%;
    left: 50%;
    margin-left: -5px;
    border-width: 5px;
    border-style: solid;
    border-color: #555 transparent transparent transparent;
}

.gplt-tooltip:hover .gplt-tooltiptext {
    visibility: visible;
    opacity: 1;
}

.gplt-save-status {
    margin-left: 12px;
}

.gplt-save-status.show {
    display: inline-block !important;
}

@media (max-width: 1200px) {
    .gplt-flex-layout {
        flex-direction: column;
    }

    .gplt-login-box {
        max-width: 100%;
        min-width: auto;
    }

    .gplt-sidebar {
        display: none; /* Hide the sidebar on smaller screens */
    }
}

@media (max-width: 768px) {
    .gplt-login-container {
        padding: 20px;
    }

    .gplt-brand {
        flex-direction: column;
        text-align: center;
        gap: 16px;
    }

    .gplt-button-group {
        flex-direction: column;
    }

    .gpltimes-toast {
        top: 20px;
        right: 20px;
        left: 20px;
    }

    .gpltimes-toast-content {
        min-width: auto;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Function to show toast notification
    window.showToast = function(message, type = 'success') {
        var toast = document.getElementById('gpltimes-toast');
        var toastMessage = document.getElementById('gpltimes-toast-message');
        var toastIcon = toast.querySelector('.gpltimes-toast-icon');

        // Set message
        toastMessage.textContent = message;

        // Set color and icon based on type
        var bgColor = '#4CAF50'; // Default success color
        var iconPath = '';

        if (type === 'error') {
            bgColor = '#f44336';
            iconPath = '<path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"></path>';
        } else if (type === 'warning') {
            bgColor = '#ff9800';
            iconPath = '<path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"></path>';
        } else if (type === 'info') {
            bgColor = '#2196F3';
            iconPath = '<path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"></path>';
        } else {
            iconPath = '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline>';
        }

        // Update the icon
        toastIcon.innerHTML = iconPath;

        // Set background color
        toast.querySelector('.gpltimes-toast-content').style.backgroundColor = bgColor;

        // Show toast
        toast.classList.remove('hide');
        toast.classList.add('show');

        // Hide toast after 3 seconds
        setTimeout(function() {
            toast.classList.remove('show');
            toast.classList.add('hide');

            // After the animation completes, remove the hide class
            setTimeout(function() {
                toast.classList.remove('hide');
            }, 300);
        }, 3000);
    }

    // Check for URL parameters to show toast messages
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('activation') && urlParams.get('activation') === 'success') {
        showToast('Plugin activated successfully!', 'success');
    } else if (urlParams.has('deactivation') && urlParams.get('deactivation') === 'success') {
        showToast('Plugin deactivated successfully!', 'info');
    } else if (urlParams.has('error')) {
        showToast('Error: ' + urlParams.get('error'), 'error');
    }
});
</script>