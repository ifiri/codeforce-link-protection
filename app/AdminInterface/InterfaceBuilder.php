<?php

namespace TeaLinkProtection\App\AdminInterface;

use TeaLinkProtection\App\Request;
use TeaLinkProtection\App\Libraries;
use TeaLinkProtection\App\Contracts;

class InterfaceBuilder {
    private $pages = [];
    private $pages_namespace = __NAMESPACE__ . '\\Pages';

    public function __construct(Contracts\IConfigRepository $ConfigRepository) {
        $this->pages = $ConfigRepository->get('system.pages');
    }

    public function init_admin_pages() {
        // depincly
        $Request = new Request\Request;
        $RequestHandler = new Request\Handler($Request);

        // Build only full complected MVC
        foreach ($this->pages as $page_classname => $page_params) {
            if(!Libraries\Utils::is_mvc_components_exists($page_classname, $this->pages_namespace)) {
                break;
            }

            // Get classnames
            $controller_classname = $this->pages_namespace . '\\Controllers\\' . $page_classname;
            $model_classname = $this->pages_namespace . '\\Models\\' . $page_classname;
            $view_classname = $this->pages_namespace . '\\Views\\' . $page_classname;

            // Build MVC
            $PageModel = new $model_classname($Request);
            $PageController = new $controller_classname($PageModel, $RequestHandler);
            $PageView = new $view_classname($PageModel, $PageController);

            $PageController->set_view($PageView);

            // Get page priority
            $page_priority = $this->get_page_priority_by($page_params);

            // Register page and handle requests
            add_action('admin_menu', [$PageController, 'register_page'], $page_priority);
            add_action('tlp_page_registered', [$PageController, 'handle_request'], 10);
        }

        // Init User Interface Elements if required
        add_action('tlp_page_registered', [$this, 'init_ui_on_load_page_hook'], 100, 2);
    }

    public function init_ui_on_load_page_hook(Contracts\IView $View, $hook) {
        add_action('load-' . $hook, [$View, 'init_ui_elements']);
    }

    private function get_page_priority_by(Array $page_params) {
        // default priority
        $page_priority = 10;

        if(isset($page_params['order'])) {
            $page_priority *= $page_params['order'];
        }

        return $page_priority;
    }
}