<?php

/**
 * @package smr plugin
 */

namespace Src\Api\Woocommerce;

use Src\Base\BaseController;

class ExteraCheckoutFields extends BaseController {
    public function register() {
        //* add custom checkbox field after billing form
        add_action( 'woocommerce_after_checkout_billing_form',  array( $this,'exteraCheckoutFields'));

        //* custom column for shop_order
        add_filter( 'manage_edit-shop_order_columns',           array( $this,'customShopOrderColumn'), 20);
        add_action( 'manage_shop_order_posts_custom_column' ,   array( $this,'customColumnContent')  , 20, 2);

        add_action( 'woocommerce_checkout_create_order',        array( $this,'addCheckoutFieldsToOrderMeta'));
        add_action( 'woocommerce_admin_order_data_after_billing_address', array( $this,'displayCustomCheckoutFieldsInAdminOrder'), 10);
    }

    function customShopOrderColumn($columns) {
        //* enqueue custom style
        wp_enqueue_style('smrOrderShop',$this->pluginUrl.'/assets/css/ordershoppage.css');

        $reordered_columns = array();
        //* Inserting columns to a specific location
        foreach( $columns as $key => $column){
            $reordered_columns[$key] = $column;
            if( $key ==  'order_status' ){
                //* Inserting after "Status" column
                $reordered_columns['installation_service_column'] = __( 'installation service','smr-plugin');
            }
        }
        return $reordered_columns;
    }

    function customColumnContent($column, $post_id) {
        switch ($column) {
            case 'installation_service_column':
                //* Get custom post meta data
                $postMeta = get_post_meta($post_id, 'installation_service', true);
                if (empty($postMeta) == false)
                    echo '<div>'.__('needed','smr-plugin').'</div>';
                break;
        }
    }

    /**
     * 
     */
    public function exteraCheckoutFields($checkout) {
        echo "<br>";
        woocommerce_form_field(
            'needs_installation',
            array(
                'type' => 'checkbox',
                'class' => array('form-row-wide'),
                'label' => '<span style="font-size: 19px;font-weight: 600;">'.__('need installation service?','smr-plugin').'</span>',
            ),
            'yes'
        );
        $this->conditionalMessage();
    }

    function conditionalMessage() {
        ?>             
        <div class="text-justify" id="needs_installation_desc" style="display:none; margin-bottom:35px;">
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
    
    //* Add custom checkout field value as custom order meta data
    function addCheckoutFieldsToOrderMeta( $order ) {
        if ( isset($_POST['needs_installation']) && empty($_POST['needs_installation']) == false ) {
            $order->update_meta_data( 'installation_service', sanitize_text_field($_POST['needs_installation']));
        }
    }

    //* Display "My field" value on the order edit pages under billing section
    function displayCustomCheckoutFieldsInAdminOrder($order){
        $orderMeta = $order->get_meta('installation_service');

        if (!empty($orderMeta)) {
            echo '<p><strong>'.__('request installation service','smr-plugin').':</strong>'.__('yes','smr-plugin').'</p>';
        }
    }
}
?>
