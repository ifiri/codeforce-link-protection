<?php

namespace TeaLinkProtection\App\Links;

use TeaLinkProtection\App\Links\Aliases;
use TeaLinkProtection\App\Links\PermissionsValidation;
use TeaLinkProtection\App\Config;
use TeaLinkProtection\App\Contracts;

class Link {
    private $id = null;
    private $data = [];
    private $rules = [];
    private $Alias = null;

    public function get_rules() {
        return $this->rules;
    }

    public function get_id() {
        return $this->id;
    }

    public function __construct($link_id) {
        // depincly
        $ConfigRepository = new Config\Repository;
        $LinksRepository = new Repository($ConfigRepository);
        
        // todo вынести в фабрику получение данных
        $this->id = $link_id;
        $this->data = $LinksRepository->get_by($link_id);

        if(isset($this->data['rules'])) {
            $this->rules = $this->data['rules'];
        }
    }

    // todo вызывать автоматически, сущность ссылки не должна создавать алиас
    public function get_actual_alias_for_current_user() {
        // depincly
        $AliasesFactory = new Aliases\Factory;
        $PermissionsValidator = new PermissionsValidation\Validator;

        $is_alias_permitted = $PermissionsValidator->is_current_user_can_see_alias($this->rules);

        if($is_alias_permitted) {
            if(!$this->Alias) {
                $this->Alias = $AliasesFactory->create_for($this);
            }

            return $this->Alias->get_address();
        }
        
        return null;
    }
}