<?php
/**
 * 	@package SMR_WP
 * 	@version 1.4.0
 * 	@author  Seyyed Morteza Razavi
 */
/*
	Plugin Name: SMR Plugin
	Plugin URI: http://s-m-r.ir/
	Description: all in one customizations.
	Author: Seyyed Morteza Razavi
	Version: 1.4.0
	Author URI: http://s-m-r.ir/
	Text Domain: smr-plugin
	Domain Path: /locale
*/

defined('ABSPATH') or die('error ABSPATH');
if (file_exists(dirname(__FILE__).'/vendor/autoload.php')) {
	require_once dirname(__FILE__).'/vendor/autoload.php';
}

use Src\Base\Activate;
use Src\Base\Deactivate;

function activate_smrp() {
	Activate::activate();
}

function deactivate_smrp() {
	Deactivate::deactivate();
}

register_deactivation_hook	(__FILE__	, 'deactivate_smrp' );
register_activation_hook	(__FILE__	, 'activate_smrp' );

if(class_exists('Src\\Init')) {
	//* load translation files.
	add_action('plugins_loaded', function() {
		load_plugin_textdomain("smr-plugin", FALSE, dirname(plugin_basename(__FILE__)) . '/locale/');
	});

	Src\Init::registerService();
}