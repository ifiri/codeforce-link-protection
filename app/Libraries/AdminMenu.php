<?php

namespace TeaLinkProtection\App\Libraries;

use TeaLinkProtection\App\Request;
use TeaLinkProtection\App\Contracts;

class AdminMenu {
    public static function register_page_in_menu_by(Array $params, Contracts\IController $PageController) {
        $hook = null;

        if(!$params['slug']) {
            return null;
        }

        if(array_key_exists('parent', $params)) {
            // Register Main Root Item
            if($params['slug'] === $params['parent']) {
                $main_item_params = $params;
                $main_item_params['menu_title'] = 'Tea Link Protection';

                $hook = self::register_menu_page($main_item_params, $PageController);
            }

            // Special logic for temporary menu items which should be hidden
            if(isset($params['in_menu']) && !$params['in_menu']) {
                $Request = new Request\Request();

                if($Request->page && $Request->page === $params['slug']) {
                    $hook = self::register_submenu_page($params, $PageController);
                }
            } else {
                $hook = self::register_submenu_page($params, $PageController);
            }
            
        } else {
            $hook = self::register_menu_page($params, $PageController);
        }

        return $hook;
    }

    private static function register_submenu_page(Array $params, Contracts\IController $Controller) {
        $hook = add_submenu_page(
            $params['parent'],
            $params['page_title'], 
            $params['menu_title'], 
            $params['capability'], 
            $params['slug'], 
            [$Controller, 'execute']
        );

        return $hook;
    }

    private static function register_menu_page(Array $params, Contracts\IController $Controller) {
        $hook = add_menu_page(
            $params['page_title'], 
            $params['menu_title'], 
            $params['capability'], 
            $params['slug'],
            [$Controller, 'execute']
            // icon url
            // position
        );

        return $hook;
    }
}