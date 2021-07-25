<?php
/**
 * @package smr plugin
 */

namespace Src\Api;

use \Src\Base\BaseController;

class Functions extends BaseController{
    public function adminGeneralPage() {
        return require_once("$this->pluginPath/templates/adminPage.php"); 
    }
    public function optionGroupFieldsFilter($input) {
        return $input;
    }
    public function adminSectionArea() {
        echo '<h5>Roles</h5>';
    }
    public function wholesaleRolesInput() {
        $value = esc_attr(get_option('smr_wholesale'));
        printf("<input id='ws-selected-roles' type='text' class='form-control' 
                name='smr_wholesale' value='%s' placeholder='' style='width: 400px;direction: ltr;' pattern='^\w+(\s*,\s*\w+)*$'>", $value);
        echo '<p id="suggestionListContainer" class="form-field" style="height:20px;"></p>';
    }
}