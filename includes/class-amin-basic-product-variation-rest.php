<?php

/**
 * Add rest api endpoint for product variations
 *
 * @link       https://AliJannesary.ir
 * @since      1.0.0
 *
 * @package    Amin_Basic
 * @subpackage Amin_Basic/includes
 */

/**
 * Add rest api endpoint for product variations.
 *
 * Maintain create, edit, delete methods for product variations.
 *
 * @package    Amin_Basic
 * @subpackage Amin_Basic/includes
 * @author     Ali Jannesary <https://AliJannesary.ir>
 */
class Amin_Basic_Product_Variations_Rest extends WC_REST_Product_Variations_Controller {
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
		$this->rest_base = 'products/variations';
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
		
		// add_log("Amin_Basic_Product_Variations_Rest: register_routes => ...");

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
			add_log("Amin_Basic_Product_Variations_Rest: create_permissions_check => error");
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'You cannot create the product variation resource.' ), array( 'status' => $this->authorization_status_code() ) );
		}
		add_log("Amin_Basic_Product_Variations_Rest: create_permissions_check => succeed");
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
			add_log("Amin_Basic_Product_Variations_Rest: edit_permissions_check => error");
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'You cannot edit the product variation resource.' ), array( 'status' => $this->authorization_status_code() ) );
		}
		add_log("Amin_Basic_Product_Variations_Rest: edit_permissions_check => succeed");
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
			add_log("Amin_Basic_Product_Variations_Rest: delete_permissions_check => error");
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'You cannot delete the product variation resource.' ), array( 'status' => $this->authorization_status_code() ) );
		}
		add_log("Amin_Basic_Product_Variations_Rest: delete_permissions_check => succeed");
		return true;
	}

	/**
	 * Create product variation
	 *
	 * @param    WP_REST_Request         $request get data from request.
	 * @return   mixed|WP_Error|WP_REST_Response
	 */
	public function create_item( $request ) {

		$data = [];
		if ( ! isset( $request['code'] ) || ! isset( $request['parent_code'] ) || ! isset( $request['attributes_value'] ) || ! isset( $request['recordID'] ) ) {
			add_log("Amin_Basic_Product_Variations_Rest: create_item => error 1");
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'Please provide all required parameters.' ), array( 'status' => 400 ) );
		}

		$args = array(
			'post_type'    => 'product_variation',
			'meta_query'   => array(
				'relation' => 'OR',
				array(
					'key'     => 'abpCode',
					'value'   => $request['code'],
					'compare' => '='
				),
				array(
					'key'     => 'abpRecordId',
					'value'   => $request['recordID'],
					'compare' => '='
				),
				array(
					'key'     => '_sku',
					'value'   => $request['technicalcode'],
					'compare' => '='
				)
			)
		);
		$postObject = get_posts( $args );

		if ( count( $postObject ) > 0 ) {
			add_log("Amin_Basic_Product_Variations_Rest: create_item => error 2");
			return new WP_Error( Amin_Basic_Response_Code::Product_Exist, __( 'A product with the code, record id or sku provided already exists.' ), array( 'status' => 400 ) );
		}

        if ( $request['parent_code'] > 0 ) {
            $args = array(
                'meta_key'     => 'abpCode',
                'meta_value'   => $request['parent_code'],
                'meta_compare' => '='
            );
            $products = wc_get_products( $args );
    
            if ( ! empty( $products ) && ! is_wp_error( $products ) ) {
                if ( $products[0]->is_type('variable') ) {
                    $variation_data['code'] = $request['code'];
                    $variation_data['parent_id'] = $products[0]->get_id();
                    $variation_data['parent_code'] = $request['parent_code'];
                    $variation_data['attributes_value'] = $request['attributes_value'];
                    $variation_data['price'] = $request['price'];
                    $variation_data['description'] = $request['description'];
                    $variation_data['sku'] = $request['technicalcode'];
                    $variation_data['recordID'] = $request['recordID'];
            
                    $res = $this->create_variation($variation_data);
                } else {
					add_log("Amin_Basic_Product_Variations_Rest: create_item => error 3");
					return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'The parent product is type of simple.' ), array( 'status' => 400 ) );
                }
            }
        }

		if ( $res ) {
			$data['code'] = Amin_Basic_Response_Code::Product_Created;
			$data['message'] = __( 'Product has been successfully added.', '' );
			$data['data'] = array( 'status' => 200 );
		} else {
			add_log("Amin_Basic_Product_Variations_Rest: create_item => error 4");
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'Internal error.' ), array( 'status' => 400 ) );
		}

		add_log("Amin_Basic_Product_Variations_Rest: create_item => succeed");
		return rest_ensure_response( $data );
	}

	/**
	 * Edit product variation
	 *
	 * @param    WP_REST_Request         $request get data from request.
	 * @return   mixed|WP_Error|WP_REST_Response
	 */
	public function edit_item( $request ) {

		$data = [];
		if ( ! isset( $request['recordID'] ) ) {
			add_log("Amin_Basic_Product_Variations_Rest: edit_item => error 1");
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'Please provide all required parameters.' ), array( 'status' => 400 ) );
		}

		$abpRecordId = $request['recordID'];
		if ( $abpRecordId == null ) {
			add_log("Amin_Basic_Product_Variations_Rest: edit_item => error 2");
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'The record id provided doesn\'t in correct format.' ), array( 'status' => 400 ) );
		}

		if ( isset( $request['technicalcode'] ) ) {
			$args = array(
				'post_type'    => 'product_variation',
				'meta_query'   => array(
					array(
						'meta_key'     => '_sku',
						'meta_value'   => $request['technicalcode'],
						'meta_compare' => '='
					)
				)
			);
			$postObject = get_posts( $args );
	
			if ( count( $postObject ) > 0 ) {
				add_log("Amin_Basic_Product_Variations_Rest: edit_item => error 3");
				return new WP_Error( Amin_Basic_Response_Code::Product_Exist, __( 'A product with the sku provided already exists.' ), array( 'status' => 400 ) );
			}
		}

		$args = array(
			'post_type'    => 'product_variation',
			'meta_query'   => array(
				array(
					'meta_key'     => 'abpRecordId',
					'meta_value'   => $request['recordID'],
					'meta_compare' => '='
				)
			)
		);
		$postObject = get_posts( $args );

		if ( empty( $postObject ) && ! is_wp_error( $postObject ) ) {
			add_log("Amin_Basic_Product_Variations_Rest: edit_item => error 4");
			return new WP_Error( Amin_Basic_Response_Code::Product_Not_Found, __( 'The record id provided doesn\'t exists.' ), array( 'status' => 400 ) );
		} else if ( is_wp_error( $postObject ) ) {
			add_log("Amin_Basic_Product_Variations_Rest: edit_item => error 5");
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( $postObject->get_error_message() ), array( 'status' => 400 ) );
		}

		

		$variation_object = wc_get_product( $postObject[0] );

		$variation_data['code'] = $request['code'];
		$variation_data['attributes_value'] = $request['attributes_value'];
		$variation_data['price'] = $request['price'];
		$variation_data['description'] = $request['description'];
		$variation_data['sku'] = $request['technicalcode'];
		$variation_data['recordID'] = $request['recordID'];

		$res = $this->edit_variation($variation_object, $variation_data);

		if ( $res ) {
			$data['code'] = Amin_Basic_Response_Code::Product_Updated;
			$data['message'] = __( 'Product category has been successfully updated.', '' );
			$data['data'] = array( 'status' => 200 );
		} else {
			add_log("Amin_Basic_Product_Variations_Rest: edit_item => error 6");
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'Internal error.' ), array( 'status' => 400 ) );
		}

		add_log("Amin_Basic_Product_Variations_Rest: edit_item => succeed");
		return rest_ensure_response( $data );
	}

	/**
	 * Delete product variation
	 *
	 * @param    WP_REST_Request         $request get data from request.
	 * @return   mixed|WP_Error|WP_REST_Response
	 */
	public function delete_item( $request ) {

		$data = [];
		if ( ! isset( $request['recordID'] ) ) {
			add_log("Amin_Basic_Product_Variations_Rest: delete_item => error 1");
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'Please provide all required parameters.' ), array( 'status' => 400 ) );
		}

		$abpRecordId = $request['recordID'];
		if ( $abpRecordId != null ) {
			$args = array(
				'post_type'    => 'product_variation',
				'meta_key'     => 'abpRecordId',
				'meta_value'   => $abpRecordId,
				'meta_compare' => '='
			);
			$postObject = get_posts($args);

			if ( count($postObject) > 0 ) {
				$res = wp_delete_post($postObject[0]->ID, true);
			} else {
				add_log("Amin_Basic_Product_Variations_Rest: delete_item => error 2");
				return new WP_Error( Amin_Basic_Response_Code::Product_Not_Found, __( 'The record id provided doesn\'t exist.' ), array( 'status' => 400 ) );
			}
		} else {
			add_log("Amin_Basic_Product_Variations_Rest: delete_item => error 2");
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'The record id provided doesn\'t in correct format.' ), array( 'status' => 400 ) );
		}

		if ( $res ) {
			$data['code'] = Amin_Basic_Response_Code::Product_Deleted;
			$data['message'] = __( 'Product has been successfully deleted.', '' );
			$data['data'] = array( 'status' => 200 );
		} else {
			add_log("Amin_Basic_Product_Variations_Rest: delete_item => error 3");
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'Internal error.' ), array( 'status' => 400 ) );
		}

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
	 * Utility method for create a product variation
	 *
	 * @return int
	 */
	public function create_variation( $data ) {

        $variation_object = new WC_Product_Variation();

        if ( $data['price'] >= 0 )
            $variation_object->set_regular_price( $data['price'] );

        $variation_object->set_parent_id( $data['parent_id'] );
        
        if( ! empty( $data['sku'] ) && $data['sku'] != null )
            $variation_object->set_sku( $data['sku'] );

        if( $data['description'] != '' )
            $variation_object->set_description( $data['description'] );

        
        $var_attributes = array();
        foreach( $data["attributes_value"] as $key => $value ){
            $attribute_id = $this->get_attribute_id_from_code( $key );
            if ( $attribute_id ) {
                $attribute_object = wc_get_attribute( $attribute_id );
                $attribute_terms = get_terms( array(
                    'taxonomy'   => $attribute_object->slug,
                    'hide_empty' => false,
                    'meta_query' => array(
                        'key'     => 'abpCode',
                        'value'   => $value,
                        'compare' => '='
                    )
                ) );

				if ( count( $attribute_terms ) > 0 ) {
                	$var_attributes[ $attribute_object->slug ] = $attribute_terms[0]->slug;
					wp_set_post_terms( $data['parent_id'], $attribute_terms[0]->name, $attribute_object->slug, true );
				}
            }
        }
        $variation_object->set_attributes($var_attributes);
        $variation_id = $variation_object->save();

        if ( $data['recordID'] > 0 )
            update_post_meta($variation_id, 'abpRecordId', $data['recordID']);
		
		if ( $data['code'] > 0 )
            update_post_meta($variation_id, 'abpCode', $data['code']);

		update_post_meta( $variation_id, '_manage_stock', true );

		add_log("Amin_Basic_Product_Variations_Rest: create_variation => ...");
		if ( $variation_id )
		    return true;
        else
            return false;
	}

	/**
	 * Utility method for edit a product variation
	 *
	 * @return int|bool
	 */
	public function edit_variation( $product, $data ) {
		$product_id = $product->ID;

		if ( $data['code'] > 0 && $data['code'] != null )
			update_post_meta( $product_id, 'abpCode', $data['code'] );

		if ( $data['price'] >= 0 && $data['price'] != null )
            $product->set_regular_price( $data['price'] );

		if( ! empty( $data['sku'] ) && $data['sku'] != null )
            $product->set_sku( $data['sku'] );

        if( $data['description'] != '' && $data['description'] != null )
            $product->set_description( $data['description'] );

		if ( $data["attributes_value"] != null ) {
			$var_attributes = array();
			foreach( $data["attributes_value"] as $key => $value ){
				$attribute_id = $this->get_attribute_id_from_code( $key );
				if ( $attribute_id ) {
					$attribute_object = wc_get_attribute( $attribute_id );
					$attribute_terms = get_terms( array(
						'taxonomy'   => $attribute_object->slug,
						'hide_empty' => false,
						'meta_query' => array(
							'key'     => 'abpCode',
							'value'   => $value,
							'compare' => '='
						)
					) );

					if ( count( $attribute_terms ) > 0 ) {
						$var_attributes[ $attribute_object->slug ] = $attribute_terms[0]->slug;
						wp_set_post_terms( $data['parent_id'], $attribute_terms[0]->name, $attribute_object->slug, true );
					}
				}
			}
			if ( count( $var_attributes ) > 0 )
				$product->set_attributes($var_attributes);
		}

		update_post_meta( $product_id, '_manage_stock', true );
		
		$res = $product->save();

		add_log("Amin_Basic_Product_Variations_Rest: edit_variation => ...");
		return $res;
	}

	/**
	 * Utility method for get attribute id by abpCode meta
	 *
	 * @return int|bool
	 */
	public function get_attribute_id_from_code( $code ) {
	    global $wpdb;
	    $attribute_id = $wpdb->get_col("SELECT attribute_id
	    FROM {$wpdb->prefix}abp_attributes
	    WHERE abpCode = {$code}");
		$id = reset($attribute_id);
		add_log("Amin_Basic_Product_Variations_Rest: get_attribute_id_from_code => ...");
		if ( $id && $id > 0 && $id != null)
	    	return $id;
		else 
			return false;
	}
}