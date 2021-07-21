<?php

/**
 * @package smr plugin
 */

namespace Src\Api\Woocommerce;

use Src\Base\BaseController;

class ExteraCheckoutFields extends BaseController {
    public function register() {
        add_action( 'woocommerce_before_order_notes', array( $this,'exteraCheckoutFields'));
        // add_filter( 'woocommerce_checkout_fields' , array( $this,'custom_override_checkout_fields'));
    }
    
    /**
     * Our hooked in function - $fields is passed via the filter!
     */
    function custom_override_checkout_fields( $fields ) {
        $fields['needs_instalation']['order_comments'] = 'My new placeholder';
        return $fields;
    }

    /**
     * 
     */
    public function exteraCheckoutFields($checkout)
    {
        woocommerce_form_field(
            'needs_installation',
            array(
                'type' => 'checkbox',
                'class' => array(
                    'form-row-wide'
                ),
                'label' => __('Needs Installation?'),
            ),
            $checkout->get_value('needs_installation')? 'yes' : 'no'
        );
        $this->conditionalMessage();
    }

    function conditionalMessage() {
		wp_enqueue_script('smrScript',  $this->pluginUrl. 'lib/jquery/3.5.1/jquery-3.5.1.min.js');

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
            jQuery(() => {
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
}
?>
