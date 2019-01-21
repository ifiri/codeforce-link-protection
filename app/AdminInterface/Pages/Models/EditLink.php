<?php

namespace TeaLinkProtection\App\AdminInterface\Pages\Models;

use TeaLinkProtection\App\Links;
use TeaLinkProtection\App\Exceptions;
use TeaLinkProtection\App\Config;
use TeaLinkProtection\App\Contracts;

class EditLink implements Contracts\IModel {
    public function get_page_params() {
        return [
            'menu_title' => __('Edit Link', 'tea-link-protection'),
            'page_title' => __('Tea Link Protection \\ Edit Link', 'tea-link-protection'),

            'slug' => 'tlp-edit-link',
            'parent' => 'tlp-links',

            'capability' => 'edit_dashboard',

            'in_menu' => false,
        ];
    }

    public function get_current_link_data_by(Contracts\IRequest $Request) {
        if(!$Request->id) {
            throw new Exceptions\IncorrectRequestException('Incorrect Request');
        }

        $ConfigRepository = new Config\Repository;
        $LinksRepository = new Links\Repository($ConfigRepository);

        $link_data = $LinksRepository->get_by($Request->id);
        
        return $link_data;
    }
}