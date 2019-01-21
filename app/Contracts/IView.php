<?php

namespace TeaLinkProtection\App\Contracts;

interface IView {
    public function __construct(IModel $Model, IController $Controller);
    public function display();
}