<?php

namespace TeaLinkProtection\App\Config;

use TeaLinkProtection\App\Contracts;

class Loader implements Contracts\IConfigLoader {
    private $Repository = null;

    public function __construct(Contracts\IConfigRepository $Repository) {
        $this->Repository = $Repository;
    }

    public function load_in_repository() {
        $config = require_once(\TeaLinkProtection\PLUGIN_PATH . '/config.php');

        if(is_array($config)) {
            $this->Repository->save($config);
        }
    }
}