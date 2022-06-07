<?php

/**
 * @package smr plugin
 */

use Automattic\WooCommerce\Admin\API\Products;
use \Src\Pages\BulkActionsAjax;

?>
<div class="wrap">
    <h3>Extra Bulk Actions </h3>

    <?php
        wp_enqueue_script("multiselect-dropdown", $this->pluginUrl . '/assets/js/multiselect-dropdown.js');
        wp_enqueue_script("simple-tag-input", $this->pluginUrl . '/assets/js/simple-tag-input.js');
        wp_enqueue_script("simple-markdown", $this->pluginUrl . '/assets/js/simple-markdown.js');
        wp_enqueue_script("bulk-actions", $this->pluginUrl . '/assets/js/bulk-actions-page.js');

        wp_enqueue_style("simple-tag-input", $this->pluginUrl . '/assets/css/simple-tag-input.css');
        wp_enqueue_style("simple-markdown", $this->pluginUrl . '/assets/css/simple-markdown.css');
        wp_enqueue_style("bootstrap-grid", $this->pluginUrl . '/assets/css/bootstrap-grid.css');
        wp_enqueue_style("bulk-actions", $this->pluginUrl . '/assets/css/bulk-actions-page.css');
        wp_enqueue_style("loader", $this->pluginUrl . '/assets/css/loader.css');

        BulkActionsAjax::ajaxNonce();
    ?>

    <div id="mainGrid" class="container">
        <div class="row code ltr pb-5">
            <div class="col-1 p-0 pr-5">Variables:</div>
            <div id="variables" class="col-11 flex-wrap">
                <label class="var" value="id">id(num)</label>
                <label class="var" value="url">url(str)</label>
                <label class="var" value="name">name(str)</label>
                <label class="var" value="regularPrice">regularPrice(num)</label>
                <label class="var" value="salePrice">salePrice(num)</label>
                <label class="var" value="taxonomies">taxonomies(array)</label>
            </div>
        </div>

        <div class="row code ltr pb-5">
            <div class="col-1 p-0 pr-5">Taxonomies:</div>
            <div id="taxonomies" class="col-11  flex-wrap">
            </div>
        </div>

        <div id="actions" class="row ltr">
            <div class="col-2 col-md-1 p-0"><button class="btn" id="send">send</button></div>
            <div class="col-2 col-md-1 p-0"><button class="btn" id="clear">clear</button></div>
            <div class="col-2 col-md-1 p-0"><button class="btn" id="refresh">refresh</button></div>
        </div>
        <div class="row ltr pb-8">
            <div class="col-12 p-0"><textarea id="querybox" class="code" disabled></textarea></div>
        </div>

        <div class="row ltr pb-8"><div id="status" class="code"></div></div>

        <div class="row">
            <div class="col-1">
                <input id="checkall" type="checkbox" alt="check all">
            </div>
            <div class="col-11">
                <div class="row">
                    <div class="col-1">#</div>
                    <div class="col-4">Name</div>
                    <div class="col-2">By</div>
                    <div class="col-2">Price</div>
                    <div class="col-3">Taxonomies</div>
                </div>
            </div>
        </div>

        <div id="productsList" class="container-fulid">
            <div class="row"><div class="loader" style="margin: 10px auto;"></div></div>
        </div>
    </div>
</div>