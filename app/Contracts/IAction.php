<?php

namespace TeaLinkProtection\App\Contracts;

interface IAction {
    public function execute(IRequest $Request);
}