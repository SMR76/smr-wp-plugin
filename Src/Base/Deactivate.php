<?php
/**
 * @package smr plugin
 */

namespace Src\Base;

class Deactivate {
    public static function deactivate() {
        flush_rewrite_rules();
    }
}