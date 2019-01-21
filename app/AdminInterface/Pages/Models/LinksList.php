<?php

namespace TeaLinkProtection\App\AdminInterface\Pages\Models;

use TeaLinkProtection\App\Contracts;

class LinksList implements Contracts\IModel {
    public function get_page_params() {
        return [
            'menu_title' => __('Links List', 'tea-link-protection'),
            'page_title' => __('Tea Link Protection \\ Links List', 'tea-link-protection'),

            'slug' => 'tlp-links',
            'parent' => 'tlp-links',

            'capability' => 'edit_dashboard',

            'in_menu' => true,
        ];
    }
}