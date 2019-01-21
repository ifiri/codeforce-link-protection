<?php

namespace TeaLinkProtection\App\Contracts;

interface IPartialView {
    public function __construct(IPartialModel $Model);
    public function set_partial(IPartial $Partial);
    public function display();
}