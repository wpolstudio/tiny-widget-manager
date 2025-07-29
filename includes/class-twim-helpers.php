<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

use Detection\MobileDetect;

TWIMH::get_instance();

/**
 * TWIM Helpers
 */
class TWIMH
{

    /* Returns class instance (singleton method) */
    private static $instance = null;
    /**
     * get_instance
     *
     * @return TWIMH
     */
    public static function get_instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private static $ismobile;
    private static $istablet;

    /* private constructor ensures that the class can only be */
    /* created using the get_instance static function */
    private function __construct()
    {
        $detect = new MobileDetect();
        self::$ismobile = $detect->isMobile();
        self::$istablet = $detect->isTablet();
    }


    /**
     * is_mobile
     *
     * @return void
     */
    public static function is_mobile()
    {
        // return true;
        return self::$ismobile;
    }

    /**
     * is_tablet
     *
     * @return void
     */
    public static function is_tablet()
    {
        return self::$istablet;
    }

}
