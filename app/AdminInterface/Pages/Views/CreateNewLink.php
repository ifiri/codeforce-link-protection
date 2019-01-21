<?php

namespace TeaLinkProtection\App\AdminInterface\Pages\Views;

use TeaLinkProtection\App\Templates;

class CreateNewLink extends BaseView {
    private $template = 'create-and-edit-link';

    public function display() {
        // depincly
        $TemplatesRenderer = new Templates\Renderer($this->ConfigRepository);

        $rules = $this->ConfigRepository->get('link.rules');
        $types = $this->ConfigRepository->get('link.types');

        // Prepare Partial views
        $rule_views = $this->get_partial_views_for($rules, 'link-rules', [
            'roles' => get_editable_roles(),
            'rules' => [],
        ]);
        $type_views = $this->get_partial_views_for($types, 'link-types', [
            'is_common' => false,
            'is_attachment' => false,
        ]);

        $type_chain_segments = $this->get_partial_views_for($types, 'link-types\chain-segments', [
            'is_common' => false,
            'is_attachment' => false,

            'is_new_link' => true,
        ]);

        // Render main template
        $TemplatesRenderer->render($this->template, [
            'rule_views' => $rule_views,
            'type_views' => $type_views,
            
            'type_chain_segments' => $type_chain_segments,

            'is_new_link' => true,
        ]);
    }
}