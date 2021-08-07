<?php
/**
 * @package smr plugin
 */

namespace Src\Api\Woocommerce;

use Src\Base\BaseController;

class WholesaleSellLimit extends BaseController {
    
    public function register() {

        $options = get_option('smr_settings_option_group');
        if(isset($options['activate_wholesale']) == false)
            return;

        /**
         * register quantity actions
         */
        add_action('woocommerce_product_options_pricing',      array($this,'woocommerceProductOptionsPricing'));
        add_action('woocommerce_admin_process_product_object', array($this,'WSSaveProductQuantitySettings'));
        add_filter('woocommerce_quantity_input_args',          array($this,'filterWholesaleQuantityInputArgs'), 30, 2);
        add_filter('woocommerce_loop_add_to_cart_args',        array($this,'AjaxAddtoCartWholesaleQuantity'), 30, 2);
        add_filter('woocommerce_available_variation',          array($this,'filterWSAvailableVariationPrice'), 30, 3);

        /**
         * register product prices
         * thanks to:
         * https://stackoverflow.com/questions/45806249/change-product-prices-via-a-hook-in-woocommerce-3/45807054#45807054
         */

        //* Generating dynamically the product "regular price"
        add_filter('woocommerce_product_get_regular_price',            array($this, 'getRegularPrice') , 30, 2);
        add_filter('woocommerce_product_variation_get_regular_price',  array($this, 'getRegularPrice') , 30, 2); 

        //* Generating dynamically the product "sale price"
        // add_filter('woocommerce_product_get_sale_price',               array($this,'getWholesaleSalePrice') , 10, 2);
        // add_filter('woocommerce_product_variation_get_sale_price',     array($this,'getWholesaleSalePrice') , 10, 2);

        //* change price for simple, grouped and external products
        add_filter('woocommerce_product_get_price',                     array($this, 'getProductPrice'), 30, 2);
        add_filter('woocommerce_variation_prices_price',                array($this, 'woocommerceGetVariationPrices'), 30, 3);

        //* Handling price caching (see explanations at the end)
        add_filter('woocommerce_get_variation_prices_hash',            array($this, 'addPriceMultiplierToVariationPricesHash'), 30, 3);

        //* Displayed formatted regular price + sale price         
        add_filter('woocommerce_get_price_html',                       array($this, 'woocommerceGetPriceHtml'), 30, 2);

        add_filter('woocommerce_is_purchasable',                        array($this, 'woocommerce_cloudways_purchasable'),10,2);
    }
    
    public function getProductPrice( $price, $product) {
        $values = $product->get_meta('smr_ws_limit');        
        if(isset($values['qty_roles']) && $this->userIsValid($values['qty_roles']))
            return (float) $values['qty_new_price'];
        return (float) $price;
    }
    
    public function woocommerceGetVariationPrices( $price, $variation, $product) {
        $values = $product->get_meta('smr_ws_limit');        
        if(isset($values['qty_roles']) && $this->userIsValid($values['qty_roles']))
            return (float) $values['qty_new_price'];            
        return (float) $price;
    }
    
    public function addPriceMultiplierToVariationPricesHash( $price_hash, $product, $for_display) {
        return $price_hash;
    }

    function woocommerce_cloudways_purchasable($value, $product) {
        $cfp = $product->get_meta('smr_call_for_price');
        return ( !empty($cfp) ? false : $value);
    }

    /**
     * add custom fields to price section.
     */
    public function woocommerceProductOptionsPricing() {
        $this->wcQuantityExteraProductField();
        $this->registerCallForPriceFields();
        $this->jqueyScripts();
    }

