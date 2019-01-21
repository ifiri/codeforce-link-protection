<?php

namespace TeaLinkProtection\App\Exceptions;

use TeaLinkProtection\App\Contracts;

class BaseException extends \Exception implements Contracts\IException {
    public function get_error_message() {
        $message = 'Error on line ' . $this->getLine() . ' in ' . $this->getFile()
         . ' with message "' . $this->getMessage() . '" and code ' . $this->getCode();

        return $message;
    }
}