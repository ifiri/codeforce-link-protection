<?php

namespace TeaLinkProtection\App\Contracts;

use TeaLinkProtection\App\Request;

interface ITemplatesRenderer {
    public function __construct(IConfigRepository $Config);
    public function render($template_name, Array $params = []);
    public function get_template_path_by($template_name);
}