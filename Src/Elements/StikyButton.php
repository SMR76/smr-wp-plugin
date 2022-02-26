<?php
/**
 * @package smr plugin
 */


namespace Src\Elements;

use \Src\Base\BaseController;

class StikyButton extends BaseController{
    public function register() {
        add_action( 'wp_footer', array($this, 'fixedInfoButton'));
    }

    public function fixedInfoButton () { 
        $values = get_option('smr_settings_option_group');
        $showStikyButton = isset($values['activate_stikybutton']) ? $values['activate_stikybutton'] : '';
		if($showStikyButton == false ||  is_admin() || (defined('DOING_AJAX') && DOING_AJAX)) {
            return;
        }
        wp_register_style ('smrStikyButtonStyle',    $this->pluginUrl .'assets/css/stikybutton.css');
        wp_register_script('smrStikyButtonScript',   $this->pluginUrl . 'assets/js/stikybutton.js');

        wp_enqueue_style ('smrStikyButtonStyle');
        wp_enqueue_script('smrStikyButtonScript');
        
        //---------------------- stiky button html start ----------------------
        ?>
        <div id="smr-stikybutton">
            <div id="contact-us" class="smr-stikybutton-option">
                <span>
                    <?php /* sticky note html */ ?>
                    در صورتی که در رابطه با خرید نیاز به مشاوره رایگان دارید می‌توانید با شماره‌های ذیل تماس حاصل نمایید.
                    <ul style="margin: 5px 0 0 0;">
                        <li><a dir="ltr" href="tel:+989172160881">+989172160881 (شفیعی)</a></li>
                        <li><a dir="ltr" href="tel:+989176049314">+989176049314 (اسکندری)</a></li>
                    </ul>                
                </span>
                <div class="ico"><i class="fas fa-mobile-alt"></i></div>
            </div>
            <div id="our-works" class="smr-stikybutton-option">
                <span>
                    <?php /* sticky note html */ ?>
                    نمونه‌کارهای ما را در 
                    <a href="https://www.instagram.com/hamta.system/" target="_blank">Instagram</a>
                        دنبال کنید
                    </span>
                <div class="ico" ><i class="fab fa-instagram"></i></div>
            </div>
            <div id="handbell" class="ico" onclick="toggle()">
                <i class="fas fa-bell"></i>
            </div>
        </div>
        <?php 
        //---------------------- stiky button html end ----------------------
    }
}
?>