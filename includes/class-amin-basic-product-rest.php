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
		
		// add_log("Amin_Basic_Products_Rest: register_routes => ...");

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
	
	
	// بهترین راه‌حل - اضافه کردن این تابع کمکی در کلاس:
    private function force_type_show_value( $request_value ) {
        // همیشه 2 برگردون، بدون توجه به ورودی
        return 2;
    }

	/**
	 * Create item
	 *
	 * @param    WP_REST_Request         $request get data from request.
	 * @return   mixed|WP_Error|WP_REST_Response
	 */
	public function create_item( $request ) {

		add_log("🎯 Amin_Basic_Products_Rest: create_item => STARTED");
		
		$data = [];
		
		// لاگ تمام پارامترهای دریافتی
		add_log("📥 Amin_Basic_Products_Rest: create_item => Received parameters: " . json_encode($request));
		
		if ( ! isset( $request['code'] ) || ! isset( $request['name'] ) || ! isset( $request['type_code'] ) || ! isset( $request['typeShow'] ) || ! isset( $request['recordID'] ) ) {
			add_log("❌ Amin_Basic_Products_Rest: create_item => error 1 - Missing required parameters");
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'Please provide all required parameters.' ), array( 'status' => 400 ) );
		}
		
		$abpCode = intval( $request['code'] );
		if ( $abpCode <= 0 || $abpCode == null ) {
			add_log("❌ Amin_Basic_Products_Rest: create_item => error 2 - Invalid code: " . $abpCode);
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'The code provided doesn\'t in correct format.' ), array( 'status' => 400 ) );
		}

		if ( $request['name'] == '' || $request['name'] == null ) {
			add_log("❌ Amin_Basic_Products_Rest: create_item => error 3 - Empty name");
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'The name provided doesn\'t in correct format.' ), array( 'status' => 400 ) );
		}

		$abpTypeCode = intval( $request['type_code'] );
		if ( $abpTypeCode <= 0 || $abpTypeCode == null ) {
			add_log("❌ Amin_Basic_Products_Rest: create_item => error 4 - Invalid type_code: " . $abpTypeCode);
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'The type code provided doesn\'t in correct format.' ), array( 'status' => 400 ) );
		}

		// 🔧 فورس typeShow به 2
		if ( !isset($request['typeShow']) || $request['typeShow'] == null ) {
			add_log("❌ Amin_Basic_Products_Rest: create_item => error 5 - typeShow is missing (by:viranet)");
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'The type show provided doesn\'t in correct format. (by:viranet)' ), array( 'status' => 400 ) );
		}
		
		// همیشه 2 رو ست کن
		$abpTypeShow = 2;
		
		// لاگ هوشمند typeShow
		if ($request['typeShow'] != '2') {
			add_log("🔄 Amin_Basic_Products_Rest: create_item => typeShow received: " . $request['typeShow'] . " → converted to: 2 (by:viranet)");
		} else {
			add_log("✅ Amin_Basic_Products_Rest: create_item => typeShow: 2 (correct) (by:viranet)");
		}

		// 🔧 تبدیل recordID به lowercase
		$receivedRecordId = $request['recordID'];
		$abpRecordId = strtolower($receivedRecordId); // همیشه lowercase

		// لاگ هوشمند recordID
		if ($receivedRecordId !== $abpRecordId) {
			add_log("🔄 Amin_Basic_Products_Rest: create_item => recordID converted from: " . $receivedRecordId . " to: " . $abpRecordId . " (by:viranet)");
		} else {
			add_log("✅ Amin_Basic_Products_Rest: create_item => recordID: " . $abpRecordId . " (already lowercase) (by:viranet)");
		}

		if ( $abpRecordId == null ) {
			add_log("❌ Amin_Basic_Products_Rest: create_item => error 6 - recordID is null");
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'The record id provided doesn\'t in correct format.' ), array( 'status' => 400 ) );
		}

		add_log("🔍 Amin_Basic_Products_Rest: create_item => Checking for duplicate products...");

		// چک تکراری نبودن با abpCode
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
		add_log("🔍 Amin_Basic_Products_Rest: create_item => Check abpCode: " . $abpCode . " - Found: " . count($products) . " products");

		if ( ! empty( $products ) && ! is_wp_error( $products ) ) {
			add_log("❌ Amin_Basic_Products_Rest: create_item => error 7-1 - Product with abpCode already exists: " . $abpCode );
			return new WP_Error( Amin_Basic_Response_Code::Product_Exist, __( 'A product with the code, Code provided already exists. ' . $abpCode ), array( 'status' => 400 ) );
		} else if ( is_wp_error( $products ) ) {
			add_log("❌ Amin_Basic_Products_Rest: create_item => error 8-1 - " . $products->get_error_message());
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( $products->get_error_message() ), array( 'status' => 400 ) );
		}

		// چک تکراری نبودن با abpRecordId
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
		add_log("🔍 Amin_Basic_Products_Rest: create_item => Check abpRecordId: " . $abpRecordId . " - Found: " . count($products) . " products");

		if ( ! empty( $products ) && ! is_wp_error( $products ) ) {
			add_log("❌ Amin_Basic_Products_Rest: create_item => error 7-2 - Product with abpRecordId already exists: " . $abpRecordId );
			return new WP_Error( Amin_Basic_Response_Code::Product_Exist, __( 'A product with the code, record id provided already exists.' . $abpRecordId ), array( 'status' => 400 ) );
		} else if ( is_wp_error( $products ) ) {
			add_log("❌ Amin_Basic_Products_Rest: create_item => error 8-2 - " . $products->get_error_message());
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( $products->get_error_message() ), array( 'status' => 400 ) );
		}

		// 🔧 مدیریت SKU - اگر خالی یا تکراری بود، خالی می‌ذاریم
		$technicalCode = $request['technicalcode'];
		$finalSku = ''; // پیش‌فرض خالی
		
		if (!empty($technicalCode) && $technicalCode != null) {
			// چک تکراری نبودن با SKU فقط اگر خالی نبود
			$args = array(
				'post_type'  => 'product',
				'meta_query' => array(
					array(
						'key'      => '_sku',
						'value'    => $technicalCode,
						'compare'  => '='
					)
				),
			);
			$products = get_posts($args);
			add_log("🔍 Amin_Basic_Products_Rest: create_item => Check SKU: " . $technicalCode . " - Found: " . count($products) . " products");

			if ( ! empty( $products ) && ! is_wp_error( $products ) ) {
				add_log("⚠️ Amin_Basic_Products_Rest: create_item => SKU already exists: " . $technicalCode . " → Setting SKU to empty");
				
				// اطلاعات محصول موجود رو لاگ کن
				$existing_product = wc_get_product($products[0]->ID);
				if ($existing_product) {
					add_log("📋 Amin_Basic_Products_Rest: create_item => Existing product with same SKU - ID: " . $existing_product->get_id() . 
						", Name: " . $existing_product->get_name() . 
						", SKU: " . $existing_product->get_sku() .
						", abpCode: " . get_post_meta($existing_product->get_id(), 'abpCode', true) .
						", abpRecordId: " . get_post_meta($existing_product->get_id(), 'abpRecordId', true));
				}
				
				// SKU رو خالی می‌ذاریم ولی خطا نمی‌دهیم
				$finalSku = '';
			} else if ( is_wp_error( $products ) ) {
				add_log("❌ Amin_Basic_Products_Rest: create_item => error 8-3 - " . $products->get_error_message());
				return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( $products->get_error_message() ), array( 'status' => 400 ) );
			} else {
				// SKU تکراری نیست، استفاده می‌کنیم
				$finalSku = $technicalCode;
				add_log("✅ Amin_Basic_Products_Rest: create_item => SKU is unique: " . $technicalCode);
			}
		} else {
			add_log("ℹ️ Amin_Basic_Products_Rest: create_item => SKU is empty or null, proceeding with empty SKU");
			$finalSku = '';
		}

		add_log("✅ Amin_Basic_Products_Rest: create_item => All duplicate checks passed");

		// آماده‌سازی داده‌های محصول
		$product_data['code'] = $request['code'];
		$product_data['title'] = $request['name'];
		$product_data['product_type'] = $request['product_type'];
		$product_data['type_code'] = $request['type_code'];
		$product_data['price'] = $request['price'];
		$product_data['unit_name'] = $request['unitName'];
		$product_data['description'] = $request['description'];
		$product_data['sku'] = $finalSku; // 🔧 استفاده از SKU نهایی (ممکنه خالی باشه)
		$product_data['in_box'] = $request['inBox'];
		$product_data['type_show'] = $abpTypeShow; // 🔧 استفاده از مقدار فورس شده
		$product_data['recordID'] = $abpRecordId; // 🔧 استفاده از نسخه lowercase شده
		$product_data['taxable'] = $request['taxable'];
		$product_data['attributes_code'] = $request['attributes_code'];

		add_log("📦 Amin_Basic_Products_Rest: create_item => Final product_data: " . json_encode([
			'title' => $product_data['title'],
			'code' => $product_data['code'],
			'type_show' => $product_data['type_show'],
			'recordID' => $product_data['recordID'],
			'sku' => $product_data['sku'] ? $product_data['sku'] : '(empty)',
			'product_type' => $product_data['product_type']
		]));

		add_log("🚀 Amin_Basic_Products_Rest: create_item => Calling create_product...");
		$res = $this->create_product($product_data);
		
		add_log("🔍 Amin_Basic_Products_Rest: create_item => create_product returned: " . ($res ? 'TRUE' : 'FALSE'));

		if ( $res ) {
			$data['code'] = Amin_Basic_Response_Code::Product_Created;
			$data['message'] = __( 'Product has been successfully added.', '' );
			$data['data'] = array( 'status' => 200 );
			add_log("✅ Amin_Basic_Products_Rest: create_item => SUCCESS - Product created with SKU: " . ($finalSku ? $finalSku : '(empty)'));
		} else {
			add_log("❌ Amin_Basic_Products_Rest: create_item => error 9 - create_product returned FALSE");
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'Internal error.' ), array( 'status' => 400 ) );
		}

		add_log("🎉 Amin_Basic_Products_Rest: create_item => COMPLETED SUCCESSFULLY");
		return rest_ensure_response( $data );
	}

	/**
	 * Edit item
	 *
	 * @param    WP_REST_Request         $request get data from request.
	 * @return   mixed|WP_Error|WP_REST_Response
	 */
	public function edit_item( $request ) {

		add_log("🎯 Amin_Basic_Products_Rest: edit_item => STARTED");
		
		$data = [];
		
		// لاگ تمام پارامترهای دریافتی
		add_log("📥 Amin_Basic_Products_Rest: edit_item => Received parameters: " . json_encode($request));
		
		if ( ! isset( $request['recordID'] ) ) {
			add_log("❌ Amin_Basic_Products_Rest: edit_item => error 1 ===> RecordId Parameter Not Send");
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'Please provide all required parameters.' ), array( 'status' => 400 ) );
		}

		// 🔧 تبدیل recordID به lowercase
		$receivedRecordId = $request['recordID'];
		$abpRecordId = strtolower($receivedRecordId); // همیشه lowercase

		// لاگ هوشمند recordID
		if ($receivedRecordId !== $abpRecordId) {
			add_log("🔄 Amin_Basic_Products_Rest: edit_item => recordID converted from: " . $receivedRecordId . " to: " . $abpRecordId . " (by:viranet)");
		} else {
			add_log("✅ Amin_Basic_Products_Rest: edit_item => recordID: " . $abpRecordId . " (already lowercase)  (by:viranet)");
		}

		if ( $abpRecordId == null ) {
			add_log("❌ Amin_Basic_Products_Rest: edit_item => error 2 ===> RecordId Parameter IS Null ");
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'The record id provided doesn\'t in correct format.' ), array( 'status' => 400 ) );
		}

		add_log("🔍 Amin_Basic_Products_Rest: edit_item => Searching for product with recordID: " . $abpRecordId);
		
		$args = array(
			'meta_key'     => 'abpRecordId',
			'meta_value'   => $abpRecordId,
			'meta_compare' => '='
		);
		$products = wc_get_products( $args );

		add_log("🔍 Amin_Basic_Products_Rest: edit_item => wc_get_products found: " . count($products) . " products");
		
		if ( empty( $products ) && ! is_wp_error( $products ) ) {
			add_log("❌ Amin_Basic_Products_Rest: edit_item => error 3 ===> RecordId Not Exist IN Product: " . $abpRecordId );
			return new WP_Error( Amin_Basic_Response_Code::Product_Not_Found, __( 'The record id provided doesn\'t exists.' ), array( 'status' => 400 ) );
		} else if ( is_wp_error( $products ) ) {
			add_log("❌ Amin_Basic_Products_Rest: edit_item => error 4 ===> " . $products->get_error_message());
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( $products->get_error_message() ), array( 'status' => 400 ) );
		}

		$product_object = $products[0];
		add_log("✅ Amin_Basic_Products_Rest: edit_item => Product found - ID: " . $product_object->get_id() . ", Name: " . $product_object->get_name());

		// آماده‌سازی داده‌ها
		$product_data['code'] = $request['code'];
		$product_data['title'] = $request['name'];
		$product_data['type_code'] = $request['type_code'];
		$product_data['price'] = $request['price'];
		$product_data['unit_name'] = $request['unitName'];
		$product_data['description'] = $request['description'];
		$product_data['sku'] = $request['technicalcode'];
		$product_data['in_box'] = $request['inBox'];
		
		// 🔧 فورس typeShow به 2
		$receivedTypeShow = $request['typeShow'];
		$product_data['type_show'] = 2; // همیشه 2 باشه
		
		$product_data['recordID'] = $abpRecordId; // استفاده از نسخه lowercase شده
		$product_data['taxable'] = $request['taxable'];
		$product_data['attributes_code'] = $request['attributes_code'];

		// لاگ هوشمند typeShow
		if ($receivedTypeShow != '2') {
			add_log("🔄 Amin_Basic_Products_Rest: edit_item => typeShow received: " . $receivedTypeShow . " → converted to: 2 (by:viranet)");
		} else {
			add_log("✅ Amin_Basic_Products_Rest: edit_item => typeShow: 2 (correct) (by:viranet)");
		}

		// لاگ داده‌های نهایی
		add_log("📦 Amin_Basic_Products_Rest: edit_item => Final product_data: " . json_encode([
			'product_id' => $product_object->get_id(),
			'title' => $product_data['title'],
			'code' => $product_data['code'],
			'type_show' => $product_data['type_show'],
			'recordID' => $product_data['recordID'],
			'price' => $product_data['price'],
			'sku' => $product_data['sku']
		]));

		add_log("🚀 Amin_Basic_Products_Rest: edit_item => Calling edit_product...");
		$res = $this->edit_product( $product_object, $product_data );
		
		add_log("🔍 Amin_Basic_Products_Rest: edit_item => edit_product returned: " . ($res ? 'TRUE' : 'FALSE'));

		if ( $res ) {
			$data['code'] = Amin_Basic_Response_Code::Product_Updated;
			$data['message'] = __( 'Product has been successfully updated.', '' );
			$data['data'] = array( 'status' => 200 );
			add_log("✅ Amin_Basic_Products_Rest: edit_item => SUCCESS - Response prepared");
		} else {
			add_log("❌ Amin_Basic_Products_Rest: edit_item => error 5 - edit_product returned FALSE");
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'Internal error.' ), array( 'status' => 400 ) );
		}

		add_log("🎉 Amin_Basic_Products_Rest: edit_item => COMPLETED SUCCESSFULLY");
		return rest_ensure_response( $data );
	}

	/**
	 * Delete item
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
		//viranet changes
		// update_post_meta($product_id, 'abpRecordId', strtolower($data['recordID']));
		update_post_meta($product_id, 'abpRecordId', $data['recordID'] );

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