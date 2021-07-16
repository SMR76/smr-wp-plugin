<?php
/**
 * @package smr plugin
 */

namespace Src\Base;

class Activate {
    public static function activate() {
        flush_rewrite_rules();
    }
}