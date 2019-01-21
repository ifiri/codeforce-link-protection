<?php

namespace TeaLinkProtection\App\Contracts;

use TeaLinkProtection\App\Request;

interface IController {
    public function __construct(IModel $Model, IRequestHandler $RequestHandler);
    public function set_view(IView $View);
    public function register_page();
    public function execute();
}