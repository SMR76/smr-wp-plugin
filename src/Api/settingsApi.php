<?php
/**
 * @package smr plugin
 */


namespace Src\Api;

use \Src\Base\BaseController;

class SettingsApi {
    public $adminPages  = array();
    public $adminSubpages = array();

    public $settings    = array();
    public $sections    = array();
    public $fields      = array();
    
    public function register() {
        if(! empty($this->adminPages)) {
            add_action('admin_menu' , array($this , 'addAdminMenu'));
        }

        if(! empty($this->settings)) {
            add_action('admin_init' , array($this , 'registerCustomFields'));
        }
    }

    public function addPages(array $pages) {
        $this->adminPages = $pages;
        return $this;
    }
    
    public function addSubpages(array $pages) {
        $this->adminSubpages = array_merge($this->adminSubpages, $pages);
        return $this;
    }
    
    public function addSettings(array $settings) {
        $this->settings = $settings;
        return $this;
    }

    public function addSections(array $section) {
        $this->sections = $section;
        return $this;
    }
    
    public function addFields(array $fields) {
        $this->fields = $fields;
        return $this;
    }    

    public function withSubpage(string $title = null) {
        if(empty($this->adminPages)) {
            return $this;
        }

        $adminPage = $this->adminPages[0];

        $subpages =  [ 
            [
                'parentSlug'    => $adminPage['menuSlug'],
                'pageTitle'		=> $adminPage['pageTitle'],
                'menuTitle'		=> ($title ) ? $title : $adminPage['menuTitle'],
                'capability'	=> $adminPage['capability'],
                'menuSlug'		=> $adminPage['menuSlug'],
                'callback'		=> $adminPage['callback']
            ]
        ];

        $this->adminSubpages = $subpages;
        return $this;
    }

    public function addAdminMenu() {
        foreach( $this->adminPages as $page) {
            add_menu_page(  $page['pageTitle'], $page['menuTitle'],$page['capability'],
                            $page['menuSlug'],  $page['callback'], $page['iconUrl'],$page['position']);
        }

        foreach( $this->adminSubpages as $subpage) {
            add_submenu_page(   $subpage['parentSlug'], $subpage['pageTitle'], $subpage['menuTitle'],     
                                $subpage['capability'], $subpage['menuSlug'],  $subpage['callback']);
        }
    }

    public function registerCustomFields() {
        
        foreach($this->settings as $setting){
            register_setting($setting['optionGroup'],$setting['optionName'],
                             (isset($setting['callback']) ? $setting['callback'] : ''));
        }

        foreach($this->sections as $section) {
            add_settings_section( $section['id'] ,$section['title'], (isset($section['callback'])?$section['callback']:''),$section['page']);
        }

        foreach($this->fields as $field) {
            add_settings_field( $field['id'] ,$field['title'],
                                isset($field['callback'])?$field['callback']:'',
                                isset($section['page'])?$section['page']:'',
                                isset($section['section'])?$section['section']:'',
                                isset($section['args'])?$section['args']:'');
        }
    }
}