<?php

/**
 * @package smr plugin
 */

namespace Src\Api\Woocommerce;

use Src\Base\BaseController;

class ExteraCheckoutFields extends BaseController {
    public function register() {
        add_action( 'woocommerce_before_order_notes',           array( $this,'exteraCheckoutFields'));

        //* custom column for shop_order
        add_filter( 'manage_edit-shop_order_columns',           array( $this,'customShopOrderColumn'), 20);
        add_action( 'manage_shop_order_posts_custom_column' ,   array( $this,'customColumnContent')  , 20, 2);

        add_action( 'woocommerce_checkout_create_order',        array( $this,'addCheckoutFieldsToOrderMeta'));
        add_action( 'woocommerce_admin_order_data_after_billing_address', array( $this,'displayCustomCheckoutFieldsInAdminOrder'), 10);
    }

    function customShopOrderColumn($columns) {
        $reordered_columns = array();
        // Inserting columns to a specific location
        foreach( $columns as $key => $column){
            $reordered_columns[$key] = $column;
            if( $key ==  'order_status' ){
                // Inserting after "Status" column
                $reordered_columns['installation_service_column'] = __( 'installation service','smr-plugin');
            }
        }
        return $reordered_columns;
    }

    function customColumnContent($column, $post_id) {
        switch ($column) {
            case 'installation_service_column':
                // Get custom post meta data
                $postMeta = get_post_meta($post_id, 'installation_service', true);
                if (!empty($postMeta))
                    echo 'needed';

                // Testing (to be removed) - Empty value case
                else
                    echo '<small>(<em>no value</em>)</small>';

                break;
        }
    }

    /**
     * 
     */
    public function exteraCheckoutFields($checkout) {
        woocommerce_form_field(
            'needs_installation',
            array(
                'type' => 'checkbox',
                'class' => array('form-row-wide'),
                'label' => __('need installation service?','smr_plugin'),
            ),
            'yes'
        );
        $this->conditionalMessage();
    }

    function conditionalMessage() {
		// wp_enqueue_script('smrScript',  $this->pluginUrl. 'lib/jquery/3.5.1/jquery-3.5.1.min.js');

        ?>             
        <div class="text-justify" id="needs_installation_desc" style="display: none;margin-bottom:35px;">
            <ul class="px-3" style="font-size: medium;">
                <li><i class="fas fa-check"></i>
                    هزینه ایاب الذهاب در شیراز رایگان و در خارج از شهر به ازای هر ۵۰ کیلومتر مبلغ ۳۰٫۰۰۰ تومان می‌باشد.</li>
                <li><i class="fas fa-check"></i>
                    بعد از تکمیل خرید در اولین وقت اداری و بعد از ثبت خرید، مشتری‌هایی که نیاز به نصب و راه اندازی (فعال کردن تیک نصب و راه اندازی) تماس می گیرد و هماهنگی های لازم راه انجام می‌دهد.</li>
                <li><i class="fas fa-check"></i>
                    هزینه نصب هر دوربین ۵۰٫۰۰۰ تومان.</li>
                <li><i class="fas fa-check"></i>
                    هزینه کابل کشی ۱٫۵۰۰ تومان.</li>
                <li><i class="fas fa-check"></i>
                    هزینه نصب و راه اندازی ۷۰٫۰۰۰ تومان.</li>
                <li><i class="fas fa-check"></i>
                    هزینه نصب فلکسی و داکت و ... به صورت رایگان انجام می‌گردد.</li>
            </ul>
        </div>

        <script>
            jQuery(function($) {
                $("#needs_installation").click(function() {
                    if($(this).is(':checked'))
                        $("#needs_installation_desc").slideDown();
                    else
                        $("#needs_installation_desc").slideUp();
                });
            });
        </script>
    <?php
    }
    
    // Add custom checkout field value as custom order meta data
    function addCheckoutFieldsToOrderMeta( $order ) {
        if ( isset($_POST['needs_installation']) && empty($_POST['needs_installation']) == false ) {
            $order->update_meta_data( 'installation_service', sanitize_text_field($_POST['needs_installation']));
        }
    }

    // Display "My field" value on the order edit pages under billing section
    function displayCustomCheckoutFieldsInAdminOrder($order){
        $orderMeta = $order->get_meta('installation_service');

        if (!empty($orderMeta)) {
            echo '<p><strong>'.__('request installation service').':</strong> true </p>';
        }
    }

}
?>
