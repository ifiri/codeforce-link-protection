<?php

namespace TeaLinkProtection\App\Links\Aliases\Params;

use TeaLinkProtection\App\Log;
use TeaLinkProtection\App\Users;
use TeaLinkProtection\App\Libraries;
use TeaLinkProtection\App\Config;
use TeaLinkProtection\App\Exceptions;
use TeaLinkProtection\App\Contracts;

class Repository {
    public function get($Alias = null) {
        $CurrentUser = new Users\User;

        $user_params = $CurrentUser->get_session_params();

        return array_merge($user_params, [
            'TRIGGER_COUNT' => 0 // todo брать default из конфига или еще откуда-нибудь
        ]);
    }

    public function get_actual_params_by(Array $link_rules, $Alias = null) {
        $alias_params = $this->get($Alias);

        $result_set = [];
        foreach ($link_rules as $rule_name => $rule_value) {
            if(isset($alias_params[$rule_name])) {
                if(is_array($alias_params[$rule_name]) && is_array($rule_value)) {
                    $intersect_for_current_param = array_intersect($alias_params[$rule_name], $rule_value);

                    $result_set[$rule_name] = $intersect_for_current_param;
                } elseif($alias_params[$rule_name] === $rule_value) {
                    $result_set[$rule_name] = $rule_value;
                } else {
                    if($rule_name === 'IP_RESTRICT') {
                        $result_set[$rule_name] = $alias_params[$rule_name];
                    }
                }
            }
        }

        return $result_set;
    }
}