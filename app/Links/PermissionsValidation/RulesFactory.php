<?php

namespace TeaLinkProtection\App\Links\PermissionsValidation;

use TeaLinkProtection\App\Libraries;
use TeaLinkProtection\App\Config;
use TeaLinkProtection\App\Contracts;

class RulesFactory {
    public function __construct() {
        // depincly
        // $ConfigRepository = new Config\Repository;
    }

    public function create_rule_by($rule_alias) {
        $rule_classname = Libraries\Utils::get_classname_by_alias($rule_alias);
        $rule_callable = __NAMESPACE__ . '\\ValidationRules\\' . $rule_classname;

        if(class_exists($rule_callable)) {
            return new $rule_callable;
        }

        return null;
    }
}