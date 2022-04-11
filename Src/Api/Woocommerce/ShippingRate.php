<?php

/**
 * @package smr plugin
 */

namespace Src\Api\Woocommerce;

use Src\Base\BaseController;

class ShippingRate extends BaseController {
    public function register() {
        add_filter('woocommerce_package_rates', [$this, 'woocommercePackageRates'], 10, 1);

        /**
         * disables woocommerce_package_rates cache.
         * so now shipping rate refresh on page refresh. (It can be removed)
         * issue link: https://github.com/woocommerce/woocommerce/issues/22100#issuecomment-701407678
         */
        add_filter('transient_shipping-transient-version', function($value, $name) { return false; }, 10, 2);
    }
    
    function woocommercePackageRates($rates) {
        $values = get_option('smr_config_option', []);
        $checkout = $values['checkout'];

        $freeShippingCities = isset($checkout['free_ship_cities']) ? $checkout['free_ship_cities'] : '';

        $freeShipCities = explode(",", trim($freeShippingCities));
        $userShipCity = WC()->customer ? WC()->customer->get_shipping_city() : [];
        
        //* set shipping rate to zero
        if(in_array($userShipCity, $freeShipCities)) {
            foreach($rates as $key => $rate ) {
                $rates[$key]->cost = 0;
                $rates[$key]->label = __('Free Shipping','smr-plugin'); //* change shipping label
            }
        }
        return $rates;
    }
}
