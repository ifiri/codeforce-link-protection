<?php

namespace TeaLinkProtection\App\AdminInterface\Partials;

use TeaLinkProtection\App\Templates;
use TeaLinkProtection\App\Libraries;
use TeaLinkProtection\App\Config;
use TeaLinkProtection\App\Contracts;

class Partial  { // implements Contracts\IPartial
    private $partial_params = [];

    public function __construct(Array $partial_params, Array $layout_variables) {
        $this->partial_params = $partial_params;
        $this->layout_variables = $layout_variables;
    }

    public function get_alias() {
        return isset($this->partial_params['alias']) ? $this->partial_params['alias'] : null;
    }

    public function get_layout_path() {
        return isset($this->partial_params['layout_path']) ? $this->partial_params['layout_path'] : null;
    }

    public function display() {
        // depincly
        $ConfigRepository = new Config\Repository;
        $TemplatesRenderer = new Templates\Renderer($ConfigRepository);

        $alias = $this->get_alias();
        $layout_path = $this->get_layout_path();

        $layout_name = Libraries\Utils::get_template_name_by_alias($alias);
        
        $layout_params = array_merge([
            'alias' => $alias,
        ], $this->partial_params, $this->layout_variables);

        $TemplatesRenderer->render($layout_path . '/' . $layout_name, $layout_params);
    }
}