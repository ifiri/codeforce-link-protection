<?php

namespace TeaLinkProtection\App\AdminInterface\Pages\Views;

use TeaLinkProtection\App\AdminInterface\Partials;
use TeaLinkProtection\App\Config;
use TeaLinkProtection\App\Libraries;
use TeaLinkProtection\App\Contracts;

class BaseView implements Contracts\IView {
    protected $ui_elements = [];

    protected $Controller = null;
    protected $Model = null;

    protected $ConfigRepository = null;

    public function __construct(Contracts\IModel $Model, Contracts\IController $Controller) {
        $this->Model = $Model;
        $this->Controller = $Controller;

        $this->ConfigRepository = new Config\Repository;
    }

    public function display() {
        // ...
    }

    public function init_ui_elements() {
        $this->add_screen_options();
        $this->add_ui_elements();
    }

    protected function get_partial_views_for(Array $elements, $layout_path, Array $layout_variables = []) {
        // depincly
        $PartialsFactory = new Partials\Factory();

        $partials = [];
        foreach ($elements as $element_alias => $element_params) {
            $partial_params = array_merge([
                'alias' => $element_alias,
                'layout_path' => $layout_path,
            ], $element_params);

            $PartialObject = $PartialsFactory->create_partial_by($partial_params, $layout_variables);

            if($PartialObject) {
                $partials[] = $PartialObject;
            }
        }

        return $partials;
    }

    protected function add_ui_elements() {
        $current_class = Libraries\Utils::get_class_shortname($this);
        $ui_elements = $this->ConfigRepository->get('system.pages.' . $current_class . '.elements');

        if(!$ui_elements) {
            return;
        }

        // use factory
        $ui_elements_namespace = '\\TeaLinkProtection\\App\\AdminInterface\\Elements\\';
        foreach ($ui_elements as $ui_element_alias => $ui_element_relative_path) {
            $ui_element_callable = $ui_elements_namespace . $ui_element_relative_path;

            if(class_exists($ui_element_callable)) {
                $UiElement = new $ui_element_callable;

                $this->ui_elements[$ui_element_alias] = $UiElement;
            }
        }
    }

    protected function add_screen_options() {
        $current_class = Libraries\Utils::get_class_shortname($this);
        $screen_options = $this->ConfigRepository->get('system.pages.' . $current_class . '.screen-options');

        if(!$screen_options) {
            return;
        }

        foreach ($screen_options as $option_alias => $option_args) {
            add_screen_option($option_alias, $option_args);
        }
    }
}