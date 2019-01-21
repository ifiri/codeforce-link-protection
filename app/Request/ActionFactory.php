<?php

namespace TeaLinkProtection\App\Request;

use TeaLinkProtection\App\Libraries;
use TeaLinkProtection\App\Contracts;

class ActionFactory {
    public function create_action_by($action_alias, Contracts\IController $Controller) {
        $action_callable = $this->get_action_absolute_classname_by($action_alias);

        if($action_callable && class_exists($action_callable)) {
            $Action = new $action_callable($Controller);

            return $Action;
        }

        return null;
    }

    private function get_action_absolute_classname_by($action_alias) {
        $action_namespace_prefix = __NAMESPACE__ . '\\Actions';

        $action_classname = Libraries\Utils::get_classname_by_alias($action_alias);

        if(Libraries\Utils::is_class_exists($action_classname, $action_namespace_prefix)) {
            $action_callable = $action_namespace_prefix . '\\' . $action_classname;

            return $action_callable;
        }

        return null;
    }
}