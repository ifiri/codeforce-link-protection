<?php

namespace TeaLinkProtection\App\Contracts;

interface IPartialFactory {
    public function create_partial_by(Array $params);
}