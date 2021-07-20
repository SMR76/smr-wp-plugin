<?php
/**
 * @package smr plugin
 */

namespace Src\Api\Woocommerce;

class ExteraRegistrationFields {
    public function register() {
        add_action( 'woocommerce_register_form',    array($this,'woocommerceExteraRgeisterFields'));
        add_action( 'woocommerce_created_customer', array($this,'woocommerceSaveExteraRegisterationFields'));
    }

    /**
     * 
     */
    function woocommerceExteraRgeisterFields() {
        ?>
            <p class="woocomerce-FormRow form-row">
                <label for="reg_ws_customer">
                    <?php _e( 'Wholesales Customer'); ?>
                </label>
                <input type="checkbox" class="checkbox" name="ws_customer" id="reg_ws_customer" value="isWholesale" />
                <small id="helpId" class="form-text text-muted">
                    check it if you are a wholesale customer.
                </small>
            </p>
            <div class="clear"></div>
        <?php
    }

    /**
     * 
     */
    function woocommerceSaveExteraRegisterationFields( $customer_id ) {
        if ( isset( $_POST['ws_customer'] ) ) {
            update_user_meta( $customer_id, 'ws_customer', 'true' );
        }
    }
}
?>