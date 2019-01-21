<?php

namespace TeaLinkProtection\App\Links\PermissionsValidation\ValidationRules;

use TeaLinkProtection\App\Config;
use TeaLinkProtection\App\Contracts;

class RoleRestrict implements Contracts\ILinkPermissionValidationRule {
    public function apply($allowable_roles) {
        if(!is_user_logged_in() || !is_array($allowable_roles)) {
            return false;
        }

        $is_user_have_allowable_role = false;
        foreach ($allowable_roles as $role) {
            if(current_user_can($role)) {
                $is_user_have_allowable_role = true;

                break;
            }
        }

        if($is_user_have_allowable_role) {
            return true;
        }
    }
}