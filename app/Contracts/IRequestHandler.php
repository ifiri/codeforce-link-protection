<?php

namespace TeaLinkProtection\App\Contracts;

interface IRequestHandler {
    public function handle_controller_request(IController $Controller);
}