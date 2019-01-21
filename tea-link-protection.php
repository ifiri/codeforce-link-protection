<?php
/*
Plugin Name: Tea Link Protection
Plugin URI: http://tsjuder.github.io/tea-link-protection
Description: This plugin allows create blocks with content of any post, and customize look of blocks via templates. Widget, shortcode, all post types is supported.
Version: 1.0.0
Text Domain: tea-link-protection
Domain Path: /languages/
Author: Raymond Costner
Author URI: https://github.com/Tsjuder
GitHub Plugin URI: https://github.com/Tsjuder/tea-link-protection
GitHub Branch: master

License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.

Copyright 2016-2017 Raymond Costner
*/

namespace TeaLinkProtection;

// use TeaLinkProtection\App\DependencyInjector;
use TeaLinkProtection\App;
use Ifiri;

// Necessary constants
define(__NAMESPACE__ . '\\MIN_PHP_REQUIRED', 5.6);
define(__NAMESPACE__ . '\\PLUGIN_FILE', __FILE__);
define(__NAMESPACE__ . '\\PLUGIN_PATH', dirname(PLUGIN_FILE));
define(__NAMESPACE__ . '\\PLUGIN_FOLDER', basename(PLUGIN_PATH));

// Functions
function get_major_php_version() {
    $php_version = explode('.', PHP_VERSION);
    $php_version = (float)implode('.', array_splice($php_version, 0, 2));

    return $php_version;
}

function run() {
    spl_autoload_register(__NAMESPACE__ . '\\autoload');

    register_activation_hook(__FILE__, __NAMESPACE__ . '\\activate');

    // $Depincly = new Ifiri\Depincly('TeaLinkProtection\\App\\', PLUGIN_PATH . '/maps/dependencies.map.php');

    set_exception_handler([new App\ExceptionHandler, 'handle']);

    add_action('plugins_loaded', [new App\Main, 'init']);
}

function activate() {
    global $wpdb;

    $table_links = $wpdb->prefix . 'tlp_links';
    $table_rules = $wpdb->prefix . 'tlp_links_rules';
    $table_aliases = $wpdb->prefix . 'tlp_links_aliases';

    // ===
    $main_query = '
    CREATE TABLE `' . $table_links . '` (
        id INT UNSIGNED NOT NULL AUTO_INCREMENT,
        link TEXT(999) NOT NULL,
        attachment_id INT UNSIGNED NULL,
        type VARCHAR(100) NOT NULL,
        created_date DATETIME NOT NULL,

        CONSTRAINT pk_link PRIMARY KEY (id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci
    ';
    $index_query = 'ALTER TABLE `' . $table_links . '` ADD FULLTEXT INDEX idx_link (link)';

    $wpdb->query($main_query);
    $wpdb->query($index_query);


    // ===
    $main_query = '
    CREATE TABLE `' . $table_rules . '` (
        id INT UNSIGNED NOT NULL AUTO_INCREMENT,
        link_id INT UNSIGNED NOT NULL,
        rule_name VARCHAR(100) NOT NULL,
        rule_value TEXT(999) NOT NULL,

        CONSTRAINT pk_link_rule PRIMARY KEY (id),
        CONSTRAINT fk_link_id_rules FOREIGN KEY (link_id) REFERENCES `' . $table_links . '` (id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci
    ';
    $index_query = 'ALTER TABLE `' . $table_rules . '` ADD FULLTEXT INDEX idx_rule (rule_name)';

    $wpdb->query($main_query);
    $wpdb->query($index_query);


    // ===
    $main_query = '
    CREATE TABLE `' . $table_aliases . '` (
        id INT UNSIGNED NOT NULL AUTO_INCREMENT,
        link_id INT UNSIGNED NOT NULL,
        link_alias VARCHAR(255) NOT NULL,
        params TEXT(999) NULL,
        created_date DATETIME NOT NULL,

        CONSTRAINT pk_link_alias PRIMARY KEY (id),
        CONSTRAINT fk_link_id_aliases FOREIGN KEY (link_id) REFERENCES `' . $table_links . '` (id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci
    ';
    $index_query = 'ALTER TABLE `' . $table_aliases . '` ADD FULLTEXT INDEX idx_alias (link_alias)';

    $wpdb->query($main_query);
    $wpdb->query($index_query);
}

function autoload($class_name) {
    if(strpos($class_name, __NAMESPACE__) === false) {
        return;
    }

    $parsed_class_name = explode('\\', $class_name);

    foreach ($parsed_class_name as $index => $namespace) {
        switch ($namespace) {
            case __NAMESPACE__:
                unset($parsed_class_name[$index]);

                break;

            case 'App':
                $parsed_class_name[$index] = 'app';

                break;
        }
    }

    $full_qualified_filename = PLUGIN_PATH . '/' . implode('/', $parsed_class_name) . '.php';

    if(file_exists($full_qualified_filename)) {
        require $full_qualified_filename;
    }
}

// Check minimal PHP installed
if(get_major_php_version() < MIN_PHP_REQUIRED) {
    if(is_admin()) {
        add_action('admin_notices', function() {
            $message = __('<b>Important!</b> Your version of PHP is less than <b>%s</b>! Tea Link Protection plugin will <b>NOT</b> run. Upgrade your PHP to version <b>%s</b> or higher. This is a minimal technical requirements.');
            $content = '<div class="error notice"><p>' . $message . '</p></div>';

            echo sprintf($content, MIN_PHP_REQUIRED, MIN_PHP_REQUIRED);
        });
    }

    return;
}

// Impossible to load via autoload
// require 'vendor/autoload.php'; // composer autoload
require 'vendor/ifiri/depincly/Depincly.php'; // composer autoload

// Start the app
run();