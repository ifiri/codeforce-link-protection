<?php

namespace TeaLinkProtection\App\AdminInterface;

use TeaLinkProtection\App\Templates;
use TeaLinkProtection\App\Contracts;

class Notices {
    private static $notices = [];

    private $option_name = 'tlp_stacked_notices';

    private $ConfigRepository = null;
    private $TemplatesRenderer = null;

    public function __construct(Contracts\IConfigRepository $ConfigRepository) {
        $this->ConfigRepository = $ConfigRepository;
        $this->TemplatesRenderer = new Templates\Renderer($ConfigRepository);

        self::$notices = $this->get_stacked_notices();
    }

    private function get_stacked_notices() {
        $stacked_notices = get_option($this->option_name, []);

        if($stacked_notices) {
            $stacked_notices = unserialize($stacked_notices);
        }

        return $stacked_notices;
    }

    public function add_notice_to_stack($notice_alias) {
        self::$notices[] = $notice_alias;

        update_option($this->option_name, serialize(self::$notices));
    }
    
    public function display() {
        $allowable_notices = $this->ConfigRepository->get('system.notices');
        $available_notices = array_intersect(self::$notices, array_keys(
            $allowable_notices
        ));

        foreach ($available_notices as $notice_alias) {
            $content = $this->TemplatesRenderer->render('notice', [
                'content' => $allowable_notices[$notice_alias],
            ]);

            echo $content;
        }

        // We need clear stack after display
        // Because we already show stacked notice
        $this->clear_stack();
    }

    private function clear_stack() {
        delete_option($this->option_name);
    }
}