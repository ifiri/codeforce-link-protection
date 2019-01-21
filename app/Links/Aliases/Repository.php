<?php

namespace TeaLinkProtection\App\Links\Aliases;

use TeaLinkProtection\App\Log;
use TeaLinkProtection\App\Libraries;
use TeaLinkProtection\App\Config;
use TeaLinkProtection\App\Exceptions;
use TeaLinkProtection\App\Contracts;

class Repository {
    private $ConfigRepository = null;

    // todo dry{1}
    const TABLE_LINKS = 'tlp_links';
    const TABLE_ALIASES = 'tlp_links_aliases';
    const TABLE_ALIASES_PARAMS = 'tlp_aliases_params';

    public function __construct(Contracts\IConfigRepository $ConfigRepository) {
        $this->ConfigRepository = $ConfigRepository;
    }

    public function get_by($link_id, Array $alias_params = [], $limit = -1) {
        $result = null;

        try {
            $result = $this->get_aliases_from_database_by($link_id, $alias_params, $limit);
        } catch(Exceptions\DatabaseException $Exception) {
            $LogWriter = new Log\Writer;
            $LogWriter->log($Exception->get_error_message());

            throw $Exception;
        }

        return $result;
    }

    public function get_alias_by_id($alias_id) {
        $result = null;

        try {
            $result = $this->get_alias_from_database_by_its_id($alias_id);
        } catch(Exceptions\DatabaseException $Exception) {
            $LogWriter = new Log\Writer;
            $LogWriter->log($Exception->get_error_message());

            throw $Exception;
        }

        return $result;
    }

    public function get_params_by($alias) {
        $result = null;

        try {
            $alias_params = $this->get_alias_data_from_database_by($alias);

            $result = [
                'params' => []
            ];
            foreach ($alias_params as $param) {
                $result['params'][$param['param_name']] = $param['param_value'];
            }

            $result['link_id'] = $param['link_id'];
        } catch(Exceptions\DatabaseException $Exception) {
            $LogWriter = new Log\Writer;
            $LogWriter->log($Exception->get_error_message());

            throw $Exception;
        }

        return $result;
    }

