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

    //* ---------------------- fields -----------------------------
    public function wholesaleRolesInput() {
        $values = get_option('smr_settings_option_group');
        $wsRoles = isset($values['ws_roles']) ? $values['ws_roles'] : '';
        ?>
        <input id='ws-selected-roles' type='text' name='smr_settings_option_group[ws_roles]'
                style='width:400px;direction:ltr;' pattern='^\w+(\s*,\s*\w+)*$'
                placeholder = "e.g. wholesale, costomer etc." value='<?php echo $wsRoles;?>'>				
        <p id="suggestionListContainer" class="form-field" style="height:20px;"></p>
        <?php
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
    
    public function freeShippingCitiesInput() {
        $dir = get_locale() == "fa_IR" ? "rtl" : "ltr";
        $values = get_option('smr_settings_option_group');
        $freeShippingCities = isset($values['free_shipping_cities']) ? $values['free_shipping_cities'] : '';
        ?>
        <input id='ws-selected-roles' type='text' name='smr_settings_option_group[free_shipping_cities]'
                style='width:400px; direction:<?php _e($dir)?>;' pattern='^\p{L}+(\s*,\s*\p{L}+)*$'
                placeholder = "e.g. London, Tehran" value='<?php echo $freeShippingCities;?>'>
        <p class="form-field" style="height:20px;max-width: 400px; direction:<?php _e($dir)?>;">
        <?php _e("please fill free shipping cities name separeted with comma.","smr-plugin") ?>
        </p>
        <?php
    }
}
?>