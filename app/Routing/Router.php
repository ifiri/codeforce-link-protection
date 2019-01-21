<?php

namespace TeaLinkProtection\App\Routing;

use TeaLinkProtection\App\Config;
use TeaLinkProtection\App\Links;
use TeaLinkProtection\App\Links\Aliases;
use TeaLinkProtection\App\Links\PermissionsValidation;
use TeaLinkProtection\App\Contracts;

class Router {
    public function __construct() {
        add_filter('template_include', [$this, 'catchs'], 1000, 1);
    }

    public function init_routing() {
        $tag = 'alias';

        add_rewrite_tag('%alias%', '([\w\d]+)');
        add_rewrite_rule('^([\w\d]+)$', 'index.php?alias=$matches[1]', 'top');
    }

    public function catchs( $template ) {
        $alias = get_query_var('alias', null);

        if(!$alias) {
            return $template;
        }

        $ConfigRepository = new Config\Repository;
        $AliasRepository = new Aliases\Repository($ConfigRepository);
        $PermissionValidator = new PermissionsValidation\Validator;

        $alias_data = $AliasRepository->get_params_by($alias);

        if($PermissionValidator->is_current_user_can_execute_alias($alias)) {
            $LinksRepository = new Links\Repository($ConfigRepository);

            $link = $LinksRepository->get_by($alias_data['link_id']);

            switch ($link['type']) {
                case 'common': // just doing redirect
                    wp_redirect($link['link'], 301);
                    exit;
                    break;

                case 'attachment':
                    $charset = get_bloginfo('charset');
                    $attachment_mime = mime_content_type($_SERVER['DOCUMENT_ROOT'] . wp_make_link_relative($link['link']));

                    header('Content-Type: ' . $attachment_mime . '; charset=' . $charset);

                    return wp_make_link_relative($link['link']);
                    break;
                
                default:
                    # code...
                    break;
            }

            echo '<pre>';
            var_dump($link);
            exit;
        }

        

        return $template;
    }
}