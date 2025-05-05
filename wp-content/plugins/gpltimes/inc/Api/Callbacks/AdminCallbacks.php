<?php

/**
 * @package  Gpltimes
 */

namespace Inc\Api\Callbacks;

use Inc\Base\BaseController;
use Inc\Plugupdate\Plugbasic;

class AdminCallbacks extends BaseController
{
    public function adminDashboard()
    {
        return require_once("$this->plugin_path/templates/admin.php");
    }


    public function disableupdate()
    {
        return require_once("$this->plugin_path/templates/disableupdate.php");
    }

    public function gplOptionsGroup($input)
    {
        return $input;
    }

    public function gplAdminSection()
    {
        $whitelabel_settings = get_option('gpltimes_whitelabel_settings', array());
        $plugin_name = isset($whitelabel_settings['name']) && !empty($whitelabel_settings['name']) ? $whitelabel_settings['name'] : 'GPL Times';
        echo "<div class=\"gplt-login-info\">Enter your " . $plugin_name . " username/email and password</div>";
    }

    public function gpltimesusername()
    {
        $value = esc_attr(get_option('username'));
        $gplstatus = get_option('gplstatus');
        if (!empty($gplstatus)) {
            echo '<div class="gplt-input-group">';
            echo '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" class="gplt-input-icon"><path fill="#1a8fc4" d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>';
            echo '<input type="text" class="gplt-input" name="username" id="username" value="********" placeholder="Plugin is activated" disabled>';
            echo '</div>';
        } else {
            echo '<div class="gplt-input-group">';
            echo '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" class="gplt-input-icon"><path fill="#1a8fc4" d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>';
            echo '<input type="text" class="gplt-input" id="username" name="username" value="' . $value . '" placeholder="Username/Email">';
            echo '</div>';
        }
    }

    public function gpltimespassword()
    {
        $value = esc_attr(get_option('password'));
        $gplstatus = get_option('gplstatus');
        if (!empty($gplstatus)) {
            echo '<div class="gplt-input-group">';
            echo '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" class="gplt-input-icon"><path fill="#1a8fc4" d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/></svg>';
            echo '<input type="text" class="gplt-input" name="password" id="password" value="********" placeholder="Plugin is activated" disabled>';
            echo '</div>';
        } else {
            echo '<div class="gplt-input-group">';
            echo '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" class="gplt-input-icon"><path fill="#1a8fc4" d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/></svg>';
            echo '<input type="password" class="gplt-input" name="password" id="password" value="' . $value . '" placeholder="Password">';
            echo '</div>';
        }
    }

    public function betaUpdatesField($args)
    {
        $option = get_option('gpltimes_beta_updates', false);
        $checked = isset($option) && $option === '1' ? 'checked' : '';

        echo '<div class="gplt-beta-updates-wrap">';
        echo '<div class="gplt-checkbox-group">';
        echo '<input type="checkbox" id="' . $args['label_for'] . '" name="gpltimes_beta_updates" value="1" ' . $checked . '>';
        echo '<label for="' . $args['label_for'] . '" class="gplt-tooltip">Enable beta updates';
        echo '<span class="gplt-tooltiptext">Enabling this option will allow you to receive beta version updates for products from our store.</span>';
        echo '</label>';
        echo '<span id="beta_updates_status" class="gplt-save-status" style="display: none; color: #4CAF50; margin-left: 12px;">Saved!</span>';
        echo '</div>';
        echo '</div>';

        // Add inline styles for tooltip and save status
        echo '<style>
            .gplt-tooltip {
                position: relative;
                display: inline-block;
                cursor: pointer;
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

            .gplt-save-status.show {
                display: inline-block !important;
            }
        </style>';
    }

    public function gplsubscriptionstatus()
    {
        $gplstatus = get_option('gplstatus');
        if (!empty($gplstatus)) {
            echo '<div class="gplt-status-active">Activated</div>';
        } else {
            echo '<div class="gplt-status-inactive">Not Active</div>';
        }
    }


    public function gplsubscription()
    {
        $tokengpltime = '';

        $username = get_option('username');
        $password = get_option('password');

        if (!empty($username) && !empty($password)) {
            $main_url = 'https://www.gpltimes.com/wp-json/jwt-auth/v1/token';
            $received_values = array(
                'username' => get_option('username'),
                'password' => get_option('password')
            );
            $options = array('timeout' => 20, 'body' => $received_values);
            $return_request = wp_safe_remote_post($main_url, $options);

            if (!empty($return_request)) {
                $retuen_response_code = $return_request['response']['code'];
                if ($retuen_response_code == 200) {
                    $response = wp_remote_retrieve_body($return_request);
                    $response_decode = json_decode($response);
                    $tokengpltime = $response_decode->token;
                    $gpltokenid = $response_decode->id;
                }
            }
        }

        if ($tokengpltime != '') {
            echo '<div class="gpl-status-active">Activated</div>';
            update_option('gplstatus', $tokengpltime);
            update_option('gpltokenid', $gpltokenid);

            $alll_test = new Plugbasic();
            $pathget = plugin_dir_path(dirname(__FILE__, 6));

            require_once($pathget.'wp-admin/includes/plugin.php');

            $slugarray = [];
            $slugdetails = [];
            $returnslugarray = [];
            $all_plugins = get_plugins();

            $gpluncheckdata = get_option('gpluncheckdata');
            foreach ($all_plugins as $key => $value) {
                $plugslug = $key;

                if (!empty($gpluncheckdata)) {
                    if (!in_array($plugslug, $gpluncheckdata)) {
                        $plugversion = $value['Version'];
                        $meta_value = $plugslug.'|'.$plugversion;
                        array_push($slugarray, $meta_value);
                        array_push($slugdetails, $plugslug);
                    }
                } else {
                    $plugversion = $value['Version'];
                    $meta_value = $plugslug.'|'.$plugversion;
                    array_push($slugarray, $meta_value);
                    array_push($slugdetails, $plugslug);
                }
            }
            $domain = get_site_url();
            $getinfo = get_option('admin_email');
            $token = esc_attr(get_option('gplstatus'));
            $gpltokenidmain = esc_attr(get_option('gpltokenid'));
            $out = implode(",", $slugarray);
            $t = 1;
            $out_final = $out.'@__@'.$token.'@__@'.$t.'@__@'.$domain.'@__@'.$getinfo.'@__@'.$gpltokenidmain;
            $out_encode = base64_encode($out_final);
        } else {
            echo '<div class="gpl-status-inactive">Not Active</div>';
            update_option('gplstatus', '');
        }
    }

}
