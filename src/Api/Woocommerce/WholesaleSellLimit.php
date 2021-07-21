<?php
/**
 * @package smr plugin
 */

namespace Src\Api\Woocommerce;

use Src\Base\BaseController;

class WholesaleSellLimit extends BaseController {
    
    public function register() {
        /**
         * register quantity actions
         */
        add_action( 'woocommerce_product_options_pricing',      array($this,'wc_qty_add_product_field'));
        add_action( 'woocommerce_admin_process_product_object', array($this,'WSSaveProductQuantitySettings'));
        add_filter( 'woocommerce_quantity_input_args',          array($this,'filterWholesaleQuantityInputArgs'), 99, 2);
        add_filter( 'woocommerce_loop_add_to_cart_args',        array($this,'AjaxAddtoCartWholesaleQuantity'), 10, 2);
        add_filter( 'woocommerce_available_variation',          array($this,'filterWSAvailableVariationPrice'), 10, 3);

        /**
         * register quantity prices
         */

        // //* Generating dynamically the product "regular price"
        add_filter( 'woocommerce_product_get_regular_price',            array($this,'getRegularPrice') , 10, 2 );
        add_filter( 'woocommerce_product_variation_get_regular_price',  array($this,'getRegularPrice') , 10, 2 ); 

        // //* Generating dynamically the product "sale price"
        add_filter( 'woocommerce_product_get_sale_price',               array($this,'getWholesaleSalePrice') , 10, 2 );
        add_filter( 'woocommerce_product_variation_get_sale_price',     array($this,'getWholesaleSalePrice') , 10, 2 );

        // //* Displayed formatted regular price + sale price         
        add_filter( 'woocommerce_get_price_html',                       array($this,'woocommerceGetPriceHtml'), 20, 2 );
    }
    
