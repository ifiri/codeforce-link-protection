<?php

namespace TeaLinkProtection\App\Links\Aliases\MorphRules;

use TeaLinkProtection\App\Config;
use TeaLinkProtection\App\Links\Aliases;
use TeaLinkProtection\App\Contracts;

class RoleRestrict implements Contracts\IAliasMorphRule {
    private $link_id = null;
    
    public function __construct($Link) {
        $this->link_id = $Link->get_id();
    }

    public function is_morph_required($rule_value, $user_data) {
        // $current_user_id = get_current_user_id();

        if($rule_value && count($rule_value)) {
            // depincly
            $ConfigRepository = new Config\Repository;
            $AliasRepository = new Aliases\Repository($ConfigRepository);

            // $current_user = get_userdata($current_user_id);

            // $roles_intersect = array_intersect($current_user->roles, $rule_value);

            // if(!empty($roles_intersect)) {
                $result = $this->check_for_existing_alias_by($rule_value);
                // $result = $this->check_for_existing_alias_by($roles_intersect);

                return !$result;
            // }
        }

        return false;
    }

    private function check_for_existing_alias_by($user_roles) {
        // todo depincly
        $ConfigRepository = new Config\Repository;
        $AliasesRepository = new Aliases\Repository($ConfigRepository);

        $link_id = $this->link_id;

        foreach($user_roles as $user_role) {
            $is_alias_exists = $AliasesRepository->is_alias_for_param_exists($link_id, 'ROLE_RESTRICT', $user_role);

            if($is_alias_exists) {
                return true;
            }
        }

        return false;
    }
}