    /**
     * add fields to price section.
     */
    public function wcQuantityExteraProductField() {
        global $product_object;    
        $values         = $product_object->get_meta('smr_ws_limit');        
        $options        = get_option('smr_settings_option_group');
        $defaultRoles   = isset($options['ws_roles']) ? $options['ws_roles'] : '';

        echo '</div><div class="options_group quantity hide_if_grouped">';
    
        //* check box
        woocommerce_wp_checkbox( array( 
            'id'            => 'qty_args',
            'label'         => __('Quantity settings', 'smr-plugin'),
            'value'         => empty($values) ? 'no' : 'yes',
            'description'   => __('Enable this to show and enable the additional quantity setting fields.', 'smr-plugin')
        ));
    
        if(empty($values))
            echo '<div class="qty-args hidden">';
        else
            echo '<div class="qty-args">';
    
        woocommerce_wp_text_input( array(
                'id'                => 'qty_roles',
                'type'              => 'text',
                'label'             => __('Roles name', 'smr-plugin'),
                'placeholder'       => 'i.e. "Admin,Customer"',
                'desc_tip'          => 'true',
                'description'       => __('Name of roles which must be effected, must be seprated with comma. (i.e. "Admin,Customer")', 'smr-plugin'),
                'custom_attributes' => array( 'pattern'  => '^\w+(\s*,\s*\w+)*$', 'dir' => 'ltr','autocomplete' => 'off'),
                'value'             => isset($values['qty_roles'])? $values['qty_roles'] : $defaultRoles
        ));
        
        //* autocomplete result container.
        echo '<p id="suggestionListContainer" class="form-field" style="height:20px"></p>';

        woocommerce_wp_text_input( array(
                'id'                => 'qty_min',
                'type'              => 'number',
                'label'             => __('Minimum Quantity','smr-plugin'),
                'desc_tip'          => 'true',
                'description'       => __('Set a minimum allowed quantity limit (a number greater than 0).', 'smr-plugin'),
                'custom_attributes' => array( 'step'  => 'any', 'min'   => '0', 'dir' => 'ltr'),
                'value'             => isset($values['qty_min']) && $values['qty_min'] > 0 ? (int) $values['qty_min'] : 0
        ));
    
        woocommerce_wp_text_input( array(
                'id'                => 'qty_max',
                'type'              => 'number',
                'label'             => __('Maximum Quantity','smr-plugin'),
                'desc_tip'          => 'true',
                'description'       => __('Set the maximum allowed quantity limit (a number greater than 0). Value "-1" is unlimited', 'smr-plugin'),
                'custom_attributes' => array( 'step'  => 'any', 'min'   => '-1', 'dir' => 'ltr'),
                'value'             => isset($values['qty_max']) && $values['qty_max'] > 0 ? (int) $values['qty_max'] : -1
        ));
    
        woocommerce_wp_text_input( array(
                'id'                => 'qty_step',
                'type'              => 'number',
                'label'             => __('Quantity step','smr-plugin'),
                'desc_tip'          => 'true',
                'description'       => __('Optional. Set quantity step  (a number greater than 0)', 'smr-plugin'),
                'custom_attributes' => array( 'step'  => 'any', 'min'   => '1', 'dir' => 'ltr'),
                'value'             => isset($values['qty_step']) && $values['qty_step'] > 1 ? (int) $values['qty_step'] : 1
        ));

        woocommerce_wp_text_input( array(
                'id'                => 'qty_new_price',
                'type'              => 'number',
                'label'             => __('New price','smr-plugin'),
                'desc_tip'          => 'true',
                'description'       => __('Optional. Set new product price. (empty means no change)', 'smr-plugin'),
                'custom_attributes' => array( 'step'  => 'any', 'min'   => '0', 'dir' => 'ltr'),
                'value'             => isset($values['qty_new_price']) && !empty($values['qty_new_price']) ? (int) $values['qty_new_price'] : ''
        ));
        echo '</div>';
    }

