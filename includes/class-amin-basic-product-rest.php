<?php

/**
 * Add rest api endpoint for products
 *
 * @link       https://AliJannesary.ir
 * @since      1.0.0
 *
 * @package    Amin_Basic
 * @subpackage Amin_Basic/includes
 */

/**
 * Add rest api endpoint for products.
 *
 * Maintain create, edit, delete and setQuantity methods for products.
 *
 * @package    Amin_Basic
 * @subpackage Amin_Basic/includes
 * @author     Ali Jannesary <https://AliJannesary.ir>
 */
class Amin_Basic_Products_Rest extends WC_REST_Products_Controller {
	/**
	 * The namespace.
	 *
     * @since    1.0.0
	 * @access   protected
	 * @var      string
	 */
	protected $namespace;
	/**
	 * Rest base for the current object.
	 *
     * @since    1.0.0
	 * @access   protected
	 * @var      string
	 */
	protected $rest_base;

	/**
	 * Initialize the main variables.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->namespace = 'wc/api';
		$this->rest_base = 'products';
	}

	/**
	 * Register the routes for the objects of the controller.
     * 
     * @since    1.0.0
	 */
	public function register_routes() {

		register_rest_route( $this->namespace, '/' . $this->rest_base . '/create', array(

			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'create_item' ),
				'permission_callback' => array( $this, 'create_permissions_check' )
			),
			'schema' => null

		) );

		register_rest_route( $this->namespace, '/' . $this->rest_base . '/edit', array(

			array(
				'methods'             => WP_REST_Server::EDITABLE,
				'callback'            => array( $this, 'edit_item' ),
				'permission_callback' => array( $this, 'edit_permissions_check' )
			),
			'schema' => null

		) );

		register_rest_route( $this->namespace, '/' . $this->rest_base . '/delete', array(

			array(
				'methods'             => WP_REST_Server::DELETABLE,
				'callback'            => array( $this, 'delete_item' ),
				'permission_callback' => array( $this, 'delete_permissions_check' )
			),
			'schema' => null

		) );

		register_rest_route( $this->namespace, '/' . $this->rest_base . '/setQuantity', array(

			array(
				'methods'             => WP_REST_Server::EDITABLE,
				'callback'            => array( $this, 'set_quantity_item' ),
				'permission_callback' => array( $this, 'set_quantity_permissions_check' )
			),
			'schema' => null

		) );
		
		add_log("Amin_Basic_Products_Rest: register_routes => ...");

	}

	/**
	 * Check permissions for the create.
	 *
     * @since    1.0.0
	 * @param    WP_REST_Request         $request get data from request.
	 * @return   bool|WP_Error
	 */
	public function create_permissions_check( $request ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			add_log("Amin_Basic_Products_Rest: create_permissions_check => error");
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'You cannot create the product resource.' ), array( 'status' => $this->authorization_status_code() ) );
		}
		add_log("Amin_Basic_Products_Rest: create_permissions_check => succeed");
		return true;
	}

	/**
	 * Check permissions for the edit.
	 *
     * @since    1.0.0
	 * @param    WP_REST_Request         $request get data from request.
	 * @return   bool|WP_Error
	 */
	public function edit_permissions_check( $request ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			add_log("Amin_Basic_Products_Rest: edit_permissions_check => error");
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'You cannot edit the product resource.' ), array( 'status' => $this->authorization_status_code() ) );
		}
		add_log("Amin_Basic_Products_Rest: edit_permissions_check => succeed");
		return true;
	}

	/**
	 * Check permissions for the delete.
	 *
     * @since    1.0.0
	 * @param    WP_REST_Request         $request get data from request.
	 * @return   bool|WP_Error
	 */
	public function delete_permissions_check( $request ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			add_log("Amin_Basic_Products_Rest: delete_permissions_check => error");
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'You cannot delete the product resource.' ), array( 'status' => $this->authorization_status_code() ) );
		}
		add_log("Amin_Basic_Products_Rest: delete_permissions_check => succeed");
		return true;
	}

	/**
	 * Check permissions for the setQuantity.
	 *
     * @since    1.0.0
	 * @param    WP_REST_Request         $request get data from request.
	 * @return   bool|WP_Error
	 */
	public function set_quantity_permissions_check( $request ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			add_log("Amin_Basic_Products_Rest: set_quantity_permissions_check => error");
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'You cannot set quantity of the product resource.' ), array( 'status' => $this->authorization_status_code() ) );
		}
		add_log("Amin_Basic_Products_Rest: set_quantity_permissions_check => succeed");
		return true;
	}

	/**
	 * Create product
	 *
	 * @param    WP_REST_Request         $request get data from request.
	 * @return   mixed|WP_Error|WP_REST_Response
	 */
	public function create_item( $request ) {

		$data = [];
		if ( ! isset( $request['code'] ) || ! isset( $request['name'] ) || ! isset( $request['type_code'] ) || ! isset( $request['typeShow'] ) || ! isset( $request['recordID'] ) ) {
			add_log("Amin_Basic_Products_Rest: create_item => error 1");
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'Please provide all required parameters.' ), array( 'status' => 400 ) );
		}
		
		$abpCode = intval( $request['code'] );
		if ( $abpCode <= 0 || $abpCode == null ) {
			add_log("Amin_Basic_Products_Rest: create_item => error 2");
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'The code provided doesn\'t in correct format.' ), array( 'status' => 400 ) );
		}

		if ( $request['name'] == '' || $request['name'] == null ) {
			add_log("Amin_Basic_Products_Rest: create_item => error 3");
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'The name provided doesn\'t in correct format.' ), array( 'status' => 400 ) );
		}

		$abpTypeCode = intval( $request['type_code'] );
		if ( $abpTypeCode <= 0 || $abpTypeCode == null ) {
			add_log("Amin_Basic_Products_Rest: create_item => error 4");
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'The type code provided doesn\'t in correct format.' ), array( 'status' => 400 ) );
		}

		$abpTypeShow = intval( $request['typeShow'] );
		if ( ( $abpTypeShow != 2 && $abpTypeShow != 1 ) || $abpTypeShow == null ) {
			add_log("Amin_Basic_Products_Rest: create_item => error 5");
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'The type show provided doesn\'t in correct format.' ), array( 'status' => 400 ) );
		}

		$abpRecordId = $request['recordID'];
		if ( $abpRecordId == null ) {
			add_log("Amin_Basic_Products_Rest: create_item => error 6");
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'The record id provided doesn\'t in correct format.' ), array( 'status' => 400 ) );
		}

		$args = array(
			'post_type'  => 'product',
			'meta_query' => array(
				array(
				   'key'       => 'abpCode',
				   'value'     => $abpCode,
				   'compare'   => '='
				)
			),
		);
		$products = get_posts($args);

		if ( ! empty( $products ) && ! is_wp_error( $products ) ) {
			add_log("Amin_Basic_Products_Rest: create_item => error 7-1 abpCode: " . $abpCode );
			return new WP_Error( Amin_Basic_Response_Code::Product_Exist, __( 'A product with the code, Code provided already exists. ' . $abpCode ), array( 'status' => 400 ) );
		} else if ( is_wp_error( $products ) ) {
			add_log("Amin_Basic_Products_Rest: create_item => error 8");
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( $products->get_error_message() ), array( 'status' => 400 ) );
		}

		$args = array(
			'post_type'  => 'product',
			'meta_query' => array(
				array(
					'key'      => 'abpRecordId',
					'value'    => $abpRecordId,
					'compare'  => '='
				)
			),
		);
		$products = get_posts($args);

		if ( ! empty( $products ) && ! is_wp_error( $products ) ) {
			add_log("Amin_Basic_Products_Rest: create_item => error 7-2 abpRecordId: " . $abpRecordId );
			return new WP_Error( Amin_Basic_Response_Code::Product_Exist, __( 'A product with the code, record id provided already exists.' . $abpRecordId ), array( 'status' => 400 ) );
		} else if ( is_wp_error( $products ) ) {
			add_log("Amin_Basic_Products_Rest: create_item => error 8");
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( $products->get_error_message() ), array( 'status' => 400 ) );
		}


		$args = array(
			'post_type'  => 'product',
			'meta_query' => array(
				array(
					'key'      => '_sku',
					'value'    => $request['technicalcode'],
					'compare'  => '='
				)
			),
		);
		$products = get_posts($args);

		if ( ! empty( $products ) && ! is_wp_error( $products ) ) {
			add_log("Amin_Basic_Products_Rest: create_item => error 7-3 _sku: " . $request['technicalcode'] );
			return new WP_Error( Amin_Basic_Response_Code::Product_Exist, __( 'A product with the code, sku provided already exists.' . $request['technicalcode'] ), array( 'status' => 400 ) );
		} else if ( is_wp_error( $products ) ) {
			add_log("Amin_Basic_Products_Rest: create_item => error 8");
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( $products->get_error_message() ), array( 'status' => 400 ) );
		}


		$product_data['code'] = $request['code'];
		$product_data['title'] = $request['name'];
		$product_data['product_type'] = $request['product_type'];
		$product_data['type_code'] = $request['type_code'];
		$product_data['price'] = $request['price'];
		$product_data['unit_name'] = $request['unitName'];
		$product_data['description'] = $request['description'];
		$product_data['sku'] = $request['technicalcode'];
		$product_data['in_box'] = $request['inBox'];
		$product_data['type_show'] = $request['typeShow'];
		$product_data['recordID'] = $request['recordID'];
		$product_data['taxable'] = $request['taxable'];
		$product_data['attributes_code'] = $request['attributes_code'];

		$res = $this->create_product($product_data);

		if ( $res ) {
			$data['code'] = Amin_Basic_Response_Code::Product_Created;
			$data['message'] = __( 'Product has been successfully added.', '' );
			$data['data'] = array( 'status' => 200 );
		} else {
			add_log("Amin_Basic_Products_Rest: create_item => error 9");
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'Internal error.' ), array( 'status' => 400 ) );
		}

		add_log("Amin_Basic_Products_Rest: create_item => succeed");
		return rest_ensure_response( $data );
	}

	/**
	 * Edit product
	 *
	 * @param    WP_REST_Request         $request get data from request.
	 * @return   mixed|WP_Error|WP_REST_Response
	 */
	public function edit_item( $request ) {

		$data = [];
		if ( ! isset( $request['recordID'] ) ) {
			add_log("Amin_Basic_Products_Rest: edit_item => error 1 ===> RecordId Parameter Not Send");
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'Please provide all required parameters.' ), array( 'status' => 400 ) );
		}
		/*
		if ( isset( $request['code'] ) ) {
			$abpCode = intval( $request['code'] );
			if ( $abpCode <= 0 || $abpCode == null ) {
				return new WP_Error( 7, __( 'The code provided doesn\'t in correct format.' ), array( 'status' => 400 ) );
			}
		}

		if ( isset( $request['name'] ) ) {
			if ( $request['name'] == '' || $request['name'] == null ) {
				return new WP_Error( 7, __( 'The name provided doesn\'t in correct format.' ), array( 'status' => 400 ) );
			}
		}

		if ( isset( $request['type_code'] ) ) {
			$abpTypeCode = intval( $request['type_code'] );
			if ( $abpTypeCode <= 0 || $abpTypeCode == null ) {
				return new WP_Error( 7, __( 'The type code provided doesn\'t in correct format.' ), array( 'status' => 400 ) );
			}
		}

		if ( isset( $request['price'] ) ) {
			$abpPrice = intval( $request['price'] );
			if ( $abpPrice < 0 || $abpPrice == null ) {
				return new WP_Error( 7, __( 'The price provided doesn\'t in correct format.' ), array( 'status' => 400 ) );
			}
		}

		if ( isset( $request['unitName'] ) ) {
			if ( $request['unitName'] == '' || $request['unitName'] == null ) {
				return new WP_Error( 7, __( 'The unit name provided doesn\'t in correct format.' ), array( 'status' => 400 ) );
			}
		}

		if ( isset( $request['description'] ) ) {
			if ( $request['description'] == null ) {
				return new WP_Error( 7, __( 'The description provided doesn\'t in correct format.' ), array( 'status' => 400 ) );
			}
		}

		if ( isset( $request['technicalcode'] ) ) {
			if ( $request['technicalcode'] == null ) {
				return new WP_Error( 7, __( 'The technicalcode provided doesn\'t in correct format.' ), array( 'status' => 400 ) );
			}
		}

		if ( isset( $request['inBox'] ) ) {
			$abpInBox = intval( $request['inBox'] );
			if ( $abpInBox < 0 || $abpInBox == null ) {
				return new WP_Error( 7, __( 'The inBox provided doesn\'t in correct format.' ), array( 'status' => 400 ) );
			}
		}

		if ( isset( $request['typeShow'] ) ) {
			$abpTypeShow = intval( $request['typeShow'] );
			if ( ( $abpTypeShow != 0 && $abpTypeShow != 1 ) || $abpTypeShow == null ) {
				return new WP_Error( 7, __( 'The type show provided doesn\'t in correct format.' ), array( 'status' => 400 ) );
			}
		}
		*/
		$abpRecordId = $request['recordID'];
		if ( $abpRecordId == null ) {
			add_log("Amin_Basic_Products_Rest: edit_item => error 2 ===> RecordId Parameter IS Null ");
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'The record id provided doesn\'t in correct format.' ), array( 'status' => 400 ) );
		}

		$args = array(
			'meta_key'     => 'abpRecordId',
			'meta_value'   => $abpRecordId,
			'meta_compare' => '='
		);
		$products = wc_get_products( $args );

		
		if ( empty( $products ) && ! is_wp_error( $products ) ) {
			add_log("Amin_Basic_Products_Rest: edit_item => error 3 ===> RecordId Not Exist IN Product: " . $abpRecordId );
			return new WP_Error( Amin_Basic_Response_Code::Product_Not_Found, __( 'The record id provided doesn\'t exists.' ), array( 'status' => 400 ) );
		} else if ( is_wp_error( $products ) ) {
			add_log("Amin_Basic_Products_Rest: edit_item => error 4");
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( $products->get_error_message() ), array( 'status' => 400 ) );
		}

		$product_object = $products[0];

		$product_data['code'] = $request['code'];
		$product_data['title'] = $request['name'];
		$product_data['type_code'] = $request['type_code'];
		$product_data['price'] = $request['price'];
		$product_data['unit_name'] = $request['unitName'];
		$product_data['description'] = $request['description'];
		$product_data['sku'] = $request['technicalcode'];
		$product_data['in_box'] = $request['inBox'];
		$product_data['type_show'] = $request['typeShow'];
		$product_data['recordID'] = $request['recordID'];
		$product_data['taxable'] = $request['taxable'];
		$product_data['attributes_code'] = $request['attributes_code'];
