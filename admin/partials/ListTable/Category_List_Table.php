<?php

if( !class_exists('WP_List_Table') ){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class Category_List_Table extends WP_List_Table{

    public function get_columns(){
        
        return [
            'cb'        => '<input type="checkbox"/>',
            'ID' => 'شناسه',
            'name' => 'نام',
            'slug' => 'نامک',
            'abpCode' => 'کد',
            'abpParentCode' => 'کد پدر',
            'abpTypeLevel' => 'سطح',
            'abpTypeShow' => 'نمایش',
            'abpRecordId' => 'رکوردآی دی',
        ];
    }

    public function column_default( $item, $column_name){
        if( isset( $item[$column_name])){
            return $item[$column_name];
        }
        return '-';
    }
    
    public function column_cb($item){
        return '<input type="checkbox" name="Category[]" value="'. $item['ID'] . '"/>';
    } 

    public function column_ID($item){
        return $item['term_id'];
    }

    public function column_name($item){

        $actions = [
            'edit' => '<a href="#" target="_blank">ویرایش</a>',
            'delete' => '<a href="#" onclick="return confirm(\'اطمینان دارید؟\');">حذف</a>',
            'show' => '<a href="' . get_category_link( $item['term_id'] ) . '" target="_blank">نمایش</a>',
        ];

        return $item['name'] . $this->row_actions($actions);
    }

    public function no_items(){
        echo 'دسته محصولی یافت نشد';
    }

    private function create_view( $key, $label, $url, $count = 0){
        $current_status = isset($_GET['category_status']) ? $_GET['category_status'] : 'all';
        $view_tag = sprintf( '<a href="%s" %s> %s</a>', $url, $current_status == $key ? 'class="current"' : '',$label);
        if( $count ){
            $view_tag .=sprintf( '<span class="count">(%d)</span>', $count);
        }
        return $view_tag;
    }

    protected function get_views(){
        global $wpdb;

        $where= '';
        if( isset( $_GET['s'])){
            $where= $wpdb->prepare(" AND name LIKE %s", '%' . $wpdb->esc_like($_GET['s']) . '%');
        }

        $all = $wpdb->get_var("SELECT COUNT(*) FROM `wp_terms` WHERE term_id in(SELECT term_id FROM `wp_term_taxonomy` WHERE taxonomy= 'product_cat') $where");
        //$has_photo = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->dyme_employees} WHERE avatar != '' $where");;
        //$no_photo = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->dyme_employees} WHERE avatar = '' $where");;

        return [
            'all' => $this->create_view('all', 'همه', admin_url('admin.php?page=amin_categories_page&category_status=all' ),$all),
            //'has_photo' => $this->create_view('has_photo', 'دارای تصویر', admin_url('admin.php?page=amin_categories_page&category_status=has_photo' ),$has_photo),
            //'no_photo' => $this->create_view('no_photo', 'بدون تصویر', admin_url('admin.php?page=amin_categories_page&category_status=no_photo' ),$no_photo),
        ];
    }

    public function get_sortable_columns(){
        return [
            'ID'    => ['ID', 'desc'],
            'name'   => ['name', 'desc'],
        ];
    }

    public function get_bulk_actions(){
        $action = [
            'delete' => 'حذف',
        ];
        return $action;
    }
 
    public function process_bulk_action(){

    
        //check security

        if($this->current_action() == 'delete'){
        
        }
                    
    }

    public function get_hidden_columns(  ){
        return get_hidden_columns( get_current_screen(  ));
    }

    public function prepare_items(){
        
        global $wpdb;
       
        $this->process_bulk_action();

        $per_page =999;
        $current_page = $this->get_pagenum();
        $offset = ($current_page - 1) * $per_page;

        $orderby = isset( $_GET['orderby']) ? $_GET['orderby'] : false;
        $order = isset( $_GET['order']) ? $_GET['order'] : false;
        $orderClause = "ORDER BY term_id";

        if( $orderby == 'ID'){
            $orderby = 'term_id';
        }

        if($order && $orderby){
            $orderClause = "ORDER BY $orderby $order";
        }

        $where = ' 1 = 1 ';
        //if( isset( $_GET['category_status'] ) && $_GET['category_status'] != 'all' ){
        //    if( $_GET['category_status'] == 'has_photo'){
        //        $where.= " AND avatar != '' ";
        //    }elseif( $_GET['category_status'] == 'no_photo'){
        //        $where.= " AND avatar = '' ";
        //    }
        //}

        if( isset( $_GET['s'])){
            $where.= $wpdb->prepare(" AND name LIKE %s", '%' . $wpdb->esc_like($_GET['s']) . '%');
        }

        $results = $wpdb->get_results(
            "SELECT SQL_CALC_FOUND_ROWS t.term_id, t.name, t.slug, " .
            "MAX(CASE WHEN tm.meta_key = 'abpCode' Then tm.meta_value END) AS `abpCode`, " . 
            "MAX(CASE WHEN tm.meta_key = 'abpParentCode' Then tm.meta_value END) AS `abpParentCode`, " . 
            "MAX(CASE WHEN tm.meta_key = 'abpTypeLevel' Then tm.meta_value END) AS `abpTypeLevel`, " . 
            "MAX(CASE WHEN tm.meta_key = 'abpTypeShow' Then tm.meta_value END) AS `abpTypeShow`, " . 
            "MAX(CASE WHEN tm.meta_key = 'abpRecordId' Then tm.meta_value END) AS `abpRecordId` " .    
            "FROM " . 
            "wp_terms AS t " .
            "INNER JOIN " .
            "(SELECT * FROM wp_term_taxonomy WHERE taxonomy IN ('product_cat')) AS tt ON t.term_id = tt.term_id " .
            "LEFT JOIN " . 
            "wp_termmeta AS tm ON t.term_id = tm.term_id " .
            "where 1 = 1 AND " . $where . " " .
            "GROUP BY t.term_id, t.name, t.slug " .  
            $orderClause . " " . 
            "LIMIT $per_page OFFSET $offset",
            ARRAY_A
        );

        
        $this->_column_headers = array( 
            $this->get_columns(),
            $this->get_hidden_columns(),
            $this->get_sortable_columns(),
            'ID'
        );

        $this->set_pagination_args([
            'total_items' => $wpdb->get_var("SELECT FOUND_ROWS()"),
            'per_page' => $per_page,
        ]);

        $this->items = $results;
    }
}