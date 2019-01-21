<?php

namespace TeaLinkProtection\App\AdminInterface\Pages\Controllers;

use TeaLinkProtection\App\AdminInterface;
use TeaLinkProtection\App\Log;
use TeaLinkProtection\App\Request;
use TeaLinkProtection\App\Config;
use TeaLinkProtection\App\Exceptions;
use TeaLinkProtection\App\Libraries;
use TeaLinkProtection\App\Contracts;

class BaseController implements Contracts\IController {
    private $View = null;
    private $Model = null;
    private $RequestHandler = null;

    private $page_hook = null;
    private $page_params = [];

    private $is_current_controller = false;

    public function __construct(Contracts\IModel $Model, Contracts\IRequestHandler $RequestHandler) {
        $this->Model = $Model;
        $this->RequestHandler = $RequestHandler;
    }

    public function set_view(Contracts\IView $View) {
        $this->View = $View;
    }

    public function is_current_controller() {
        // depincly
        $Request = new Request\Request;

        if(!$Request->page) {
            return false;
        }

        $params = $this->Model->get_page_params();
        
        return $params['slug'] === $Request->page;
    }

    public function register_page() {
        $params = $this->Model->get_page_params();
        $hook = Libraries\AdminMenu::register_page_in_menu_by($params, $this);

        $this->page_hook = $hook;

        if($this->is_current_controller()) {
            do_action('tlp_page_registered', $this->View, $this->page_hook);
        }
    }

    public function handle_request() {
        if($this->is_current_controller()) {
            $this->RequestHandler->handle_controller_request($this);
        }
    }

    // todo отрефакторить класс, выкинуть многое
    public function get_page_hook() {
        return $this->page_hook;
    }

    public function execute() {
        do_action('tpl_admin_notices');

        // todo выглядит некрасиво
        try {
            $this->View->display();
        } catch(Contracts\IException $Exception) {
            $LogWriter = new Log\Writer;
            $LogWriter->log($Exception->get_error_message());
        }
    }
}