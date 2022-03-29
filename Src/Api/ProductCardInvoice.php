<?php
/**
 * @package smr plugin
 */

namespace Src\Api;

use \Src\Base\BaseController;

/**
 * 
 */
class ProductCardInvoice extends BaseController { 
    public function toPDF(string $htmlValue) {
        $current_user = wp_get_current_user();
        $username = esc_html($current_user->user_login);
        $salt = wp_generate_password(5, false);
        // create file name from random value and username. 
        $filename = "$username-$salt";
        // make filename unique.
        $filename = wp_unique_filename( "path", $filename );

        //TODO: convert to pdf.
        $pdfValue = $htmlValue;
        return $pdfValue;
    }

    public function createHTML() {
        global $woocommerce;
        $items = $woocommerce->cart->get_cart();

        $invoiceHTML = "";
    
        foreach($items as $item => $values) { 
            $Product =  wc_get_product($values['data']->get_id());
            $productDetail = wc_get_product($values['product_id']);
            $price = get_post_meta($values['product_id'] , '_price', true);
            $image = $productDetail->get_image();
            $title = $Product->get_title();
            $quantity = $values['quantity']; 

            $reqularPrice = get_post_meta($values['product_id'] , '_regular_price', true);
            $salePrice = get_post_meta($values['product_id'] , '_sale_price', true);
        }

        return "";
    }
} 