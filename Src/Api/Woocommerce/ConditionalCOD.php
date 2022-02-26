<?php

/**
 * @package smr plugin
 */

namespace Src\Api\Woocommerce;

use Src\Base\BaseController;

class ConditionalCOD extends BaseController{
    public function register() {
        add_filter( 'woocommerce_available_payment_gateways', array($this,'conditional_available_payment_gateways'));
    }

    function conditional_available_payment_gateways($available_gateways) {
        if (is_admin()) {
            return $available_gateways; // ignore if user is admin
        }

        $values = get_option('smr_settings_option_group');
        $freeCODCities = isset($values['cod_cities']) ? $values['cod_cities'] : '';

        $freeCODCitiesArray = explode(",", trim($freeCODCities));
        $userShipCity = WC()->customer ? WC()->customer->get_shipping_city() : '';

        if(in_array($userShipCity, $freeCODCitiesArray) == false && isset($available_gateways['cod'])) {
            unset($available_gateways['cod']);
        }

        return $available_gateways;
    }
}
?>