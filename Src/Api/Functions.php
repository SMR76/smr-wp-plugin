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

    //* ---------------------- fields -----------------------------
    public function wholesaleRolesInput() {
        $values     = get_option('smr_settings_option_group');
        $wsRoles    = isset($values['ws_roles']) ? $values['ws_roles'] : '';
        ?>
        <input id='ws-selected-roles' type='text' name='smr_settings_option_group[ws_roles]'
                style='width:400px;direction:ltr;' pattern='^\w+(\s*,\s*\w+)*$'
                placeholder = "e.g. wholesale, costomer etc." value='<?php echo $wsRoles;?>'>				
        <p id="suggestionListContainer" class="form-field" style="height:20px;"></p>
        <?php
    }

    public function activateWholesale() {
        $values     = get_option('smr_settings_option_group');
        ?>
        <input id="activate_wholesale" type='checkbox' name='smr_settings_option_group[activate_wholesale]' <?php echo isset($values['activate_wholesale'])? 'checked' : '';?>> 
        <?php
    }

    public function activateCheckout() {
        $values     = get_option('smr_settings_option_group');
        ?>
        <input type='checkbox' name='smr_settings_option_group[activate_checkout]' <?php echo isset($values['activate_checkout'])? 'checked' : '';?>> 
        <?php
    }
}