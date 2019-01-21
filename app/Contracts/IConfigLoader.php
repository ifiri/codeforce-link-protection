<?php

namespace TeaLinkProtection\App\Contracts;

interface IConfigLoader {
    public function __construct(IConfigRepository $Repository);
    public function load_in_repository();
}