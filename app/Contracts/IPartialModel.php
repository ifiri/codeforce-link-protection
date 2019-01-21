<?php

namespace TeaLinkProtection\App\Contracts;

interface IPartialModel {
    public function get_layout_params_for(IPartial $Partial);
}