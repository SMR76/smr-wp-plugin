<?php
/**
 * @package smr plugin
 */

namespace Src\Pages;

use \Src\Base\BaseController;

class BulkActionsFunctions extends BaseController {
    private $cssDirection = "ltr";

    public function __construct() {
        parent::__construct();
        $this->cssDirection = is_rtl() ? "rtl" : "ltr";
    }

    public function bulkActionsSubmenuPage() {
        return require_once($this->pluginPath."/templates/bulkActionsPage.php");
    }

    public function optionGroupFieldsFilter($inputs) {
        return $inputs;
    }

    static public function arrayToAssociativeArray($array, $key) {
        $associativeArray = array();
        foreach ($array as $value) {
            $associativeArray[$value->$key] = array_filter((array)$value, function ($k) use ($key) {
                return $k !== $key;
            }, ARRAY_FILTER_USE_KEY);
        }
        return $associativeArray;
    }

    function updateProductPrice($productId, $regularPrice, $salePrice) {
        $product = wc_get_product($productId);
        $product->set_price($regularPrice);
        $product->set_regular_price($regularPrice);
        $product->set_sale_price($salePrice);
        $product->save();
    }

    // add attributes to product. e.g. (1, 123)
    function addProductAttribute(int $productId, $termsId) {
        $termTaxonomies = $this->getWpTermTaxonomies();
        $attributesMeta = [];

        wp_set_object_terms($productId, null, "");

        foreach($termsId as $idx => $tid) {
            if(isset($termTaxonomies[$tid])) {
                $termTaxonomy = $termTaxonomies[$tid];

                wp_set_object_terms($productId, intval($tid), $termTaxonomy['taxonomy'], $idx === 0 ? false : true);

                $attributesMeta[$termTaxonomy['taxonomy']] = [
                    'name' => $termTaxonomy['taxonomy'],
                    'value' => '',
                    'position' => 0,
                    'is_visible' => 1,
                    'is_variation' => 0,
                    'is_taxonomy' => 1,
                ];
            }
        }
        update_post_meta($productId, '_product_attributes', $attributesMeta);
    }

    // update product data e.g. (1, ['regular_price' => 100, 'price' => 80, ...])
    function updateProduct($id, $product) {
        if(isset($product['regular_price'], $product['price'])) {
            $this->updateProductPrice($id, $product['regular_price'], $product['price']);
        }

        if(isset($product['taxonomies'])) {
            $this->addProductAttribute($id, $product['taxonomies']);
        }
    }

    function getProducts() {
        $productsInfoArray = $this->getProductsInfoDbArray();
        $productsPriceArray = $this->getProductsPriceDbArray();
        $productsTaxonomyArray = $this->getProductsTermTaxonomiesDbArray();
        $productsInfo = $this->arrayToAssociativeArray($productsInfoArray, 'id');

        // add product prices to productsInfo
        foreach ($productsPriceArray as $productPrice) {
            if (isset($productsInfo[$productPrice->id])) {
                $productsInfo[$productPrice->id][substr($productPrice->meta_key, 1)] = $productPrice->meta_value;
            }
        }

        // add product term taxonomies to productsInfo
        foreach ($productsTaxonomyArray as $productsTT) {
            if (isset($productsInfo[$productsTT->id])) {
                $productsInfo[$productsTT->id]['taxonomies'][] = $productsTT->term_id;
            }
        }

        return $productsInfo;
    }

    static function getWpTermTaxonomies() {
        return BulkActionsFunctions::arrayToAssociativeArray(BulkActionsFunctions::getWpTermTaxonomiesDbArray(), 'term_id');
    }

    static function getProductsInfoDbArray(): ?array {
        global $wpdb;
        return $wpdb->get_results("SELECT `id`,post_author,post_name,`guid` FROM $wpdb->posts
                                    WHERE post_type = 'product' AND post_status = 'publish' ORDER BY `id`");
    }

    static function getProductsPriceDbArray(): ?array {
        global $wpdb;
        return $wpdb->get_results("SELECT wp.id,wpm.meta_key,wpm.meta_value
                                    FROM $wpdb->posts as wp JOIN $wpdb->postmeta as wpm ON wp.ID = wpm.post_id
                                        WHERE wp.post_type = 'product'
                                            AND wp.post_status = 'publish'
                                            AND (wpm.meta_key = '_regular_price' OR wpm.meta_key = '_price')  ORDER BY wp.id");

    }

    static function getProductsTermTaxonomiesDbArray(): ?array {
        global $wpdb;
        return $wpdb->get_results("SELECT wtr.object_id as `id`, wtt.term_id
                                   FROM wp_term_taxonomy as wtt JOIN wp_term_relationships as wtr ON wtt.term_taxonomy_id = wtr.term_taxonomy_id
                                   WHERE wtt.taxonomy LIKE 'pa_%'");
    }

    static function getWpTermTaxonomiesDbArray(): ?array {
        global $wpdb;
        return $wpdb->get_results("SELECT wtt.term_id, wt.name, wt.slug, wtt.taxonomy, wtm.meta_key, wtm.meta_value
                                    FROM wp_term_taxonomy AS wtt JOIN wp_termmeta AS wtm ON wtt.term_id = wtm.term_id
                                    JOIN wp_terms AS wt ON wtt.term_id = wt.term_id
                                    WHERE NOT wtm.meta_value = ''
                                        AND (wtm.meta_key = 'image' OR wtm.meta_key = 'color')
                                        AND wtt.taxonomy LIKE 'pa_%' GROUP BY wtt.term_id");
    }
}

?>