<?php

/**
 * @package smr plugin
 */

namespace Src\Api\Woocommerce;

use Src\Base\BaseController;
/**
 * Include a conditional field in the billing form and a related column on the order page.
 * The conditional field was accompanied by a custom message following the field.
 * The message will be displayed when you check the checkbox.
 */
class ExtraCheckoutFields extends BaseController
{
    public function register()
    {
        $options = get_option('smr_config_option');
        $checkoutOption = $options['checkout'];

        if (isset($checkoutOption['billing_field_active']) == false)
            return;

        //* add custom checkbox field after billing form
        add_action('woocommerce_after_checkout_billing_form', [$this, 'exteraCheckoutFields']);

        //* custom column for shop_order
        add_filter('manage_edit-shop_order_columns', [$this, 'customShopOrderColumn'], 20);
        add_action('manage_shop_order_posts_custom_column', [$this, 'customColumnContent'], 20, 2);

        add_action('woocommerce_checkout_create_order', [$this, 'addCheckoutFieldsToOrderMeta']);
        add_action('woocommerce_admin_order_data_after_billing_address', [$this, 'displayCustomCheckoutFieldsInAdminOrder']);
    }

    function customShopOrderColumn($columns) {
        //* enqueue custom style
        wp_enqueue_style('smrOrderShop', $this->pluginUrl . '/assets/css/ordershoppage.css');

        $reordered_columns = [];
        //* Inserting columns to a specific location
        foreach ($columns as $key => $column) {
            $reordered_columns[$key] = $column;
            if ($key == 'order_status') {
                //* Inserting after "Status" column
                $reordered_columns['installation_service_column'] = __('installation service', 'smr-plugin');
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
                    echo '<div>'.__('needed', 'smr-plugin') . '</div>';
                break;
        }
    }

    /**
     *
     */
    public function exteraCheckoutFields($checkout) {
        $options = get_option('smr_config_option');
        $checkoutOption = $options['checkout'];

        echo "<br>";
        woocommerce_form_field(
            'custom_checkbox',
            [
                'type' => 'checkbox',
                'class' => ['form-row-wide'],
                'label' => '<span style="font-size: 19px;font-weight: 600;">' . __('need installation service?', 'smr-plugin') . '</span>',
            ],
            'yes'
        );

        ?>
        <div id="custom_checkbox_msg" class="text-justify" style="display:none; margin-bottom:35px;">
            <?= $this->markdownaParser($checkoutOption['billing_field_message'] ?? ""); ?>
        </div>

        <script>
            jQuery(function($) {
                $("#custom_checkbox").click(function() {
                    if (this.checked) {
                        $("#custom_checkbox_msg").slideDown();
                    } else {
                        $("#custom_checkbox_msg").slideUp();
                    }
                });
            });
        </script>
        <?php
    }

    // As custom order meta data, include the value of the custom checkout field.
    function addCheckoutFieldsToOrderMeta($order) {
        if (isset($_POST['custom_checkbox']) && empty($_POST['custom_checkbox']) == false) {
            $order->update_meta_data('installation_service', sanitize_text_field($_POST['custom_checkbox']));
        }
    }

    // Display the value of the "custom field" on the order edit pages under the billing section.
    function displayCustomCheckoutFieldsInAdminOrder($order) {
        $orderMeta = $order->get_meta('installation_service');

        if (!empty($orderMeta)) {
            echo '<p><strong>' . __('request installation service', 'smr-plugin') . ':</strong>' . __('yes', 'smr-plugin') . '</p>';
        }
    }
}
?>