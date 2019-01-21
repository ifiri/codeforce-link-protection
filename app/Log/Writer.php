<?php

namespace TeaLinkProtection\App\Log;

use TeaLinkProtection\App\Libraries;
use TeaLinkProtection\App\Request;
use TeaLinkProtection\App\Contracts;

class Writer {
    private $log_file = \TeaLinkProtection\PLUGIN_PATH . '\errors.log';

    private $queue = [];

    const MAX_LOG_FILESIZE = 5096;

    // Catching all exceptions
    public function log($message, $with_request = true) {
        $log_record = $this->generate_log_record($message);

        $this->add_to_queue($log_record);

        if($with_request) {
            $request_state = $this->generate_request_state();

            $this->add_to_queue($request_state);
        }

        return $this->write_from_queue();
    }

    private function generate_log_record($message) {
        $current_datetime = Libraries\Utils::get_current_gmt_datetime();

        return sprintf('[%s] %s', $current_datetime, $message);
    }

    private function generate_request_state() {
        $Request = new Request\Request;

        $request_state = '';
        foreach ($Request as $key => $value) {
            if(is_array($value) || is_object($value)) {
                $value = serialize($value);
            }

            $request_state .= $key . ' = ' . $value;
            $request_state .= ', ';
        }
        $request_state = rtrim($request_state, ', ');

        return sprintf('[%s]: %s', 'Request State', $request_state);
    }

    private function add_to_queue($record) {
        $this->queue[] = $record;
    }

    private function write_from_queue() {
        if($this->is_queue_empty()) {
            return false;
        }

        // $log_filesize = filesize($this->log_file);
        // todo make maximum filesize support

        $log_resource = fopen($this->log_file, 'a');

        foreach ($this->queue as $record) {
            fwrite($log_resource, $record);
            fwrite($log_resource, "\r\n");
        }
        fwrite($log_resource, "\r\n");

        fclose($log_resource);

        return true;
    }

    private function is_queue_empty() {
        return empty($this->queue);
    }
}