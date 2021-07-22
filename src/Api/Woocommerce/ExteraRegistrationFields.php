<?php
/**
 * @package smr plugin
 */

namespace Src\Api\Woocommerce;

use Src\Base\BaseController;

class ExteraRegistrationFields extends BaseController{
    public function register() {
        add_action( 'woocommerce_register_form',    array($this,'woocommerceExteraRgeisterFields'));
        add_action( 'woocommerce_created_customer', array($this,'woocommerceSaveExteraRegisterationFields'));
    }

    /**
     * 
     */
    function woocommerceExteraRgeisterFields() {
        $user  = wp_get_current_user();
        $value = isset($_POST['billing_account_number']) ? esc_attr($_POST['billing_account_number']) : $user->billing_account_number;
    
        ?>
            <p class="woocomerce-FormRow form-row">
                <label for="reg_has_ws_req">
                    <?php __( 'Wholesales Customer');?>
                </label>
                <input type="checkbox" class="checkbox" name="has_ws_req" id="reg_has_ws_req" value="true"/>
                <small id="helpId" class="form-text text-muted">
                    <?php __( 'check it if you are a wholesale customer.');?>
                </small>
            </p>
            <p class="woocomerce-FormRow form-row">
                <label for="reg_company_name">
                    <?php __( 'Wholesales Customer');?>
                </label>
                <input type="text" class="input-text" name="ws_company_name" id="reg_company_name" value="true"/>
            </p>
            <div class="clear"></div>
        <?php
    }

    /**
     * 
     */
    function woocommerceSaveExteraRegisterationFields( $customer_id ) {
        if ( isset( $_POST['has_ws_req']))
            update_user_meta( $customer_id, 'has_ws_req'        , 'true' );

        if ( isset( $_POST['ws_company_name'])) 
            update_user_meta( $customer_id, 'ws_company_name'   , 'true' );
    }
}
?>