<?php

/**
 * @package  Gpltimes
 */

namespace Inc\Base;

use Inc\Base\BaseController;

class RefreshUpdate extends BaseController
{
    public function register()
    {
        add_filter("plugin_action_links_$this->plugin", array( $this, 'update_link' ));
    }

    public function update_link($links)
    {
        $update_link = '<a href="admin.php?page=disable_update_check">Check Update</a>';
        array_push($links, $update_link);
        return $links;
    }
}
