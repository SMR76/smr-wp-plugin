<?php
/**
 * @package smr plugin
 */


namespace Src\Elements;

use \Src\Base\BaseController;

class StickyButton extends BaseController {
    public function register() {
        add_action( 'wp_footer', array($this, 'fixedInfoButton'));
    }

    public function fixedInfoButton() {
        $values = get_option('smr_settings_option_group');
        $isActive = isset($values['activate_stickybutton']) ? $values['activate_stickybutton'] : '';
        if ($isActive == false ||  is_admin() || (defined('DOING_AJAX') && DOING_AJAX)) {
            return;
        }
        // Bug: The WordPress admin bar will be in conflict if register style is relocated to the class register method.
        wp_register_style('smrStickyButtonStyle', $this->pluginUrl . 'assets/css/sticky-button.css');
        wp_register_script('smrStickyButtonScript', $this->pluginUrl . 'assets/js/sticky-button.js');

        wp_enqueue_style ('smrStickyButtonStyle');
        wp_enqueue_script('smrStickyButtonScript');
        
        //---------------------- sticky button html start ----------------------
        ?>
        <div id="smr-sticky-button">
            <div id="contact-us" class="smr-sticky-button-option">
                <span>
                    <?php echo $values['stickybutton_wi'] ?? '';  ?>
                </span>
                <div class="ico"><i class="fab fa-whatsapp" style="font-size: 17px;"></i></div>
            </div>
            <div id="contact-us" class="smr-sticky-button-option">
                <span>
                    <?php echo $values['stickybutton_ci'] ?? '';  ?>            
                </span>
                <div class="ico"><i class="fas fa-mobile-alt"></i></div>
            </div>
            <div id="our-works" class="smr-sticky-button-option">
                <span>
                    <?php echo $values['stickybutton_ii'] ?? ''; ?>
                </span>
                <div class="ico" ><i class="fab fa-instagram"></i></div>
            </div>
            <div id="handbell" class="ico" onclick="toggle()">
                <i class="fas fa-bell"></i>
            </div>
        </div>
        <?php 
        //---------------------- sticky button html end ----------------------
    }
}
?>