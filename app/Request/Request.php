<?php

namespace TeaLinkProtection\App\Request;

use TeaLinkProtection\App\Config;
use TeaLinkProtection\App\Libraries;
use TeaLinkProtection\App\Contracts;

class Request implements Contracts\IRequest, \IteratorAggregate {
    private $_request = null;

    public function __construct() {
        $this->_request = array_merge($_GET, $_POST);
    }

    public function __get($name) {
        return $this->get($name);
    }

    public function get($name, $default = null) {
        if(isset($this->_request[$name])) {
            return $this->_request[$name];
        }

        return $default;
    }

    public function is_executable() {
        $supported_actions = $this->get_supported_actions();
        
        if($this->action && in_array($this->action, $supported_actions)) {
            return true;
        }

        return false;
    }

    private function get_supported_actions() {
        // depincly
        $ConfigRepository = new Config\Repository;
        
        return $ConfigRepository->get('supported-actions');
    }

    // IteratorAggregate implementation
    public function getIterator() {
        $RequestIterator = new \ArrayIterator($this->_request);

        return $RequestIterator;
    }
}