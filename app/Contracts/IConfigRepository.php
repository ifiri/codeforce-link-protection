<?php

namespace TeaLinkProtection\App\Contracts;

interface IConfigRepository {
    public function save(Array $config);
    public function get($param);
}