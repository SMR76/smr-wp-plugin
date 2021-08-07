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
		add_menu_page('SMR plugin', 'SMR plugin', 'manage_options', 'smr_general_page', array($this->functions,'adminGeneralPage'),'dashicons-store',58.5);
		add_submenu_page('smr_general_page', 'SMR plugin', 'SMR', 'manage_options', 'smr_general_page');
	}
	
	public function registerCustomFields() {
		
		register_setting( 
			'smr_option_group',
			'smr_settings_option_group',
			[$this->functions, 'optionGroupFieldsFilter']
		);
		
		add_settings_section(
			'smr_wholesale_section',
			__('Settings','smr-plugin'),
			[$this->functions, 'wholesaleSection'],
			'smr_general_page'
		);
			
		add_settings_section(
			'smr_options_activate_section',
			__('Options Control','smr-plugin'),
			[$this->functions, 'activateOptionsSection'],
			'smr_general_page'
		);

		$this->wholesaleSectionFields();
		$this->pluginOptionsSectionFields();
	}

	private function wholesaleSectionFields() {
		add_settings_field( 
			'ws_roles',
			__('cooperator valid roles','smr-plugin'),
			[$this->functions, 'wholesaleRolesInput'],
			'smr_general_page',
			'smr_wholesale_section', 
			[	'label_for'		=> 'ws_roles',
				'class'			=> 'text-dark']
		);

	}
	
	private function pluginOptionsSectionFields() {	
		add_settings_field( 
			'activate_wholesale',
			__('activate wholesale ','smr-plugin'),
			[$this->functions, 'activateWholesale'],
			'smr_general_page',
			'smr_options_activate_section', 
			['label_for'	=> 'activate_wholesale']
		);

		add_settings_field( 
			'activate_checkout',
			__('activate checkout fields','smr-plugin'),
			[$this->functions, 'activateCheckout'],
			'smr_general_page',
			'smr_options_activate_section', 
			['label_for'	=> 'activate_checkout']
		);
	}
}