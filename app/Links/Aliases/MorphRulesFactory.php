<?php

namespace TeaLinkProtection\App\Links\Aliases;

use TeaLinkProtection\App\Libraries;
use TeaLinkProtection\App\Config;
use TeaLinkProtection\App\Contracts;

class MorphRulesFactory {
    // todo типы
    public function create_rule_by($Link, $rule_alias) {
        $rule_classname = Libraries\Utils::get_classname_by_alias($rule_alias);
        $rule_callable = __NAMESPACE__ . '\\MorphRules\\' . $rule_classname;

        if(class_exists($rule_callable)) {
            return new $rule_callable($Link);
        }

        return null;
    }
}