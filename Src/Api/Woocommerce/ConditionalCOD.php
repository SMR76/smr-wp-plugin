<?php

/**
 * @package smr plugin
 */

namespace Src\Api\Woocommerce;

use Src\Base\BaseController;

class ConditionalCOD extends BaseController{
    public function register() {
        add_filter( 'woocommerce_available_payment_gateways', [$this,'conditional_available_payment_gateways')];
    }

    function conditional_available_payment_gateways($available_gateways) {
        if (is_admin()) { // only in frontend
            return $available_gateways; 
        }

        $values = get_option('smr_config_option');
        $checkout = $values['checkout'] ?? [];
        $freeCODCities = $checkout['cod_cities'] ?? '';

        $freeCODCitiesArray = explode(",", trim($freeCODCities));
        $userShipCity = WC()->customer ? WC()->customer->get_shipping_city() : '';

        if(in_array($userShipCity, $freeCODCitiesArray) == false && isset($available_gateways['cod'])) {
            unset($available_gateways['cod']);
        }

        return $available_gateways;
    }
}
?>