<?php

/**
 * @package smr plugin
 */

namespace Src\Api\Woocommerce;

use Src\Base\BaseController;

class ExtraRegistrationFields extends BaseController{
    public function register() {
        add_action('woocommerce_register_form', [$this,'woocommerceExtraRgeisterFields']);
        add_action('woocommerce_created_customer', [$this,'woocommerceSaveExtraRegisterationFields']);

        //* custom column in users page.
        add_filter('manage_users_columns', [$this, 'manageUsersColumns']);
        add_filter('manage_users_custom_column', [$this, 'manageUsersCustomColumn'], 20, 3);
    }

    /**
     * insert custom column into users columns
     */
    public function manageUsersColumns($columns) {
        //* enqueue custom css style.
        wp_enqueue_style('smr-users-page',$this->pluginUrl.'/assets/css/users-page.css');

        $newColumn = ["smr_ws" => __("Cooperator", "smr-plugin")];
        $idx = count($columns) - 1;

        //* insert custom column just before last column.
        return array_merge(array_slice($columns, 0, $idx),
                            $newColumn,
                            array_slice($columns, $idx, count($columns)));
    }

    /**
     *
     */
    public function manageUsersCustomColumn($output,$columnName,$userId) {
        switch ($columnName) {
            case 'smr_ws':
                $wsRequest = get_the_author_meta('ws_request', $userId);
                $user_meta = get_userdata($userId);
                $user_roles = $user_meta->roles;

                if($wsRequest == 'true') {
                    return '<div>'.__('requested for cooperate', 'smr-plugin').'</div>';
                } else if(in_array('wholesale', $user_roles)) {
                    return '';
                }
        }
        return $output;
    }

    /**
     *
     */
    public function woocommerceExtraRgeisterFields() {
        ?>
            <p class="woocomerce-FormRow form-row">
                <label for="reg_ws_request">
                    <?= __('Cooperator Customers','smr-plugin');?>
                </label>
                <input type="checkbox" class="checkbox" name="ws_request" id="reg_ws_request" value="true"/>
                <small id="helpId" class="form-text text-muted">
                    <?= __( 'check it if you are a cooperator customer.','smr-plugin');?>
                </small>
            </p><!--
            <p class="woocomerce-FormRow form-row">
                <label for="reg_company_name">
                    <?php //__( 'Wholesales Customer');?>
                </label>
                <input type="text" class="input-text" name="ws_company_name" id="reg_company_name" value="true"/>
            </p> -->
            <div class="clear"></div>
        <?php
    }

    /**
     *
     */
    public function woocommerceSaveExtraRegisterationFields( $customer_id ) {
        if(isset($_POST['ws_request']))
            update_user_meta($customer_id, 'ws_request', 'true');

        // if ( isset( $_POST['ws_company_name']))
        //     update_user_meta( $customer_id, 'ws_company_name','true');
    }
}
?>