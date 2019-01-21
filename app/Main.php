<?php

namespace TeaLinkProtection\App;

use TeaLinkProtection\App\AdminInterface;
use TeaLinkProtection\App\Routing;
use TeaLinkProtection\App\Shortcodes;
use TeaLinkProtection\App\Contracts;

class Main {
    public function __construct() {
        // #
    }

    public function init() {
        $this->init_language();
        $this->init_routing();
        $this->load_config();

        $this->add_admin_notices();

        $this->add_shortcode();

        add_action('admin_enqueue_scripts', [$this, 'add_admin_assets'], 100, 1);
        // $this->localize_admin_assets();

        add_action('init', [$this, 'add_admin_elements'], 1000);

        register_deactivation_hook(\TeaLinkProtection\PLUGIN_FILE, [$this, 'deactivate']);
    }

    public function add_shortcode() {
        $LinkShortcode = new Shortcodes\Link;

        add_shortcode('tea_link_protection', [$LinkShortcode, 'shortcode']);
    }

    public function init_routing() {
        $Router = new Routing\Router;

        add_action('init', [$Router, 'init_routing'], 1000);
    }

    // todo это в uninstall
    public function deactivate() {
        global $wpdb;

        $sql = 'DROP TABLE `' . $wpdb->prefix . 'tlp_links_aliases`';
        $wpdb->query($sql);
        $sql = 'DROP TABLE `' . $wpdb->prefix . 'tlp_links_rules`';
        $wpdb->query($sql);
        $sql = 'DROP TABLE `' . $wpdb->prefix . 'tlp_links`';
        $wpdb->query($sql);
    }

    private function init_language() {
        load_plugin_textdomain('tea-link-protection', false, \TeaLinkProtection\PLUGIN_FOLDER . '/languages/');
    }

    private function load_config() {
        $ConfigRepository = new Config\Repository;
        $ConfigLoader = new Config\Loader($ConfigRepository);

        add_action('init', [$ConfigLoader, 'load_in_repository'], 999);
    }

    public function add_admin_elements() {
        // depincly
        $ConfigRepository = new Config\Repository;
        $InterfaceBuilder = new AdminInterface\InterfaceBuilder($ConfigRepository);
        $InterfaceBuilder->init_admin_pages();
    }

    private function add_admin_notices() {
        $ConfigRepository = new Config\Repository;
        $Notices = new AdminInterface\Notices($ConfigRepository);

        add_action('tpl_admin_notices', [$Notices, 'display'], 100); 
    }

    public function add_admin_assets($hook) {
        if(strpos($hook, 'tea-link-protection_page') !== false || $hook === 'toplevel_page_tlp-links') {
            $ConfigRepository = new Config\Repository;

            $url = plugins_url('/assets', \TeaLinkProtection\PLUGIN_FILE);
            
            wp_enqueue_media();

            wp_enqueue_script(
                'tea-page-content-js',
                $url . '/js/tea-link-protection.js',
                array('jquery'),
                $ConfigRepository->get('system.versions.scripts'),
                true
            );

            wp_enqueue_style(
                'tea-page-content-css',
                $url . '/css/tea-link-protection.css',
                array(),
                $ConfigRepository->get('system.versions.styles'),
                'all'
            );
        }
    }

    private function localize_admin_assets() {
        // add_action('admin_enqueue_scripts', [$this->Assets, 'localize'], 150, 1);
    }
}