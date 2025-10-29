<?php

if( !class_exists('WP_List_Table') ){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class Customer_List_Table extends WP_List_Table{

    public function get_columns(){
        
        return [
            'cb'        => '<input type="checkbox"/>',
            'ID' => 'شناسه',
            'name' => 'نام',
            'user_email' => 'ایمیل',
            'user_url' => 'سایت',
            'abpCustomerID' => 'کد مشتری',
            'digits_phone' => 'تلفن دی جیتس',
        ];
    }

    public function column_default( $item, $column_name){
        if( isset( $item[$column_name])){
            return $item[$column_name];
        }
        return '-';
    }
    
    public function column_cb($item){
        return '<input type="checkbox" name="Customer[]" value="'. $item['ID'] . '"/>';
    } 

    public function column_name($item){

        $actions = [
            'edit' => '<a href="#" target="_blank">ویرایش</a>',
            'delete' => '<a href="#" onclick="return confirm(\'اطمینان دارید؟\');">حذف</a>',
            //'show' => '<a href="' . get_permalink( $item['ID'] ) . '" target="_blank">نمایش</a>',
        ];

        return $item['display_name'] . $this->row_actions($actions);
    }

    public function no_items(){
        echo 'کاربری یافت نشد';
    }

    private function create_view( $key, $label, $url, $count = 0){
        $current_status = isset($_GET['customer_status']) ? $_GET['customer_status'] : 'all';
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
            $where= $wpdb->prepare(" AND display_name LIKE %s", '%' . $wpdb->esc_like($_GET['s']) . '%');
        }

        $all = $wpdb->get_var("SELECT COUNT(*) FROM `wp_users` WHERE 1=1 $where");
        //$has_photo = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->dyme_employees} WHERE avatar != '' $where");;
        //$no_photo = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->dyme_employees} WHERE avatar = '' $where");;

        return [
            'all' => $this->create_view('all', 'همه', admin_url('admin.php?page=amin_customers_page&customer_status=all' ),$all),
            //'has_photo' => $this->create_view('has_photo', 'دارای تصویر', admin_url('admin.php?page=amin_customers_page&customer_status=has_photo' ),$has_photo),
            //'no_photo' => $this->create_view('no_photo', 'بدون تصویر', admin_url('admin.php?page=amin_customers_page&customer_status=no_photo' ),$no_photo),
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
        $orderClause = "ORDER BY ID";

        if( $orderby == 'name'){
            $orderby = 'display_name';
        }

        if($order && $orderby){
            $orderClause = "ORDER BY $orderby $order";
        }

        $where = ' 1 = 1 ';
        //if( isset( $_GET['customer_status'] ) && $_GET['customer_status'] != 'all' ){
        //    if( $_GET['customer_status'] == 'has_photo'){
        //        $where.= " AND avatar != '' ";
        //    }elseif( $_GET['customer_status'] == 'no_photo'){
        //        $where.= " AND avatar = '' ";
        //    }
        //}

        if( isset( $_GET['s'])){
            $where.= $wpdb->prepare(" AND display_name LIKE %s", '%' . $wpdb->esc_like($_GET['s']) . '%');
        }
        
        //get_users( array( 'role__in' => array( 'author', 'subscriber' ) ) )

        $results = $wpdb->get_results(
            "SELECT SQL_CALC_FOUND_ROWS u.ID, u.display_name, u.user_email, u.user_url, " .
            "MAX(CASE WHEN um.meta_key = 'abpCustomerID' Then um.meta_value END) AS `abpCustomerID`, " . 
            "MAX(CASE WHEN um.meta_key = 'digits_phone' Then um.meta_value END) AS `digits_phone` " . 
            "FROM " . 
            "wp_users AS u " .
            "LEFT JOIN " . 
            "wp_usermeta AS um ON u.ID = um.user_id " .
            "where 1 = 1 AND " . $where . " " .
            "GROUP BY u.ID, u.display_name, u.user_email, u.user_url " .  
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