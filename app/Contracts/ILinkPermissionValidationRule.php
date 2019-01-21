<?php

namespace TeaLinkProtection\App\Contracts;

interface ILinkPermissionValidationRule {
    public function apply($link_rule);
}