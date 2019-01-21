<?php

namespace TeaLinkProtection\App\Shortcodes;

use TeaLinkProtection\App\Log;
use TeaLinkProtection\App\Config;
use TeaLinkProtection\App\Links;
use TeaLinkProtection\App\Contracts;

class Link {
    public function __construct() {
        // ...
    }

    public function shortcode(Array $user_attrs, $shortcode_content = null) {
        if(!isset($user_attrs['id'])) {
            // todo throw exception
        }

        // we need prevent alias creation when user updating post
        // todo возможно, вынести в другое место
        if(is_admin()) {
            return;
        }

        $link_id = $user_attrs['id'];

        // depincly
        $LinksFactory = new Links\Factory;

        $Link = $LinksFactory->create_by($link_id);
        $alias = $Link->get_actual_alias_for_current_user();

        return '<a href="/' . $alias  . '">' . $alias . '</a>';
    }
}