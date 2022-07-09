<?php
/**
 * @package smr plugin
 */

namespace Src\Api\Woocommerce;

use \Src\Base\BaseController;

/**
 *
 */
class ProductCartInvoice extends BaseController {
    public function register() {
        // Ajax
        add_action('wp_ajax_cart_invoice', [$this, 'cartInvoiceHtml']); // for logged in users

        add_action("woocommerce_review_order_after_submit", [$this, "cartInvoiceButton"]);
    }

    function cartInvoiceButton() {
        wp_enqueue_script('smsContactForm', $this->pluginUrl.'assets/js/cart-invoice.js');

        ProductCartInvoice::ajaxNonce(); // put ajax nonce
        ?>
        <style>
            #cartInvoiceButton {
                width: 100%;
                padding: 14px 8px;
                border: 2px solid #ddd;
                border-radius: var(--btn-shop-brd-radius);
                margin-top: 5px;
                color: gray;
            }
            #cartInvoiceButton.loading::after {
                position: absolute;
                width: 20px; height: 20px; left: 20px;
                content: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Ccircle cx='12' cy='12' r='11' fill='%230000' stroke-dasharray='30' stroke='%230007'/%3E%3Ccircle cx='12' cy='12' r='11' fill='%230000' stroke='%230005'/%3E%3C/svg%3E");
                animation: rot-anim 0.5s infinite;
                animation-timing-function: linear;
            }

            @keyframes rot-anim {
                from {}
                to {transform: rotate(360deg);}
            }
        </style>
        <div id="cartInvoiceButton" class="button alt" onclick="createCartInvoice(this);">پیش فاکتور</div>
        <?php
    }

    function cartInvoiceHtml() {
        $command = $_POST['command'];

        switch($command) {
            case 'getUserCartInvoice': // get user cart invoice
                ob_start();
                require_once("$this->pluginPath/templates/invoiceHtml.php");
                $cartInvoiceHtml = ob_get_contents();
                ob_clean();
                wp_send_json_success($cartInvoiceHtml);
                break;
            default:
                wp_send_json_error("invalid command");

        }
    }

    public static function ajaxNonce() {
        ?> <!-- wordpress nounce -->
        <input id="security" type="hidden" name="security" value="<?= wp_create_nonce("cart_invoice_nonce") ?>">
        <input id="action" type="hidden" name="action" value="cart_invoice">
        <!-- referral url -->
        <input id="referralUrl" type="hidden" name="referralUrl" value="<?= admin_url("admin-ajax.php"); ?>"> <?php
    }
}