<?php
/**
 * @package smr plugin
 */


namespace Src\Pages;

use \Src\Base\BaseController;
use \Src\Api\SettingsApi;
use \Src\Api\MOF;

class Admin extends BaseController{

	public $settings;
	public $pages;
	public $subpages;
	public $functions;

	public function __construct() {
		parent::__construct();

		$this->settings = new SettingsApi();
		$this->functions = new MOF();

		$this->setPages();
		$this->setSubpage();
		
		$this->setSettings();
		$this->setSections();
		$this->setFields();

	}

	public function setPages() {
		$this->pages = [
			[
			'pageTitle'		=> 'SMR plugin',
			'menuTitle'		=> 'SMR',
			'capability'	=> 'manage_options',
			'menuSlug'		=> 'smr_plugin',
			'callback'		=> array($this->functions,'adminGeneralPage'),
			'iconUrl'		=> '',
			'position'		=> null
			]
		];
	}

	public function setSubpage() { 
		$this->subpages = [
			[
			'parentSlug'	=> 'smr_plugin',
			'pageTitle'		=> 'custom post type',
			'menuTitle'		=> 'CPT',
			'capability'	=> 'manage_options',
			'menuSlug'		=> 'smrcpt_manager',
			'callback'		=> function(){ echo '<h1>CPT manager</h1>';},
			],
			[
			'parentSlug'	=> 'smr_plugin',
			'pageTitle'		=> 'custom post type',
			'menuTitle'		=> 'Login',
			'capability'	=> 'manage_options',
			'menuSlug'		=> 'llogin_manager',
			'callback'		=> function(){ echo '<h1>CPT manager</h1>';},
			],
			[
			'parentSlug'	=> 'smr_plugin',
			'pageTitle'		=> 'custom whole sale',
			'menuTitle'		=> 'whole sales',
			'capability'	=> 'manage_options',
			'menuSlug'		=> 'smrws_manager',
			'callback'		=> function(){ echo '<h1>CPT manager</h1>';},
			]
		];
	}

	public function setSettings() {
		$args = [
			[
				'optionGroup' 	=> 'smr_option_group',
				'optionName' 	=> 'example_text',
				'callback' 		=> array($this->functions, 'optionGroup')
			]
		];

		$this->settings->addSettings($args);
	}

	public function setSections() {
		$args = [
			[
				'id' 		=> 'smr_option_index',
				'title' 	=> 'example_text',
				'callback' 	=> array($this->functions, 'adminSection'),
				'page' 		=> 'smr_plugin'
			]
		];
		$this->settings->addSections($args);
	}

	public function setFields() {
		$args = [
			[
				'id' 		=> 'example_text',
				'title' 	=> 'example text',
				'callback'	=> array($this->functions, 'smrExampleText'),
				'page' 		=> 'smr_plugin',
				'section' 	=> 'smr_option_index',
				'args' 		=> [
					'label_for'	=> 'example_text',
					'class'		=> 'text-dark'
				]
			]
		];
		$this->settings->addFields($args);
	}	

    public function register() {		
		$this->settings->addPages($this->pages)->withSubpage('General')->addSubpages($this->subpages);
		$this->settings->register();
	}
}