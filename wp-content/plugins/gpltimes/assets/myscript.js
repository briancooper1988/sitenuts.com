document.addEventListener('DOMContentLoaded', function() {
    const activateButton = document.getElementById('activate-button');
    if (activateButton) {
        activateButton.addEventListener('click', function(event) {
            event.preventDefault();
            
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value.trim();
            const nonce = document.getElementById('gpltimes_activation_nonce').value;

            // Check for empty fields
            if (!username || !password) {
                showToast('Please fill in all fields', 'error');
                return;
            }

            // Update button state
            this.innerHTML = 'Activating...';
            this.disabled = true;
            
            jQuery.post(ajaxurl, {
                action: 'gpltimes_activation',
                username: username,
                password: password,
                nonce: nonce
            }, function(response) {
                if (response.success) {
                    showToast('Activation successful', 'success');
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                } else {
                    showToast('Activation Failed: ' + response.data, 'error');
                    activateButton.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="18" height="18" class="gplt-button-icon"><path fill="currentColor" d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/></svg>Activate';
                    activateButton.disabled = false;
                }
            }).fail(function() {
                showToast('Connection error. Please try again.', 'error');
                activateButton.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="18" height="18" class="gplt-button-icon"><path fill="currentColor" d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/></svg>Activate';
                activateButton.disabled = false;
            });
        });
    }
});

document.addEventListener('DOMContentLoaded', function() {
    const deactivateButton = document.getElementById('deactivate_plugin');
    if (deactivateButton) {
        deactivateButton.addEventListener('click', function(event) {
            event.preventDefault();
            
            const nonce = document.getElementById('gpltimes_deactivation_nonce').value;
            
            // Update button state
            this.innerHTML = 'Deactivating...';
            this.disabled = true;

            jQuery.post(ajaxurl, {
                action: 'gpltimes_deactivation',
                nonce: nonce
            }, function(response) {
                if (response.success) {
                    showToast('Deactivation successful', 'success');
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                } else {
                    showToast('Deactivation Failed: ' + response.data, 'error');
                    deactivateButton.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="18" height="18" class="gplt-button-icon"><path fill="currentColor" d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>Deactivate';
                    deactivateButton.disabled = false;
                }
            }).fail(function() {
                showToast('Connection error. Please try again.', 'error');
                deactivateButton.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="18" height="18" class="gplt-button-icon"><path fill="currentColor" d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>Deactivate';
                deactivateButton.disabled = false;
            });
        });
    }
});

// Function to show toast notification
function showToast(message, type = 'success') {
    var toast = document.getElementById('gpltimes-toast');
    var toastMessage = document.getElementById('gpltimes-toast-message');
    var toastContent = toast.querySelector('.gpltimes-toast-content');
    var toastIcon = toast.querySelector('.gpltimes-toast-icon');
    
    // Ensure the toast is visible in the DOM
    toast.style.display = 'block';
    
    // Set message
    toastMessage.textContent = message;
    
    // Reset classes - remove all type-specific classes
    toastContent.className = 'gpltimes-toast-content';
    
    // Create a style element to override the inline styles with !important
    var styleElement = document.createElement('style');
    document.head.appendChild(styleElement);
    var styleSheet = styleElement.sheet;
    
    // Set color and icon based on type
    var bgColor = '#4CAF50'; // Default success color
    
    if (type === 'error') {
        bgColor = '#f44336';
        // Error icon
        toastIcon.innerHTML = '<path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"></path>';
    } else if (type === 'warning') {
        bgColor = '#ff9800';
        // Warning icon
        toastIcon.innerHTML = '<path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"></path>';
    } else if (type === 'info') {
        bgColor = '#2196F3';
        // Info icon
        toastIcon.innerHTML = '<path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"></path>';
    } else {
        // Success icon (default)
        toastIcon.innerHTML = '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline>';
    }
    
    // Add a rule with !important to override the inline style
    var toastId = 'toast-' + Date.now();
    toastContent.id = toastId;
    styleSheet.insertRule('#' + toastId + ' { background-color: ' + bgColor + ' !important; }', 0);
    
    // Force a reflow to ensure CSS transitions work properly
    void toast.offsetWidth;
    
    // Show toast
    toast.classList.add('show');
    
    // Hide toast after 4 seconds
    setTimeout(function() {
        toast.classList.remove('show');
        
        // After the transition completes, reset display and remove the style element
        setTimeout(function() {
            toast.style.display = '';
            document.head.removeChild(styleElement);
        }, 300);
    }, 4000);
}