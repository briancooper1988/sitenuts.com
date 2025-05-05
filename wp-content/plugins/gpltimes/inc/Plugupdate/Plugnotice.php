<?php
/**
 * @package  Gpltimes
 */

namespace Inc\Plugupdate;

class Plugnotice
{
    public function __construct()
    {
        add_action('admin_notices', array($this, 'show_admin_notice'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_notice_styles'));
    }

    public function enqueue_notice_styles()
    {
        $status = get_option('gplstatus');
        if (is_null($status) || empty($status)) {
            wp_add_inline_style('admin-bar', $this->get_notice_styles());
        }
    }

    private function get_notice_styles()
    {
        return "
            .gplt-admin-notice {
                position: relative;
                background: #fff;
                border: none;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
                margin: 20px 20px 20px 2px;
                border-radius: 8px;
                padding: 20px;
                border-left: 4px solid #dc2626;
                display: flex;
                align-items: center;
                gap: 15px;
            }

            .gplt-notice-icon {
                flex-shrink: 0;
                width: 24px;
                height: 24px;
                background: #fee2e2;
                border-radius: 6px;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 12px;
            }

            .gplt-notice-content {
                flex-grow: 1;
            }

            .gplt-notice-message {
                margin: 0 0 12px 0;
                color: #393E46;
                font-size: 14px;
                line-height: 1.5;
            }

            .gplt-notice-button {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                background: #1a8fc4;
                color: white;
                padding: 8px 16px;
                border-radius: 6px;
                text-decoration: none;
                font-size: 13px;
                font-weight: 500;
                border: none;
                cursor: pointer;
                transition: all 0.2s ease;
                box-shadow: 0 2px 4px rgba(26, 143, 196, 0.1);
            }

            .gplt-notice-button:hover {
                background: #1a8fc4;
                color: white;
                transform: translateY(-1px);
            }

            .gplt-notice-button:focus {
                outline: none;
                box-shadow: 0 0 0 3px rgba(26, 143, 196, 0.1);
            }
        ";
    }

    public function show_admin_notice()
    {
        $status = get_option('gplstatus');
        if (is_null($status) || empty($status)) {
            $pluginpageurl = admin_url('admin.php?page=gpltimes_plugin');
            ?>
            <div class="gplt-admin-notice">
                <div class="gplt-notice-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="#dc2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="12" y1="8" x2="12" y2="12"/>
                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                </div>
                <div class="gplt-notice-content">
                    <p class="gplt-notice-message">Please login to the Dashboard to activate automatic updates.</p>
                    <a href="<?php echo esc_url($pluginpageurl); ?>" class="gplt-notice-button">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                            <polyline points="10 17 15 12 10 7"/>
                            <line x1="15" y1="12" x2="3" y2="12"/>
                        </svg>
                        Go to Dashboard
                    </a>
                </div>
            </div>
            <?php
        }
    }
}
