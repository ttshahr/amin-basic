<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://AliJannesary.ir
 * @since      1.0.0
 *
 * @package    Amin_Basic
 * @subpackage Amin_Basic/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Amin_Basic
 * @subpackage Amin_Basic/public
 * @author     Ali Jannesary <https://AliJannesary.ir>
 */
class Amin_Basic_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		$this->load_dependencies();

	}

		/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Amin_Basic_Loader. Orchestrates the hooks of the plugin.
	 * - Amin_Basic_i18n. Defines internationalization functionality.
	 * - Amin_Basic_Admin. Defines all hooks for the admin area.
	 * - Amin_Basic_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-amin-basic-category-rest.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-amin-basic-product-rest.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-amin-basic-customer-rest.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-amin-basic-product-variation-rest.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-amin-basic-attribute-rest.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-amin-basic-attribute-term-rest.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-amin-basic-order-rest.php';

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/amin-basic-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/amin-basic-public.js', array( 'jquery' ), $this->version, false );

	}

	/**
	* .
	* 
	* @since	1.0.0
	*/
	public function filter_products( $query ) {
		if( ! is_admin() && $query->is_main_query() && ( $query->query_vars['post_type'] == 'product' || isset( $query->query_vars['product_cat'] ) ) ) {
			$query->set( 'meta_query', array(
        	    array(
        	        'key'     => 'abpTypeShow',
        	        'value'   => 2,
        	        'compare' => '=',
        	    )
        	) );
    	}
	}

	/**
	* .
	* 
	* @since	1.0.0
	*/
	public function filter_terms( $terms, $taxonomies, $args ) {
		$new_terms = array();
		if ( in_array( 'product_cat', $taxonomies ) && !is_admin() ) {
			foreach ( $terms as $key => $term ) {
				$termTypeShow = get_term_meta( $term->term_id, 'abpTypeShow', true );
				if ( $termTypeShow ) {
					$new_terms[] = $term;
				}
			}
			$terms = $new_terms;
		}
		return $terms;
	}

	/**
	* .
	*
	* @since    1.0.0
	*/
	public function register_category_controller() {
		$controller = new Amin_Basic_Product_Categories_Rest();
		$controller->register_routes();
	}

	/**
	* .
	*
	* @since    1.0.0
	*/
	public function register_customer_controller() {
		$controller = new Amin_Basic_Customers_Rest();
		$controller->register_routes();
	}

	/**
	* .
	*
	* @since    1.0.0
	*/
	public function register_product_controller() {
		$controller = new Amin_Basic_Products_Rest();
		$controller->register_routes();
	}
	
	/**
	* .
	*
	* @since    1.0.0
	*/
	public function register_product_variation_controller() {
		$controller = new Amin_Basic_Product_Variations_Rest();
		$controller->register_routes();
	}

	/**
	* .
	*
	* @since    1.0.0
	*/
	public function register_attribute_controller() {
		$controller = new Amin_Basic_Product_Attributes_Rest();
		$controller->register_routes();
	}

	/**
	* .
	*
	* @since    1.0.0
	*/
	public function register_attribute_term_controller() {
		$controller = new Amin_Basic_Product_Attributes_Terms_Rest();
		$controller->register_routes();
	}

	/**
	* .
	*
	* @since    1.0.0
	*/
	public function register_order_controller() {
		$controller = new Amin_Basic_Orders_Rest();
		$controller->register_routes();
	}
}
