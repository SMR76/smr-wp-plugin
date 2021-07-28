<?php
/**
 * @package smr plugin
 */


namespace Src\Pages;

use \Src\Base\BaseController;
use \Src\Api\Functions;

class Admin extends BaseController{
	public $functions;

	function __construct() {
		parent::__construct();
		$this->functions = new Functions();
	}

    public function register() {
		add_action('admin_menu', array($this, 'setupSettingPage'));
		add_action('admin_init', array($this, 'registerCustomFields'));
	}
	
	public function setupSettingPage() {
		add_menu_page('SMR plugin','SMR','manage_options','smr_plugin'		,array($this->functions,'adminGeneralPage'),'',null);
		//add_submenu_page('smr_plugin','SMR plugin','SMR','manage_options'	,'smr_plugin_general',array($this->functions,'adminGeneralPage'),null);
	}
	
	public function registerCustomFields() {		
		register_setting( 
			'smr_ws_option_group',
			'smr_wholesale',
			array($this->functions, 'optionGroupFieldsFilter')
		);
		
		add_settings_section(
			'smr_option_index',
			__('Settings','smr-plugin'),
			array($this->functions, 'adminSectionArea'),
			'smr_plugin'
		);

		add_settings_field( 
			'ws_roles',
			__('cooperator valid roles','smr-plugin'),
			array($this->functions, 'wholesaleRolesInput'),
			'smr_plugin',
			'smr_option_index', 
			[	'label_for'	=> 'ws_roles',
				'class'		=> 'text-dark']
		);
	}	
}