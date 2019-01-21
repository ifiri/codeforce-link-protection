<?php

namespace TeaLinkProtection\App\Links;

use TeaLinkProtection\App\Log;
use TeaLinkProtection\App\Libraries;
use TeaLinkProtection\App\Config;
use TeaLinkProtection\App\Exceptions;
use TeaLinkProtection\App\Contracts;

class Repository {
    private $ConfigRepository = null;

    // todo dry{1}
    const TABLE_LINKS = 'tlp_links';
    const TABLE_RULES = 'tlp_links_rules';
    const TABLE_ALIASES = 'tlp_links_aliases';

    public function __construct(Contracts\IConfigRepository $ConfigRepository) {
        $this->ConfigRepository = $ConfigRepository;
    }

    public function get_by($link_id) {
        $result = null;

        try {
            $result = $this->get_link_data_from_database_by($link_id);
        } catch(Exceptions\DatabaseException $Exception) {
            $LogWriter = new Log\Writer;
            $LogWriter->log($Exception->get_error_message());

            throw $Exception;
        }

        return $result;
    }

    public function save_by(Contracts\IRequest $Request) {
        $result = false;

        try {
            $inserted_link_id = $this->add_link_to_database_by($Request);
            $this->add_rules_to_database_by($inserted_link_id, $Request);

            $result = $inserted_link_id;
        } catch(Exceptions\DatabaseException $Exception) {
            $LogWriter = new Log\Writer;
            $LogWriter->log($Exception->get_error_message());

            throw $Exception;
        }

        return $result;
    }

    public function update_by(Contracts\IRequest $Request) {
        $result = false;

        try {
            if($this->is_link_exists($Request->id)) {
                $is_link_affected = $this->update_link_in_database_by($Request);
                $is_rules_affected = $this->update_rules_in_database_by($Request);

                // how much rowa was affected by queries
                $result = $is_link_affected + array_sum($is_rules_affected);
            }
        } catch(Exceptions\DatabaseException $Exception) {
            $LogWriter = new Log\Writer;
            $LogWriter->log($Exception->get_error_message());

            throw $Exception;
        }

        return $result;
    }

    public function delete_by($link_id) {
        $result = false;

        try {
            $rows_affected = $this->delete_link_and_rules_from_database_by($link_id);

            $result = $rows_affected;
        } catch(Exceptions\DatabaseException $Exception) {
            $LogWriter = new Log\Writer;
            $LogWriter->log($Exception->get_error_message());

            throw $Exception;
        }

        return $result;
    }

    private function get_link_data_from_database_by($link_id) {
        global $wpdb;

        if(!$link_id) {
            throw new Exceptions\IncorrectRequestException('Incorrect Input Parameter');
        }

        $table_links = $wpdb->prefix . self::TABLE_LINKS;
        $table_rules = $wpdb->prefix . self::TABLE_RULES;

        $link_data = $wpdb->get_row(
            $wpdb->prepare('
                SELECT link, type FROM ' . $table_links . '
                WHERE id = %d', $link_id
            ), ARRAY_A
        );

        $link_data['rules'] = $wpdb->get_results(
            $wpdb->prepare(
                'SELECT rule_name, rule_value FROM ' . $table_rules . ' WHERE link_id = %d', $link_id
            ), ARRAY_A
        );

        $link_data = $this->format_link_data($link_data);

        return $link_data;
    }

    private function add_link_to_database_by(Contracts\IRequest $Request) {
        global $wpdb;

        if(!$Request->link || !$Request->link_type) {
            throw new Exceptions\IncorrectRequestException('Incorrect Request');
        }

        $table_links = $wpdb->prefix . self::TABLE_LINKS;

        // Common parameters
        $columns = [
            'link' => $Request->link,
            'type' => $Request->link_type,
            'created_date' => Libraries\Utils::get_current_utc_datetime(),
        ];
        $placeholders = ['%s', '%s', '%s'];

        // If new link should have attachment type
        if($Request->attachment_id) {
            $columns['attachment_id'] = $Request->attachment_id;
            $placeholders[] = '%d';
        }

        $wpdb->insert($table_links, $columns, $placeholders);

        $inserted_link_id = $wpdb->insert_id;
        
        if(!$inserted_link_id) {
            throw new Exceptions\LinkCreateFailException('Link Insertion Failed');
        }

        return $inserted_link_id;
    }

    private function add_rules_to_database_by($link_id, Contracts\IRequest $Request) {
        global $wpdb;

        if(!$link_id || !is_numeric($link_id)) {
            throw new Exceptions\IncorrectRequestException('Incorrect Input Parameter');
        }

        $table_rules = $wpdb->prefix . self::TABLE_RULES;

        $inserted_ids = [];

        $rules = $this->ConfigRepository->get('link.rules');
        foreach ($rules as $rule_name => $rule_params) {
            if(!$Request->$rule_name) {
                continue;
            }

            $rule_value = $this->format_rule_value($rule_name, $Request->$rule_name);

            $wpdb->insert($table_rules, [
                'link_id' => $link_id,
                'rule_name' => $rule_name,
                'rule_value' => $rule_value,
            ], ['%d', '%s', '%s']);

            $inserted_rule_id = $wpdb->insert_id;

            if(!$inserted_rule_id) {
                throw new Exceptions\LinkCreateFailException('Rule Insertion Failed');
            }

            $inserted_ids[] = $inserted_rule_id;
        }
        
        return $inserted_ids;
    }

