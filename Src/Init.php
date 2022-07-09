<?php
/**
 * @package smr plugin
 */

namespace Src;

use \Src\Pages;
use \Src\Base;
use \Src\Elements;
use \Src\Api;
use \Src\Api\Woocommerce;

/**
 *
 */

class Init {
    public static function getService() {
        return [
            Pages\Admin::class,
            Pages\BulkActions::class,
            Pages\BulkActionsAjax::class,
            Base\Enqueue::class,
            Base\SettingLinks::class,
            Elements\StickyButton::class,
            Woocommerce\WholesaleSellLimit::class,
            Woocommerce\ExtraCheckoutFields::class,
            Woocommerce\ExtraRegistrationFields::class,
            Woocommerce\ShippingRate::class,
            Woocommerce\ConditionalCOD::class,
            Woocommerce\ProductCartInvoice::class,
            Api\SmsContactFormAjax::class,
            Api\UserActions::class,
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