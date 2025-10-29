<?php

/**
 * Add rest api endpoint for order
 *
 * @link       https://AliJannesary.ir
 * @since      1.0.0
 *
 * @package    Amin_Basic
 * @subpackage Amin_Basic/includes
 */

/**
 * Add rest api endpoint for order.
 *
 * Maintain GetNewAllOrders, setOrderStateReiceved methods for order.
 *
 * @package    Amin_Basic
 * @subpackage Amin_Basic/includes
 * @author     Ali Jannesary <https://AliJannesary.ir>
 */
class Amin_Basic_Orders_Rest extends WC_REST_Orders_Controller {
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
		$this->rest_base = 'orders';
	}

	/**
	 * Register the routes for the objects of the controller.
     * 
     * @since    1.0.0
	 */
	public function register_routes() {

		register_rest_route( $this->namespace, '/' . $this->rest_base . '/GetNewAllOrders', array(

			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_all_new_orders' ),
				'permission_callback' => array( $this, 'get_all_new_orders_permissions_check' )
			),
			'schema' => null

		) );

		register_rest_route( $this->namespace, '/' . $this->rest_base . '/setOrderStateReiceved', array(

			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'set_order_state_reiceved' ),
				'permission_callback' => array( $this, 'set_order_state_reiceved_permissions_check' )
			),
			'schema' => null

		) );
		
		add_log("Amin_Basic_Orders_Rest: register_routes => ...");

	}

	/**
	 * Check permissions for the GetNewAllOrders.
	 *
     * @since    1.0.0
	 * @param    WP_REST_Request         $request get data from request.
	 * @return   bool|WP_Error
	 */
	public function get_all_new_orders_permissions_check( $request ) {
		if ( ! current_user_can( 'read' ) ) {
			add_log("Amin_Basic_Orders_Rest: get_all_new_orders_permissions_check => error");
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'You cannot get the order resource.' ), array( 'status' => $this->authorization_status_code() ) );
		}
		add_log("Amin_Basic_Orders_Rest: get_all_new_orders_permissions_check => succeed");
		return true;
	}

	/**
	 * Check permissions for the setOrderStateReiceved.
	 *
     * @since    1.0.0
	 * @param    WP_REST_Request         $request get data from request.
	 * @return   bool|WP_Error
	 */
	public function set_order_state_reiceved_permissions_check( $request ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			add_log("Amin_Basic_Orders_Rest: set_order_state_reiceved_permissions_check => error");
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'You cannot edit the order resource.' ), array( 'status' => $this->authorization_status_code() ) );
		}
		add_log("Amin_Basic_Orders_Rest: set_order_state_reiceved_permissions_check => succeed");
		return true;
	}

	/**
	 * Get all new orders
	 *
	 * @param    WP_REST_Request         $request get data from request.
	 * @return   mixed|WP_Error|WP_REST_Response
	 */
	public function get_all_new_orders( $request ) {
		global $wpdb, $table_prefix;
		$newOrderStatusesTable = $table_prefix . 'amin_basic_new_order_statuses';
		$newOrderStatuses = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$newOrderStatusesTable}" ) );

		$orders = [];
		$new_orders = [];
		foreach( $newOrderStatuses as $newOrderStatus ) {
			$ab = wc_get_orders( array(
				'status'         => array( $newOrderStatus->order_status ),
				'payment_method' => $newOrderStatus->gateway
			) );
			$new_orders = array_merge( $new_orders, $ab );
		}

		foreach ($new_orders as $new_order) {
			$payment_method = $new_order->get_payment_method();
			$order_status = $new_order->get_status();
			$order_status_code = $this->get_order_status_code( $order_status );
			$customer_id = $new_order->get_customer_id();
			$customer = new WC_Customer( $customer_id );
			$customerMeta = get_user_meta( $customer_id );
			$order_date_created = $new_order->get_date_created();
			$order_shipping_address = $new_order->get_shipping_address_1() . $new_order->get_shipping_address_2();
			$order_billing_address = $new_order->get_billing_address_1() . $new_order->get_billing_address_2();
			$order_location = $new_order->get_meta( 'abpLocation', true );
			$order_country = $new_order->get_shipping_country() != '' ? $new_order->get_shipping_country() : $new_order->get_billing_country();
			$order_city = $new_order->get_shipping_city() != '' ? $new_order->get_shipping_city() : $new_order->get_billing_city();
			$order_state = $new_order->get_shipping_state() != '' ? $new_order->get_shipping_state() : $new_order->get_billing_state();
			$order_state = WC()->countries->states[$order_country][$order_state];
			$order_shipping_name = $new_order->get_shipping_first_name() . ' ' . $new_order->get_shipping_last_name();
			$order_delivery_time = $new_order->get_meta( 'abpDeliveryTime', true );

			$order_date_paid = $new_order->get_date_paid();
			if ( $order_date_paid != null )
				$order_date_paid = $order_date_paid->date('Y-m-d');

			$transaction_id = $new_order->get_transaction_id();
			$order_price = $new_order->get_total();

			$cost_detail = [];
			$order_discount = $new_order->get_total_discount();
			if ( $order_discount > 0 ) {
				array_push( $cost_detail, array (
					'costTitle' => 'تخفیف',
					'costType'  => 'کاهنده',
					'Cost'      => $order_discount
				) );
			}

			$order_taxes = (float)$new_order->get_total_tax();
			if ( $order_taxes > 0 ) {
				array_push( $cost_detail, array (
					'costTitle' => 'مالیات',
					'costType'  => 'افزاینده',
					'Cost'      => $order_taxes
				) );
			}

			$order_shipping = (float)$new_order->get_shipping_total();
			if ( $order_shipping > 0 ) {
				array_push( $cost_detail, array (
					'costTitle' => 'حمل و نقل',
					'costType'  => 1,
					'Cost'      => $order_shipping
				) );
			}

			$order_fees = (float)$new_order->get_total_fees();
			if ( $order_fees > 0 ) {
				array_push( $cost_detail, array (
					'costTitle' => 'دستمزد',
					'costType'  => 'افزاینده',
					'Cost'      => $order_fees
				) );
			}

			$order_products = [];
			$order_items = $new_order->get_items();
			foreach ( $order_items as $order_item ) {
				$product = $order_item->get_product();
				$product_id = $product->get_id();
				if ( $product->is_type('variation') ) {
					$product_arr['itemCode'] = get_post_meta( $product->get_parent_id(), 'abpCode', true );
				} else {
					$product_arr['itemCode'] = get_post_meta( $product_id, 'abpCode', true );
				}
				$product_arr['total'] = $order_item->get_quantity();
				$product_arr['recordID'] = get_post_meta( $product_id, 'abpRecordId', true );
				$product_arr['price'] = $product->get_price();
				if ( $product->is_on_sale() ) {
					$product_arr['Discount'] = $product->get_price() - $product->get_sale_price();
					$product_arr['discountPercent'] = ( $product_arr['Discount'] * 100 ) / $product->get_regular_price();
				}

				if ( $product->is_type( 'simple' ) || $product->is_type( 'variable' ) ) {
					$product_arr['packet'] = get_post_meta( $product_id, 'abpInBox', true );
				} else if ( $product->is_type( 'variation' ) ) {
					$product_arr['packet'] = get_post_meta( $product->get_parent_id(), 'abpInBox', true );
					$variation_attribute = $product->get_attributes();
					reset($variation_attribute);
					$term_id = get_term_by( 'slug', reset($variation_attribute), key($variation_attribute) )->term_id;
					$variation_attribute_value_code = get_term_meta( $term_id, 'abpCode', true);
					$product_arr['itemCode2'] = $variation_attribute_value_code;
				}

				array_push( $order_products, $product_arr );
			}

			//if ( ( $payment_method == 'bacs' && $order_status == 'on-hold' ) || ( $payment_method != 'bacs' && $order_status == 'processing' ) ) {
				array_push( $orders, array (
					'CustomerName'    => $customer->get_display_name(),
					'CustomerCode'    => 0,
					'CustomerID'      => $customerMeta['abpCustomerID'][0],
					'order_No'        => $new_order->get_order_number(),
					'StatusCode'      => $order_status_code,
					'paymentData'     => array (
						'customerHesabCode' => 0,
						'payHesabCode'      => 0,
						'orderDate'         => $order_date_paid,
						'paySerial'         => $transaction_id,
						'payPrice'          => $order_price,
						'payMemo'           => ''
					),
					'CostDetail'      => $cost_detail,
					'products'        => $order_products,
					'orderDate'       => $order_date_created->date('Y-m-d'),
					'orderTime'       => $order_date_created->date('H:i'),
					'Address'         => $order_shipping_address != '' ? $order_shipping_address : $order_billing_address,
					'location'        => $order_location,
					'city'            => $order_city,
					'township'        => $order_state,
					'deliveryTime'    => '',
					'deliveryMessage' => $new_order->get_customer_note(),
					'Mobile'          => $customerMeta['abpMobile'][0],
					'Phone'           => $new_order->get_billing_phone(),
					'fullName'        => $order_shipping_name,
					'orderDiscount'   => $new_order->get_discount_total(),
					'description'     => ''
				) );
			//}
		}

		if ( count( $orders ) > 0 ) {
			$data['data'] = array( 'status' => 200, 'result' => $orders );
		} else {
			add_log("Amin_Basic_Orders_Rest: get_all_new_orders => error");
			return new WP_Error( Amin_Basic_Response_Code::New_Orders_Not_Found, __( 'No new order found.' ), array( 'status' => 400 ) );
        }

		add_log("Amin_Basic_Orders_Rest: get_all_new_orders => succeed");
		return rest_ensure_response( $data );
	}

	/**
	 * Set order status
	 *
	 * @param    WP_REST_Request         $request get data from request.
	 * @return   mixed|WP_Error|WP_REST_Response
	 */
	public function set_order_state_reiceved( $request ) {

		if ( ! isset( $request['order_no'] ) || ! isset( $request['status_code'] ) ) {
			add_log("Amin_Basic_Orders_Rest: set_order_state_reiceved => error 1");
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'Please provide all required parameters.' ), array( 'status' => 400 ) );
		}

		$order = wc_get_order( $request['order_no'] );
		if ( $order ) {
			$status = $this->get_order_status_name( $request['status_code'] );
			$res = $order->update_status( $status );
		} else {
			add_log("Amin_Basic_Orders_Rest: set_order_state_reiceved => error 2");
			return new WP_Error( Amin_Basic_Response_Code::Order_Number_Not_Found, __( 'Order number not found.' ), array( 'status' => 400 ) );
		}

		if ( $res ) {
			$data['code'] = Amin_Basic_Response_Code::Order_Status_Updated;
			$data['message'] = __( 'Order status has been successfully updated.', '' );
			$data['data'] = array( 'status' => 200 );
		} else {
			add_log("Amin_Basic_Orders_Rest: set_order_state_reiceved => error 3");
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'Internal error.' ), array( 'status' => 400 ) );
		}

		add_log("Amin_Basic_Orders_Rest: set_order_state_reiceved => succeed");
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
	 * Utility method for .......
	 *
	 * @return int
	 */
	public function get_order_status_code( $status ) {
		$status_code = 0;
		switch ( $status ) {
			case 'pending':
				$status_code = 1;
				break;
			case 'on-hold':
				$status_code = 2;
				break;
			case 'processing':
				$status_code = 3;
				break;
			case 'completed':
				$status_code = 4;
				break;
			case 'cancelled':
				$status_code = 5;
				break;
			case 'refunded':
				$status_code = 6;
				break;
			case 'failed':
				$status_code = 7;
				break;
			default:
				$status_code = 0;
		}
		return $status_code;
	}

	public function get_order_status_name( $status_code ) {
		$status_name = '';
		switch ( $status_code ) {
			case 1:
				$status_name = 'pending';
				break;
			case 2:
				$status_name = 'on-hold';
				break;
			case 3:
				$status_name = 'processing';
				break;
			case 4:
				$status_name = 'completed';
				break;
			case 5:
				$status_name = 'cancelled';
				break;
			case 6:
				$status_name = 'refunded';
				break;
			case 7:
				$status_name = 'failed';
				break;
			default:
				$status_name = 'processing';
		}
		return $status_name;
	}
}