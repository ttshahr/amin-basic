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
class Amin_Basic_Admin {

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
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Amin_Basic_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Amin_Basic_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/amin-basic-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Amin_Basic_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Amin_Basic_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/amin-basic-admin.js', array( 'jquery' ), $this->version, false );

	}

	function abp_add_metabox(){

		add_meta_box( 
			'abp_product_meta', 
			'اطلاعات افزونه امین', 
			array( $this, 'abp_metabox_callback'), 
			'product'
		);
	}

	function abp_metabox_callback($post){
	
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/metabox/amin-basic-metabox.php';
	}

	function abp_add_dashboard_widget(){

		wp_add_dashboard_widget( 
			'abp_dashboard_widget', 
			'اطلاعات افزونه امین', 
			array( $this, 'abp_add_dashboard_widget_render'), 
			array( $this, 'abp_add_dashboard_widget_control') 
		);
	}

	function abp_add_dashboard_widget_render(){

		echo 'This is dashboard widget';
	}

	function abp_add_dashboard_widget_control(){

		echo 'config';
	}
}
