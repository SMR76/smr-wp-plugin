<?php

/**
 * @package smr plugin
 */

namespace Src\Pages;

use \Src\Base\BaseController;
use \Src\Pages;

class BulkActionsAjax extends BaseController
{
    public $functions;

    public function __construct() {
        parent::__construct();
        $this->functions = new BulkActionsFunctions();
    }

    public function register() {
        add_action('wp_ajax_bulk_actions', [$this, 'getProductsInfoAjax']); // for logged in users
    }
    /**
     * @method getProductsInfoAjax
     * bulk action page ajax
     */
    public function getProductsInfoAjax() {
        check_ajax_referer('bulk_actions_nonce', 'security'); // check nonce

        if(isset($_POST['command'])) {
            $command = $_POST['command'];

            switch($command) {
                case 'getProducts': // get products info
                    $productsInfo = $this->functions->getProducts();
                    wp_send_json_success($productsInfo);
                    break;
                case 'getTaxonomies': // get taxonomies
                    $termtaxonomies = $this->functions->getWpTermTaxonomies();
                    wp_send_json_success($termtaxonomies);
                    break;
                case 'updateProduct': // update product
                    $this->functions->updateProduct($_POST['id'], $_POST['product']);
                    wp_send_json_success();
            }
        } else {
            wp_send_json_error('there is no command');
        }
    }

    public static function ajaxNonce() {
        ?> <!-- wordpress nounce -->
        <input id="security" type="hidden" name="security" value="<?= wp_create_nonce("bulk_actions_nonce") ?>">
        <input id="action" type="hidden" name="action" value="bulk_actions">
        <!-- referral url -->
        <input id="referralUrl" type="hidden" name="referralUrl" value="<?= admin_url("admin-ajax.php"); ?>"> <?php
    }
}
