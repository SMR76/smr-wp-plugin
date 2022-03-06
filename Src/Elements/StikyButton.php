<?php
/**
 * @package smr plugin
 */


namespace Src\Elements;

use \Src\Base\BaseController;

class StikyButton extends BaseController {
    public function register() {
        add_action( 'wp_footer', array($this, 'fixedInfoButton'));
    }

    public function fixedInfoButton() { 
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
                    <?php echo $values['stikybutton_ci'] ?? '';  ?>            
                </span>
                <div class="ico"><i class="fas fa-mobile-alt"></i></div>
            </div>
            <div id="our-works" class="smr-stikybutton-option">
                <span>
                    <?php echo $values['stikybutton_ii'] ?? ''; ?>
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