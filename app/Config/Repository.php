<?php

namespace TeaLinkProtection\App\Config;

use TeaLinkProtection\App\Contracts;

class Repository implements Contracts\IConfigRepository {
    private static $_config = [];

    public function save(Array $config) {
        self::$_config = $config;
    }

    /**
     * @param string $params Dot-separated path to needly parameter
     * 
     * @return mixed|null
     */
    public function get($param) {
        $parsed_param = explode('.', $param);

        $result = $this->get_param_from(self::$_config, $parsed_param);

        return $result;
    }

    /**
     * @param Array $config 
     * @param Array $parsed_param 
     * 
     * @return mixed|null
     */
    private function get_param_from(Array $config, Array $parsed_param) {
        $result = null;

        $current_stack = $config;
        for ($i = 0, $param_length = count($parsed_param); $i <= $param_length; $i++) {
            if($this->is_parsed_param_in_config($current_stack, $parsed_param, $i)) {
                
                $current_stack = $current_stack[$parsed_param[$i]];
                continue;
            
            } elseif($this->is_last_piece_in_parsed_params($param_length, $i)) {
                $result = $current_stack;
            }

            break;
        }

        return $result;
    }

    /**
     * @param int $param_length 
     * @param int $i 
     * 
     * @return boolean
     */
    private function is_last_piece_in_parsed_params($param_length, $i) {
        return $i === $param_length;
    }

    /**
     * @param mixed $stack 
     * @param array $parsed_param 
     * @param int $i 
     * 
     * @return boolean
     */
    private function is_parsed_param_in_config($stack, Array $parsed_param, $i) {
        return is_array($stack) && isset($parsed_param[$i]) && isset($stack[$parsed_param[$i]]);
    }
}