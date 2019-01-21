<?php

namespace TeaLinkProtection\App\AdminInterface\Pages\Models;

use TeaLinkProtection\App\Links;
use TeaLinkProtection\App\Contracts;

class CreateNewLink implements Contracts\IModel {
    public function get_page_params() {
        return [
            'menu_title' => __('Create New', 'tea-link-protection'),
            'page_title' => __('Tea Link Protection \\ Create New', 'tea-link-protection'),

            'slug' => 'tlp-create-new',
            'parent' => 'tlp-links',

            'capability' => 'edit_dashboard',

            'in_menu' => true,
        ];
    }
}