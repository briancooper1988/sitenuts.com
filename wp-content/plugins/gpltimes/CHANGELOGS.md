# Changelog

## v4.0.14
### Enhancements
* Updated brand colors from pink/purple to blue color scheme
* Changed main color from #BC5994 to #1a8fc4
* Updated RGB/RGBA color values from 188, 89, 148 to 26, 143, 196
* Updated hover color for buttons from #a94d82 to #1a8fc4
* Changed soft pink backgrounds to light blue equivalents

## v4.0.13
### UI Improvements
* Removed feature cards from the dashboard for a cleaner interface
* Moved the status card below the login box for better workflow
* Made the status card more compact and modern with a simplified status display
* Added colored border accents to status cards for better visual cues
* Refined the typography and spacing in status indicators
* Set max-width of 700px for the login box and status cards
* Optimized icon sizes and spacing for a more professional look

## v4.0.12
### Enhancements
* Redesigned the main dashboard layout with optimized 65/35 split between login box and sidebar
* Removed redundant feature cards to create a more focused UI
* Enhanced feature cards with improved typography and larger icons
* Added responsive behavior that hides sidebar on narrow screens
* Standardized icon sizes across all sidebar elements for visual consistency
* Improved typography with optimized font sizes and weights
* Added minimum width to the login section for better display on various screen sizes
* Fine-tuned spacing and padding throughout the dashboard
* Relocated "Hide whitelabel settings page?" option from Settings page back to Whitelabel Settings page for better organization
* Enhanced whitelabel settings UI with streamlined "Hide whitelabel settings page" option directly on the page
* Improved visual design of whitelabel settings with border accents and eye-slash icon

## v4.0.11
### Major Changes
* Restructured the settings pages for improved usability and organization
* Renamed "Export & Import Settings" page to "Settings" for better clarity
* Moved visibility settings from whitelabel settings page to the main settings page
* Combined plugin configuration options into a more logical, centralized interface

### Enhancements
* Improved import settings functionality with disabled button until file is selected
* Enhanced error messages for invalid JSON files during import
* Cleaner URL management after saving settings and importing/exporting
* Fixed issue with duplicate toast notifications after importing settings
* Added URL parameter cleanup to prevent recurring import notifications
* Improved page refresh behavior after saving whitelabel settings
* Modified whitelabel settings page to refresh in-place rather than redirect to dashboard
* Added 0.8 second delay before refresh to allow visibility of success notifications
* Removed outdated plugin reactivation notice from whitelabel settings page
* Added changelog display from CHANGELOGS.md when viewing plugin details for GPLTimes plugin
* Restored "Hide whitelabel settings page" option to the whitelabel settings page
* Added back important reactivation notice for hiding whitelabel settings
* Restored dashboard redirect after saving whitelabel settings

### Fixes
* Fixed nonce mismatch issue that was causing "Security check failed" errors
* Ensured consistent nonce creation and verification across the plugin
* Improved error handling for failed form submissions
* Fixed styling issues with the save changes button in the whitelabel settings page
* Made UI more consistent across all settings pages

## v4.0.10
### Enhancements
* Removed animations from dashboard login page for more consistent user experience
* Updated subtitle text on Whitelabel Settings page to be more descriptive
* Updated subtitle text on Export & Import Settings page for better clarity
* Improved toast notification system consistency across all pages
* Fixed UI consistency issues across the plugin interface

## v4.0.9
### Enhancements
* Added modern toast notifications to the login screen for all conditions
* Added toast notifications to the whitelabel settings page for save and reset actions
* Added toast notifications to the disable updates page when saving changes
* Added toast notifications to the export/import settings page
* Implemented BEM methodology for CSS to prevent styling conflicts
* Created custom CSS variables for easy customization
* Added support for various notification types: success, error, warning, and info
* Improved user experience with animated transitions
* Optimized for WordPress admin environment
* Ensured compatibility with all WordPress versions
* Simplified whitelabel settings form with AJAX-only submission for better UX
* Added quick redirect to dashboard after saving whitelabel settings (800ms delay)
* Converted disable updates page to use AJAX for smoother user experience
* Redesigned dashboard login page with modern UI and improved user experience
* Improved dashboard layout with side-by-side design for better space utilization
* Enhanced beta updates tooltip to only show on hover for cleaner interface
* Adjusted status message colors and visibility for better user feedback
* Removed redundant activation status indicator for cleaner UI
* Optimized feature cards with 2x2 grid layout and simplified content
* Streamlined disable updates page by removing redundant description and selection actions
* Optimized script loading by only enqueueing beta-updates.js on pages where it's needed
* Refined UI spacing with improved padding for status cards and button groups
* Enhanced whitelabel settings page with modern warning note design
* Modernized info box on disable updates page with improved styling and SVG icon
* Modern toast notifications with improved UX
* Optimized script loading for better performance
* Modernized info box on disable updates page
* Completely redesigned whitelabel settings page with improved UI and functionality
* Redesigned disable updates page with custom checkboxes and improved table styling
### Fixes
* Fixed toast notification JavaScript to correctly reference HTML elements
* Resolved "Cannot read properties of null" error when deactivating the plugin
* Fixed toast notification colors to match the notification type (success, error, warning, info)
* Added type-specific icons for different notification types
* Fixed spacing between beta updates label and save notification
* Fixed JavaScript error in beta-updates.js on pages where the element doesn't exist
* Fixed toast notification JavaScript
* Resolved errors related to plugin deactivation
* Ensured correct notification colors and icons

