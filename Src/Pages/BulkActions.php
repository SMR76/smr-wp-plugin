<?php
/**
 * @package smr plugin
 */

namespace Src\Pages;

use \Src\Base\BaseController;

class BulkActions extends BaseController {
    public $functions;

    function __construct() {
        parent::__construct();
        $this->functions = new BulkActionsFunctions();
    }

    public function register() {
        add_action('admin_menu', [$this, 'setupSettingPage']);
        add_action('admin_init', [$this, 'registerCustomFields']);
    }

    public function setupSettingPage() {
        add_submenu_page('edit.php?post_type=product', 'Woocomerce bulk actions', 'Extra Bulk Actions', 'manage_options', 'smr-woo-bulk-actions', [$this->functions,'bulkActionsSubmenuPage']);
    }

    public function registerCustomFields() {
        register_setting(
            'smr_option_group',
            'smr_config_option',
            [$this->functions, 'optionGroupFieldsFilter']
        );
    }
}