    /**
     * add fields to price section.
     */
    public function wc_qty_add_product_field() {
        global $product_object;
    
        $values = $product_object->get_meta('smr_ws_limit');    

        echo '</div><div class="options_group quantity hide_if_grouped">';
    
        //check box
        woocommerce_wp_checkbox( array( 
            'id'            => 'qty_args',
            'label'         => __( 'Quantity settings', 'woocommerce' ),
            'value'         => empty($values) ? 'no' : 'yes',
            'description'   => __( 'Enable this to show and enable the additional quantity setting fields.', 'woocommerce' ),
        ) );
    
        if(empty($values))
            echo '<div class="qty-args hidden">';
        else
            echo '<div class="qty-args">';
    
        woocommerce_wp_text_input( array(
                'id'                => 'qty_roles',
                'type'              => 'text',
                'label'             => __( 'Roles name', 'woocommerce' ),
                'placeholder'       => '',
                'desc_tip'          => 'true',
                'description'       => __( 'Name of roles which must be effected, must be seprated with comma. (i.e. Admin,Customer).', 'woocommerce' ),
                'custom_attributes' => array( 'pattern'  => '^\w+(\s*,\s*\w+)*$', 'dir' => 'ltr'),
                'value'             => isset($values['qty_roles']) && !empty($values['qty_roles']) ? $values['qty_roles'] : '',
        ));
        
        echo '<p id="suggestionListContainer" class="form-field"></p>';

        woocommerce_wp_text_input( array(
                'id'                => 'qty_min',
                'type'              => 'number',
                'label'             => __( 'Minimum Quantity', 'woocommerce-max-quantity' ),
                'placeholder'       => '',
                'desc_tip'          => 'true',
                'description'       => __( 'Set a minimum allowed quantity limit (a number greater than 0).', 'woocommerce' ),
                'custom_attributes' => array( 'step'  => 'any', 'min'   => '0', 'dir' => 'ltr'),
                'value'             => isset($values['qty_min']) && $values['qty_min'] > 0 ? (int) $values['qty_min'] : 0,
        ));
    
        woocommerce_wp_text_input( array(
                'id'                => 'qty_max',
                'type'              => 'number',
                'label'             => __( 'Maximum Quantity', 'woocommerce-max-quantity' ),
                'placeholder'       => '',
                'desc_tip'          => 'true',
                'description'       => __( 'Set the maximum allowed quantity limit (a number greater than 0). Value "-1" is unlimited', 'woocommerce' ),
                'custom_attributes' => array( 'step'  => 'any', 'min'   => '-1', 'dir' => 'ltr'),
                'value'             => isset($values['qty_max']) && $values['qty_max'] > 0 ? (int) $values['qty_max'] : -1,
        ));
    
        woocommerce_wp_text_input( array(
                'id'                => 'qty_step',
                'type'              => 'number',
                'label'             => __( 'Quantity step', 'woocommerce-quantity-step' ),
                'placeholder'       => '',
                'desc_tip'          => 'true',
                'description'       => __( 'Optional. Set quantity step  (a number greater than 0)', 'woocommerce' ),
                'custom_attributes' => array( 'step'  => 'any', 'min'   => '1', 'dir' => 'ltr'),
                'value'             => isset($values['qty_step']) && $values['qty_step'] > 1 ? (int) $values['qty_step'] : 1,
        ));

        woocommerce_wp_text_input( array(
                'id'                => 'qty_new_price',
                'type'              => 'number',
                'label'             => __( 'New price', 'woocommerce-quantity-step' ),
                'placeholder'       => '',
                'desc_tip'          => 'true',
                'description'       => __( 'Optional. Set new product price. (empty means no change)', 'woocommerce' ),
                'custom_attributes' => array( 'step'  => 'any', 'min'   => '0', 'dir' => 'ltr'),
                'value'             => isset($values['qty_new_price']) && !empty($values['qty_new_price']) ? (int) $values['qty_new_price'] : '',
        ));
    
        echo '</div>';

        $this->toggleWholesaleProductSection();
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
            if (in_array($role , (array) $user->roles) ) {
                return true;
            }
        }        
        return false;
    }

    /**
     * toggle setting fields (admin product pages)
     */
    public function toggleWholesaleProductSection() {
            global $wp_roles;
            $allRoleNames = $wp_roles->get_names();
        ?>
        <script>
        jQuery(function($){
            $('input#qty_args').click(function(){
                if( $(this).is(':checked')) {
                    $('div.qty-args').removeClass('hidden');
                } else {
                    $('div.qty-args').addClass('hidden');
                }
            });

            var source=["<?php echo implode('","',array_keys($allRoleNames)) ?>"];
            
            var suggestionClicked = function() {
                let suggest = $(this).html();
                let input   = $('#qty_roles');
                let value   = input.val();
                let lastIndex   = value.lastIndexOf(',');
                let newInput    = lastIndex == -1? '' : value.substr(0,lastIndex) + ',';

                input.val(newInput + suggest);
                $("#suggestionListContainer").html('');
            };

            $('#qty_roles').keyup(function() {
                let rolesInput  = $(this).val().toLocaleLowerCase().replaceAll(' ','');
                let lastIndex   = rolesInput.lastIndexOf(',');
                let result      = []; 

                rolesInput = lastIndex == -1 ? rolesInput : rolesInput.substr(lastIndex + 1);
                for(let x of source) {
                    if(rolesInput != '' && x.toLocaleLowerCase().includes(rolesInput) == true) {
                        result.push(`<a class="btn suggestedItem">${x}</a>`);
                    }
                }
                $("#suggestionListContainer").html(result.join(', '));

                if(result.length)
                    $('.suggestedItem').click(suggestionClicked);
            });
        });
        </script>
        <?php
    }
    
    /**
     * Save quantity setting fields values.
     */ 
    public function WSSaveProductQuantitySettings( $product ) {
        if ( isset($_POST['qty_args']) ) {
            $values = $product->get_meta('smr_ws_limit');
    
            $product->update_meta_data( 'smr_ws_limit', array(
                'qty_roles'     => isset($_POST['qty_roles'])       && !empty($_POST['qty_roles'])      ? $_POST['qty_roles']               : '',
                'qty_min'       => isset($_POST['qty_min'])         && $_POST['qty_min'] > 0            ? (int) wc_clean($_POST['qty_min']) : 0 ,
                'qty_max'       => isset($_POST['qty_max'])         && $_POST['qty_max'] > 0            ? (int) wc_clean($_POST['qty_max']) : -1,
                'qty_step'      => isset($_POST['qty_step'])        && $_POST['qty_step'] > 1           ? (int) wc_clean($_POST['qty_step']): 1 ,
                'qty_new_price' => isset($_POST['qty_new_price'])   && !empty($_POST['qty_new_price'])  ? (int) $_POST['qty_new_price']     : ''
            ) );
        } else {
            $product->update_meta_data( 'smr_ws_limit', array() );
        }
    }
    
    /**
     * The quantity settings in action on front end
     */
    public function filterWholesaleQuantityInputArgs( $args, $product ) {
        
        
        if ( $product->is_type('variation') ) {
            $parent_product = wc_get_product( $product->get_parent_id() );
            $values  = $parent_product->get_meta( 'smr_ws_limit' );
        } else {
            $values  = $product->get_meta( 'smr_ws_limit' );
        }
    
        if (empty( $values ) == false && $this->userIsValid($values['qty_roles'])) {
            //* set min value
            if ( isset( $values['qty_min'] ) && $values['qty_min'] > 1 ) {
                $args['min_value'] = $values['qty_min'];
    
                if( ! is_cart() ) {
                    $args['input_value'] = $values['qty_min']; //* set starting value
                }
            }
    
            //* set max value
            if ( isset( $values['qty_max'] ) && $values['qty_max'] > 0 ) {
                $args['max_value'] = $values['qty_max'];
    
                if ( $product->managing_stock() && ! $product->backorders_allowed() ) {
                    $args['max_value'] = min( $product->get_stock_quantity(), $args['max_value'] );
                }
            }
    
            //* set step value
            if ( isset( $values['qty_step'] ) && $values['qty_step'] > 1 ) {
                $args['step'] = $values['qty_step'];
            }
        }
        return $args;
    }
    
    /**
     * Ajax add to cart, set "min quantity" as quantity on shop and archives pages
     */
    public function AjaxAddtoCartWholesaleQuantity( $args, $product ) {
        $values  = $product->get_meta( 'smr_ws_limit' );
    
        if (empty( $values ) == false && $this->userIsValid($values['qty_roles'])) {
            // Min value
            if ( isset( $values['qty_min'] ) && $values['qty_min'] > 1 ) {
                $args['quantity'] = $values['qty_min'];
            }
        }
        return $args;
    }
    
    /**
     * The quantity settings in action on front end (For variable productsand their variations)
     */
    public function filterWSAvailableVariationPrice( $data, $product, $variation ) {
        $values  = $product->get_meta( 'smr_ws_limit' );
    
        if (empty( $values ) == false && $this->userIsValid($values['qty_roles'])) {
            if ( isset( $values['qty_min'] ) && $values['qty_min'] > 1 ) {
                $data['min_qty'] = $values['qty_min'];
            }
    
            if ( isset( $values['qty_max'] ) && $values['qty_max'] > 0 ) {
                $data['max_qty'] = $values['qty_max'];
    
                if ( $variation->managing_stock() && ! $variation->backorders_allowed() ) {
                    $data['max_qty'] = min( $variation->get_stock_quantity(), $data['max_qty'] );
                }
            }
        }
        return $data;
    }

    /**
     * 
     */
    public function getRegularPrice( $regular_price, $product ) {
        if( empty($regular_price) || $regular_price == 0 )
            return $product->get_price();
        else
            return $regular_price;
    }

    /**
     * 
     */
    public function getWholesaleSalePrice( $sale_price, $product ) {
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
        
        if(isset($values['qty_roles']) && $this->userIsValid($values['qty_roles'])) {
            $price_html = wc_format_sale_price(wc_get_price_to_display( $product, array('price' => $product->get_regular_price())), 
                                               wc_get_price_to_display( $product, array('price' => $product->get_sale_price()))).$product->get_price_suffix();
        }

        return $price_html;
    }
}