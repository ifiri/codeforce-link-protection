<?php

namespace TeaLinkProtection\App\Libraries;

use TeaLinkProtection\App\Contracts;

class Utils {
    public static function is_mvc_components_exists($module_name, $namespace = null) {
        $mvc_parts_namespaces = ['Controllers', 'Models', 'Views'];

        foreach ($mvc_parts_namespaces as $mvc_part_namespace) {
            $absolute_module_namespace = $namespace . '\\' . $mvc_part_namespace;

            if(!Utils::is_class_exists($module_name, $absolute_module_namespace)) {
                return false;
            }
        }

        return true;
    }

    public static function is_class_exists($classname, $namespace = null) {
        $class_prefix = $classname;

        if($namespace) {
            $class_prefix = $namespace . '\\' . $classname;
        }

        return class_exists($class_prefix);
    }

    public static function get_classname_by_alias($alias) {
        $alias = preg_replace('/[-_\s]+/', '%%', $alias);
        $alias_pieces = explode('%%', $alias);

        foreach ($alias_pieces as &$piece) {
            $piece = ucfirst(strtolower(trim($piece)));
        }

        return implode('', $alias_pieces);
    }

    public static function get_template_name_by_alias($alias) {
        $alias_pieces = explode('_', $alias);

        foreach ($alias_pieces as &$piece) {
            $piece = strtolower(trim($piece, '_'));
        }

        return implode('-', $alias_pieces);
    }

    public static function get_current_gmt_datetime() {
        // Note: GMT and Timezone depends on Wordpress settings!
        $gmt_offset = get_option('gmt_offset');
        
        $current_timestamp = time();
        if($gmt_offset) {
            $current_timestamp += 3600 * $gmt_offset;
        }

        return date('Y-m-d H:i:s', $current_timestamp);
    }

    public static function get_current_utc_datetime() {
        $DateTime = new \DateTime();
        $DateTime->setTimezone(new \DateTimeZone('UTC'));

        return $DateTime->format('Y-m-d H:i:s');
    }

    public static function get_class_shortname($Class) {
        if(!is_object($Class)) {
            return null;
        }

        $Reflection = new \ReflectionClass($Class);

        return $Reflection->getShortName();
    }

    public static function verify_nonce_for($nonce_alias, Contracts\IRequest $Request) {
        $nonce = $Request->_wpnonce;
        
        if($nonce && wp_verify_nonce($nonce, $nonce_alias)) {
            return true;
        }

        return false;
    }

    public static function generate_alias() {
        $symbol_set = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        $symbol_set_length = strlen($symbol_set);

        $max_alias_length = 22;
        $current_alias_length = 0;

        $alias = '';
        while ($current_alias_length < $max_alias_length) {
            $symbol_number = (int)round(rand(0, $symbol_set_length - 1));

            $alias .= $symbol_set[$symbol_number];

            $current_alias_length++;
        }

        return $alias;
    }
}