<?php

namespace TeaLinkProtection\App\Links\Aliases;

use TeaLinkProtection\App\Libraries;
use TeaLinkProtection\App\Users;
use TeaLinkProtection\App\Config;
use TeaLinkProtection\App\Contracts;
// use TeaLinkProtection\App\Log;

class Morpher {
    private $rules = [];
    private $link_rules = [];
    private $alias_params = [];

    public function __construct($Link) {
        $CurrentUser = new Users\User;

        $this->link_rules = $Link->get_rules();

        // depincly
        $MorphRulesFactory = new MorphRulesFactory;
        foreach ($this->link_rules as $rule_alias => $rule_value) {
            $MorphRule = $MorphRulesFactory->create_rule_by($Link, $rule_alias);
            $this->rules[$rule_alias] = $MorphRule;
        }
    }

    public function get_morphed_alias_for($link_id) {
        // depincly
        $ConfigRepository = new Config\Repository;
        $AliasRepository = new Repository($ConfigRepository);

        do {
            $alias = Libraries\Utils::generate_alias();
        } while($AliasRepository->is_alias_exists($alias));

        return $alias;
    }

    public function is_morph_required($Alias = null) {
        $AliasParamsRepository = new Params\Repository;
        $alias_params = $AliasParamsRepository->get($Alias);

        $link_rules = $this->link_rules;

        $is_morph_required = false;
        foreach ($this->rules as $rule_alias => $MorphRule) {
            if($MorphRule->is_morph_required($link_rules[$rule_alias], $alias_params)) {
                $is_morph_required = true;
                break;
            }
        }

        return $is_morph_required;
    }
}