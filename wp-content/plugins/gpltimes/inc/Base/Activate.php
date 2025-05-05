<?php

/**
 * @package  Gpltimes
 */

namespace Inc\Base;

class Activate
{
    public static function activate()
    {
        // ob_start();
        $cronpath =  plugin_dir_path(dirname(__FILE__, 2));


        require_once($cronpath.'inc/GplCron/gpl-wp-cron.php');

        // flush_rewrite_rules();

    }
}
