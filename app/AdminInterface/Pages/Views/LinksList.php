<?php

namespace TeaLinkProtection\App\AdminInterface\Pages\Views;

use TeaLinkProtection\App\Request;
use TeaLinkProtection\App\Templates;

class LinksList extends BaseView {
    private $template = 'links-list';

    public function display() {
        // depincly
        $TemplatesRenderer = new Templates\Renderer($this->ConfigRepository);

        $TemplatesRenderer->render($this->template, [
            'ui_elements' => $this->ui_elements,
        ]);
    }
}