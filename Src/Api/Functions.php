<?php
/**
 * @package smr plugin
 */

namespace Src\Api;

use \Src\Base\BaseController;

class Functions extends BaseController{
    /**
     * admin setting page (general page)
     */
    public function adminGeneralPage() {
        return require_once("$this->pluginPath/templates/adminPage.php"); 
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
        _e('set default valid roles of wholesale users.','smr-plugin');
    }

    public function activateOptionsSection() {
        _e('activate or deactivate other parts of plugin.','smr-plugin');
    }

    public function checkoutSection() {
        _e('modify checkout settigns.','smr-plugin');
    }

    public function othersSection() {
        _e('modify others settigns.','smr-plugin');
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
        <input type='checkbox' name='smr_settings_option_group[activate_checkout]' <?php echo isset($values['activate_checkout'])? 'checked' : '';?>> 
        <?php
    }
    
    public function activateStikyButton() {
        $values = get_option('smr_settings_option_group');
        ?>
        <input type='checkbox' name='smr_settings_option_group[activate_stikybutton]' <?php echo isset($values['activate_stikybutton'])? 'checked' : '';?>> 
        <?php
    }
    
    /**
     * callback function.
     *  input for free shipping cities.
     */
    public function freeShippingCitiesInput() {
        $dir = (get_locale() == "fa_IR" ? "rtl" : "ltr");
        $values = get_option('smr_settings_option_group');
        $freeShippingCities = isset($values['free_shipping_cities']) ? $values['free_shipping_cities'] : '';
        ?>
        <input id='free-shipping-cities' name='smr_settings_option_group[free_shipping_cities]'
                type="text" taged
                class="regular-text"
                value="<?php echo $freeShippingCities;?>"
                placeholder = "e.g. London, Tehran">
        <p class="form-field" style="height:20px;max-width: 400px; direction:<?php echo $dir; ?>;">
        <?php _e("please fill free shipping cities.","smr-plugin") ?>
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
        <input id="cod-cities" name="smr_settings_option_group[cod_cities]"
                type="text" taged
                class="regular-text"
                value="<?php echo $freeShippingCities;?>"
                placeholder="<?php _e("enter cities name.","smr-plugin")?>">
        <?php
    }

    /**
     * callback function.
     *  input for sticky button instagram info.
     */
    public function stickyButtonInstaInfo() {
        $values = get_option('smr_settings_option_group');
        $stickyText = $values['stikybutton_ii'] ?? 
        'follow my work at: <a href="https://www.instagram.com/s_m_r76/" target="_blank">Instagram</a>';
        
        ?>
            <textarea id="stikybutton_ii" name="smr_settings_option_group[stikybutton_ii]"
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
        $stickyText = $values['stikybutton_ci'] ?? 
        ('your custom call info.
        <ul style="margin: 5px 0 0 0;">
            <li><a dir="ltr" href="tel:+123456789">+123456789 (xxxx)</a></li>
            <li><a dir="ltr" href="tel:+123456789">+123456789 (yyyy)</a></li>
        </ul>');
        
        ?>
            <textarea id="stikybutton_ci" name="smr_settings_option_group[stikybutton_ci]"
                class="large-text code" style="direction: ltr; height: 120px;"
                placeholder="<?php _e("enter sticky button calls info.","smr-plugin")?>"><?php echo esc_attr($stickyText);?></textarea>
        <?php
    }
}
?>