    private function update_link_in_database_by(Contracts\IRequest $Request) {
        global $wpdb;

        if(!$Request->link || !$Request->link_type || !$Request->id) {
            throw new Exceptions\IncorrectRequestException('Incorrect Request');
        }

        $table_links = $wpdb->prefix . self::TABLE_LINKS;

        // Common parameters
        $columns = [
            'link' => $Request->link,
            'type' => $Request->link_type,
        ];
        $where = [
            'id' => $Request->id,
        ];

        $placeholders_columns = ['%s', '%s'];
        $placeholders_where = ['%d'];

        // Parameters for attachment link type
        if($Request->attachment_id) {
            $columns['attachment_id'] = $Request->attachment_id;
            $placeholders[] = '%d';
        }

        $rows_affected = $wpdb->update($table_links, $columns, $where, $placeholders_columns, $placeholders_where);

        if($rows_affected === false) {
            throw new Exceptions\LinkUpdateFailException('Link Update Failed');
        }

        return $rows_affected;
    }

    private function update_rules_in_database_by(Contracts\IRequest $Request) {
        global $wpdb;

        if(!$Request->id) {
            throw new Exceptions\IncorrectRequestException('Incorrect Request');
        }

        $results = [];

        $rules = $this->ConfigRepository->get('link.rules');
        foreach ($rules as $rule_name => $rule_params) {
            if(!$Request->$rule_name) {
                continue;
            }

            if($this->is_rule_for_link_exists($Request->id, $rule_name)) {
                $last_result = $this->update_rule_in_database_by($Request, $rule_name);
            } else {
                $last_result = $this->add_rule_in_database_by($Request, $rule_name);
            }

            if($last_result === false) {
                throw new Exceptions\LinkUpdateFailException('Rule Update Failed');
            }

            $results[] = $last_result;
        }

        return $results;
    }

    private function delete_link_and_rules_from_database_by($link_id) {
        global $wpdb;

        if(!$link_id || !is_numeric($link_id)) {
            throw new Exceptions\IncorrectRequestException('Incorrect Input Parameter');
        }

        $table_links = $wpdb->prefix . self::TABLE_LINKS;
        $table_rules = $wpdb->prefix . self::TABLE_RULES;
        
        $wpdb->delete($table_rules, [
            'link_id' => $link_id,
        ], ['%d']);

        $is_link_affected = $wpdb->delete($table_links, [
            'id' => $link_id,
        ], ['%d']);

        if($is_link_affected === false) {
            throw new Exceptions\LinkDeleteFailException('Link Delete Failed');
        }

        return $is_link_affected;
    }

    private function update_rule_in_database_by(Contracts\IRequest $Request, $rule_name) {
        global $wpdb;

        if(!$Request->id || !$Request->$rule_name) {
            throw new Exceptions\IncorrectRequestException('Incorrect Request');
        }

        $table_rules = $wpdb->prefix . self::TABLE_RULES;
        $rule_value = $this->format_rule_value($rule_name, $Request->$rule_name);

        $rows_affected = $wpdb->update($table_rules, [
            'rule_value' => $rule_value,
        ], [
            'link_id' => $Request->id,
            'rule_name' => $rule_name,
        ], ['%s'], ['%d', '%s']);

        return $rows_affected;
    }

    private function add_rule_in_database_by($Request, $rule_name) {
        global $wpdb;

        if(!$Request->id || !$Request->$rule_name) {
            throw new Exceptions\IncorrectRequestException('Incorrect Request');
        }

        $table_rules = $wpdb->prefix . self::TABLE_RULES;
        $rule_value = $this->format_rule_value($rule_name, $Request->$rule_name);

        $inserted_rule_id = $wpdb->insert($table_rules, [
            'link_id' => $Request->id,
            'rule_name' => $rule_name,
            'rule_value' => $rule_value,
        ], ['%d', '%s', '%s']);

        return $inserted_rule_id;
    }

    private function is_link_exists($link_id) {
        global $wpdb;

        if(!$link_id || !is_numeric($link_id)) {
            throw new Exceptions\IncorrectRequestException('Incorrect Input Parameter');
        }

        $table_links = $wpdb->prefix . self::TABLE_LINKS;

        $is_link_exists = $wpdb->get_var(
            $wpdb->prepare('SELECT true FROM ' . $table_links . ' WHERE id = %d LIMIT 1', [$link_id])
        );

        return $is_link_exists;
    }

    private function is_rule_for_link_exists($link_id, $rule_name) {
        global $wpdb;

        if(!$link_id || !is_numeric($link_id) || !$rule_name) {
            throw new Exceptions\IncorrectRequestException('Incorrect Input Parameter');
        }

        $table_rules = $wpdb->prefix . self::TABLE_RULES;

        $is_rule_exists = $wpdb->get_var(
            $wpdb->prepare('SELECT true FROM ' . $table_rules . ' WHERE link_id = %d AND rule_name = %s LIMIT 1', [$link_id, $rule_name])
        );

        return $is_rule_exists;
    }

    private function format_link_data(Array $link_data) {
        if(!$link_data['rules']) {
            return $link_data;
        }

        foreach ($link_data['rules'] as $rule_index => $rule_data) {
            $rule_name = $rule_data['rule_name'];
            $rule_value = $rule_data['rule_value'];

            if(is_serialized($rule_value)) {
                $rule_value = unserialize($rule_value);
            }

            $link_data['rules'][$rule_name] = $rule_value;

            unset($link_data['rules'][$rule_index]);
        }

        return $link_data;
    }

    // todo может, этому тут не место
    private function format_rule_value($rule_name, $rule_value) {
        if($rule_name === 'IP_RESTRICT') {
            $rule_value = explode(',', $rule_value);
            $rule_value = array_map(function($item) {
                return trim($item);
            }, $rule_value);
        }

        if(is_array($rule_value)) {
            $rule_value = serialize($rule_value);
        }

        return $rule_value;
    }
}