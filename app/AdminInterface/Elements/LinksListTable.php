<?php

namespace TeaLinkProtection\App\AdminInterface\Elements;

use TeaLinkProtection\App\Config;
use TeaLinkProtection\App\Contracts;

class LinksListTable extends \WP_List_Table {
    // todo много чего сделать, класс кривоват
    public function __construct() {
        parent::__construct([
            'singular' => __('Link', 'tea-link-protection'),
            'plural' => __('Links', 'tea-link-protection'),
            'ajax' => false,
        ]);

        // $this->Config = new Config\Repository;

        $this->prepare_items();
    }

    public function prepare_items() {
        $user_search_key = isset( $_REQUEST['s'] ) ? wp_unslash( trim( $_REQUEST['s'] ) ) : '';

        $this->_column_headers = $this->get_column_info();

        $table_data = $this->fetch_table_data();

        if($user_search_key) {
            $table_data = $this->filter_table_data( $table_data, $user_search_key );
        }

        $links_per_page = $this->get_items_per_page('links_per_page');
        $current_page = $this->get_pagenum();     

        // provide the ordered data to the List Table
        // we need to manually slice the data based on the current pagination
        $this->items = array_slice($table_data, (($current_page - 1) * $links_per_page), $links_per_page);

        // set the pagination arguments     
        $total_users = count($table_data);
        $this->set_pagination_args(array(
            'total_items' => $total_users,
            'per_page'    => $links_per_page,
            'total_pages' => ceil($total_users / $links_per_page)
        ));
    }

    public function filter_table_data( $table_data, $search_key ) {
        $filtered_table_data = array_values( array_filter( $table_data, function( $row ) use( $search_key ) {
            foreach( $row as $row_val ) {
                if( stripos( $row_val, $search_key ) !== false ) {
                    return true;
                }               
            }           
        } ) );
        return $filtered_table_data;
    }

    public function fetch_table_data() {
        global $wpdb;

        $wpdb_table = $wpdb->prefix . 'tlp_links';        
        $orderby = ( isset( $_GET['orderby'] ) ) ? esc_sql( $_GET['orderby'] ) : 'created_date';
        $order = ( isset( $_GET['order'] ) ) ? esc_sql( $_GET['order'] ) : 'ASC';
        $user_query = "SELECT 
                        ID, link, type, created_date
                      FROM 
                        $wpdb_table 
                      ORDER BY $orderby $order";

        $query_results = $wpdb->get_results($user_query, ARRAY_A);

        return $query_results;        
    }



    public function get_bulk_actions() {
        $actions = [
            'bulk-delete' => 'Delete'
        ];

        return $actions;
    }

    public function get_columns() {
        $table_columns = array(
            'cb'        => '<input type="checkbox" />',         
            'ID'        => __('ID', 'tea-link-protection'),
            'link'    => __( 'Link Address', 'tea-link-protection'),     
            'type'    => __('Link Type', 'tea-link-protection'),     
            'created_date' => _x('Created On', 'column name', 'tea-link-protection'),
        );      

        return $table_columns;
    }

    protected function get_sortable_columns() {
        $sortable_columns = array (
            'ID' => array('ID', true),
            'link' => 'link',
            'type' => 'type',
            'created_date' => 'created_date',
        );

        return $sortable_columns;
    }

    public function column_default($item, $column_name) {
        return $item[$column_name];
    }

    protected function column_cb($item) {
        return sprintf(
            '<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['ID']
        );
    }

    public function column_link($item) {
        $delete_nonce = wp_create_nonce('tlp-delete-link');

        $actions = [
            'edit' => sprintf('<a href="?page=%s&id=%s">Edit</a>', 'tlp-edit-link', intval($item['ID'])),
            'delete' => sprintf('<a href="?page=%s&action=%s&id=%s&_wpnonce=%s">Delete</a>', esc_attr($_GET['page']), 'delete', intval($item['ID']), $delete_nonce),
        ];

        // Built-in function in WP_LIST_TABLE class, create action links for column
        return $item['link'] . $this->row_actions($actions);
    }


    public function no_items() {
        // todo plugin textdomain to constant
        echo __('No links avaliable.', 'tea-link-protection');
    }
}