<?php

namespace TeaLinkProtection\App\Links\Aliases\MorphRules;

use TeaLinkProtection\App\Links\Aliases;
use TeaLinkProtection\App\Users;
use TeaLinkProtection\App\Config;
use TeaLinkProtection\App\Contracts;

class IpRestrict implements Contracts\IAliasMorphRule {
    private $link_id = null;
    
    public function __construct($Link) {
        $this->link_id = $Link->get_id();
    }

    public function is_morph_required($rule_value, $user_data) {
        $CurrentUser = new Users\User;
        $user_ip_address = $CurrentUser->get_user_ip();

        if($rule_value && count($rule_value) && $user_ip_address) {
            // depincly
            $ConfigRepository = new Config\Repository;
            $AliasRepository = new Aliases\Repository($ConfigRepository);

            $result = $this->check_for_existing_alias_by($user_ip_address);

            return !$result;
        }

        return false;
    }

    private function check_for_existing_alias_by($user_ip) {
        // todo depincly
        $ConfigRepository = new Config\Repository;
        $AliasesRepository = new Aliases\Repository($ConfigRepository);

        $link_id = $this->link_id;

        $is_alias_exists = $AliasesRepository->is_alias_for_param_exists($link_id, 'IP_RESTRICT', $user_ip);

        return $is_alias_exists;
    }
}