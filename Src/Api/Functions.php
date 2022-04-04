<?php
/**
 * @package smr plugin
 */

namespace Src\Api;

use \Src\Base\BaseController;

class Functions extends BaseController{
    private $cssDirection = "ltr";

    public function __construct() {
        parent::__construct();
        $this->cssDirection = is_rtl() ? "rtl" : "ltr";
    }
    /**
     * admin setting page (general page)
     */
    public function adminGeneralPage() {
        return require_once($this->pluginPath."/templates/adminPage.php"); 
    }

    /**
     * sanetize group
     */
    public function optionGroupFieldsFilter($input) {
        // if need a walk.
        // array_walk($input, function ($value, $key) { $value; });
        return $input;
    }

    public function wholesaleSection() {
        _e('define the default valid roles for wholesale users.','smr-plugin');
    }

    public function activateOptionsSection() {
        _e('This checkbox activates or deactivates additional plugin components.','smr-plugin');
    }

    public function checkoutSection() {
        _e('Modify the checkout settings.','smr-plugin');
    }

    public function othersSection() {
        _e('Modify the checkout settings.','smr-plugin');
    }

    //* ---------------------- fields -----------------------------
    public function wholesaleRolesInput() {
        global $wp_roles;
        $allRoleNames = $wp_roles->get_names();

        $values = get_option('smr_settings_option_group');
        $wsRoles = isset($values['ws_roles']) ? $values['ws_roles'] : '';			
        
        $dropdown = 
            '<select name="smr_settings_option_group[ws_roles][]"
                     id="ws-selected-roles" multiple>
                     style="width:25em"';
        foreach($allRoleNames as $roles ) {
            $dropdown .= "<option value='$roles' ".selected(in_array($roles, $wsRoles),true,false).">$roles</option>";
        }
        $dropdown .= '</select>';
        echo $dropdown;
    }

    public function activateWholesale() {
        $values = get_option('smr_settings_option_group');
        ?>
        <input id="activate_wholesale" type='checkbox' name='smr_settings_option_group[activate_wholesale]' <?php echo isset($values['activate_wholesale'])? 'checked' : '';?>> 
        <?php
    }

    public function activateCheckout() {
        $values = get_option('smr_settings_option_group');
        ?>
        <input id="activate_checkout" type='checkbox' name='smr_settings_option_group[activate_checkout]' <?php echo isset($values['activate_checkout'])? 'checked' : '';?>> 
        <?php
    }
    
    public function activateStickyButton() {
        $values = get_option('smr_settings_option_group');
        ?>
        <input id="activate_stickybutton" type='checkbox' name='smr_settings_option_group[activate_stickybutton]' <?php echo isset($values['activate_stickybutton'])? 'checked' : '';?>> 
        <?php
    }
    
    /**
     * callback function.
     *  input for free shipping cities.
     */
    public function freeShippingCitiesInput() {
        $values = get_option('smr_settings_option_group');
        $freeShippingCities = isset($values['free_shipping_cities']) ? $values['free_shipping_cities'] : '';
        ?>
        <input id='free_shipping_cities' name='smr_settings_option_group[free_shipping_cities]'
                type="text" taged
                class="regular-text"
                value="<?php echo $freeShippingCities;?>"
                placeholder = "e.g. London, Tehran">
        <p class="form-field" style="height:20px;max-width: 400px; direction:<?= $this->cssDirection; ?>;">
            <?php _e("To input the city name, press Tab/Space/Enter.","smr-plugin") ?>
        </p>
        <?php
    }

    /**
     * callback function.
     *  input for cash on delivery cities.
     */
    public function codCitiesInput() {
        $values = get_option('smr_settings_option_group');        
        $freeShippingCities = isset($values['cod_cities']) ? $values['cod_cities'] : '';
        
        ?>
        <input id="cod_cities" name="smr_settings_option_group[cod_cities]"
                type="text" taged
                class="regular-text"
                value="<?php echo $freeShippingCities;?>"
                placeholder="<?php _e("e.g. London, Tehran","smr-plugin")?>">
        <p class="form-field" style="height:20px;max-width: 400px; direction:<?= $this->cssDirection; ?>;">
            <?php _e("To input the city name, press Tab/Space/Enter.","smr-plugin") ?>
        </p>
        <?php
    }