add_log(' recordID:' .$request['recordID'] . ' typeShow:' . $request['typeShow']);
		$res = $this->edit_product( $product_object, $product_data );

		if ( $res ) {
			$data['code'] = Amin_Basic_Response_Code::Product_Updated;
			$data['message'] = __( 'Product has been successfully updated.', '' );
			$data['data'] = array( 'status' => 200 );
		} else {
			add_log("Amin_Basic_Products_Rest: edit_item => error 5");
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'Internal error.' ), array( 'status' => 400 ) );
		}

		add_log("Amin_Basic_Products_Rest: edit_item => succeed");
		return rest_ensure_response( $data );
	}

	/**
	 * Delete product
	 *
	 * @param    WP_REST_Request         $request get data from request.
	 * @return   mixed|WP_Error|WP_REST_Response
	 */
	public function delete_item( $request ) {

		$data = [];
		if ( ! isset( $request['recordID'] ) ) {
			add_log("Amin_Basic_Products_Rest: delete_item => error 1");
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'Please provide all required parameters.' ), array( 'status' => 400 ) );
		}

		$abpRecordId = $request['recordID'];
		if ( $abpRecordId != null ) {
			$args = array(
				'post_type'  => 'product',
				'meta_key'     => 'abpRecordId',
				'meta_value'   => $abpRecordId,
				'meta_compare' => '='
			);
			$postObject = get_posts($args);

			if ( count($postObject) > 0 ) {
				$res = wp_delete_post($postObject[0]->ID, true);
			} else {
				add_log("Amin_Basic_Products_Rest: delete_item => error 2");
				return new WP_Error( Amin_Basic_Response_Code::Product_Not_Found, __( 'The record id provided doesn\'t exist.' ), array( 'status' => 400 ) );
			}
		} else {
			add_log("Amin_Basic_Products_Rest: delete_item => error 3");
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'The record id provided doesn\'t in correct format.' ), array( 'status' => 400 ) );
		}

		if ( $res ) {
			$data['code'] = Amin_Basic_Response_Code::Product_Deleted;
			$data['message'] = __( 'Product has been successfully deleted.', '' );
			$data['data'] = array( 'status' => 200 );
		} else {
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'Internal error.' ), array( 'status' => 400 ) );
		}

		add_log("Amin_Basic_Products_Rest: delete_item => succeed");
		return rest_ensure_response( $data );
	}

	/**
	 * Set qunatity of product
	 *
	 * @param    WP_REST_Request         $request get data from request.
	 * @return   mixed|WP_Error|WP_REST_Response
	 */
	public function set_quantity_item( $request ) {

		$data = [];
		if ( ! isset( $request['itemCode'] ) || ! isset( $request['total'] ) ) {
			add_log("Amin_Basic_Products_Rest: set_quantity_item => error 1");
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'Please provide all required parameters.' ), array( 'status' => 400 ) );
		}

		$args = array(
			'meta_key'     => 'abpCode',
			'meta_value'   => $request['itemCode'],
			'meta_compare' => '='
		);
		$productObject = wc_get_products( $args );

		if ( isset( $request['itemCode2'] ) && $request['itemCode2'] > 0 ) {
			$foundItemCode2 = false;

			$variations_arr = $productObject[0]->get_available_variations();
			foreach ( $variations_arr as $variation_arr ) {
				$variation = wc_get_product( $variation_arr['variation_id'] );
				$variation_attribute = $variation->get_attributes();
				$termObject = get_term_by( 'slug', reset( $variation_attribute ), key( $variation_attribute ) );
				$termCode = get_term_meta( $termObject->term_id, 'abpCode', true ); 
				if ( $termCode == $request['itemCode2'] ) {
					$res = wc_update_product_stock( $variation_arr['variation_id'], $request['total'] );
					$foundItemCode2 = true;
				}
			}

			if ( !$foundItemCode2 ){
				add_log("Amin_Basic_Products_Rest: set_quantity_item => error 2");
				return new WP_Error( Amin_Basic_Response_Code::Product_Not_Found, __( 'The item code 2 provided doesn\'t exist.' ), array( 'status' => 400 ) );
			}
		} else {
			if ( count( $productObject ) > 0 ) {
				$res = wc_update_product_stock( $productObject[0], $request['total'] );
			} else {
				add_log("Amin_Basic_Products_Rest: set_quantity_item => error 3 " . $request['itemCode']);
				return new WP_Error( Amin_Basic_Response_Code::Product_Not_Found, __( 'The item code provided doesn\'t exist.' ), array( 'status' => 400 ) );
			}
		}

		if ( $res !== null && ! is_wp_error( $res ) ) {
			$data['code'] = Amin_Basic_Response_Code::Product_Updated;
			$data['message'] = __( 'Product has been successfully updated.', '' );
			$data['data'] = array( 'status' => 200 );
		} else {
			add_log("Amin_Basic_Products_Rest: set_quantity_item => error 4");
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'Internal error.' ), array( 'status' => 400 ) );
		}

		add_log("Amin_Basic_Products_Rest: set_quantity_item => succeed");
		return rest_ensure_response( $data );
	}

	/**
	 * Sets up the proper HTTP status code for authorization.
	 *
	 * @return int
	 */
	public function authorization_status_code() {

		$status = 401;

		if ( is_user_logged_in() ) {
			$status = 403;
		}

		return $status;
	}

	/**
	 * Utility method for create a product (simple or variable)
	 *
	 * @return int|bool
	 */
	public function create_product( $data ){	
		$productName = sanitize_title( $data['title'] );
	
		$productData = array(
			'post_name'     => $productName,
			'post_title'    => $data['title'],
			'post_content'  => $data['description'],
			'post_status'   => 'publish',
			'post_type'     => 'product'
			//'guid'          => home_url( '/product/'.$postname.'/' ),
		);
	
		// Create the product (post data)
		$product_id = wp_insert_post( $productData );

		if ( ! empty( $product_id ) ) {
			// If product type is variable, get an instance of the WC_Product_Variable object and save it
			if ($data['product_type'] == 'variable') {
				$product = new WC_Product_Variable( $product_id );
				$product->save();
			} else {
				$product = new WC_Product( $product_id );
				$product->save();
			}
		} else {
			return false;
		}
	
		// Set price (for simple product)
		if ( $data['product_type'] == 'simple' ) {
			if ( ! empty( $data['price'] ) && $data['price'] != null )
				$product->set_regular_price( $data['price'] );
		}
	
		// Set sku
		if( ! empty( $data['sku'] ) && $data['sku'] != null )
			$product->set_sku( $data['sku'] );
	
		// Set tax status
		$productTaxable = intval( $data['taxable'] );
		if ( $productTaxable == 0 ) {
			$product->set_tax_status( 'none' );
		} else {
			$product->set_tax_status( 'taxable' );
		}

		// Set category
		if ( ! empty( $data['type_code'] ) && $data['type_code'] != null ) {
			$categoryArgs = array(
				'hide_empty' => false,
				'meta_query' => array(
					array(
					   'key'       => 'abpCode',
					   'value'     => $data['type_code'],
					   'compare'   => '='
					)
				),
				'taxonomy'  => 'product_cat',
			);
			$categories = get_terms( $categoryArgs );

			if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) {
				$product->set_category_ids( array( $categories[0]->term_id ) );
			}
		}

		if ($data['attributes_code'] != '' && $data['attributes_code'] != null) {
			$product_attributes = array();
			
			foreach( $data['attributes_code'] as $attribute_code ){
				$attribute_id = $this->get_attribute_id_from_code( $attribute_code );
				if ( $attribute_id ) {
					$attributeObject = wc_get_attribute( $attribute_id );
					$product_attributes[$attributeObject->slug] = array (
						'name'         => $attributeObject->slug,
						'value'        => '',
						'position'     => '',
						'is_visible'   => 0,
						'is_variation' => ( $data['product_type'] == 'simple' ? 0 : 1 ),
						'is_taxonomy'  => 1
					);
				}
			}
			update_post_meta( $product_id, '_product_attributes', $product_attributes );
		}

		update_post_meta( $product_id, '_manage_stock', true );
		update_post_meta( $product_id, 'abpCode', $data['code'] );
		update_post_meta( $product_id, 'abpTypeShow', $data['type_show'] );
		update_post_meta( $product_id, 'abpUnitName', $data['unit_name'] );
		update_post_meta( $product_id, 'abpInBox', $data['in_box'] );
		update_post_meta($product_id, 'abprecordid', strtolower($data['recordID']));
		$res = $product->save();

		add_log("Amin_Basic_Products_Rest: create_product => ...");
		return $res;
	}

	/**
	 * Utility method for edit a product (simple or variable)
	 *
	 * @return int|bool
	 */
	public function edit_product( $product, $data ){
		$product_id = $product->get_id();

		if ( $data['code'] > 0 && $data['code'] != null )
			update_post_meta( $product_id, 'abpCode', $data['code'] );

		$notUpdateNameKala = get_option('notUpdateNameKala'); 
		if ($notUpdateNameKala != 'on'){		
			if ( $data['title'] != '' && $data['title'] != null ) {
				wp_update_post([
					'ID'         => $product_id,
					'post_title' => $data['title'],
					'post_name'  => sanitize_title( $data['title'] )
				]);
			}
		}

		if ( $data['type_code'] > 0 && $data['type_code'] != null ) {
			$newCategories = [];
			$currentCategories = $product->get_category_ids();
			foreach( $currentCategories as $currentCategory ) {
				$category = get_term_by( 'id', $currentCategory, 'product_cat' );
				$categoryAbpCode = get_term_meta( $currentCategory, 'abpCode', true );

				if( $categoryAbpCode == '' )
					$newCategories[] = $currentCategory;
			}
			$categoryArgs = array(
				'hide_empty' => false,
				'meta_query' => array(
					array(
					   'key'       => 'abpCode',
					   'value'     => $data['type_code'],
					   'compare'   => '='
					)
				),
				'taxonomy'  => 'product_cat',
			);
			$categories = get_terms( $categoryArgs );
			$newCategories[] = $categories[0]->term_id;

			if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) {
				$product->set_category_ids( $newCategories );
				$product->save();
			}
		}

		if ( $product->is_type( 'simple' ) ) {
			if ( $data['price'] >= 0 && $data['price'] != null )
				$product->set_regular_price( $data['price'] );
		}

		if ( $data['unit_name'] != '' && $data['unit_name'] != null )
			update_post_meta( $product_id, 'abpUnitName', $data['unit_name'] );

		if ( $data['description'] != '' && $data['description'] != null ) {
			wp_update_post([
				'ID'           => $product_id,
				'post_content' => $data['description'],
			]);
		}

		if( ! empty( $data['sku'] ) && $data['sku'] != null )
			$product->set_sku( $data['sku'] );

		if( $data['in_box'] >= 0 && $data['in_box'] != null )
			update_post_meta( $product_id, 'abpInBox', $data['in_box'] );

		if ( $data['type_show'] != null ) {
			if( $data['type_show'] == 1 || $data['type_show'] == 2 ) {
				update_post_meta( $product_id, 'abpTypeShow', $data['type_show'] );
			}
		}

		if ( $data['taxable'] != null ) {
			if ( $data['taxable'] == 0 ) {
				$product->set_tax_status( 'none' );
			} else {
				$product->set_tax_status( 'taxable' );
			}
		}

		if ($data['attributes_code'] != '' && $data['attributes_code'] != null) {
			$product_attributes = array();
			
			foreach( $data['attributes_code'] as $attribute_code ){
				$attribute_id = $this->get_attribute_id_from_code( $attribute_code );
				if ( $attribute_id ) {
					$attributeObject = wc_get_attribute( $attribute_id );
					$product_attributes[$attributeObject->slug] = array (
						'name'         => $attributeObject->slug,
						'value'        => '',
						'position'     => '',
						'is_visible'   => 0,
						'is_variation' => 1,
						'is_taxonomy'  => 1
					);
				}
			}
			update_post_meta( $product_id, '_product_attributes', $product_attributes );
		}

		$res = $product->save();

		add_log("Amin_Basic_Products_Rest: edit_product => ...");
		return $res;
	}

	/**
	 * Utility method for get attribute id by abpCode meta
	 *
	 * @return int|bool
	 */
	public function get_attribute_id_from_code( $code ){
	    global $wpdb;
	    $attribute_id = $wpdb->get_col("SELECT attribute_id
	    FROM {$wpdb->prefix}abp_attributes
	    WHERE abpCode = {$code}");
		$id = reset($attribute_id);
		if ( $id && $id > 0 && $id != null)
	    	return $id;
		else 
			return false;
	}
}