<?php

namespace TeaLinkProtection\App\Request;

use TeaLinkProtection\App\Libraries;
use TeaLinkProtection\App\Contracts;

class Handler implements Contracts\IRequestHandler {
    private $Request = null;

    public function __construct(Contracts\IRequest $Request) {
        $this->Request = $Request;
    }

    public function handle_controller_request(Contracts\IController $Controller) {
        // depincly
        $RequestExecuter = new Executer($this->Request);

        if($this->Request->is_executable()) {
            $RequestExecuter->execute_controller_action($Controller);
        }
    }
}