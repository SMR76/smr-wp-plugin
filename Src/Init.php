<?php
/**
 * @package smr plugin
 */

namespace Src;

use \Src\Pages\Admin;
use \Src\Base;
use \Src\Elements;
use \Src\Api\Woocommerce;

/**
 * 
 */

class Init {
    public static function getService() {
        return [
            Admin::class,
            Base\Enqueue::class,
            Base\SettingLinks::class,
            Elements\StikyButton::class,

            Api\Woocommerce\WholesaleSellLimit::class,
            Api\Woocommerce\ExteraCheckoutFields::class,
            Api\Woocommerce\ExteraRegistrationFields::class
        ];
    }

    public static function registerService() {
        foreach( self::getService() as $class) {
            $service = self::createInstatiate($class);
            if(method_exists($service,'register') )
                $service -> register();
        }
    }

    public static function createInstatiate($class) {
        return new $class();
    }
}