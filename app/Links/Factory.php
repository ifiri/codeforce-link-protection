<?php

namespace TeaLinkProtection\App\Links;

class Factory {
    public function create_by($link_id) {
        $Link = new Link($link_id);

        return $Link;
    }
}