## v4.0.8
* Fixed beta updates checkbox alignment in admin dashboard:
  * Improved HTML structure for better accessibility
  * Enhanced CSS styling for consistent alignment
  * Optimized tooltip display
* Enhanced JavaScript handling for better cache management:
  * Implemented proper versioning for all JavaScript files using plugin version constant
  * Renamed myscript-4.0.js to myscript.js for better maintainability
  * Added nonce verification to all forms and AJAX calls for improved security
  * Fixed potential caching issues with JavaScript files during plugin updates
* Optimized update checking mechanism:
  * Reduced API calls with improved 30-minute transient caching
  * Consolidated duplicate update check functions
  * Added automatic transient recreation if expired
  * Improved integration with WordPress "Check Again" feature
  * Enhanced error handling and logging
  * Added automatic cleanup of old crons and transients during update
  * Removed unused notice-related transients
  * Improved transient management during plugin activation/deactivation
  * Maintained backward compatibility with existing installations
  * Reduced server load while ensuring timely updates
  * Fixed multiple API requests when using WordPress "Check Again" button
  * Fixed login screen deactivation to properly restore WordPress repository updates
  * Fixed PHP errors during deactivation via login screen
  * Added filtered updates transient for improved database performance
* Re-added "Disable All Admin Notices" option to whitelabel settings:
  * Added option to disable all WordPress admin notices universally
  * Updated export/import functionality to handle this option
  * Simplified implementation with improved performance
* Improved settings reset functionality:
  * Added modern toast notification for settings reset confirmation
  * Replaced standard WordPress notice with sleek animated toast
  * Enhanced user experience with automatic dismissal after 3 seconds
* Removed all notice functionality:
  * Removed gpltimes_banners.json integration
  * Removed admin notice display functionality
  * Removed notice-related options from whitelabel settings
  * Removed notice-related transients and cron jobs
  * Removed notice-related options from export/import functionality
  * Simplified plugin codebase
* Code quality improvements:
  * Applied PSR-12 coding standards across all PHP files
  * Fixed indentation and spacing for better readability
  * Standardized brace placement and control structures
  * Added proper visibility declarations to methods
  * Removed trailing whitespace and improved code organization
  * Cleaned up project structure by removing empty directories

## v4.0.7
* Change GET to POST for version check and customer validation (membership/single order)
* POST request token based cache for 30 min.

## v4.0.6
* Fixed "Function _load_textdomain_just_in_time was called incorrectly" error
* Redesigned interface.

## v4.0.5
* CSS changes as per our new color scheme.
* Plugin Icon change to SVG

## v4.0.4
* Fixed a small bug during plugin deactivation.

## v4.0.3
* Fixed theme updates from developer, if disabled via "disable Update" page.

## v4.0.2
* Bug fixes

## v4.0.1
* Bug fixes
* Added banner and icon for plugin version details section.

## v4.0
* Membership Details added in the GPL Times Dashboard Panel.
* Redesigned "View version details" section.

## v3.9.9.6
* Bug fixes.

## v3.9.9.5
* Fresh API request after updater plugin is updated.
* plugin and theme update transient clear on Deactivation.

## v3.9.9.3
* Pre-validations added for external API requests.

## v3.9.9.2
* Color Pallete Change as per GPLTimes website.
* Fixed plugin override by Thim Core / ThimPress plugin/themes.

## v3.9.9.1
* Added "Beta Updates" options in Plugins Dashboard Page, which allows you to enable/disable beta updates of plugins from our store.

## v3.9.9
* Added "Select All" and "Deselect All" option in Disable Updates page.

## v3.9.8
* Transient refresh when saving "Disable Updates" page in order to override some plugin update notices.

## v3.9.7
* Fixed the calculation of plugin and themes PHP path, which is giving error in some host.

## v3.9.6
* Added a fix for WP 6.4.3 Incompatible Archive Error.

## v3.9.5
* Major update - Added automatic updates for themes. Currently only few themes will have access to updates.
* Added Themes in "Disable Updates" page.

## v3.9.2
* Disable updates page improved - plugin name as per the customer site.
* Added a fix for WPMU plugins update override.

## v3.9.0 and 3.9.1
* Code structured and improved. Code clean up.
* Improved "Disable Updates" page.
* Update data array improved in plugupdate, version compare >=.

## v3.8.9
* Added export and import feature to export and import the Whitelabel settings.