    public function save_by($link_id, $link_alias, Array $alias_params = []) {
        $result = false;

        // $LogWriter = new Log\Writer;
        // $LogWriter->log('SAVE ALIAS BY ' . $link_alias);

        try {
            $inserted_alias_id = $this->add_alias_to_database_by($link_id, $link_alias, $alias_params);

            $result = $inserted_alias_id;
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
            if($this->is_alias_exists($Request->id)) {
                // $is_link_affected = $this->update_link_in_database_by($Request);
                // $is_rules_affected = $this->update_rules_in_database_by($Request);

                // // how much rowa was affected by queries
                // $result = $is_link_affected + array_sum($is_rules_affected);
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
            $rows_affected = $this->delete_aliases_from_database_by($link_id);

            $result = $rows_affected;
        } catch(Exceptions\DatabaseException $Exception) {
            $LogWriter = new Log\Writer;
            $LogWriter->log($Exception->get_error_message());

            throw $Exception;
        }

        return $result;
    }

    private function get_aliases_from_database_by($link_id, Array $alias_params, $limit = -1) {
        global $wpdb;

        if(!$link_id || !is_numeric($link_id)) {
            throw new Exceptions\IncorrectRequestException('Incorrect Input Parameter');
        }

        $table_aliases = $wpdb->prefix . self::TABLE_ALIASES;
        $table_aliases_params = $wpdb->prefix . self::TABLE_ALIASES_PARAMS;

        $query_args = $this->get_where_clauses_for_params_query($alias_params);

        // Form query values
        // // todo некрасиво, вынести в отдельную функцию
        $query_values = [];

        $query_values[] = $link_id;
        $query_values[] = 'TRIGGER_COUNT';

        $query_values = array_merge($query_values, $query_args['query_values']);
        $parameters_where_clause = $query_args['where_clauses'];

        $query_values[] = $link_id;

        if($limit > 0) {
            $query_values[] = $limit;
        }

        // todo протестировать корректную выборку алиасов в рамках одной ссылки
        $sql = $wpdb->prepare('
            SELECT aliases.id, aliases.link_alias, trigger_count_table.param_value as trigger_count FROM ' . $table_aliases . ' as aliases '.

            'LEFT JOIN ' . $table_aliases_params . ' as trigger_count_table ON aliases.link_id = %d AND trigger_count_table.param_name = %s' .

            (count($parameters_where_clause) ? (
                'INNER JOIN (
                    SELECT alias_id FROM ' . $table_aliases_params . ' WHERE ' .

                    implode(' OR ', $parameters_where_clause) .

                    ' GROUP BY alias_id 
                    HAVING COUNT(*) = ' . count($parameters_where_clause) .
                ') as params'
            ) : '' ) .

            ' WHERE aliases.link_id = %d AND aliases.id = params.alias_id 
            ORDER BY aliases.id DESC ' . 
            
            (
                $limit > 0 ? 'LIMIT %d' : ''
            ),

            $query_values
        );

        // echo '<pre>';
        // var_dump($sql);
        // echo '<br><br>';
        // var_dump($query_values);
        // echo '</pre>';

        $aliases = $wpdb->get_results(
            $sql, ARRAY_A
        );

        return $aliases;
    }

    private function get_alias_from_database_by_its_id($alias_id) {
        global $wpdb;

        if(!$alias_id || !is_numeric($alias_id)) {
            throw new Exceptions\IncorrectRequestException('Incorrect Input Parameter');
        }

        $table_aliases = $wpdb->prefix . self::TABLE_ALIASES;

        $sql = $wpdb->prepare('
            SELECT link_alias, link_id FROM ' . $table_aliases .

            ' WHERE id = %d', 
            $alias_id
        );

        $alias = $wpdb->get_row(
            $sql, ARRAY_A
        );

        return $alias;
    }

    private function get_alias_data_from_database_by($alias) {
        global $wpdb;

        if(!$alias) {
            throw new Exceptions\IncorrectRequestException('Incorrect Input Parameter');
        }

        // var_dump($alias);

        $table_aliases = $wpdb->prefix . self::TABLE_ALIASES;
        $table_aliases_params = $wpdb->prefix . self::TABLE_ALIASES_PARAMS;

        $sql = $wpdb->prepare('
            SELECT aliases.link_id, aliases.id as alias_id, params.param_name, params.param_value FROM ' . $table_aliases . ' as aliases
            INNER JOIN ' . $table_aliases_params . ' as params
            ON params.alias_id = aliases.id

            WHERE aliases.link_alias = %s
            ', $alias
        );

        $data = $wpdb->get_results(
            $sql, ARRAY_A
        );

        return $data;
    }

    private function add_alias_to_database_by($link_id, $link_alias, Array $alias_params = []) {
        global $wpdb;

        if(!$link_alias || !$link_id) {
            throw new Exceptions\IncorrectRequestException('Incorrect Input Parameter');
        }

        $table_aliases = $wpdb->prefix . self::TABLE_ALIASES;
        $table_aliases_params = $wpdb->prefix . self::TABLE_ALIASES_PARAMS;

        // Common parameters
        $wpdb->insert($table_aliases, [
            'link_id' => $link_id,
            'link_alias' => $link_alias,

            // todo разобраться с сохранением в UTC
            'created_date' => Libraries\Utils::get_current_utc_datetime(),
        ], '%s');

        $inserted_alias_id = $wpdb->insert_id;
        
        if(!$inserted_alias_id) {
            throw new Exceptions\AliasCreateFailException('Alias Insertion Failed');
        } else {
            $this->add_alias_params_to_database_by($inserted_alias_id, $alias_params);
        }

        return $inserted_alias_id;
    }

    // todo при исключении удалять параметры и алиас из БД
    private function add_alias_params_to_database_by($alias_id, Array $alias_params = [], $replacing_param_name = null) {
        global $wpdb;

        if(!$alias_id || !is_array($alias_params)) {
            throw new Exceptions\IncorrectRequestException('Incorrect Input Parameters');
        }

        $table_aliases_params = $wpdb->prefix . self::TABLE_ALIASES_PARAMS;

        foreach($alias_params as $param_name => $param_value) {
            if(is_array($param_value)) {
                $this->add_alias_params_to_database_by($alias_id, $param_value, $param_name);

                continue;
            }

            $wpdb->insert($table_aliases_params, [
                'alias_id' => $alias_id,
                'param_name' => $replacing_param_name ? $replacing_param_name : $param_name,
                'param_value' => $param_value,
            ], ['%d', '%s', '%s']);

            $inserted_param_id = $wpdb->insert_id;

            if(!$inserted_param_id) {
                throw new Exceptions\AliasCreateFailException('Param Insertion Failed');
            }
        }
    }

    private function update_alias_in_database_by(Contracts\IRequest $Request) {
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

    private function delete_aliases_from_database_by($link_id) {
        global $wpdb;

        if(!$link_id || !is_numeric($link_id)) {
            throw new Exceptions\IncorrectRequestException('Incorrect Input Parameter');
        }

        $table_aliases = $wpdb->prefix . self::TABLE_ALIASES;
        
        $rows_affected = $wpdb->delete($table_aliases, [
            'link_id' => $link_id,
        ], ['%d']);

        if($rows_affected === false) {
            throw new Exceptions\AliasDeleteFailException('Alias Delete Failed');
        }

        return $rows_affected;
    }

    public function is_alias_for_link_exists($link_id) {
        global $wpdb;

        if(!$link_id || !is_numeric($link_id)) {
            throw new Exceptions\IncorrectRequestException('Incorrect Input Parameter');
        }

        $table_aliases = $wpdb->prefix . self::TABLE_ALIASES;

        $is_alias_exists = $wpdb->get_var(
            $wpdb->prepare('SELECT true FROM ' . $table_aliases . ' WHERE link_id = %d LIMIT 1', [$link_id])
        );

        return $is_alias_exists;
    }

    public function is_alias_for_param_exists($link_id, $param_name, $param_value) {
        global $wpdb;

        if(!$param_name || !$param_value || !is_numeric($link_id)) {
            // todo здесь бы изменить тип исключения, т.к. реквест - это GET + POST
            throw new Exceptions\IncorrectRequestException('Incorrect Input Parameter');
        }

        $table_aliases = $wpdb->prefix . self::TABLE_ALIASES;
        $table_aliases_params = $wpdb->prefix . self::TABLE_ALIASES_PARAMS;

        $is_alias_exists = $wpdb->get_var(
            $wpdb->prepare('
                SELECT true FROM ' . $table_aliases . ' as aliases
                INNER JOIN ' . $table_aliases_params . ' as params
                ON params.param_name = %s AND params.param_value = %s
                WHERE aliases.id = params.alias_id AND aliases.link_id = %d
                LIMIT 1
            ', [$param_name, $param_value, (int)$link_id])
        );

        return $is_alias_exists;
    }

    public function is_alias_exists($link_alias) {
        global $wpdb;

        if(!$link_alias) {
            throw new Exceptions\IncorrectRequestException('Incorrect Input Parameter');
        }

        $table_aliases = $wpdb->prefix . self::TABLE_ALIASES;

        $is_alias_exists = $wpdb->get_var(
            $wpdb->prepare('SELECT true FROM ' . $table_aliases . ' WHERE link_alias = %s LIMIT 1', [$link_alias])
        );

        return $is_alias_exists;
    }

    private function get_where_clauses_for_params_query(Array $alias_params) {
        $result = [
            'where_clauses' => [],
            'query_values' => [],
        ];

        foreach ($alias_params as $param_name => $param_value) {
            $result['query_values'][] = $param_name;

            if(is_array($param_value) && count($param_value)) {
                foreach ($param_value as $current_param_value) {
                    $result['where_clauses'][] = '(param_name = %s AND param_value = %s)';
                    $result['query_values'][] = $current_param_value;
                }

                continue;
            }

            $result['where_clauses'][] = '(param_name = %s AND param_value = %s)';
            $result['query_values'][] = $param_value;
        }

        return $result;
    }
}