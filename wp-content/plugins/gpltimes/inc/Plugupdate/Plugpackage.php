<?php

/**
 * @package Gpltimes
 */

namespace Inc\Plugupdate;

class Plugpackage
{
    private $type; // 'plugin' or 'theme'

    public function __construct($value)
    {
        $this->type = $this->determineItemType($value);

        $option_name = $this->type === 'theme' ? 'gpltheme_slugdetails' : 'gplslugdetails';
        $all_package = get_option($option_name, []);

        if (!in_array($value, $all_package)) {
            $all_package[] = $value;
            update_option($option_name, $all_package);
        }
    }

    private function determineItemType($slug)
    {
        // Use slug format to determine the item type
        if (strpos($slug, '/') !== false && substr($slug, -4) === '.php') {
            return 'plugin';
        } else {
            return 'theme';
        }
    }
}
