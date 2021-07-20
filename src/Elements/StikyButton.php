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
    //---------------------- stiky button html start ----------------------
    ?>
    <div id="custom-notifier-cont">
        <div id="contact-us" class="option">
            <span>
                در صورتی که در رابطه با خرید نیاز به مشاوره رایگان دارید می‌توانید با شماره‌های ذیل تماس حاصل نمایید.
                <ul style="margin: 5px 0 0 0;">
                    <li><a dir="ltr" href="tel:+989172160881">+989172160881 (شفیعی)</a></li>
                    <li><a dir="ltr" href="tel:+989176049314">+989176049314 (اسکندری)</a></li>
                </ul>                
            </span>
            <div class="ico"><i class="fas fa-mobile-alt"></i></div>
        </div>
        <div id="our-works" class="option">
            <span>نمونه‌کارهای ما را در 
                <a href="https://www.instagram.com/hamta.system/" target="_blank">Instagram</a>
                    دنبال کنید</span>
            <a class="ico" href="https://www.instagram.com/hamta.system/" target="_blank"><i class="fab fa-instagram"></i></a>
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