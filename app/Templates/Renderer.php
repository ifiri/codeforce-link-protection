<?php

namespace TeaLinkProtection\App\Templates;

use TeaLinkProtection\App\Contracts;

class Renderer implements Contracts\ITemplatesRenderer {
    private $Config = null;

    public function __construct(Contracts\IConfigRepository $Config) {
        $this->Config = $Config;
    }

    /**
     * @param string $template_name Template alias without path or extension
     * @param array $params 
     * 
     * @return null
     */
    public function render($template_name, Array $params = []) {
        $content = null;
        $template_path = $this->get_template_path_by($template_name);

        if($template_path) {
            $content = $this->load_template_content($template_path, $params);
        }

        echo $content;
    }

    /**
     * @param string $template_name Template alias without path or extension
     * 
     * @return string|null
     */
    public function get_template_path_by($template_name) {
        if($template_name) {
            $templates_directory = $this->Config->get('system.template-directories.admin');
            $template_path = $templates_directory['path'] . '/' . $template_name . '.php';

            if(file_exists($template_path)) {
                return $template_path;
            }
        }

        return null;
    }

    /**
     * @param string $template_path Full path to template
     * @param array $params 
     * 
     * @return string|null
     */
    private function load_template_content($template_path, Array $params = []) {
        if(!$template_path) {
            return null;
        }

        ob_start();
        $this->set_query_vars_for_template($params);
        load_template($template_path, false);
        $content = ob_get_clean();

        return $content;
    }

    private function set_query_vars_for_template(Array $params) {
        foreach ($params as $title => $value) {
            set_query_var($title, $value);
        }
    }
}