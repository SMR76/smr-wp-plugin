<?php
/**
 * @package smr plugin
 */

 if( ! defined('WP_UNISTALL_PLUGIN')) {
     die;
 }

// clear database data?
//      $book = get_posts( array( 'post_type' => 'book', 'numberposts' => -1));
//      foreach( $books as $book) {
//          wp_delete_post($book -> ID, true);
// }

global $wpdb;

// $wpdb->query("DELETE FROM wp_posts WHERE post_type = 'book'");
// $wpdb->query("DELETE FROM wp_postmeta WHERE post_id NOT IN (SELECT id FROM wp_posts)");
// $wpdb->query("DELETE FROM wp_posts WHERE post_type = 'smrctp'");