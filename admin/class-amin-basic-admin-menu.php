<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://AliJannesary.ir
 * @since      1.0.0
 *
 * @package    Amin_Basic
 * @subpackage Amin_Basic/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Amin_Basic
 * @subpackage Amin_Basic/admin
 * @author     Ali Jannesary <https://AliJannesary.ir>
 */
class Amin_Basic_Admin_Menu {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * add menus
	 */
	public function add_menus() {
		
		$menu_suffix = add_menu_page( 
			'امین' , 
			'تنظیمات افزونه امین', 
			'manage_options', 
			'amin_dashboard', 
			array( $this, 'dashboard_page' ),
			AMIN_BASIC_DASHBOARD_IMAGE . 'amin-basic-menu.png',
			66
		);

		add_action('load-' . $menu_suffix, array( $this, 'amin_help_dashboard_page' ));

		add_submenu_page( 
			'amin_dashboard',
			 'روش پرداخت',
			 'روش پرداخت',
			 'manage_options', 
			 'amin_payment_page',
			 array( $this, 'amin_payment_page' )
		);

		add_submenu_page( 
			'amin_dashboard',
			 'تنظیمات ارسال',
			 'تنظیمات ارسال',
			 'manage_options', 
			 'amin_sending_setting_page',
			 array( $this, 'amin_sending_setting_page' )
		);

		add_submenu_page( 
			'amin_dashboard',
			 'لاگ افزونه امین',
			 'لاگ',
			 'manage_options', 
			 'amin_log_page',
			 array( $this, 'amin_log_page' )
		);

		$menu_suffix = add_submenu_page( 
			'amin_dashboard',
			 'دسته بندی ها',
			 'دسته بندی ها',
			 'manage_options', 
			 'amin_categories_page',
			 array( $this, 'amin_categories_page' )
		);
	
		add_action('load-' . $menu_suffix, array( $this, 'amin_process_table_category' ));

		$menu_suffix = add_submenu_page( 
			'amin_dashboard',
			 'محصولات',
			 'محصولات',
			 'manage_options', 
			 'amin_products_page',
			 array( $this, 'amin_products_page' )
		);
	
		add_action('load-' . $menu_suffix, array( $this, 'amin_process_table_product' ));

		$menu_suffix = add_submenu_page( 
			'amin_dashboard',
			 'مشتریان',
			 'مشتریان',
			 'manage_options', 
			 'amin_customers_page',
			 array( $this, 'amin_customers_page' )
		);
		
		add_action('load-' . $menu_suffix, array( $this, 'amin_process_table_customer' ));
	
	}
	
	/**
	 * تنظیمات افزونه امین
	 */
	public function dashboard_page() {

		 /**
		  * تنظیمات افزونه امین
		  */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/view/amin-basic-view-dashboard.php';

	}
	
	/**
	 * راهنما
	 *
	 * @return void
	 */
	public function amin_help_dashboard_page(){

		/**
		 * Manage help tabe
		 */

		 $screen = get_current_screen();
		 $screen->add_help_tab([
			'title' => 'شیوه پرداخت',
			'id' => 'gateway-help-tab',
			'content' => 'شیوه پرداخت از کجا تنظیم می شود؟',
			'callback' => function($scr, $tabs){
				echo '<p>در منوی ووکامرس، بخش پیکربندی، تب شیوه پرداخت انواع روش های پرداخت فعال می گردد</p>';
			}
		 ]);
		 $screen->add_help_tab([
			'title' => 'وضعیت سفارش',
			'id' => 'order-status-help-tab',
			'content' => 'وضعیت سفارش چیست؟',
			'callback' => function($scr, $tabs){
				echo '<p>هر سفارش پس درخواست مشتری ممکن است در حالت تایید، در حال انجام و ... قرار بگیرد</p>';
			}
		 ]);

		 $screen->set_help_sidebar(
			'<p><a href="https://aminsrp.ir" target="_blank">نرم افزار حسابداری امین</a></p>'
		 );

	}

	/**
	 * روش پرداخت
	 */
	public function amin_payment_page() {

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/view/amin-basic-view-payment.php';

	}

	/**
	 * تنظیمات ارسال
	 */
	public function amin_sending_setting_page() {

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/view/amin-basic-view-sending-setting.php';

	}
	/**
	 * لاگ
	 */
	public function amin_log_page() {

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/view/amin-basic-view-log.php';

	}

	public function amin_process_table_category(){
		
		require plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/ListTable/Category_List_Table.php';
		$GLOBALS['category_list_table'] = new Category_List_Table();
		$GLOBALS['category_list_table']->prepare_items();
	}
	
	/**
	 * دسته بندی
	 */
	public function amin_categories_page() {
		
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/view/amin-basic-view-categories.php';

	}
	
	public function amin_process_table_product(){
		
		require plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/ListTable/Product_List_Table.php';
		$GLOBALS['product_list_table'] = new Product_List_Table();
		$GLOBALS['product_list_table']->prepare_items();
	}

	/**
	 * محصول
	 */
	public function amin_products_page() {

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/view/amin-basic-view-products.php';

	}
	
	public function amin_process_table_customer(){
		
		require plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/ListTable/Customer_List_Table.php';
		$GLOBALS['customer_list_table'] = new Customer_List_Table();
		$GLOBALS['customer_list_table']->prepare_items();
	}

	/**
	 * مشتری
	 */
	public function amin_customers_page() {

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/view/amin-basic-view-customers.php';

	}
	
	/**
	 * منوی بالا
	 */
	public function add_bar_menus(WP_Admin_Bar $admin_bar) {

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		
		$title = sprintf('<img src="%samin-basic-menu.png" width="24" height="24" />', AMIN_BASIC_DASHBOARD_IMAGE );

		$admin_bar->add_menu( array(
			'id'    => 'menu-bar-amin-basic',
			'parent' => null,
			'group'  => null,
			'title' => $title, 
		) );

		$admin_bar->add_menu( array(
			'parent' => 'menu-bar-amin-basic',
			'id'    => 'menu-bar-amin-payment',
			'group'  => null,
			'title' => 'روش پرداخت', 
			'href'  => admin_url('admin.php?page=amin_payment_page'),
		) );
		
		$admin_bar->add_menu( array(
			'parent' => 'menu-bar-amin-basic',
			'id'    => 'menu-bar-amin-sending-setting',
			'group'  => null,
			'title' => 'تنظیمات ارسال', 
			'href'  => admin_url('admin.php?page=amin_sending_setting_page'),
		) );

		$admin_bar->add_menu( array(
			'parent' => 'menu-bar-amin-basic',
			'id'    => 'menu-bar-amin-log',
			'group'  => null,
			'title' => 'لاگ', 
			'href'  => admin_url('admin.php?page=amin_log_page'),
		) );
		
		$admin_bar->add_menu( array(
			'parent' => 'menu-bar-amin-basic',
			'id'    => 'menu-bar-amin-categories',
			'group'  => null,
			'title' => 'دسته بندی ها', 
			'href'  => admin_url('admin.php?page=amin_categories_page'),
		) );
		
		$admin_bar->add_menu( array(
			'parent' => 'menu-bar-amin-basic',
			'id'    => 'menu-bar-amin-products',
			'group'  => null,
			'title' => 'محصولات', 
			'href'  => admin_url('admin.php?page=amin_products_page'),
		) );
		
		$admin_bar->add_menu( array(
			'parent' => 'menu-bar-amin-basic',
			'id'    => 'menu-bar-amin-customers',
			'group'  => null,
			'title' => 'مشتریان', 
			'href'  => admin_url('admin.php?page=amin_customers_page'),
		) );		
	}

}