    function registerCallForPriceFields() {
        global $product_object;
        $callForPriceMeta = $product_object->get_meta('smr_call_for_price');
        // $callForPriceDefault = get_option('smr_call_for_price_txt');

        echo '</div><div class="options_group quantity hide_if_grouped">';
        
        woocommerce_wp_checkbox( array(
                'id'            => 'call_for_price',
                'label'         => __('Call For Price', 'smr-plugin'),
                'value'         => empty($callForPriceMeta) ? 'no' : 'yes',
                'description'   => __('Enable this checkbox to hide and disable addToCart button.', 'smr-plugin')
        ));

        if(empty($callForPriceMeta))
            echo '<div class="cfp_section hidden">';
        else
            echo '<div class="cfp_section">';
    
        woocommerce_wp_text_input( array(
                'id'                => 'call_for_price_txt',
                'type'              => 'text',
                'label'             => __('New Text', 'smr-plugin'),
                'style'             => 'direction: ltr;',
                'description'       => '<a id="resetValue">default</a>',
                'value'             => empty($callForPriceMeta) == false ? $callForPriceMeta : ''
        ));

        echo '</div>';
    }

    /**
     * @method currentUserHas
     * check if weather user has the valid roles or not.
     */
    private function userIsValid(string $validRoles) {
        $user       = wp_get_current_user();
        $validRoles = trim($validRoles);
        $requiredRoles      = explode(',',$validRoles);

        foreach($requiredRoles as $role) {
            if (in_array($role , (array) $user->roles)) {
                return true;
            }
        }        
        return false;
    }

    /**
     * toggle setting fields (admin product pages)
     */
    public function jqueyScripts() {
            global $wp_roles;
            $allRoleNames = $wp_roles->get_names();
            $defaultValue = '<h3 style="color:red;">'.__('call for price: <a dir="ltr" href="tel:+123456789">+123456789</a> (name)', 'smr-plugin')."</h3>";

            //*
            //wp_register_script("simple-autocomplete");
            wp_enqueue_script("simple-autocomplete",$this->pluginUrl.'/assets/js/simple-autocomplete.js',['jquery']);
        ?>
        <script>
            jQuery(function($){
                jQuery('#resetValue').click(function(){
                    $("#call_for_price_txt").val(`<?php echo $defaultValue;?>`);
                });

                jQuery('input#call_for_price').click(function(){
                    if( jQuery(this).is(':checked')) {
                        jQuery('.cfp_section').removeClass('hidden');
                    } else {
                        jQuery('.cfp_section').addClass('hidden');
                    }
                });

                jQuery('input#qty_args').click(function(){
                    if( jQuery(this).is(':checked')) {
                        jQuery('div.qty-args').removeClass('hidden');
                    } else {
                        jQuery('div.qty-args').addClass('hidden');
                    }
                });
            });
            
            jQuery(document).ready(() => {
                let source = ["<?php echo implode('","',array_keys($allRoleNames)) ?>"];
                var autoComplete = new autoCompleter(source,"#suggestionListContainer","#qty_roles");
            });
        </script>
        <?php
    }
    
    /**
     * Save quantity setting fields values.
     */ 
    public function WSSaveProductQuantitySettings( $product) {
        if ( isset($_POST['qty_args'])) {    
            $product->update_meta_data( 'smr_ws_limit', array(
                'qty_roles'     => isset($_POST['qty_roles'])       ? $_POST['qty_roles']              : '',
                'qty_min'       => isset($_POST['qty_min'])         && $_POST['qty_min']  > 0          ? (int) wc_clean($_POST['qty_min']) : 0 ,
                'qty_max'       => isset($_POST['qty_max'])         && $_POST['qty_max']  > 0          ? (int) wc_clean($_POST['qty_max']) : -1,
                'qty_step'      => isset($_POST['qty_step'])        && $_POST['qty_step'] > 1          ? (int) wc_clean($_POST['qty_step']): 1 ,
                'qty_new_price' => isset($_POST['qty_new_price'])   ? (int) $_POST['qty_new_price']    : ''
            ));
        } else {
            $product->update_meta_data( 'smr_ws_limit', array());
        }
        
        if ( isset($_POST['call_for_price'])) {
            $product->update_meta_data('smr_call_for_price', wp_unslash(isset($_POST['call_for_price_txt']) ? $_POST['call_for_price_txt'] : ''));
        } else {            
            $product->update_meta_data('smr_call_for_price', null);
        }
    }
    
