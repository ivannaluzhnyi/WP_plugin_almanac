<?php

class Support
{
    /**
     * Test PHP and WordPress
     *
     * @param string 
     * @return boolean - is the existing version of the system supported?
     */
    public static function supportedVersion($system)
    {
        if ($supported = wp_cache_get($system, 'tribe_version_test')) {
            return $supported;
        } else {
            switch (strtolower($system)) {
                case 'wordpress':
                    $supported = version_compare(get_bloginfo('version'), '3.0', '>=');
                    break;
                case 'php':
                    $supported = version_compare(phpversion(), '5.2', '>=');
                    break;
            }
            $supported = apply_filters('tribe_events_supported_version', $supported, $system);
            wp_cache_set($system, $supported, 'tribe_version_test');
            return $supported;
        }
    }
}
