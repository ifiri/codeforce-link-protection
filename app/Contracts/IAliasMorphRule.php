<?php

namespace TeaLinkProtection\App\Contracts;

interface IAliasMorphRule {
    public function __construct($link_id);
    public function is_morph_required($rule_value, $user_data);
}