    /**
     * The quantity settings in action on front end
     */
    public function filterWholesaleQuantityInputArgs( $args, $product) {

        if ( $product->is_type('variation')) {
            $parent_product = wc_get_product( $product->get_parent_id());
            $values = $parent_product->get_meta( 'smr_ws_limit');
        } else {
            $values = $product->get_meta( 'smr_ws_limit');
        }

        if (empty($values) == false && $this->userIsValid($values['qty_roles'])) {
            //* set min value
            if ( isset( $values['qty_min']) && $values['qty_min'] > 1) {
                $args['min_value'] = $values['qty_min'];    
                if( ! is_cart()) {
                    $args['input_value'] = $values['qty_min']; //* set starting value
                }
            }
    
            //* set max value
            if ( isset( $values['qty_max']) && $values['qty_max'] > 0) {
                $args['max_value'] = $values['qty_max'];    
                if ( $product->managing_stock() && ! $product->backorders_allowed()) {
                    $args['max_value'] = min( $product->get_stock_quantity(), $args['max_value']);
                }
            }
    
            //* set step value
            if ( isset( $values['qty_step']) && $values['qty_step'] > 1) {
                $args['step'] = $values['qty_step'];
            }
        }
        return $args;
    }
    
    /**
     * Ajax add to cart, set "min quantity" as quantity on shop and archives pages.
     */
    public function AjaxAddtoCartWholesaleQuantity( $args, $product) {
        $values  = $product->get_meta( 'smr_ws_limit');
    
        if (empty( $values) == false && $this->userIsValid($values['qty_roles'])) {
            // Min value
            if ( isset( $values['qty_min']) && $values['qty_min'] > 1) {
                $args['quantity'] = $values['qty_min'];
            }
        }
        return $args;
    }
    
    /**
     * The quantity settings in action on front end (For variable productsand their variations).
     */
    public function filterWSAvailableVariationPrice( $data, $product, $variation) {
        $values  = $product->get_meta( 'smr_ws_limit');
    
        if (empty( $values) == false && $this->userIsValid($values['qty_roles'])) {
            if ( isset( $values['qty_min']) && $values['qty_min'] > 1) {
                $data['min_qty'] = $values['qty_min'];
            }
    
            if ( isset( $values['qty_max']) && $values['qty_max'] > 0) {
                $data['max_qty'] = $values['qty_max'];
    
                if ( $variation->managing_stock() && ! $variation->backorders_allowed()) {
                    $data['max_qty'] = min( $variation->get_stock_quantity(), $data['max_qty']);
                }
            }
        }
        return $data;
    }

    /**
     * 
     */
    public function getRegularPrice( $regular_price, $product) {
        if( empty($regular_price) || $regular_price == 0)
            return $product->get_price();
        else
            return $regular_price;
    }

    /**
     * 
     */
    public function getWholesaleSalePrice( $sale_price, $product) {
        $values = $product->get_meta('smr_ws_limit');

        if(((empty($sale_price) || $sale_price == 0)) && isset($values['qty_new_price']) 
                                                      && $values['qty_new_price'] != '' && $this->userIsValid($values['qty_roles'])) {
            return (int) $values['qty_new_price'];
        }
        return $sale_price;
    }

    /**
     * 
     */
    public function woocommerceGetPriceHtml($price_html, $product) {
        if( $product->is_type('variable')) {
            return $price_html;
        }

        $values = $product->get_meta('smr_ws_limit');
        $callForPrice = $product->get_meta('smr_call_for_price');

        if(empty($callForPrice) == false) {
            return $callForPrice;
        }

        
        if(isset($values['qty_roles']) && $this->userIsValid($values['qty_roles'])) {
            $price_html = wc_format_sale_price(wc_get_price_to_display( $product, array('price' => $product->get_regular_price())), 
                                               wc_get_price_to_display( $product, array('price' => $product->get_sale_price()))).$product->get_price_suffix();
        }

        return $price_html;
    }
}