<?php

namespace TeaLinkProtection\App\AdminInterface\Pages\Views;

use TeaLinkProtection\App\Templates;
use TeaLinkProtection\App\Request;

class EditLink extends BaseView {
    private $template = 'create-and-edit-link';

    public function display() {
        // depincly
        $Request = new Request\Request;
        $TemplatesRenderer = new Templates\Renderer($this->ConfigRepository);

        $rules = $this->ConfigRepository->get('link.rules');
        $types = $this->ConfigRepository->get('link.types');

        $link_data = $this->Model->get_current_link_data_by($Request);

        // Prepare Partial views
        $rule_views = $this->get_partial_views_for($rules, 'link-rules', [
            'link_data' => $link_data,
            'roles' => get_editable_roles(),
            'rules' => $link_data['rules'],
        ]);
        $type_views = $this->get_partial_views_for($types, 'link-types', [
            'is_common' => $link_data['type'] === 'common',
            'is_attachment' => $link_data['type'] === 'attachment',
        ]);

        $type_chain_segments = $this->get_partial_views_for($types, 'link-types\chain-segments', [
            'link_data' => $link_data,
            'is_new_link' => false,
            'is_common' => $link_data['type'] === 'common',
            'is_attachment' => $link_data['type'] === 'attachment',
        ]);

        // Render main template
        $TemplatesRenderer->render($this->template, [
            'rule_views' => $rule_views,
            'type_views' => $type_views,
            
            'type_chain_segments' => $type_chain_segments,

            'link_data' => $link_data,
            'is_new_link' => false,
        ]);
    }
}