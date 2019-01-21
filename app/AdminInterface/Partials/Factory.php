<?php

namespace TeaLinkProtection\App\AdminInterface\Partials;

use TeaLinkProtection\App\Libraries;
use TeaLinkProtection\App\Contracts;

class Factory  { // implements Contracts\IPartialFactory
    public function create_partial_by(Array $partial_params, Array $layout_variables) {
        if(!isset($partial_params['alias']) || !isset($partial_params['layout_path'])) {
            return null;
        }

        $PartialObject = new Partial($partial_params, $layout_variables);

        return $PartialObject;
    }
}