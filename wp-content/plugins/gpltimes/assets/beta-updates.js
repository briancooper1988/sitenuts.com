document.addEventListener('DOMContentLoaded', function() {
    console.log('Beta updates script loaded');
    
    // Make sure ajaxurl is defined
    if (typeof ajaxurl === 'undefined' && typeof betaUpdatesData !== 'undefined') {
        var ajaxurl = betaUpdatesData.ajaxurl;
    }
    
    // Get the beta updates toggle
    const betaUpdatesToggle = document.getElementById('beta_updates');
    
    // Only proceed if the element exists on this page
    if (betaUpdatesToggle) {
        console.log('Beta updates toggle element found');
        
        // Add event listener for change
        betaUpdatesToggle.addEventListener('change', function() {
            const isChecked = this.checked ? '1' : '0';
            
            // Save the original state in case we need to revert
            const originalState = this.checked;
            
            // Disable the toggle while processing
            this.disabled = true;
            
            // Show status indicator if it exists
            const statusElement = document.getElementById('beta_updates_status');
            if (statusElement) {
                statusElement.textContent = 'Saving...';
                statusElement.style.display = 'inline-block';
                statusElement.classList.add('show');
            }
            
            // Send AJAX request
            jQuery.post(ajaxurl, {
                action: 'gpltimes_save_beta_updates',
                beta_updates: isChecked,
                nonce: betaUpdatesData.nonce
            }, function(response) {
                // Re-enable the toggle
                betaUpdatesToggle.disabled = false;
                
                if (response.success) {
                    // Show success status
                    if (statusElement) {
                        statusElement.textContent = 'Saved!';
                        statusElement.style.color = '#4CAF50';
                        setTimeout(function() {
                            statusElement.style.display = 'none';
                            statusElement.classList.remove('show');
                        }, 3000);
                    }
                } else {
                    // Error - revert the toggle to its original state
                    betaUpdatesToggle.checked = originalState;
                    
                    // Show error status
                    if (statusElement) {
                        statusElement.textContent = 'Failed!';
                        statusElement.style.color = '#e53e3e';
                        setTimeout(function() {
                            statusElement.style.display = 'none';
                            statusElement.classList.remove('show');
                        }, 3000);
                    }
                }
            }).fail(function() {
                // Re-enable the toggle
                betaUpdatesToggle.disabled = false;
                
                // Revert to original state
                betaUpdatesToggle.checked = originalState;
                
                // Show error status
                if (statusElement) {
                    statusElement.textContent = 'Error!';
                    statusElement.style.color = '#e53e3e';
                    setTimeout(function() {
                        statusElement.style.display = 'none';
                        statusElement.classList.remove('show');
                    }, 3000);
                }
            });
        });
    }
    // No need to log an error if the element doesn't exist on this page
});