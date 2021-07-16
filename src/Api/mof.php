<?php
/**
 * @package smr plugin
 */

namespace Src\Api;

use \Src\Base\BaseController;

class MOF extends BaseController{
    public function adminGeneralPage() {
        return require_once("$this->pluginPath/templates/adminPage.php"); 
    }
    public function optionGroup($input) {
        return $input;
    }
    public function adminSection() {
        echo 'this is the section';
    }
    public function smrExampleText() {
        echo  'hi';
        $value = esc_attr( get_option('example_text') );
        echo $value;
        echo "<input type='text' class='form-control' name='example_text' value='' placeholder='$'>";
    }
}