<?php

namespace TeaLinkProtection\App\Contracts;

interface IRequest {
    public function __get($name);
    public function get($name);
}