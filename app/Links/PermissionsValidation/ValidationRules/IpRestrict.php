<?php

namespace TeaLinkProtection\App\Links\PermissionsValidation\ValidationRules;

use TeaLinkProtection\App\Config;
use TeaLinkProtection\App\Users;
use TeaLinkProtection\App\Contracts;

class IpRestrict implements Contracts\ILinkPermissionValidationRule {
    public function apply($ip_addresses) {
        if(!is_array($ip_addresses)) {
            return false;
        }
        
        $CurrentUser = new Users\User;
        $user_ip_address = $CurrentUser->get_user_ip();

        foreach ($ip_addresses as $ip_address) {
            if($ip_address === $user_ip_address) {
                return true;
            } else {
                $user_ip_address_parts = explode('.', $user_ip_address);
                $current_ip_address_parts = explode('.', $ip_address);

                if(count($user_ip_address_parts) !== count($current_ip_address_parts)) {
                    continue;
                }

                $matched_parts_count = 0;
                foreach ($current_ip_address_parts as $index => $ip_part) {
                    if($ip_part === '*') {
                        $matched_parts_count++;
                        continue;
                    }

                    if($ip_part === $user_ip_address_parts[$index]) {
                        $matched_parts_count++;
                    }
                }

                if($matched_parts_count === count($current_ip_address_parts)) {
                    return true;
                }
            }
        }

        return false;
    }
}