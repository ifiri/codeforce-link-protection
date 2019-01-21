<?php

namespace TeaLinkProtection\App\Links\PermissionsValidation;

use TeaLinkProtection\App\Links;
use TeaLinkProtection\App\Config;
use TeaLinkProtection\App\Contracts;

class Validator {
    public function __construct() {
        // depincly
        // $ConfigRepository = new Config\Repository;
    }

    public function is_current_user_can_see_alias(Array $link_rules) {
        // depincly
        $ValidationRulesFactory = new RulesFactory;

        $is_current_user_can_see_link = true;
        foreach ($link_rules as $rule_alias => $rule_value) {
            $ValidationRule = $ValidationRulesFactory->create_rule_by($rule_alias);
            
            if(!$ValidationRule->apply($rule_value)) {
                $is_current_user_can_see_link = false;
                break;
            }
        }

        return $is_current_user_can_see_link;
    }

    public function is_current_user_can_execute_alias($alias) {
        // depincly
        $ConfigRepository = new Config\Repository;
        $AliasRepository = new Links\Aliases\Repository($ConfigRepository);
        $LinksFactory = new Links\Factory;

        $alias_data = $AliasRepository->get_params_by($alias);
        $link_id = $alias_data['link_id'];

        $Link = $LinksFactory->create_by($link_id);

        if($this->is_current_user_can_see_alias($Link->get_rules())) {
            return true;
        }

        return false;
    }
}