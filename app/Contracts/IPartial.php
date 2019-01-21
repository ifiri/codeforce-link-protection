<?php

namespace TeaLinkProtection\App\Contracts;

interface IPartial {
    public function __construct(Array $params);

    public function get_view();
    
    public function get_alias();
    public function get_title();
    public function get_params();
    public function get_layout_path();
}