<?php

namespace TeaLinkProtection\App\Links\Aliases;

use TeaLinkProtection\App\Libraries;
use TeaLinkProtection\App\Config;
use TeaLinkProtection\App\Users;
use TeaLinkProtection\App\Contracts;

class Alias {
    private $address;
    private $alias_params = [];

    private $trigger_count = 0;

    private $Link = null;

    // todo возможно, вынести это из конструктора куда-то еще
    public function __construct($alias_id) {
        // todo depincly
        $ConfigRepository = new Config\Repository;
        $AliasesRepository = new Repository($ConfigRepository);

        $alias_data = $AliasesRepository->get_alias_by_id($alias_id);
        $alias_params = $AliasesRepository->get_params_by($alias_data['link_alias']);

        $this->id = $alias_id;
        $this->address = $alias_data['link_alias'];
        $this->trigger_count = $alias_params['params']['TRIGGER_COUNT'];
        $this->alias_params = $alias_params['params'];

        // echo 'ALIAS DATA<pre>';
        // var_dump($alias_params);
        // exit;
    }

    public function get_id() {
        return $this->id;
    }

    public function get_address() {
        return $this->address;
    }

    public function get_trigger_count() {
        return $this->trigger_count;
    }
}