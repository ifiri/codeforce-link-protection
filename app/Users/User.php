<?php

namespace TeaLinkProtection\App\Users;

class User {
    private $is_user_logged_in = false;

    private $id = null;
    private $data = null;

    public function __construct() {
        if(is_user_logged_in()) {
            $this->is_user_logged_in = true;

            $this->id = get_current_user_id();
            $this->data = get_userdata($this->id);
        }
    }

    public function is_user_logged_in() {
        return $this->is_user_logged_in;
    }

    public function get_user_data() {
        return $this->data;
    }

    public function get_user_id() {
        return $this->id;
    }

    public function get_user_roles() {
        $user_data = $this->get_user_data();

        return $user_data->roles;
    }

    public function get_user_capabilities() {
        $user_data = $this->get_user_data();

        $capabilities = $user_data->caps;

        return array_map(function($name, $value) {
            if($value) {
                return $name;
            }

            return $value;
        }, array_keys($capabilities), array_values($capabilities));
    }

    public function get_user_ip() {
        $user_ip_addresses = explode(',', $_SERVER['REMOTE_ADDR']);
        $user_ip_address = end($user_ip_addresses);

        return $user_ip_address;
    }

    public function get_session_params() {
        $params = [];

        if($this->is_user_logged_in()) {
            // todo в константы, в бд сделать enum
            $params['ROLE_RESTRICT'] = $this->get_user_roles();
            $params['USER_ID'] = $this->get_user_id();
        }

        $params['IP_RESTRICT'] = $this->get_user_ip();
        $params['SESSION'] = session_id();

        return $params;
    }
}