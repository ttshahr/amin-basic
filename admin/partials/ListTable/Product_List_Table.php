<?php

if( !class_exists('WP_List_Table') ){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class Product_List_Table extends WP_List_Table{

    public function get_columns(){
        
        return [
            'cb'        => '<input type="checkbox"/>',
            'ID' => 'شناسه',
            'name' => 'نام',
            'post_name' => 'نامک',
            'abpCode' => 'کد',
            'abpInBox' => 'تعداد در بسته',
            'abpRecordId' => 'رکوردآی دی',
            'abpTypeShow' => 'نمایش',
            'abpUnitName' => 'واحد',
            '_price' => 'قیمت',
            '_regular_price' => 'قیمت عادی',
            '_manage_stock' => 'manage_stock',
            '_stock' => 'stock',
        ];
    }

    public function column_default( $item, $column_name){
        if( isset( $item[$column_name])){
            return $item[$column_name];
        }
        return '-';
    }
    
    public function column_cb($item){
        return '<input type="checkbox" name="Product[]" value="'. $item['ID'] . '"/>';
    } 

    public function column_name($item){

        $actions = [
            'edit' => '<a href="#" target="_blank">ویرایش</a>',
            'delete' => '<a href="#" onclick="return confirm(\'اطمینان دارید؟\');">حذف</a>',
            'show' => '<a href="' . get_permalink( $item['ID'] ) . '" target="_blank">نمایش</a>',
        ];

        return $item['post_title'] . $this->row_actions($actions);
    }

    public function no_items(){
        echo 'محصولی یافت نشد';
    }

    private function create_view( $key, $label, $url, $count = 0){
        $current_status = isset($_GET['product_status']) ? $_GET['product_status'] : 'all';
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
            $where= $wpdb->prepare(" AND post_title LIKE %s", '%' . $wpdb->esc_like($_GET['s']) . '%');
        }

        $all = $wpdb->get_var("SELECT COUNT(*) FROM `wp_posts` WHERE post_type='product' $where");
        //$has_photo = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->dyme_employees} WHERE avatar != '' $where");;
        //$no_photo = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->dyme_employees} WHERE avatar = '' $where");;

        return [
            'all' => $this->create_view('all', 'همه', admin_url('admin.php?page=amin_products_page&product_status=all' ),$all),
            //'has_photo' => $this->create_view('has_photo', 'دارای تصویر', admin_url('admin.php?page=amin_products_page&product_status=has_photo' ),$has_photo),
            //'no_photo' => $this->create_view('no_photo', 'بدون تصویر', admin_url('admin.php?page=amin_products_page&product_status=no_photo' ),$no_photo),
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
            $orderby = 'post_title';
        }

        if($order && $orderby){
            $orderClause = "ORDER BY $orderby $order";
        }

        $where = ' 1 = 1 ';
        //if( isset( $_GET['product_status'] ) && $_GET['product_status'] != 'all' ){
        //    if( $_GET['product_status'] == 'has_photo'){
        //        $where.= " AND avatar != '' ";
        //    }elseif( $_GET['product_status'] == 'no_photo'){
        //        $where.= " AND avatar = '' ";
        //    }
        //}

        if( isset( $_GET['s'])){
            $where.= $wpdb->prepare(" AND post_title LIKE %s", '%' . $wpdb->esc_like($_GET['s']) . '%');
        }

        $results = $wpdb->get_results(
            "SELECT SQL_CALC_FOUND_ROWS p.ID, p.post_title, p.post_name, " .
            "MAX(CASE WHEN pm.meta_key = 'abpCode' Then pm.meta_value END) AS `abpCode`, " . 
            "MAX(CASE WHEN pm.meta_key = 'abpInBox' Then pm.meta_value END) AS `abpInBox`, " . 
            "MAX(CASE WHEN pm.meta_key = 'abpRecordId' Then pm.meta_value END) AS `abpRecordId`, " . 
            "MAX(CASE WHEN pm.meta_key = 'abpTypeShow' Then pm.meta_value END) AS `abpTypeShow`, " . 
            "MAX(CASE WHEN pm.meta_key = 'abpUnitName' Then pm.meta_value END) AS `abpUnitName`, " . 
            "MAX(CASE WHEN pm.meta_key = '_price' Then pm.meta_value END) AS `_price`, " . 
            "MAX(CASE WHEN pm.meta_key = '_regular_price' Then pm.meta_value END) AS `_regular_price`, " . 
            "MAX(CASE WHEN pm.meta_key = '_manage_stock' Then pm.meta_value END) AS `_manage_stock`, " . 
            "MAX(CASE WHEN pm.meta_key = '_stock' Then pm.meta_value END) AS `_stock` " . 
            "FROM " . 
            "(SELECT * FROM wp_posts WHERE post_type='product') AS p " .
            "LEFT JOIN " . 
            "wp_postmeta AS pm ON p.ID = pm.post_id " .
            "where 1 = 1 AND " . $where . " " .
            "GROUP BY p.ID, p.post_title, p.post_name " .  
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