    /**
     * callback function.
     *  input for sticky button instagram info.
     */
    public function stickyButtonInstaInfo() {
        $values = get_option('smr_settings_option_group');
        
        $stickyText = $values['stickybutton_ii'] ?? '';
        if(strlen($stickyText) < 3) {
            $stickyText = 'follow my work at: <a href="https://www.instagram.com/s_m_r76/" target="_blank">Instagram</a>';
        }
        
        ?>
            <textarea id="stickybutton_ii" name="smr_settings_option_group[stickybutton_ii]"
                    class="large-text code" style="direction: ltr;"
                    placeholder="<?php _e("enter sticky button instagram info","smr-plugin")?>"><?php echo esc_attr($stickyText);?></textarea>
        <?php
    }

    /**
     * callback function.
     *  input sticky button call info.
     */
    public function stickyButtonCallInfo() {
        $values = get_option('smr_settings_option_group');
        
        $stickyText = $values['stickybutton_ci'] ?? '';
        if(strlen($stickyText) < 3) {
            $stickyText = 
'your custom call info.
<ul style="margin: 5px 0 0 0;">
    <li><a dir="ltr" href="tel:+123456789">+123456789 (xxxx)</a></li>
    <li><a dir="ltr" href="tel:+123456789">+123456789 (yyyy)</a></li>
</ul>';
        }
        
        ?>
            <textarea id="stickybutton_ci" name="smr_settings_option_group[stickybutton_ci]"
                class="large-text code" style="direction: ltr; height: 120px;"
                placeholder="<?php _e("enter sticky button calls info.","smr-plugin")?>"><?php echo esc_attr($stickyText);?></textarea>
        <?php
    }

    /**
     * callback function.
     *  input sticky button call info.
     */
    public function stickyButtonWhatsAppInfo() {
        $values = get_option('smr_settings_option_group');
        $stickyText = $values['stickybutton_wi'] ?? '';
        if(strlen($stickyText) < 3) {
            $stickyText = 
'your custom whatsapp info.
<ul style="margin: 5px 0 0 0;">
    <li><a dir="ltr" href="https://wa.me/+123456789?text=hi">+123456789 (xxxx)</a></li>
</ul>';
        }
        
        ?>
            <textarea id="stickybutton_wi" name="smr_settings_option_group[stickybutton_wi]"
                class="large-text code" style="direction: ltr; height: 120px;"
                placeholder="<?php _e("enter sticky button whatsapp info.","smr-plugin")?>"><?php echo esc_attr($stickyText);?></textarea>
        <?php
    }

    public function contactFormSmsUsername() {
        $values = get_option('smr_settings_option_group');
        $username = $values['sms_username'] ?? '';
        ?>
            <input id="sms_username" name="smr_settings_option_group[sms_username]" style="direction: ltr;"
                    placeholder="<?= __("sms username" ,"smr-plugin")?>" 
                    type="text" class="regular-text" value="<?= $username;?>">
        <?php
    }
    public function contactFormSmsPassword() {
        $values = get_option('smr_settings_option_group');
        $password = $values['sms_password'] ?? '';
        ?>
            <input id="sms_password" name="smr_settings_option_group[sms_password]" style="direction: ltr;"
                    placeholder="<?= __("sms password" ,"smr-plugin")?>" 
                    type="text" class="regular-text" value="<?= $password;?>">
        <?php
    }
    public function contactFormSmsId() {
        $values = get_option('smr_settings_option_group');
        $id = $values['sms_id'] ?? '';
        ?>
            <input id="sms_id" name="smr_settings_option_group[sms_id]" style="direction: ltr;"
                pattern="\d+" placeholder=<?= __("sms id" ,"smr-plugin") ?> 
                type="text" class="regular-text" value="<?= $id;?>">
        <?php
    }
}
?>